<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Song;
use Exception;
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

    public function featured(): JsonResponse
    {
        $endpoint = config('services.youtube.videos_endpoint');
        $chart = 'mostPopular';

        $url = "$endpoint?part=$this->part&maxResults=$this->maxResults&chart=$chart&videoCategoryId=$this->category&key=$this->apiKey";

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

        $songRecord->youtube_id = $result;
        $songRecord->save();

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

    public function getLyrics(Request $request): JsonResponse
    {

        $artist = $request->get('artist');
        $title = $request->get('title');

        try {
            $artistRecord = Artist::whereName($artist)->first();
            if ($artistRecord) {
                $songRecord = Song::where(['title' => $title, 'artist_id' => $artistRecord->id])->first();
                if ($songRecord->lyrics)
                    return response_success($songRecord->lyrics);
            }
        } catch (Exception $exception) {
            return response_internal_server_error(response_message($exception->getMessage()));
        }

        $result = getLyrics($artist, $title);

        if (isset($songRecord)) {
            $songRecord->lyrics = $result;
            $songRecord->save();
        }
        return response_success($result);
    }

}
