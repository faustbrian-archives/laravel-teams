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

namespace KodeKeep\Teams\Tests\Http\Middleware;

use Illuminate\Support\Facades\Route;
use KodeKeep\Teams\Http\Middleware\VerifyUserHasTeam;
use KodeKeep\Teams\Tests\TestCase;

/**
 * @covers \KodeKeep\Teams\Http\Middleware\VerifyUserHasTeam
 */
class VerifyUserHasTeamTest extends TestCase
{
    /** @test */
    public function redirects_the_user_if_it_doesnt_have_teams(): void
    {
        Route::middleware(VerifyUserHasTeam::class)->get('/', fn () => []);

        $this->actingAs($this->user())->get('/')->assertRedirect();
    }

    /** @test */
    public function fulfils_the_request_if_the_user_has_teams(): void
    {
        Route::middleware(VerifyUserHasTeam::class)->get('/', fn () => []);

        $this->actingAs($user = $this->user());

        $this->team($user)->addMember($user, 'owner', []);

        $this->get('/')->assertOk();
    }
}
