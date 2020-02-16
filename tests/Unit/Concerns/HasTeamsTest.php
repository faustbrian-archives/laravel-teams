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

namespace KodeKeep\Teams\Tests\Unit\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Event;
use KodeKeep\Teams\Events\TeamMemberLeft;
use KodeKeep\Teams\Exceptions\TeamException;
use KodeKeep\Teams\Tests\TestCase;

/**
 * @covers \KodeKeep\Teams\Concerns\HasTeams
 */
class HasTeamsTest extends TestCase
{
    /** @test */
    public function a_user_has_many_invitations(): void
    {
        $this->assertInstanceOf(HasMany::class, $this->user()->invitations());
    }

    /** @test */
    public function can_determine_if_the_user_has_any_teams(): void
    {
        $team = $this->team();
        $user = $this->user();

        $this->assertFalse($user->hasTeams());

        $team->addMember($user, 'member', []);

        $this->assertTrue($user->fresh()->hasTeams());

        $team->removeMember($user);

        $this->assertFalse($user->fresh()->hasTeams());
    }

    /** @test */
    public function can_determine_if_the_user_is_on_a_team(): void
    {
        $team = $this->team();
        $user = $this->user();

        $this->assertFalse($user->onTeam($team));

        $team->addMember($user, 'member', []);

        $this->assertTrue($user->fresh()->onTeam($team));

        $team->removeMember($user);

        $this->assertFalse($user->fresh()->onTeam($team));
    }

    /** @test */
    public function can_determine_if_the_user_owns_a_team(): void
    {
        $user        = $this->user();
        $anotherUser = $this->user();
        $team        = $this->team($user);

        $this->assertTrue($user->ownsTeam($team));
        $this->assertFalse($anotherUser->ownsTeam($team));
    }

    /** @test */
    public function can_return_all_teams_owned_by_the_user(): void
    {
        $user        = $this->user();
        $anotherUser = $this->user();

        $this->team($user);
        $this->team($user);
        $this->team($anotherUser);

        $this->assertCount(2, $user->ownedTeams);
    }

    /** @test */
    public function can_determine_what_role_the_user_has_on_a_team(): void
    {
        $user        = $this->user();
        $team        = $this->team();
        $anotherTeam = $this->team();

        $team->addMember($user, 'owner', []);
        $anotherTeam->addMember($user, 'member', []);

        $this->assertSame('owner', $user->roleOn($team));
        $this->assertSame('member', $user->roleOn($anotherTeam));
        $this->assertEmpty($user->roleOn($this->team()));
    }

    /** @test */
    public function can_determine_what_role_the_user_has_on_the_current_team(): void
    {
        $user        = $this->user();
        $team        = $this->team();
        $anotherTeam = $this->team();

        $team->addMember($user, 'owner', []);
        $anotherTeam->addMember($user, 'member', []);

        $user->switchToTeam($team);

        $this->assertSame('owner', $user->roleOnCurrentTeam());

        $user->switchToTeam($anotherTeam);

        $this->assertSame('member', $user->roleOnCurrentTeam());
    }

    /** @test */
    public function can_determine_the_current_team(): void
    {
        $user        = $this->user();
        $team        = $this->team();
        $anotherTeam = $this->team();

        $team->addMember($user, 'owner', []);
        $anotherTeam->addMember($user, 'member', []);

        $user->switchToTeam($team);

        $this->assertSame($team->id, $user->current_team->id);
        $this->assertSame($team->id, $user->currentTeam()->id);

        $user->switchToTeam($anotherTeam);

        $this->assertSame($anotherTeam->id, $user->current_team->id);
        $this->assertSame($anotherTeam->id, $user->currentTeam()->id);

        $user->teams->each->delete();

        $this->assertEmpty($user->fresh()->current_team);
    }

    /** @test */
    public function users_cant_switch_to_teams_they_are_not_on(): void
    {
        $user = $this->user();
        $team = $this->team();

        $this->expectException(TeamException::class);

        $user->switchToTeam($team);
    }

    /** @test */
    public function can_determine_if_the_user_owns_the_current_team(): void
    {
        $user        = $this->user();
        $anotherUser = $this->user();

        $this->assertFalse($user->ownsCurrentTeam());

        $team        = $this->team($user);
        $anotherTeam = $this->team($anotherUser);

        $team->addMember($user, 'owner', []);
        $anotherTeam->addMember($anotherUser, 'owner', []);
        $anotherTeam->addMember($user, 'member', []);

        $user->refresh();

        $user->switchToTeam($team);

        $this->assertTrue($user->ownsCurrentTeam());

        $user->switchToTeam($anotherTeam);

        $this->assertFalse($user->ownsCurrentTeam());
    }

    /** @test */
    public function can_refresh_the_current_team(): void
    {
        $user        = $this->user();
        $team        = $this->team();
        $anotherTeam = $this->team();

        $team->addMember($user, 'member', []);
        $anotherTeam->addMember($user, 'member', []);

        $user->switchToTeam($team);

        $this->assertSame($team->id, $user->currentTeam()->id);

        $user->refreshCurrentTeam();

        $this->assertNotNull($user->currentTeam());
    }

    /** @test */
    public function can_leave_a_team(): void
    {
        $user = $this->user();
        $team = $this->team();

        $team->addMember($user, 'member', []);

        $user->leaveTeam($team);

        Event::assertDispatched(TeamMemberLeft::class);
    }

    /** @test */
    public function can_not_leave_a_team_if_not_a_member(): void
    {
        $this->expectExceptionMessage('The user does not belong to the given team.');

        $team = $this->team();

        $this->user()->leaveTeam($team);

        Event::assertNotDispatched(TeamMemberLeft::class);
    }
}
