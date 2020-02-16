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

use KodeKeep\Teams\Exceptions\TeamException;
use KodeKeep\Teams\Tests\TestCase;

/**
 * @covers \KodeKeep\Teams\Exceptions\TeamException
 */
class TeamExceptionTest extends TestCase
{
    /** @test */
    public function throws_the_correct_message_for_does_not_belong_to_team():void
    {
        $this->expectExceptionMessage('The user does not belong to the given team.');

        throw TeamException::doesNotBelongToTeam();
    }

    /** @test */
    public function throws_the_correct_message_for_does_not_have_ownership():void
    {
        $this->expectExceptionMessage('The user does not have ownership on the given team.');

        throw TeamException::doesNotHaveOwnership();
    }

    /** @test */
    public function throws_the_correct_message_for_email_already_on_team():void
    {
        $this->expectExceptionMessage('The user is already on the team.');

        throw TeamException::emailAlreadyOnTeam();
    }

    /** @test */
    public function throws_the_correct_message_for_email_already_invited():void
    {
        $this->expectExceptionMessage('The user is already invited to the team.');

        throw TeamException::emailAlreadyInvited();
    }

    /** @test */
    public function throws_the_correct_message_for_can_not_remove_owner():void
    {
        $this->expectExceptionMessage('The user is holds ownership of the given team.');

        throw TeamException::canNotRemoveOwner();
    }
}
