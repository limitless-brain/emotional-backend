<?php

namespace App\Http\Controllers;

use Aerni\Spotify\Facades\SpotifyFacade as Spotify;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpotifyController extends Controller
{
    public function getAlbums(Request $request): JsonResponse
    {
        $ids = $request->get('ids');

        $response = Spotify::albums($ids)->get();

        return response_success($response);
    }

    public function getArtists(Request $request): JsonResponse
    {
        $ids = $request->get('ids');

        $response = Spotify::artists($ids)->get();

        return response_success($response);
    }
}
