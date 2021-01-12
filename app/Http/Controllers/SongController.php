<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use App\Models\Song;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SongController extends Controller
{

    /**
     * The method that returns list of all songs
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // gets songs
        return response_success(Song::withInteractions()->orderBy($request->get('orderBy'),$request->get('order'))
            ->paginate(10));
    }

    /**
     * The method that returns a specific song by youtube id.
     *
     * @param $id string youtube id
     * @return JsonResponse
     */
    public function getSong(string $id): JsonResponse
    {
        // get the song
        $song = Song::withInteractions()->where(['id' => $id])->get()->first();

        // return success response with song information
        return response_success($song);
    }

    /**
     * The method that returns a specific song by youtube id.
     *
     * @param $id string youtube id
     * @return JsonResponse
     */
    public function getInteractions(string $id): JsonResponse
    {
        // get the song
        $song = Interaction::where(['id' => $id])->get()->first();

        // return success response with song information
        return response_success($song);
    }

    /**
     * The method that returns lyrics for specific song.
     *
     * @param Song $song
     * @return JsonResponse
     */
    public function getLyrics(Song $song): JsonResponse
    {
        // return  success response with lyrics of the song
        if (!$song->lyrics) {
            $result = getLyrics($song->artist()->name, $song->title);
            return response_success($result);
        }
        return response_success($song->lyrics);
    }

    /**
     * The method that likes a specific song.
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
     * The method that dislikes a specific song.
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
     * The method that match a specific song.
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
     * The method that un match a specific song.
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
     * The method that mark a specific song as played.
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
