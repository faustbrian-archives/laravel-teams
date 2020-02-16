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

namespace KodeKeep\Teams\Tests\Unit\Mail;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use KodeKeep\Teams\Mail\InviteExistingUser;
use KodeKeep\Teams\Models\TeamInvitation;
use KodeKeep\Teams\Tests\TestCase;

/**
 * @covers \KodeKeep\Teams\Mail\InviteExistingUser
 */
class InviteExistingUserTest extends TestCase
{
    /** @test */
    public function sends_the_mail_to_the_invited_user()
    {
        [$user, $team, $invitation] = $this->createModels();

        Mail::to($user)->send(new InviteExistingUser($invitation));

        Mail::assertQueued(InviteExistingUser::class, fn ($mail) =>$mail->hasTo($user->email));
    }

    /** @test */
    public function builds_the_mail_with_the_correct_subject()
    {
        [$user, $team, $invitation] = $this->createModels();

        $mail = new InviteExistingUser($invitation);

        $this->assertSame('New Invitation!', $mail->build()->subject);
    }

    private function createModels(): array
    {
        $user = $this->user();
        $team = $this->team($user);

        $invitation = TeamInvitation::create([
            'team_id'      => $team->id,
            'user_id'      => $user->id,
            'email'        => $user->email,
            'role'         => 'member',
            'permissions'  => [],
            'accept_token' => Str::random(40),
            'reject_token' => Str::random(40),
        ]);

        return [$user, $team, $invitation];
    }
}
