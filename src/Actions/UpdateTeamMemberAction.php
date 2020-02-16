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
use KodeKeep\Teams\Contracts\Team;

class UpdateTeamMemberAction
{
    private Team $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    public function execute(Authenticatable $member, string $role, array $permissions): void
    {
        $this->team->members()->updateExistingPivot($member, compact('role', 'permissions'));
    }
}
