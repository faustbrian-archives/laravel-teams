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

use KodeKeep\Teams\Actions\UpdateTeamMemberAction;
use KodeKeep\Teams\Tests\TestCase;

/**
 * @covers \KodeKeep\Teams\Actions\UpdateTeamMemberAction
 */
class UpdateTeamMemberActionTest extends TestCase
{
    /** @test */
    public function can_update_the_team_member(): void
    {
        $user = $this->user();
        $team = $this->team();

        $team->addMember($user, 'member', []);

        $this->actingAs($team->owner);

        $this->assertDatabaseMissing('team_users', [
            'user_id'     => $user->id,
            'role'        => 'moderator',
            'permissions' => json_encode(['all']),
        ]);

        (new UpdateTeamMemberAction($team))->execute($user, 'moderator', ['all']);

        $this->assertDatabaseHas('team_users', [
            'user_id'     => $user->id,
            'role'        => 'moderator',
            'permissions' => json_encode(['all']),
        ]);
    }
}
