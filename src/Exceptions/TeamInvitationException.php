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

namespace KodeKeep\Teams\Exceptions;

use Exception;

class TeamInvitationException extends Exception
{
    public static function expirationExceeded(): self
    {
        return new static('The invitation has exceeded the expiration date.');
    }

    public static function attemptedClaimByUnauthorizedUser(): self
    {
        return new static('The user is not authorized to claim the given invitation.');
    }
}
