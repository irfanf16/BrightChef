<?php
namespace App\Services\OpenAI;

use App\Models\InstagramPost;
use App\Models\InstagramReel;
use App\Models\InstagramImage;
use App\Models\InstagramVideo;
use Illuminate\Support\Facades\Http;
use App\Exceptions\ReporterException;
use App\Exceptions\Api\Facebook\FacebookApiException;
use Illuminate\Http\Response;

class OpenAITextCompletionService extends OpenAIService
{
    protected $instagramAccountId;

    /**
     * __construct
     *
     * @param  string $accessToken
     * @param  string $instagramAccountId
     * @return void
     */
    public function __construct(
    ) {
    }
}