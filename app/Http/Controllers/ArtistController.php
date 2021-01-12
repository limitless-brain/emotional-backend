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
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // gets artists
        return response_success(Artist::all()->orderBy($request->get('orderBy'),$request->get('order'))
            ->paginate(10));
    }

    /**
     * The method that returns list of all artist albums.
     *
     * @param Request $request
     * @param Artist $artist
     * @return JsonResponse
     */
    public function show(Request $request, Artist $artist): JsonResponse
    {
        // get the artist albums
        $album = $artist->albums()->orderBy($request->get('orderBy'),$request->get('order'));

        /// return success response with song information
        return response_success([
            'artist' => $artist,
            'album' => $album
        ]);
    }
}
