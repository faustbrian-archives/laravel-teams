<?php

use Faker\Generator as Faker;
use KodeKeep\Teams\Models\Team;
use KodeKeep\Teams\Tests\Unit\ClassThatHasTeams;

$factory->define(Team::class, function (Faker $faker) {
    return [
        'owner_id' => factory(ClassThatHasTeams::class),
        'name'     => $faker->unique()->firstName,
        'slug'     => $faker->slug,
    ];
});
