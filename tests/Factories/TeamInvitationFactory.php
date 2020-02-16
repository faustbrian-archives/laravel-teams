<?php

use Faker\Generator as Faker;
use KodeKeep\Teams\Models\Team;
use KodeKeep\Teams\Models\TeamInvitation;
use KodeKeep\Teams\Tests\Unit\ClassThatHasTeams;

$factory->define(TeamInvitation::class, function (Faker $faker) {
    return [
        'team_id'      => factory(Team::class),
        'user_id'      => factory(ClassThatHasTeams::class),
        'email'        => $faker->email,
        'role'         => 'member',
        'permissions'  => [],
        'accept_token' => $faker->uuid,
        'reject_token' => $faker->uuid,
    ];
});
