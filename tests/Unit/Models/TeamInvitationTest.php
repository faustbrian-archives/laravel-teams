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

namespace KodeKeep\Teams\Tests\Unit\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;
use KodeKeep\Teams\Models\TeamInvitation;
use KodeKeep\Teams\Tests\TestCase;

/**
 * @covers \KodeKeep\Teams\Models\TeamInvitation
 */
class TeamInvitationTest extends TestCase
{
    /** @test */
    public function can_use_the_configured_table_name(): void
    {
        $invitation = new TeamInvitation();

        $this->assertSame(Config::get('teams.tables.invitations'), $invitation->getTable());
    }

    /** @test */
    public function an_invitation_belongs_to_a_team_and_user()
    {
        $user       = $this->user();
        $team       = $this->team();
        $invitation = factory(TeamInvitation::class)->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(BelongsTo::class, $invitation->team());
        $this->assertInstanceOf(BelongsTo::class, $invitation->user());
        $this->assertSame($team->id, $invitation->team_id);
        $this->assertSame($user->id, $invitation->user_id);
    }

    /** @test */
    public function can_determine_if_the_invitation_is_expired()
    {
        $invitation             = new TeamInvitation();
        $invitation->created_at = Carbon::now()->subWeeks(2);

        $this->assertTrue($invitation->isExpired());

        $invitation->created_at = Carbon::now()->addWeeks(2);

        $this->assertFalse($invitation->isExpired());
    }

    /** @test */
    public function can_find_an_invitation_by_its_accept_token(): void
    {
        $invitation = factory(TeamInvitation::class)->create();

        $this->assertSame($invitation->id, TeamInvitation::findByAcceptToken($invitation->accept_token)->id);
    }

    /** @test */
    public function can_find_an_invitation_by_its_reject_token(): void
    {
        $invitation = factory(TeamInvitation::class)->create();

        $this->assertSame($invitation->id, TeamInvitation::findByRejectToken($invitation->reject_token)->id);
    }
}
