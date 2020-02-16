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

namespace KodeKeep\Teams\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use KodeKeep\Teams\Models\Team;
use KodeKeep\Teams\Providers\TeamsServiceProvider;
use KodeKeep\Teams\Tests\Unit\ClassThatHasTeams;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        Event::fake();
        Mail::fake();

        $this->migrate();

        $this->withFactories(realpath(__DIR__.'/Factories'));
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('teams.models.user', ClassThatHasTeams::class);
    }

    protected function getPackageProviders($app): array
    {
        return [TeamsServiceProvider::class];
    }

    protected function migrate(): void
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        $this->artisan('migrate', ['--database' => 'testbench'])->run();
    }

    protected function user(): ClassThatHasTeams
    {
        return ClassThatHasTeams::create([
            'name'     => $this->faker->name,
            'email'    => $this->faker->safeEmail,
            'password' => $this->faker->password,
        ]);
    }

    protected function team(?ClassThatHasTeams $user = null): Team
    {
        $user = $user ?: $this->user();

        $team = Team::create([
            'owner_id' => $user->id,
            'name'     => 'Personal',
            'slug'     => 'personal',
        ]);

        $team->addMember($user, 'owner', []);

        return $team;
    }
}
