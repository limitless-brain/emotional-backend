<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SongController extends Controller
{

    public function store($array)
    {

    }

    /**
     * Get a specific song by youtube id.
     *
     * @param $id string youtube id
     * @return JsonResponse
     */
    public function getSong(string $id): JsonResponse
    {
        // get the song
        $song = Song::withInteractions()->where(['youtube' => $id])->get()->first();

        // return success response with song information
        return response_success($song);
    }

    /**
     * Get lyrics for specific song.
     *
     * @param Song $song
     * @return JsonResponse
     */
    public function getLyrics(Song $song): JsonResponse
    {
        // return  success response with lyrics of the song
        return response_success($song->lyrics);
    }

    /**
     * like a specific song.
     *
     * @param Song $song
     * @return JsonResponse
     */
    public function like(Song $song): JsonResponse
    {
        // like the song
        $song->like();

        // return success response
        return response_success(response_message('The record successfully updated!'));
    }

    /**
     * dislike a specific song.
     *
     * @param Song $song
     * @return JsonResponse
     */
    public function dislike(Song $song): JsonResponse
    {
        // dislike the song
        $song->dislike();

        // return success response
        return response_success(response_message('The record successfully updated!'));
    }

    /**
     * match a specific song.
     *
     * @param Song $song
     * @return JsonResponse
     */
    public function match(Song $song): JsonResponse
    {
        // song match the feeling
        $song->match();

        // return success response
        return response_success(response_message('The record successfully updated!'));
    }

    /**
     * un match a specific song.
     *
     * @param Song $song
     * @return JsonResponse
     */
    public function unMatch(Song $song): JsonResponse
    {
        // un match song
        $song->unMatch();

        // return success response
        return response_success(response_message('The record successfully updated!'));
    }

    /**
     * un match a specific song.
     *
     * @param Song $song
     * @return JsonResponse
     */
    public function played(Song $song): JsonResponse
    {
        // increase play count
        $song->played();

        // return success response
        return response_success(response_message('The record successfully updated!'));
    }
}
