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

namespace KodeKeep\Teams\Tests\Unit\Actions;

use Illuminate\Support\Facades\Mail;
use KodeKeep\Teams\Actions\InviteTeamMemberAction;
use KodeKeep\Teams\Mail\InviteExistingUser;
use KodeKeep\Teams\Mail\InviteNewUser;
use KodeKeep\Teams\Models\TeamInvitation;
use KodeKeep\Teams\Tests\TestCase;

/**
 * @covers \KodeKeep\Teams\Actions\InviteTeamMemberAction
 */
class InviteTeamMemberActionTest extends TestCase
{
    /** @test */
    public function throws_when_the_email_already_is_on_the_team(): void
    {
        $this->expectExceptionMessage('The user is already on the team.');

        $team = $this->team();

        (new InviteTeamMemberAction($team))->execute($team->owner->email, 'member', ['*']);
    }

    /** @test */
    public function throws_when_the_email_is_already_invited_to_the_team(): void
    {
        $this->expectExceptionMessage('The user is already invited to the team.');

        $user = $this->user();
        $team = $this->team();

        factory(TeamInvitation::class)->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'email'   => $user->email,
        ]);

        (new InviteTeamMemberAction($team->fresh()))->execute($user->email, 'member', ['*']);
    }

    /** @test */
    public function can_invite_an_existing_user(): void
    {
        $user = $this->user();
        $team = $this->team();

        $this->assertDatabaseMissing('team_invitations', [
            'user_id' => $user->id,
            'email'   => $user->email,
            'role'    => 'member',
        ]);

        (new InviteTeamMemberAction($team->fresh()))->execute($user->email, 'member', ['*']);

        $this->assertDatabaseHas('team_invitations', [
            'user_id' => $user->id,
            'email'   => $user->email,
            'role'    => 'member',
        ]);

        Mail::assertQueued(InviteExistingUser::class);
    }

    /** @test */
    public function can_invite_a_new_user(): void
    {
        $email = 'john@doe.com';
        $team  = $this->team();

        $this->assertDatabaseMissing('team_invitations', [
            'email' => $email,
            'role'  => 'member',
        ]);

        (new InviteTeamMemberAction($team->fresh()))->execute($email, 'member', ['*']);

        $this->assertDatabaseHas('team_invitations', [
            'email' => $email,
            'role'  => 'member',
        ]);

        Mail::assertQueued(InviteNewUser::class);
    }
}
