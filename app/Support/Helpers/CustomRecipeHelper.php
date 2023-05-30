<?php

namespace App\Support\Helpers;

class CustomRecipeHelper
{
    /**
     * Format the recipe from text
     *
     * text format
     * [recipe]:Breakfast Sandwich
     * [ingredients]:2 slices bread@2 eggs@1 tsp butter@1 pinch salt@1 pinch pepper@1/4 cup cheese (optional)
     * [instructions]:Preheat a skillet over medium heat.@Spread butter onto one side of the bread slices and place them buttered-side-down onto the skillet.@Crack the eggs directly onto the skillet, and season with salt and pepper.@Scramble the eggs until they are cooked to your desired doneness.@Place the cooked eggs between two slices of buttered bread. If desired, top with cheese.@Gently press the sandwich together and flip on the skillet. Cook until the bread is golden and the cheese is melted, if desired.@Serve warm. Enjoy!
     * [calories]:400 cals/serving
     * [servings]:2
     * [cook_time]:20 minutes
     *
     * @param  string   $text
     * @return array
     */
    public static function formatRecipe(string $text): array
    {
        $separator = config('generator.custom_recipe.openai.training_text.separator');
        $recipe = StringHelper::getBetween($text, "[recipe]:", "[ingredients]:");
        $ingredientsRaw = StringHelper::getBetween($text, "[ingredients]:", "[instructions]:");
        $ingredients = explode($separator, $ingredientsRaw);
        $instructionsRaw = StringHelper::getBetween($text, "[instructions]:", "[calories]:");
        $instructions = explode($separator, $instructionsRaw);
        $calories = StringHelper::getBetween($text, "[calories]:", "[servings]:");
        $servings = StringHelper::getBetween($text, "[servings]:", "[cook_time]:");
        $cookTime = StringHelper::getBetween($text, "[cook_time]:", null);

        return [
            'recipe' => $recipe,
            'ingredients' => $ingredients,
            'instructions' => $instructions,
            'calories' => $calories,
            'servings' => $servings,
            'cook_time' => $cookTime,
        ];
    }

    public static function preparePrompt($inputParams)
    {
        $trainingText = config('generator.custom_recipe.openai.training_text');
        $separator = config('generator.custom_recipe.openai.training_text.separator');

        $prompt = '[prompt]:';
        foreach ($trainingText['prompt'] as $key => $value) {
            $prompt .= "$key:$value" . $separator;
        }

        $prompt = rtrim($prompt, $separator) . "\n";

        foreach ($trainingText['response'] as $key => $value) {
            $prompt .= "[$key]:$value\n";
        }

        $prompt .= config('generator.custom_recipe.openai.stop_sequence') . "\n" . "[prompt]:" . $inputParams;

        return $prompt;
    }
}
