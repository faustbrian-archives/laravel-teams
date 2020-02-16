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

use Illuminate\Foundation\Events\Dispatchable;
use KodeKeep\Teams\Contracts\Team;

class DeletingTeam
{
    use Dispatchable;

    public Team $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }
}
