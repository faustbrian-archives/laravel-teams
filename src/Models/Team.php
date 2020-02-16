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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;
use KodeKeep\Teams\Contracts\Team as Contract;
use KodeKeep\Teams\Events\DeletingTeam;
use KodeKeep\Teams\Events\TeamCreated;
use KodeKeep\Teams\Events\TeamDeleted;
use KodeKeep\Teams\Models\Concerns\HasInvitations;
use KodeKeep\Teams\Models\Concerns\HasMembers;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Team extends Model implements Contract
{
    use HasInvitations;
    use HasMembers;
    use HasSlug;

    protected $fillable = ['owner_id', 'name', 'slug'];

    protected $dispatchesEvents = [
        'created'  => TeamCreated::class,
        'deleted'  => TeamDeleted::class,
        'deleting' => DeletingTeam::class,
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Config::get('teams.models.user'), 'owner_id');
    }

    public function purge(): void
    {
        $this
            ->members()
            ->where('current_team_id', $this->id)
            ->update(['current_team_id' => null]);

        $this->members()->detach();

        $this->delete();
    }

    public function getEmailAttribute(): string
    {
        return $this->owner->email;
    }

    public static function findByslug(string $slug): self
    {
        return static::where('slug', $slug)->firstOrFail();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function getTable(): string
    {
        return Config::get('teams.tables.teams');
    }
}
