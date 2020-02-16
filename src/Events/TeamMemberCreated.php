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

namespace KodeKeep\Teams\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use KodeKeep\Teams\Contracts\Team;

class TeamMemberCreated
{
    use Dispatchable;

    public Team $team;

    public Authenticatable $user;

    public function __construct(Team $team, Authenticatable $user)
    {
        $this->team = $team;
        $this->user = $user;
    }
}
