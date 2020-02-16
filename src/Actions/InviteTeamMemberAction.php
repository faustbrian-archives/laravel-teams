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

namespace KodeKeep\Teams\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use KodeKeep\Teams\Contracts\Team;
use KodeKeep\Teams\Exceptions\TeamException;
use KodeKeep\Teams\Mail\InviteExistingUser;
use KodeKeep\Teams\Mail\InviteNewUser;
use Ramsey\Uuid\Uuid;

class InviteTeamMemberAction
{
    private Team $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    public function execute(string $email, string $role, array $permissions): void
    {
        if ($this->emailAlreadyOnTeam($email)) {
            throw TeamException::emailAlreadyOnTeam();
        }

        if ($this->emailAlreadyInvited($email)) {
            throw TeamException::emailAlreadyInvited();
        }

        $invitedUser = $this->findInvitedUser($email);

        $invitation = $this->team->invitations()->create([
            'user_id'      => $invitedUser ? $invitedUser->id : null,
            'role'         => $role,
            'permissions'  => $permissions,
            'email'        => $email,
            'accept_token' => Uuid::uuid4(),
            'reject_token' => Uuid::uuid4(),
        ]);

        $mail = Mail::to($invitation->email);

        if ($invitation->user_id) {
            $mail->send(new InviteExistingUser($invitation));
        } else {
            $mail->send(new InviteNewUser($invitation));
        }
    }

    private function emailAlreadyOnTeam(string $email): bool
    {
        return $this->team->members()->where('email', $email)->exists();
    }

    private function emailAlreadyInvited(string $email): bool
    {
        return $this->team->hasPendingInvitation($email);
    }

    private function findInvitedUser(string $email): ?Authenticatable
    {
        $userModel = Config::get('teams.models.user');

        return $userModel::where('email', $email)->first();
    }
}
