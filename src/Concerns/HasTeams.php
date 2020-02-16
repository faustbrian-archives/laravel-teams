<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Teams.
 *
 * (c) KodeKeep <hello@kodekeep.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KodeKeep\Teams\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Config;
use KodeKeep\Teams\Contracts\Team;
use KodeKeep\Teams\Events\TeamMemberLeft;
use KodeKeep\Teams\Exceptions\TeamException;

trait HasTeams
{
    public function invitations(): HasMany
    {
        return $this->hasMany(Config::get('teams.models.invitation'));
    }

    public function hasTeams(): bool
    {
        return $this->teams->isNotEmpty();
    }

    public function teams(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Config::get('teams.models.team'),
                'team_users',
                'user_id',
                'team_id'
            )
            ->using(Config::get('teams.models.member'))
            ->withPivot(['role', 'permissions'])
            ->orderBy('name', 'asc');
    }

    public function onTeam(Team $team): bool
    {
        return $this->teams->contains($team);
    }

    public function ownsTeam(Team $team): bool
    {
        return $this->id && $team->owner_id && $this->id === $team->owner_id;
    }

    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Config::get('teams.models.team'), 'owner_id');
    }

    public function roleOn(Team $team): ?string
    {
        if ($team = $this->teams->find($team->id)) {
            return $team->pivot->role;
        }

        return null;
    }

    public function roleOnCurrentTeam(): string
    {
        return $this->roleOn($this->currentTeam);
    }

    public function getCurrentTeamAttribute(): ?Team
    {
        return $this->currentTeam();
    }

    public function currentTeam(): ?Team
    {
        if (! $this->hasTeams()) {
            return null;
        }

        if (! is_null($this->current_team_id)) {
            $currentTeam = $this->teams->find($this->current_team_id);

            return $currentTeam ?: $this->refreshCurrentTeam();
        }

        $this->switchToTeam($this->teams()->first());

        return $this->currentTeam();
    }

    public function ownsCurrentTeam(): bool
    {
        $currentTeam = $this->currentTeam();

        if (! $currentTeam) {
            return false;
        }

        $ownerId = (int) $currentTeam->owner_id;

        return $currentTeam && $ownerId === $this->id;
    }

    public function switchToTeam(Team $team): void
    {
        if (! $this->onTeam($team)) {
            throw TeamException::doesNotBelongToTeam();
        }

        $this->current_team_id = $team->id;

        $this->save();
    }

    public function refreshCurrentTeam(): ?Team
    {
        $this->current_team_id = null;

        $this->save();

        return $this->currentTeam();
    }

    public function leaveTeam(Team $team): void
    {
        if (! $this->onTeam($team)) {
            throw TeamException::doesNotBelongToTeam();
        }

        $team->removeMember($this);

        TeamMemberLeft::dispatch($team, $this);
    }
}
