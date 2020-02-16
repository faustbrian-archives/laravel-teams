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

namespace KodeKeep\Teams\Tests\Unit\Excptions;

use KodeKeep\Teams\Exceptions\TeamInvitationException;
use KodeKeep\Teams\Tests\TestCase;

/**
 * @covers \KodeKeep\Teams\Exceptions\TeamInvitationException
 */
class TeamInvitationExceptionTest extends TestCase
{
    /** @test */
    public function throws_the_correct_message_for_expiration_exceeded():void
    {
        $this->expectExceptionMessage('The invitation has exceeded the expiration date.');

        throw TeamInvitationException::expirationExceeded();
    }

    /** @test */
    public function throws_the_correct_message_for_attempted_claim_by_unauthorized_user():void
    {
        $this->expectExceptionMessage('The user is not authorized to claim the given invitation.');

        throw TeamInvitationException::attemptedClaimByUnauthorizedUser();
    }
}
