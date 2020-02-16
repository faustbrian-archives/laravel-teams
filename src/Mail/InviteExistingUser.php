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

namespace KodeKeep\Teams\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use KodeKeep\Teams\Contracts\TeamInvitation;

class InviteExistingUser extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public TeamInvitation $invitation;

    public function __construct(TeamInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function build()
    {
        return $this
            ->subject('New Invitation!')
            ->markdown('teams::mails.invite-existing-user');
    }
}
