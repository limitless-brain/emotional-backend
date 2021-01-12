<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AIController extends Controller
{
    /**
     * The method that predict sentiment for a give paragraph
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function predict(Request $request): JsonResponse
    {
        $paragraphs = htmlentities($request->get('paragraphs'));

        $process = new Process(["python3", app_path('/Python/emotional_ai.py'),'-p', $paragraphs]);
        $process->run(null,['HOME'=>'/home/limitless']);

        // executes after the prediction finishes
        if (!$process->isSuccessful()) {
            return response_internal_server_error(response_message($process->getErrorOutput()));
        }

        $result = [
            'paragraphs' => $paragraphs,
            'result'=>json_decode($process->getOutput())
        ];

        // return the result
        return response_success($result);
    }
}
