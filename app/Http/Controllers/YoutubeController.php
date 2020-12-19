<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
}
