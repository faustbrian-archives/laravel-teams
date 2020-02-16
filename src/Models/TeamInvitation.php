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

namespace KodeKeep\Teams\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;
use KodeKeep\Teams\Contracts\TeamInvitation as Contract;

class TeamInvitation extends Model implements Contract
{
    protected $fillable = ['team_id', 'user_id', 'email', 'role', 'permissions', 'accept_token', 'reject_token'];

    protected $casts = ['permissions' => 'json'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Config::get('teams.models.team'));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Config::get('teams.models.user'));
    }

    public function isExpired(): bool
    {
        return Carbon::now()->subWeek()->gte($this->created_at);
    }

    public static function findByAcceptToken(string $token): self
    {
        return static::where('accept_token', $token)->firstOrFail();
    }

    public static function findByRejectToken(string $token): self
    {
        return static::where('reject_token', $token)->firstOrFail();
    }

    public function getTable(): string
    {
        return Config::get('teams.tables.invitations');
    }
}
