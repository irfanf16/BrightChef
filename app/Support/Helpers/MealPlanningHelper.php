<?php

namespace App\Support\Helpers;

class MealPlanningHelper
{
    /**
     * Format the meals from text
     *
     * text format
     * day,type,recipe,time,cals
     * 1,breakfast,Spaghetti carbonara,30m,550c
     * 1,lunch,Italian-style cannellini bean salad with chicken and cheese,45m,600c
     * 1,dinner,Roasted garlic olive spaghetti,25m,400c
     *
     * @param  string  $text
     * @return array
     */
    public static function format(string $text): array
    {
        $separator = config('generator.custom_recipe.openai.training_text.separator');
        $plan = [];

        $array = explode("\n", trim($text));
        $keys = explode(',', $array[0]);
        $data = array_splice($array, 1, sizeof($array) - 1);

        foreach ($data as $key => $value) {
            $plan[] = array_combine($keys, explode(',', $value));
        }

        $output = [];

        foreach ($plan as $meal) {
            // Group the meals by day
            $day = $meal['day'];
            if (!isset($output[$day])) {
                $output[$day] = [
                    'day' => $day,
                    'meals' => [],
                ];
            }

            // Add the meal to the day's meals array
            $output[$day]['meals'][] = [
                'type' => $meal['type'],
                'recipe' => $meal['recipe'],
                'time' => str_replace('m', ' mins', $meal['time']),
                'cals' => str_replace('c', ' cals', $meal['cals']),
            ];
        }

        // Convert the output array to indexed array
        $output = array_values($output);

        return $output;
    }

    public static function preparePrompt($inputParams)
    {
        $trainingText = config("generator.meal_planning.openai.training_text");
        $separator = config("generator.meal_planning.openai.training_text.separator");

        $prompt = "";
        foreach ($trainingText["prompt"] as $key => $value) {
            $prompt .= "$key:$value" . $separator;
        }

        $prompt = rtrim($prompt, $separator) . "\n";

        $prompt .= $trainingText["response"]["columns"] . "\n";
        foreach ($trainingText["response"]["recipes"] as $key => $value) {
            $prompt .= "$value\n";
        }

        $prompt .= config("generator.meal_planning.openai.stop_sequence") . "\n" . $inputParams;

        return $prompt;
    }
}
