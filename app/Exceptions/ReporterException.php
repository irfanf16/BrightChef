<?php

namespace App\Exceptions;

use Exception;
use App\Facades\Bot;
use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * This class is used as a reporter exception from the catch blocks
 * so is then going to be catched via
 * app/Exceptions/Handler.php
 *
 * } catch (\Throwable $th) {
 *                 throw new ReporterException(
                $th->getMessage(),
                $th->getMessage(),
                $th->getCode()
            );
 * }
 */
class ReporterException extends RuntimeException
{

    /**
     * @see https://laravel.com/docs/8.x/errors#renderable-exceptions
     *
     * @var boolean
     */
    protected $mustReport = true;

    /**
     * Tool to log exceptions
     *
     * @param string $humans
     * @param mixed $log
     * @param integer $code
     * @param string $reporter
     * @param Exception $previous
     */
    public function __construct(string $humans, $log, $code = 0, string $reporter = '', Exception $previous = null)
    {
        $report['code'] = $code; //code
        $report['message'] = $log; //issue
        // $report['tracker'] = 'X-Request-Id';
        // $report['token'] = $this->getRequestTracker(); //X-Request-Id
        $report['reporter'] = $reporter ?: __CLASS__;
        $tracer = [];
        if (config('app.debug')) {
            $tracer = debug_backtrace();
        }

        Log::error(json_encode($report), $tracer);
        // make sure everything is assigned properly
        parent::__construct($humans, $code, $previous);
    }

    /**
     * Retrieves the Request Tracker Token set on app/Http/Middleware/RequestTracker.php
     *
     * @see app/Http/Middleware/RequestTracker.php
     *
     * @return string
     */
    // public function getRequestTracker(): string
    // {
    //     if (!defined('REQUEST_TRACKER')) {
    //         return Str::orderedUuid()->toString();
    //     }
    //     return REQUEST_TRACKER;
    // }

    /**
     * Report the exception.
     *
     * @see https://laravel.com/docs/8.x/errors#renderable-exceptions
     *
     * @return bool|void
     */
    public function report()
    {
        // Determine if the exception needs custom reporting...
        return $this->mustReport;
    }

    /**
     * Custom string representation of object
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
