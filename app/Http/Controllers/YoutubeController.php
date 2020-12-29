<?php

namespace App\Http\Controllers;

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
        $endpoint = config('services.youtube.search_endpoint');
        $type = 'video';

        $url = "$endpoint?part=$this->part&maxResults=$this->maxResults&type=$type&videoCategoryId=$this->category&key=$this->apiKey&q=$query";

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

    public function getAudioFile($id)
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

        if($video->getError() !== null)
        {
            return response_failure_401(response_message($video->getError()));
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

    public function getLyrics(): JsonResponse
    {
        $result = Http::get('https://api.lyrics.ovh/v1/faydee/catch%20me');

        return response_success($result->json('lyrics'));
    }
}
