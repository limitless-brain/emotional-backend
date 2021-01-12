<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    /**
     * The method that returns list of all albums
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // gets albums
        return response_success(Album::all()->orderBy($request->get('orderBy'),$request->get('order'))
            ->paginate(10));
    }

    /**
     * The method that returns list of all album songs.
     *
     * @param Request $request
     * @param \App\Models\Album $album
     * @return JsonResponse
     */
    public function show(Request $request, Album $album): JsonResponse
    {
        // get the album songs
        $songs = $album->songs()->orderBy($request->get('orderBy'),$request->get('order'));
        // return success response with song information
        return response_success([
            'album' => $album,
            'songs' => $songs
        ]);
    }
}
