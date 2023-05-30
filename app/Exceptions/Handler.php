<?php

namespace App\Exceptions;

use App\Events\NotifyOperatorsViaSlackEvent;
use App\Exceptions\Api\Facebook\FacebookApiException;
use Throwable;
use Illuminate\Http\Response;
use InvalidArgumentException;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function render(
        $request,
        Throwable $th
    ) {
        if ($th instanceof ModelNotFoundException) {
            $statusCode = Response::HTTP_NOT_FOUND;
            // dd($th);
            $model = str_replace('App\Models\\', '', $th->getModel());
            $message = $model . ' not found.';
            return response()->json(
                $this->responseFormatter(
                    false,
                    $statusCode,
                    [$message],
                ),
                $statusCode
            );
        }
        if ($th instanceof QueryException) {
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            return response()->json(
                $this->responseFormatter(
                    false,
                    $statusCode,
                    [$th->getMessage()],
                ),
                $statusCode
            );
        }
        if ($th instanceof AuthenticationException) {
            $statusCode = $th->getCode();
            if (!$statusCode) {
                $statusCode = 401;
            }
            return response()->json(
                $this->responseFormatter(
                    false,
                    $statusCode,
                    [$th->getMessage()],
                ),
                $statusCode
            );
        }
        if ($th instanceof InvalidArgumentException) {
            $statusCode = $th->getCode();
            if (!$statusCode) {
                $statusCode = Response::HTTP_BAD_REQUEST;
            }
            return response()->json(
                $this->responseFormatter(
                    false,
                    $statusCode,
                    [$th->getMessage()],
                ),
                $statusCode
            );
        }
        if ($th instanceof AuthorizationException) {
            $statusCode = Response::HTTP_FORBIDDEN;
            return response()->json(
                $this->responseFormatter(
                    false,
                    $statusCode,
                    ['You are not authorized to perform this action.']
                ),
                $statusCode
            );
        }

        return parent::render($request, $th);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        // if (!config('app.debug')) {
        $this->renderable(function (ReporterException $e) {
            $statusCode = $e->getCode();
            if (!$statusCode) {
                $statusCode = 500;
            }
            return response()->json(
                $this->responseFormatter(
                    false,
                    $statusCode,
                    [$e->getMessage() . " at line " . $e->getLine()],
                ),
                $statusCode
            );
        });
        $this->renderable(function (ValidationException $e) {
            $statusCode = Response::HTTP_BAD_REQUEST;
            return response()->json(
                $this->responseFormatter(
                    false,
                    $statusCode,
                    [$e->validator->errors()->first()],
                ),
                $statusCode
            );
        });
        // }
    }

    /**
     * Formats an API response the canonical way
     *
     * @param  boolean $status
     * @param  integer $code
     * @param  array   $errors
     * @return array
     */
    public function responseFormatter(
        bool $status,
        int $code,
        array $errors = []
    ): array{
        $payload = [];
        $payload['success'] = $status;
        $payload['code'] = $code;
        $payload['errors'] = $errors;
        // $payload['X-Request-Id'] = $this->getRequestTracker();
        return $payload;
    }
}
