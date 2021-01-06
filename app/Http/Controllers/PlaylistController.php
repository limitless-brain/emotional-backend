<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     * @return Response
     */
    public function store(Request $request)
    {
        // validate request data
        $attrs = $request->validate([
            'name' => 'string|required'
        ]);

        // create the playlist for the current user
        $attrs['user_id'] = current_user()->id;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Playlist  $playlist
     * @return Response
     */
    public function show(Playlist $playlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  \App\Models\Playlist  $playlist
     * @return Response
     */
    public function update(Request $request, Playlist $playlist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Playlist  $playlist
     * @return Response
     */
    public function destroy(Playlist $playlist)
    {
        //
    }
}
