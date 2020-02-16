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

namespace KodeKeep\Teams\Tests\Unit\Events;

use Illuminate\Support\Facades\Event;
use KodeKeep\Teams\Events\TeamMemberLeft;
use KodeKeep\Teams\Tests\TestCase;

/**
 * @covers \KodeKeep\Teams\Events\TeamMemberLeft
 */
class TeamMemberLeftTest extends TestCase
{
    /** @test */
    public function can_properly_dispatch_the_event()
    {
        $this->migrate();

        Event::fake();

        $user = $this->user();
        $team = $this->team($user);

        TeamMemberLeft::dispatch($team, $user);

        Event::assertDispatched(TeamMemberLeft::class, fn ($e) => $e->user->id === $user->id);
    }
}
