<?php

namespace App\Support\Helpers;

use Illuminate\Http\Response;

class ResponseHelper
{
    /**
     * Extracts the string between the start and end delimeters
     *
     * @param  array                          $response
     * @return Illuminate\Http\JsonResponse
     */
    public static function getDefaultResponse(
        array $response,
    ): \Illuminate\Http\JsonResponse{

        $payload = [];

        if (isset($response['errors']) &&
            count($response['errors']) > 0) {
            $payload['success'] = false;
            $payload['errors'] = $response['errors'];
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        } else {
            $statusCode = Response::HTTP_OK;
            $payload['data'] = $response;
        }

        return response()->json(
            $payload,
            $statusCode,
            []
        );
    }

    /**
     * get message from exception
     *
     * @param  object   $exception
     * @param  string   $statusCode
     * @return string
     */
    public static function getMessageFromException(
        object $exception,
        string $statusCode
    ): string {
        $message = isset(config('gmail.errors')[$statusCode]) ? config('gmail.errors')[$statusCode] : 'Unknown error occurred.';
        $googleBrief = isset($exception->error->errors) ? $exception->error->errors[0]->message : $exception->error ?? json_encode($exception);
        $detailedMessage = $exception->error->message ?? $exception->error_description ?? json_encode($exception);
        return json_encode([
            'message' => $message,
            'google_brief' => $googleBrief,
            'detailed_message' => $detailedMessage,
        ]);
    }
}
