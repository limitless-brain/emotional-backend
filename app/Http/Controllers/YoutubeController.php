<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

class YoutubeController extends Controller
{
    protected $apiKey;
    protected $part = 'snippet';
    protected $maxResults = 12;
    /**
     * Category id for music
     * @link  https://gist.github.com/dgp/1b24bf2961521bd75d6c
     * @var int
     */
    protected $category = 10;

    public function __construct()
    {
        $this->apiKey = config('services.youtube.api_key');
    }

    /**
     * the method that send http request to google api to receive
     * youtube video list with the given query
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('query');
        $token = $request->get('pageToken');
        $endpoint = config('services.youtube.search_endpoint');
        $type = 'video';

        $url = "$endpoint?part=$this->part&maxResults=$this->maxResults&type=$type&videoCategoryId=$this->category&order=relevance&key=$this->apiKey";
        if ($query)
            $url .= "&q=" . urlencode($query);
        if ($token)
            $url .= "&pageToken=$token";

        $response = Http::get($url);

        return response_success($response->body());
    }

    public function featured(Request $request): JsonResponse
    {
        $token = $request->get('pageToken');

        $endpoint = config('services.youtube.videos_endpoint');
        $chart = 'mostPopular';

        $url = "$endpoint?part=$this->part&maxResults=$this->maxResults&chart=$chart&videoCategoryId=$this->category&key=$this->apiKey";

        if ($token)
            $url .= "&pageToken=$token";

        $response = Http::get($url);

        return response_success($response->body());
    }

    public function getVideoId(Request $request): JsonResponse
    {
        $artist = $request->get('artist');
        $title = $request->get('title');

        try {
            $artistRecord = Artist::whereName($artist)->first();
            $songRecord = Song::where(['title' => $title, 'artist_id' => $artistRecord->id])->first();
            if ($songRecord->youtube_id)
                return response_success(['youtube_id' => $songRecord->youtube_id]);
        } catch (Exception $exception) {

        }

        $endpoint = config('services.youtube.search_endpoint');
        $type = 'video';

        $url = "$endpoint?part=$this->part&maxResults=1&type=$type&videoCategoryId=$this->category&key=$this->apiKey";
        $url .= '&q=' . urlencode($artist) . urlencode($title);

        $response = Http::get($url);

        $result = json_decode($response->body())->items[0]->id->videoId;
        if (isset($songRecord)) {
            $songRecord->youtube_id = $result;
            $songRecord->save();
        }

        return response_success($result);
    }

    public function getAudioFile($id): JsonResponse
    {
        // remember to install yt-dl
        // sudo curl -L https://yt-dl.org/downloads/latest/youtube-dl -o /usr/local/bin/youtube-dl
        // sudo chmod a+rx /usr/local/bin/youtube-dl
        // sudo apt install python
        // sudo apt install ffmpeg

        // create yt instance
        $yt = new YoutubeDl();

        // video endpoint
        $endpoint = config('services.youtube.video_endpoint');

        // request file download
        $collection = $yt->download(
            Options::create()
                ->downloadPath("mp3/$id")
                ->output("{$id}.%(ext)s")
                ->url("$endpoint?v=$id")
        );

        $video = $collection->getVideos()[0];

        if ($video->getError() !== null) {
            return response_unauthorized_401(response_message($video->getError()));
        }

        $result = [
            'url' => "http://${_SERVER["SERVER_ADDR"]}/" . htmlentities($video->getFilename()),
            'track' => $video->getTrack(),
            'album' => $video->getAlbum(),
            'artist' => $video->getArtist(),
            'title' => $video->getTitle(),
            'description' => $video->getDescription(),
            'subtitles' => $video->getSubtitles(),
            'youtube_id' => $id
        ];

        return response_success($result);
    }

    public function getVideoInfo($id): JsonResponse
    {
        // get song record
        $song = Song::where(['youtube_id' => $id])->first();

        // return if we have a record
        if ($song)
            // return the record
            return response_success($song);

        $video = $this->getVideoInfo($id);

        if ($video->getError() !== null) {
            return response_internal_server_error(response_message($video->getError()));
        }

        // check if we have record for the artist
        $artist = Artist::where(['name' => strtolower($video->getArtist())])->first();
        if (!$artist)
            $artist = Artist::create(['name' => $video->getArtist()]);

        $song = $artist->songs()->where(['title' => strtolower($video->getTrack())])->first();
        // check if we have record for the song
        if (!$song) {// create the album record
            if ($video->getAlbum()) {
                $album = $artist->albums()->where(['name' => strtolower($video->getAlbum())])->first();
                if (!$album) {
                    $album = Album::create([
                        'name' => $video->getAlbum(),
                        'artist_id' => $artist->id
                    ]);
                }
            } else {
                $album = Album::updateOrCreate([
                    'name' => 'unknown',
                    'artist_id' => $artist->id
                ]);
            }
            // get the lyrics
            $lyrics = getSongLyrics($video->getArtist(), $video->getTrack());

            // create song record
            $song = Song::updateOrCreate([
                'title' => $video->getTrack(),
                'artist_id' => $artist->id,
                'album_id' => $album->id,
                'youtube_id' => $video->getId(),
                'track_number' => intval($video->getTrack()) | 0,
                'lyrics' => $lyrics
            ]);
        }

        if (!$song->youtube_id) {
            $song->youtube_id = $id;
        }

        if (!$song->lyrics && !isset($lyrics)) {
            // get the lyrics
            $lyrics = getSongLyrics($video->getArtist(), $video->getTrack());
            $song->lyrics = $lyrics;
        }

        // saving song
        $song->save();

        return response_success($song);
    }

    public function getLyrics(Request $request): JsonResponse
    {

        $youtube_id = $request->get('youtubeId');

        $songRecord = Song::where(['youtube_id' => $youtube_id])->first();
        if ($songRecord && $songRecord->lyrics)
            return response_success(['lyrics' => $songRecord->lyrics, 'artist' => $songRecord->artist()->first()->name, 'title' => $songRecord->title]);

        $video = getYoutubeVideoInfo($youtube_id);

        $result = getSongLyrics($video->getArtist(), $video->getTrack());

        $artist = Artist::where(['name' => $video->getArtist()])->first();

        if ($artist) {
            $songRecord = $artist->songs()->where(['title' => $video->getTrack()])->first();
            if(!$songRecord) {
            }
            if($songRecord) {
                $songRecord->youtube_id = $youtube_id;
                $songRecord->lyrics = $result;
                $songRecord->save();
            }
        }

        return response_success(['lyrics' => $result, 'artist' => $video->getArtist(), 'title' => $video->getTrack()]);
    }

}
