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

namespace KodeKeep\Teams\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Config;
use KodeKeep\Teams\Events\TeamMemberCreated;
use KodeKeep\Teams\Events\TeamMemberDeleted;
use KodeKeep\Teams\Events\TeamOwnerCreated;

trait HasMembers
{
    public function members(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Config::get('teams.models.user'),
                Config::get('teams.tables.members'),
                'team_id',
                'user_id'
            )
            ->using(Config::get('teams.models.member'))
            ->withPivot(['role', 'permissions']);
    }

    public function addMember($user, string $role, array $permissions): void
    {
        $this->members()->detach($user);

        $this->members()->attach($user, compact('role', 'permissions'));

        unset($this->members);

        TeamMemberCreated::dispatch($this, $user);

        if ($role === 'owner') {
            TeamOwnerCreated::dispatch($this, $user);
        }
    }

    public function removeMember($user): void
    {
        $this->members()->detach($user);

        TeamMemberDeleted::dispatch($this, $user);
    }
}
