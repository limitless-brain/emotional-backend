<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    /**
     * The method that return a list of all playlists for the current user.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // get all playlists
        $playlists = current_user()->playlists()->orderBy('name')->get();

        // return success response with playlists
        return response_success($playlists);
    }

    /**
     * The method that store a newly created playlist in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // validate request data
        $attrs = $request->validate([
            'name' => 'string|required',
            'emotion' => 'string'
        ]);

        // create the playlist for the current user
        $attrs['user_id'] = current_user()->id;

        // store the playlist
        $playlist = Playlist::create($attrs);

        // return success response with playlist object
        return response_success($playlist);
    }

    /**
     * The method that returns a playlist with it's song.
     *
     * @param Playlist $playlist
     * @return JsonResponse
     */
    public function show(Playlist $playlist): JsonResponse
    {
        $result = [
            'playlist' => $playlist,
            'songs' => $playlist->songs()
        ];

        return response_success($result);
    }

    /**
     * The method that update playlist name and emotion.
     *
     * @param Request $request
     * @param Playlist $playlist
     * @return JsonResponse
     */
    public function update(Request $request, Playlist $playlist): JsonResponse
    {
        if ($playlist->user()->id === current_user()->id) {
            $attrs = $request->validate([
                'name' => 'string|required',
                'emotion' => 'string'
            ]);

            $playlist->update($attrs);

            return response_success($playlist);
        } else {
            return response_unauthorized_401(response_message('Playlist is not owned by current user'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Playlist $playlist
     * @return JsonResponse
     */
    public function destroy(Playlist $playlist): JsonResponse
    {
        if ($playlist->user()->id === current_user()->id) {
            $playlist->delete();

            return response_success(response_message('Playlist deleted'));
        } else {
            return response_unauthorized_401(response_message('Playlist is not owned by current user'));
        }
    }

    public function addSong(Playlist $playlist, Song $song): JsonResponse
    {
        if ($playlist->user()->id === current_user()->id) {
            $playlist->songs()->updateOrCreate(['song_id' => $song->id]);

            return response_data_created(response_message('Successfully added song to playlist'));
        } else {
            return response_unauthorized_401(response_message('Playlist is not owned by current user'));
        }
    }

    public function removeSong(Playlist $playlist, Song $song): JsonResponse
    {
        if ($playlist->user()->id === current_user()->id) {
            $playlist->songs()->where(['song_id' => $song->id])->delete();

            return response_success(response_message('Successfully removed song from playlist'));
        } else {
            return response_unauthorized_401(response_message('Playlist is not owned by current user'));
        }
    }

    public function getPlaylistsByEmotion(Request $request): JsonResponse
    {
        $emotion = $request->get('emotion');
        $playlists = Playlist::where(['emotion' => $emotion])->orderBy('name')->paginate(10);

        return response_success($playlists);
    }

    public function own(Playlist $playlist): JsonResponse
    {
        $owned = current_user()->id === $playlist->user()->id;

        return response_success($owned);
    }
}
