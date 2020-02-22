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

use KodeKeep\Teams\Models\Team;
use KodeKeep\Teams\Models\TeamInvitation;
use KodeKeep\Teams\Models\TeamMember;

return [

    'models' => [

        /*
         * When using the "HasTeams" trait from this package, we need to
         * know which Eloquent model should be used to retrieve your teams.
         *
         * The model you want to use as a Team model needs to implement the
         * `KodeKeep\Teams\Contracts\Team` contract.
         */

        'team' => Team::class,

        /*
         * When using the "HasTeams" trait from this package, we need to
         * know which Eloquent model should be used to retrieve your team members.
         *
         * The model you want to use as a Team model needs to implement the
         * `KodeKeep\Teams\Contracts\TeamMember` contract.
         */

        'member' => TeamMember::class,

        /*
         * When using the "HasTeams" trait from this package, we need to
         * know which Eloquent model should be used to retrieve your team invitations.
         *
         * The model you want to use as a Team model needs to implement the
         * `KodeKeep\Teams\Contracts\TeamInvitation` contract.
         */

        'invitation' => TeamInvitation::class,

        /*
         * When using the "HasTeams" trait from this package, we need to
         * know which Eloquent model should be used to create relationships for
         * teams and all of their team members through a pivot table.
         */

        'user' => 'App\Models\User',

    ],

    'tables' => [

        /*
         * When using the "HasTeams" trait from this package, we need to know which
         * table should be used to retrieve your teams. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'teams' => 'teams',

        /*
         * When using the "HasTeams" trait from this package, we need to know which
         * table should be used to retrieve your members. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'members' => 'team_users',

        /*
         * When using the "HasTeams" trait from this package, we need to know which
         * table should be used to retrieve your invitations. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'invitations' => 'team_invitations',

        /*
         * When using the "HasTeams" trait from this package, we need to know which
         * table should be used to retrieve your users. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'users' => 'users',

    ],

];
