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

namespace KodeKeep\Teams\Tests\Unit\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use KodeKeep\Teams\Events\TeamMemberCreated;
use KodeKeep\Teams\Events\TeamMemberDeleted;
use KodeKeep\Teams\Models\Team;
use KodeKeep\Teams\Models\TeamInvitation;
use KodeKeep\Teams\Tests\TestCase;
use Spatie\Sluggable\SlugOptions;

/**
 * @covers \KodeKeep\Teams\Models\Team
 */
class TeamTest extends TestCase
{
    /** @test */
    public function can_use_the_configured_table_name(): void
    {
        $team = new Team();

        $this->assertSame(Config::get('teams.tables.teams'), $team->getTable());
    }

    /** @test */
    public function can_find_a_team_by_slug(): void
    {
        $team = $this->team();

        $this->assertSame(Team::findBySlug($team->slug)->id, $team->id);
    }

    /** @test */
    public function configures_the_slug_options(): void
    {
        $this->assertInstanceOf(SlugOptions::class, $this->team()->getSlugOptions());
    }

    /** @test */
    public function a_team_has_many_invitations(): void
    {
        $this->assertInstanceOf(HasMany::class, $this->team()->invitations());
    }

    /** @test */
    public function a_team_has_pending_invitations(): void
    {
        $user = $this->user();
        $team = $this->team();

        $this->assertFalse($team->hasPendingInvitation($user->email));

        factory(TeamInvitation::class)->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'email'   => $user->email,
        ]);

        $this->assertTrue($team->fresh()->hasPendingInvitation($user->email));
    }

    /** @test */
    public function can_determine_the_owners_email_address(): void
    {
        $user = $this->user();
        $team = $this->team($user);

        $this->assertSame($user->email, $team->email);
    }

    /** @test */
    public function can_determine_if_user_has_access_to_team(): void
    {
        $team        = $this->team();
        $user        = $this->user();
        $anotherUser = $this->user();

        $team->addMember($anotherUser, 'member', []);

        $this->assertFalse($user->onTeam($team));
        $this->assertTrue($anotherUser->onTeam($team));

        $team->addMember($user, 'member', []);

        $this->assertTrue($user->fresh()->onTeam($team));
        $this->assertTrue($anotherUser->fresh()->onTeam($team));

        $team->removeMember($user);

        $this->assertFalse($user->fresh()->onTeam($team));
        $this->assertTrue($anotherUser->fresh()->onTeam($team));
    }

    /** @test */
    public function proper_share_events_are_fired(): void
    {
        $team        = $this->team();
        $user        = $this->user();
        $anotherUser = $this->user();

        $team->addMember($user, 'member', []);
        $team->addMember($anotherUser, 'member', []);

        Event::assertDispatched(TeamMemberCreated::class, fn ($event) => $event->user->id === $user->id);
    }

    /** @test */
    public function proper_unshare_events_are_fired(): void
    {
        $team = $this->team();
        $user = $this->user();

        $team->addMember($user, 'member', []);
        $team->removeMember($user);

        Event::assertDispatched(TeamMemberDeleted::class, fn ($event) => $event->user->id === $user->id);
    }

    /** @test */
    public function can_purge_a_team(): void
    {
        $team = $this->team();

        $this->assertDatabaseHas('teams', ['id' => $team->id]);

        $team->purge();

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }
}
