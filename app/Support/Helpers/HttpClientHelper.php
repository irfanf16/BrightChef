<?php
namespace App\Support\Helpers;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Exceptions\ReporterException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Response as ResponseStatusCodes;

class HttpClientHelper
{
    /**
     * make http get request
     *
     * @param string $url
     * @param array  $requestData
     */
    public static function get(
        string $url,
        array $requestData
    ) {
        $response = Http::get($url, $requestData);
        if (!$response->ok()) {
            self::handleResponseException($response);
        }
        return $response;
    }

    /**
     * make http post request
     *
     * @param string $url
     * @param array  $requestData
     */
    public static function post(
        string $url,
        array $requestData
    ) {
        $response = Http::post($url, $requestData);
        if (!$response->ok()) {
            self::handleResponseException($response);
        }
        return $response;
    }

    /**
     * handleException
     *
     * handles exception of failed requests to api url
     * @param  Response $response
     * @return void
     */
    public static function handleResponseException(Response $response): void
    {
        try {
            $response->throw();
        } catch (RequestException $e) {
            throw new ReporterException(
                $e->getMessage(),
                $e->getMessage(),
                ResponseStatusCodes::HTTP_BAD_REQUEST
            );
        } catch (ConnectionException $e) {
            throw new ReporterException(
                $e->getMessage(),
                $e->getMessage(),
                ResponseStatusCodes::HTTP_REQUEST_TIMEOUT
            );
        } catch (\Exception$e) {
            throw new ReporterException(
                $e->getMessage(),
                $e->getMessage(),
                ResponseStatusCodes::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}