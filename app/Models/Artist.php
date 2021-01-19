<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Artist extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'id' => 'string'
    ];
    protected $hidden = ['created_at','updated_at'];

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    public function songs(): HasManyThrough
    {
        return $this->hasManyThrough(Song::class,Album::class);
    }

    public function songsCount(): int
    {
        return $this->songs()->count();
    }

    public function albumsCount(): int
    {
        return $this->songs()->count();
    }

    public function scopeWithData(Builder $query)
    {
        $query->leftJoinSub(
            'select artist_id, count(`name`) albums_count from albums group by artist_id',
            'albums',
            'albums.artist_id',
            '=',
            'artists.id'
        )->leftJoinSub(
            'select artist_id, count(title) songs_count from songs group by artist_id',
            'songs',
            'songs.artist_id',
            '=',
            'artists.id'
        );
    }

}
