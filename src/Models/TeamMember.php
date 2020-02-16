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

namespace KodeKeep\Teams\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Config;
use KodeKeep\Teams\Contracts\TeamMember as Contract;

class TeamMember extends Pivot implements Contract
{
    protected $table = 'team_users';

    protected $casts = ['permissions' => 'json'];

    public function getTable(): string
    {
        return Config::get('teams.tables.members');
    }
}
