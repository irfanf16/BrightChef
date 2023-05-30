<?php

namespace App\Actions\Api\Recipe;

use App\Models\Team;
use App\Models\OpenAiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Support\Helpers\MealPlanningHelper;
use Lorisleiva\Actions\ActionRequest;
use App\Services\OpenAI\OpenAIService;
use App\Support\Helpers\ResponseHelper;
use Lorisleiva\Actions\Concerns\AsAction;

class MealPlanningAction
{
    use AsAction;

    public function authorize(ActionRequest $request)
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'ingredients' => 'required',
            'cuisine' => 'required',
            'diet' => 'required',
            'max_calories' => 'required',
            'max_cook_time' => 'required',
            'planning' => 'required',
        ];
    }

    /**
     * handle
     *
     * @param  string      $cuisine
     * @param  string      $diet
     * @param  string|null $exclude
     * @param  int         $maxCalories
     * @param  int         $maxCookTime
     * @param  string      $planning
     * @return array
     */
    public function handle(
        string $cuisine,
        string $diet,
        string | null $exclude,
        int $maxCalories,
        int $maxCookTime,
        string $planning
    ): array{
        DB::beginTransaction();
        try {
            $planning = config('generator.meal_planning.openai.planning')[$planning];
            $separator = config('generator.meal_planning.openai.training_text.separator');
            $inputParams = "cuisine:$cuisine" . $separator;
            $inputParams .= "diet:$diet" . $separator;
            $inputParams .= "exclude:$exclude" . $separator;
            $inputParams .= "maxCalories:$maxCalories" . $separator;
            $inputParams .= "maxCookTime:$maxCookTime" . $separator;
            $inputParams .= "planning:$planning";

            $prompt = MealPlanningHelper::preparePrompt($inputParams);

            // dd($prompt);
            $openAiService = new OpenAIService();
            $response = $openAiService->completition([
                'prompt' => $prompt,
                ...config('generator.meal_planning.openai.params'),
            ]);

            $plan = OpenAiResponse::create([
                'type' => 'meal_planning',
                'prompt' => $inputParams,
                'generated_text' => $response['text'],
                'prompt_tokens' => $response['usage']['prompt_tokens'],
                'completion_tokens' => $response['usage']['completion_tokens'],
                'total_tokens' => $response['usage']['total_tokens'],
            ]);

        } catch (\Exception$e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return [
            'plan' => $plan,
        ];
    }

    /**
     *
     * @param  ActionRequest $request
     * @return mixed
     */
    public function asController(
        ActionRequest $request,
    ) {
        return $this->handle(
            // $request->plan_meals,
            // $request->meal_types,
            // $request->ingredients,
            $request->cuisine,
            $request->diet,
            $request->exclude,
            $request->max_calories,
            $request->max_cook_time,
            $request->planning,
        );
    }

    public function htmlResponse(array $response)
    {
        return $this->response($response);
    }

    public function jsonResponse(array $response)
    {
        return $this->response($response);
    }

    private function response(array $response)
    {
        return ResponseHelper::getDefaultResponse($response);
    }
}