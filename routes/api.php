<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\YoutubeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
|--------------------------------------------------------------------------
| API V1 - URL /api/v1/{routes}
|--------------------------------------------------------------------------
| This group contains APIs v1
|
*/

Route::prefix('v1')->group(function () {

    // add your public api here
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/signup', [AuthController::class, 'signup']);



    Route::middleware('auth:api')->group(function () {

        // add your private api here
        // User
        Route::resource('/user', UserController::class,['only' => ['show','update','destroy']]);
        Route::get('/logout', [AuthController::class, 'logout']);

        // Youtube
        Route::get('/search', [YoutubeController::class, 'search']);
        Route::get('/featured', [YoutubeController::class, 'featured']);
        Route::get('/videos/{id}/audio', [YoutubeController::class, 'getAudioFile']);
        Route::get('/videos/{id}/info', [YoutubeController::class, 'getVideoInfo']);
        Route::get('/videos/find',[YoutubeController::class,'getVideoId']);
        Route::get('/lyrics',[YoutubeController::class, 'getLyrics']);

        // Spotify
        Route::get('/spotify/albums',[SpotifyController::class,'getAlbums']);
        Route::get('/spotify/artists',[SpotifyController::class,'getArtists']);

        // Song
        Route::get('/songs',[SongController::class,'index']);
        Route::get('/songs/{song}',[SongController::class,'getSong']);
        Route::get('/songs/{song}/lyrics',[SongController::class,'getLyrics']);
        // interactions
        Route::post('/songs/{song}/played',[SongController::class,'played']);
        Route::post('/songs/{song}/like',[SongController::class,'like']);
        Route::post('/songs/{song}/unlike',[SongController::class,'dislike']);
        Route::post('/songs/{song}/match',[SongController::class,'match']);
        Route::post('/songs/{song}/un-match',[SongController::class,'unMatch']);
        Route::get('/songs/{song}/interactions',[SongController::class,'getInteractions']);

        // album
        Route::get('/albums',[AlbumController::class,'index']);
        Route::get('/albums/{album}',[AlbumController::class,'show']);

        // artist
        Route::get('/artists',[ArtistController::class,'index']);
        Route::get('/artists/{artist}',[ArtistController::class,'show']);

        // playlist
        Route::apiResource('/playlists', PlaylistController::class);
        Route::post('/playlists/{playlist}/{song}',[PlaylistController::class,'addSong']);
        Route::get('/playlists/{playlist}/own',[PlaylistController::class,'getPlaylistsByEmotion']);
        Route::delete('/playlist/{playlist}/{song}',[PlaylistController::class,'removeSong']);
        Route::get('/playlists/{emotion}',[PlaylistController::class,'getPlaylistsByEmotion']);

        // ai
        Route::get('/ai/emotion',[AIController::class,'predict']);
    });
});
