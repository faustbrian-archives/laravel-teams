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
use KodeKeep\Teams\Http\Middleware\VerifyUserHasOwnership;
use KodeKeep\Teams\Tests\TestCase;

/**
 * @covers \KodeKeep\Teams\Http\Middleware\VerifyUserHasOwnership
 */
class VerifyUserHasOwnershipTest extends TestCase
{
    /** @test */
    public function aborts_the_request_if_the_user_doesnt_have_ownership(): void
    {
        Route::middleware(VerifyUserHasOwnership::class)->get('/', fn () => []);

        $user = $this->user();
        $team = $this->team();

        $team->addMember($user, 'member', []);

        $this
            ->actingAs($user)
            ->get('/')
            ->assertForbidden();
    }

    /** @test */
    public function aborts_the_request_if_the_user_doesnt_have_any_teams(): void
    {
        Route::middleware(VerifyUserHasOwnership::class)->get('/', fn () => []);

        $this
            ->actingAs($this->user())
            ->get('/')
            ->assertForbidden();
    }

    /** @test */
    public function fulfils_the_request_if_the_user_has_ownership(): void
    {
        Route::middleware(VerifyUserHasOwnership::class)->get('/', fn () => []);

        $this
            ->actingAs($this->team()->owner)
            ->get('/')
            ->assertOk();
    }
}
