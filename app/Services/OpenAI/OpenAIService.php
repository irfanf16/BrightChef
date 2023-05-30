<?php
namespace App\Services\OpenAI;

use Illuminate\Support\Facades\Http;
use App\Support\Helpers\HttpClientHelper;

class OpenAIService
{
    private $token;
    private $base_url;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
    ) {
        $this->token = config('openai.api_key');
        $this->base_url = config('openai.base_url');
    }

    /**
     * make request to openai api
     *
     * @param  string  $url
     * @param  array   $params
     * @return array
     */
    private function makeRequest(
        string $url,
        string $method,
        array $params = []
    ) {
        $response = Http::withToken($this->token)
            ->$method($url, $params);
        if (!$response->ok()) {
            HttpClientHelper::handleResponseException($response);
        }
        $response = $response->json();
        $usage = $response['usage'];
        return [
            'text' => $response['choices'][0]['text'],
            'usage' => $usage,
        ];
        // if ($usage['total_tokens'] < $params['max_tokens']) {
        //     return [
        //         'text' => $response['choices'][0]['text'],
        //         'usage' => $usage,
        //     ];
        // } else {
        //     // TODO: return error of long token
        //     return [
        //         'error' => true,
        //         'usage'=>$usage
        //     ];
        // }
    }

    /**
     * Complete The Text
     * https://platform.openai.com/docs/guides/completion/text-completion
     *
     * @param  string  $prompt
     * @return array
     */
    public function completition(array $params)
    {
        $url = $this->base_url . '/' . config('openai.endpoints.completions.url');
        $method = config('openai.endpoints.completions.method');
        $response = $this->makeRequest($url, $method, $params);

        return $response;
    }
}