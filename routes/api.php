<?php

use App\Jobs\TestJob;
use Illuminate\Http\Request;
use App\Models\OpenAiResponse;
use App\Support\Helpers\RecipeHelper;
use App\Support\Helpers\StringHelper;
use Illuminate\Support\Facades\Route;
use App\Actions\Api\Recipe\MealPlanningAction;
use App\Actions\Api\Recipe\GenerateCustomRecipe;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    return view('welcome');
});

// recipes
Route::get('custom-recipe', GenerateCustomRecipe::class);
// meals
Route::get('fetch-custom-recipes', function () {
    $recipes = OpenAiResponse::orderBy('id', 'desc')->where('type', 'custom_recipe_v2')->get();
    return [
        'recipes' => $recipes,
    ];
});
Route::prefix('meals')->group(function () {
    Route::post('/', MealPlanningAction::class);
    Route::get('/', function () {
        $plans = OpenAiResponse::orderBy('id', 'desc')->where('type', 'meal_planning')->get();
        return [
            'plans' => $plans,
        ];
    });
});

Route::get('test', function () {
    // $recipes = json_decode(file_get_contents(public_path('seeders') . '/custom_recipe_v2_seeder.json'));
    // // return collect($recipes[0])->toArray();
    // foreach ($recipes as $recipe) {
    //     OpenAiResponse::create(collect($recipe)->toArray());
    // };
});
