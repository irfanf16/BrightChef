<?php

// "params" => [
//     "model" => "text-davinci-003",
//     "temperature" => 0.3,
//     "max_tokens" => 800,
//     "top_p" => 0.3,
//     "best_of" => 1,
// ],

$defaultOpenAiParams = [
    "model" => "text-davinci-003",
    "temperature" => 1,
    "max_tokens" => 600,
    "top_p" => 1,
    "best_of" => 1,
    "frequency_penalty" => 0.0,
    "presence_penalty" => 2.0,
];

$defaultTrainingTextSeparator = "@";
$defaultStopSequence = "###";

return [
    "custom_recipe" => [
        "openai" => [
            "params" => $defaultOpenAiParams,
            "stop_sequence" => $defaultStopSequence,
            "training_text" => [
                "separator" => $defaultTrainingTextSeparator,
                "prompt" => [
                    "ingredients" => "carrots, lamb steak, onions, broccoli, pasta, rice, ketchup, lemons, white vinegar",
                    "cuisine" => "american",
                    "maxCalories" => "500",
                    "maxCookTime" => "30",
                    "servings" => "2",
                    "pantry" => "salt,water,pepper,oil",
                    "difficulty" => "easy",
                ],
                "response" => [
                    "recipe" => "Lamb and Vegetable Stir-Fry",
                    "ingredients" => "4 oz lamb steak (cut into small cubes)@1 carrot (sliced)@2 onions (diced)@1/4 head of broccoli (florets only)@1/3 cup rice noodles@1 tbsp olive oil@2 tsp ketchup@juice from half a lemon @2 tsp white vinegar@1 pinch salt@1 pinch pepper",
                    "instructions" => "Heat the oil in a large pan over medium heat.@Add the lamb, onion, and carrots and stir-fry until the lamb is cooked through and the vegetables are tender.@Add the broccoli florets and continue to stir-fry for an additional two minutes.@Stir in the noodles and cook for one minute before adding the ketchup, lemon juice, vinegar, and seasonings.@Mix everything together and let simmer until the sauce thickens.@Serve hot. Bon appetit!",
                    "calories" => "380 cals/serving",
                    "servings" => "2",
                    "cook_time" => "20 minutes",
                ],
            ],
        ],
    ],
    "meal_planning" => [
        "openai" => [
            "params" => $defaultOpenAiParams,
            "stop_sequence" => $defaultStopSequence,
            // "planning" => ["daily" => "3/day for 1 day", "weekly" => "3/day for 7 days"],
            "planning" => ["daily" => "lunch,dinner for 1 day", "weekly" => "lunch,dinner for 7 days"],
            "training_text" => [
                "separator" => $defaultTrainingTextSeparator,
                "prompt" => [
                    // "ingredients" => "cheese, pasta, spaghetti, bread, eggs, chicken",
                    "cuisine" => "italian",
                    "diet" => "vegetarian",
                    "exclude" => "eggs",
                    "maxCookTime" => "120",
                    "maxCalories" => "2200",
                    // "servings" => "2",
                    // "planning" => "3/day for 1 day",
                    "planning" => "2/day for 1 day",
                ],
                "response" => [
                    "columns" => "day,type,recipe,time,cals",
                    // "recipes" => ["1,breakfast,Spaghetti carbonara,30m,550c", "1,lunch,Italian-style cannellini bean salad with chicken and cheese,45m,600c", "1,dinner,Roasted garlic olive spaghetti,25m,400c"],
                    "recipes" => ["1,Lunch,Pasta Primavera,40m,478c", "1,Dinner,eggplant parmesan,90m,750c"],
                ],
            ],
        ],
    ],

    "recipe_generator" => [
        "openai" => [
            "params" => $defaultOpenAiParams,
            "stop_sequence" => $defaultStopSequence,
            "training_text" => [
                "separator" => $defaultTrainingTextSeparator,
                "prompt" => [
                    "ingredients" => "carrots, lamb steak, onions, broccoli, pasta, rice, ketchup, lemons, white vinegar",
                    "cuisine" => "american",
                    "maxCalories" => "500",
                    "maxCookTime" => "30",
                    "servings" => "2",
                    "pantry" => "salt,water,pepper,oil",
                    "difficulty" => "easy",
                ],
                "response" => [
                    "recipe" => "Lamb and Vegetable Stir-Fry",
                    "ingredients" => "4 oz lamb steak (cut into small cubes)@1 carrot (sliced)@2 onions (diced)@1/4 head of broccoli (florets only)@1/3 cup rice noodles@1 tbsp olive oil@2 tsp ketchup@juice from half a lemon @2 tsp white vinegar@1 pinch salt@1 pinch pepper",
                    "instructions" => "Heat the oil in a large pan over medium heat.@Add the lamb, onion, and carrots and stir-fry until the lamb is cooked through and the vegetables are tender.@Add the broccoli florets and continue to stir-fry for an additional two minutes.@Stir in the noodles and cook for one minute before adding the ketchup, lemon juice, vinegar, and seasonings.@Mix everything together and let simmer until the sauce thickens.@Serve hot. Bon appetit!",
                    "calories" => "380 cals/serving",
                    "servings" => "2",
                    "cook_time" => "20 minutes",
                ],
            ],
        ],
    ],

];