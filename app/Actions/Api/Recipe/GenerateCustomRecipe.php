<?php

namespace App\Actions\Api\Recipe;

use App\Models\Team;
use App\Models\OpenAiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Support\Helpers\CustomRecipeHelper;
use Lorisleiva\Actions\ActionRequest;
use App\Services\OpenAI\OpenAIService;
use App\Support\Helpers\ResponseHelper;
use Lorisleiva\Actions\Concerns\AsAction;

class GenerateCustomRecipe
{
    use AsAction;

    public function authorize(
        ActionRequest $request
    ) {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'plan_meals' => 'required',
            // 'meal_types' => 'required',
            'difficulty' => 'required',
            'cuisine' => 'required',
            'max_calories' => 'required',
            'max_cook_time' => 'required',
            'servings' => 'required',
        ];
    }

    /**
     * handle
     *
     * @param  string      $difficulty
     * @param  string|null $ingredients
     * @param  string      $cuisine
     * @param  int         $maxCalories
     * @param  int         $maxCookTime
     * @param  int         $servings
     * @return array
     */
    public function handle(
        // string $planMeals,
        // string $mealTypes,
        string $difficulty,
        string | null $ingredients,
        string $cuisine,
        int $maxCalories,
        int $maxCookTime,
        int $servings
    ): array{
        $quantity = 'true';
        $instructions = 'true';
        $recipeName = 'yes';
        $nutritionInfo = 'false';
        $shoppingList = 'false';
        $pantryIngredients = config('generator.custom_recipe.openai.training_text.prompt.pantry');
        DB::beginTransaction();
        try {
            $separator = config('generator.custom_recipe.openai.training_text.separator');
            $inputParams = "ingredients:$ingredients" . $separator;
            $inputParams .= "cuisine:$cuisine" . $separator;
            $inputParams .= "maxCalories:$maxCalories" . $separator;
            $inputParams .= "maxCookTime:$maxCookTime" . $separator;
            $inputParams .= "servings:$servings" . $separator;
            $inputParams .= "pantry:$pantryIngredients" . $separator;
            $inputParams .= "difficulty:$difficulty";

            $prompt = CustomRecipeHelper::preparePrompt($inputParams);

            // dd($prompt);
            $openAiService = new OpenAIService();
            $response = $openAiService->completition([
                'prompt' => $prompt,
                ...config('generator.custom_recipe.openai.params'),
            ]);

            $recipe = OpenAiResponse::create([
                'type' => 'custom_recipe_v2',
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
            'recipe' => $recipe,
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
            $request->difficulty,
            $request->ingredients,
            $request->cuisine,
            $request->max_calories,
            $request->max_cook_time,
            $request->servings,
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
