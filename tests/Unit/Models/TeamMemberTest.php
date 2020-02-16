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

use Illuminate\Support\Facades\Config;
use KodeKeep\Teams\Models\TeamMember;
use KodeKeep\Teams\Tests\TestCase;

/**
 * @covers \KodeKeep\Teams\Models\TeamMember
 */
class TeamMemberTest extends TestCase
{
    /** @test */
    public function can_use_the_configured_table_name(): void
    {
        $member = new TeamMember();

        $this->assertSame(Config::get('teams.tables.members'), $member->getTable());
    }
}
