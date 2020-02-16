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

use Illuminate\Support\Facades\Auth;
use KodeKeep\Teams\Contracts\TeamInvitation;
use KodeKeep\Teams\Exceptions\TeamInvitationException;

class AcceptTeamInvitationAction
{
    private TeamInvitation $invitation;

    public function __construct(TeamInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function execute(): void
    {
        $expectedUser = Auth::id() === (int) $this->invitation->user_id;

        if (! $expectedUser) {
            throw TeamInvitationException::attemptedClaimByUnauthorizedUser();
        }

        $this->invitation->team->addMember(
            Auth::user(),
            $this->invitation->role,
            $this->invitation->permissions
        );

        $this->invitation->delete();
    }
}
