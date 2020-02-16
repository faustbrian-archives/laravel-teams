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

use KodeKeep\Teams\Actions\RemoveTeamMemberAction;
use KodeKeep\Teams\Tests\TestCase;

/**
 * @covers \KodeKeep\Teams\Actions\RemoveTeamMemberAction
 */
class RemoveTeamMemberActionTest extends TestCase
{
    /** @test */
    public function throws_when_the_owner_is_attempted_to_be_removed(): void
    {
        $this->expectExceptionMessage('The user is holds ownership of the given team.');

        $team = $this->team();

        $this->actingAs($team->owner);

        (new RemoveTeamMemberAction($team))->execute($team->owner);
    }

    /** @test */
    public function can_remove_the_given_member(): void
    {
        $user = $this->user();
        $team = $this->team();

        $team->addMember($user, 'member', []);

        $this->actingAs($team->owner);

        $this->assertTrue($user->onTeam($team));

        (new RemoveTeamMemberAction($team))->execute($user);

        $this->assertFalse($user->fresh()->onTeam($team));
    }
}
