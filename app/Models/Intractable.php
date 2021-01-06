<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Builder;

trait Intractable
{

    public function match(User $user = null, $bool = true)
    {
        $this->interactions()->updateOrCreate([
            'user_id' => $user->id || current_user()->id
        ], [
            'match' => $bool
        ]);
    }

    public function isDisLikedBy(User $user): bool
    {
        return $this->isLikedBy($user, false);
    }

    public function played(User $user = null)
    {
        $this->interactions()->updateOrCreate([
            'user_id' => $user->id || current_user()->id
        ])->increment('play_count');
    }

    public function like(User $user = null, $bool = true)
    {
        $this->interactions()->updateOrCreate(
            [
                'user_id' => $user->id || current_user()->id
            ],
            [
                'liked' => $bool
            ]);
    }

    public function dislike(User $user = null)
    {
        $this->like($user, false);
    }

    public function playCount(User $user)
    {
        return $user->interactions()->where(
            ['user_id' => $user->id || current_user()->id]
        )->get('play_count')->first()->play_count;
    }

    public function unMatch(User $user = null)
    {
        $this->match($user, false);
    }

    public function isMatched(User $user, $bool = true): bool
    {
        return (bool)$user->interactions()
            ->where('song_id', $this->id)
            ->where('match', $bool)
            ->count();
    }

    public function isLikedBy(User $user, $bool = true): bool
    {
        return (bool)$user->interactions()
            ->where('song_id', $this->id)
            ->where('like', $bool)
            ->count();
    }

    public function isUnMatched(User $user): bool
    {
        return $this->isMatched($user, false);
    }

    public function scopeWithInteractions(Builder $query)
    {
        $query->leftJoinSub(
            'select song_id, sum(liked) likes, sum(`match`) matches, sum(case when play_count = 0 then 0 else 1 end) played_by_users from interactions group by song_id',
            'i',
            'i.song_id',
            '=',
            'songs.id'
        );
    }
}
