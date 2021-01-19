<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtistController extends Controller
{

    /**
     * The method that returns list of all artists
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // gets artists
        return response_success(Artist::orderBy('id')->paginate(10));
    }

    /**
     * The method that returns list of all artist albums.
     *
     * @param Artist $artist
     * @return JsonResponse
     */
    public function show(Artist $artist): JsonResponse
    {
        // get the artist albums
        $album = $artist->albums();

        /// return success response with song information
        return response_success([
            'artist' => $artist,
            'albums' => $album,
            'total_albums' => $artist->albumsCount(),
            'total_songs' => $artist->songsCount()
        ]);
    }
}
