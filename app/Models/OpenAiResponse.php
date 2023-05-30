<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\Helpers\CustomRecipeHelper;
use App\Support\Helpers\MealPlanningHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OpenAiResponse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'prompt',
        'generated_text',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
    ];

    public function getFormattedTextAttribute()
    {
        if ($this->attributes['type'] === 'custom_recipe') {
            return $this->attributes['generated_text'];
        } else if ($this->attributes['type'] === 'meal_planning') {
            return MealPlanningHelper::format($this->attributes['generated_text']);
        }

        return CustomRecipeHelper::formatRecipe($this->attributes['generated_text']);
    }

    protected $appends = ['formatted_text'];

}
