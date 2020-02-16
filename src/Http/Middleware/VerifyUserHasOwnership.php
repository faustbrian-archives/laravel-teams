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

namespace KodeKeep\Teams\Http\Middleware;

class VerifyUserHasOwnership
{
    public function handle($request, $next)
    {
        $user = $request->user();

        abort_unless($user && $user->ownsCurrentTeam(), 403);

        return $next($request);
    }
}
