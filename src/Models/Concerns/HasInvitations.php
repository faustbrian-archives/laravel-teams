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

namespace KodeKeep\Teams\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Config;

trait HasInvitations
{
    public function invitations(): HasMany
    {
        return $this->hasMany(Config::get('teams.models.invitation'));
    }

    public function hasPendingInvitation(string $email): bool
    {
        return $this->invitations()->where('email', $email)->exists();
    }
}
