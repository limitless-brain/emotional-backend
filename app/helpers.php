<?php
/*
|--------------------------------------------------------------------------
| Helper methods that makes the syntax more readable
|--------------------------------------------------------------------------
| Add methods that has common usage here
|
*/


use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use YoutubeDl\Entity\Video;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

/**
 * The variable define token life at specific number days
 */
define('TOKEN_EXPIRES_AT', 15);

/**
 * The variable define token life at specific number days
 */
define('REFRESH_TOKEN_EXPIRES_AT', 30);

/**
 * The variable define token life at specific number days
 */
define('PERSONAL_TOKEN_EXPIRES_AT', 60);

/**
 * The variable define token life at specific number days
 */
define('COOKIE_EXPIRES_AT', TOKEN_EXPIRES_AT);

/**
 * The method that returns the current user from the current session
 *
 * @return User|Authenticatable|null
 */
function current_user()
{
    // return the current user
    return auth()->user();
}

/**
 * the method that returns a json response object with status code 200
 *
 * @param array $data array of data to send it with the response
 * @return JsonResponse
 */
function response_success($data = []): JsonResponse
{
    return response()->json($data);
}

/**
 * the method that returns a json response object with status code 401
 *
 * @param array $data array of data to send it with the response
 * @return JsonResponse
 */
function response_unauthorized_401($data = []): JsonResponse
{
    return response()->json($data,  Response::HTTP_UNAUTHORIZED);
}

/**
 * the method that returns a json response object with status code 201
 *
 * @param array $data array of data to send it with the response
 * @return JsonResponse
 */
function response_data_created($data = []): JsonResponse
{
    return response()->json($data, Response::HTTP_CREATED);
}

/**
 * the method that returns a json response object with status code 204
 *
 * @param array $data array of data to send it with the response
 * @return JsonResponse
 */
function response_no_content($data = []): JsonResponse
{
    return response()->json($data, Response::HTTP_NO_CONTENT);
}

/**
 * the method that returns a json response object with status code 500
 *
 * @param array $data array of data to send it with the response
 * @return JsonResponse
 */
function response_internal_server_error($data = []): JsonResponse
{
    return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
}

/**
 * the method that returns an array with the provided msg (key->value)
 *
 * @param $message
 * @return array
 */
function response_message($message): array
{
    return ['message' => $message];
}

/**
 * the method that returns an array from string
 *
 * @param $str
 * @param string $separator
 * @param string[] $junk
 * @param string $replace
 * @return array
 */
function str_to_array($str, $separator=',', $junk = ['[',']','\''], $replace=''): array
{
    // clean the junk
    $str = str_replace($junk,$replace,$str);
    if (!str_contains(',',$str))
        return [$str];
    // return the array
    return explode($separator,$str);
}

/**
 * The method that grab lyrics for a song by artist and song name
 * @param string $artist
 * @param string $title
 * @return string the lyrics string
 */
function getSongLyrics(string $artist, string $title): string {

    // html delimiters
    $delim1 = "</div></div></div></div><div class=\"hwc\"><div class=\"BNeawe tAd8D AP7Wnd\"><div><div class=\"BNeawe tAd8D AP7Wnd\">";
    $delim2 = "</div></div></div></div></div><div><span class=\"hwc\"><div class=\"BNeawe uEec3 AP7Wnd\">";

    // scaffolding google search url
    $url = config('services.youtube.google_search_endpoint');
    if ($artist)
        $url .= urlencode($artist) . '+';
    if ($title)
        $url .= urlencode($title);

    // result variable
    $result = 'not found';

    try {
        // read page content as string
        $page = file_get_contents($url . '+lyrics');

        // split by delimiter 1
        $page = explode($delim1, $page)[1];

        // split by delimiter 2
        $result = explode($delim2, $page)[0];

    } catch (Exception $exception) {

        // lyrics not found let's try with different search
        try {

            // read page content as string
            $page = file_get_contents($url . '+song+lyrics');

            // split by delimiter 1
            $page = explode($delim1, $page)[1];

            // split by delimiter 2
            $result = explode($delim2, $page)[0];

        } catch (Exception $exception) {

            // lyrics not found let's try with different search
            try {

                // read page content as string
                $page = file_get_contents($url . '+song+');

                // split by delimiter 1
                $page = explode($delim1, $page)[1];

                // split by delimiter 2
                $result = explode($delim2, $page)[0];

            } catch (Exception $exception) {

                // lyrics not found let's try with different search
                try {

                    // read page content as string
                    $page = file_get_contents($url);

                    // split by delimiter 1
                    $page = explode($delim1, $page)[1];

                    // split by delimiter 2
                    $result = explode($delim2, $page)[0];

                } catch (Exception $exception) {
                    // send an exception
                    return 'lyrics not found';
                }
            }
        }
    }

    // return the result
    return strip_tags($result);
}

function getYoutubeVideoInfo(string  $id): Video
{
    // create yt instance
    $yt = new YoutubeDl();

    // video endpoint
    $endpoint = config('services.youtube.video_endpoint');

    // request file download
    $collection = $yt->download(
        Options::create()
            ->downloadPath("mp3/$id")
            ->url("$endpoint?v=$id")
            ->skipDownload(true)
    );

    return $collection->getVideos()[0];
}


