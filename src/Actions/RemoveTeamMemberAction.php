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

namespace KodeKeep\Teams\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use KodeKeep\Teams\Contracts\Team;
use KodeKeep\Teams\Exceptions\TeamException;

class RemoveTeamMemberAction
{
    private Team $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    public function execute(Authenticatable $member): void
    {
        if ($this->isOwner($member)) {
            throw TeamException::canNotRemoveOwner();
        }

        // if ($this->isCurrentUser($member)) {
        //     throw TeamException::canNotRemoveCurrentUser();
        // }

        $this->team->removeMember($member);
    }

    private function isOwner(Authenticatable $member): bool
    {
        $currentUser = Auth::user();

        return $currentUser->ownsTeam($this->team) && $currentUser->id === $member->id;
    }

    // private function isCurrentUser(Authenticatable $member): bool
    // {
    //     return Auth::id() !== $member->id;
    // }
}
