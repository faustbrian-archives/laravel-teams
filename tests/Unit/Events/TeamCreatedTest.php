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
use KodeKeep\Teams\Events\TeamCreated;
use KodeKeep\Teams\Tests\TestCase;

/**
 * @covers \KodeKeep\Teams\Events\TeamCreated
 */
class TeamCreatedTest extends TestCase
{
    /** @test */
    public function can_properly_dispatch_the_event()
    {
        $team = $this->team();

        TeamCreated::dispatch($team);

        Event::assertDispatched(TeamCreated::class, fn ($e) => $e->team->id === $team->id);
    }
}
