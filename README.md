# BrightChef — AI-Powered Recipe & Meal Planning API

> **Laravel 9 REST API** for AI-generated custom recipes and weekly meal plans using OpenAI GPT-3 (`text-davinci-003`). Implements few-shot prompt engineering via configurable training examples, structured text parsing for recipe extraction, Redis-backed Laravel Horizon for async queue monitoring, and ULID primary keys. Saves every AI generation to an audit log for history retrieval.

![Laravel](https://img.shields.io/badge/Laravel-9-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.0-777BB4?style=flat-square&logo=php&logoColor=white)
![OpenAI](https://img.shields.io/badge/OpenAI-GPT--3-412991?style=flat-square&logo=openai&logoColor=white)
![Laravel Horizon](https://img.shields.io/badge/Laravel_Horizon-Redis-FF2D20?style=flat-square)

---

## Tech Stack

| Package | Version | Purpose |
|---|---|---|
| `laravel/framework` | ^9.19 | Core framework |
| `openai/openai-php` via HTTP | — | GPT-3 `text-davinci-003` completions (raw Guzzle calls) |
| `guzzlehttp/guzzle` | ^7.2 | HTTP client for OpenAI API |
| `lorisleiva/laravel-actions` | ^2.5 | Single-action class architecture |
| `laravel/horizon` | ^5.14 | Queue monitoring dashboard |
| `predis/predis` | ^2.1 | Redis PHP client |
| `laravel/sanctum` | ^3.0 | API token authentication |
| `laravel/sail` | ^1.0.1 | Docker development environment |
| `laravel/pint` | ^1.0 | Code style fixer |

---

## Routes

| Method | Route | Auth | Description |
|---|---|---|---|
| `GET` | `/api/user` | Sanctum | Return authenticated user |
| `GET` | `/api/custom-recipe` | — | Generate a custom recipe via GPT-3 |
| `GET` | `/api/fetch-custom-recipes` | — | Retrieve all stored recipe generations |
| `POST` | `/api/meals` | — | Generate a meal plan (daily or weekly) |
| `GET` | `/api/meals` | — | Retrieve all stored meal plan generations |
| `GET` | `/horizon` | — | Laravel Horizon queue monitoring dashboard |

---

## Database Schema

**`users`**
| Column | Type | Notes |
|---|---|---|
| `id` | ULID | Primary key (HasUlids trait — not auto-increment integer) |
| `name` | string | |
| `email` | string | unique |
| `password` | string | bcrypt |
| `email_verified_at` | timestamp | nullable |

**`open_ai_responses`** — Audit log for all AI generations
| Column | Type | Notes |
|---|---|---|
| `id` | integer | auto-increment |
| `prompt` | longText | Full prompt sent to OpenAI |
| `generated_text` | longText | Raw GPT-3 completion |
| `type` | string | `"custom_recipe_v2"` or `"meal_planning"` |
| `prompt_tokens` | integer | OpenAI token usage |
| `completion_tokens` | integer | |
| `total_tokens` | integer | |

---

## AI Integration

**Model:** `text-davinci-003` completions endpoint (`POST /v1/completions`)
**Parameters:** `temperature=1`, `max_tokens=600`, `top_p=1`, `best_of=1`, `frequency_penalty=0.0`, `presence_penalty=2.0`, `stop=["###"]`

**`OpenAIService::completition()`** — single method wrapping the Guzzle HTTP call to OpenAI.

---

## Custom Recipe Generation

**Endpoint:** `GET /api/custom-recipe`

**Request parameters:**
```
difficulty    string   (easy/medium/hard)
cuisine       string   (Italian, Thai, etc.)
max_calories  integer
max_cook_time integer  (minutes)
servings      integer
ingredients   string   optional (comma-separated preferred ingredients)
```

**How it works:**
1. `GenerateCustomRecipe` (laravel-action) validates input via `rules()`
2. `CustomRecipeHelper::preparePrompt()` builds a few-shot prompt from `config/generator.php` training examples
3. The prompt uses `@` as field separator between prompt parameters; `###` as stop sequence
4. `OpenAIService::completition()` calls GPT-3 with the assembled prompt
5. Raw completion saved to `open_ai_responses` with full token counts
6. `getFormattedTextAttribute()` accessor parses bracketed sections from GPT-3 response:

```
GPT-3 returns (example):
[recipe]: Chicken Tikka Masala
[ingredients]: 500g chicken @ 200ml coconut cream @ ...
[instructions]: 1. Marinate chicken for 30 minutes @ 2. Heat oil in pan @ ...
[calories]: 450
[servings]: 4
[cook_time]: 35
```

Parsed into structured array: `{ recipe, ingredients[], instructions[], calories, servings, cook_time }`

---

## Meal Planning

**Endpoint:** `POST /api/meals`

**Request parameters:**
```
cuisine       string
diet          string   (vegetarian, vegan, keto, etc.)
max_calories  integer
max_cook_time integer
planning      string   ("daily" or "weekly")
exclude       string   optional (ingredients to exclude)
```

**Planning mapping:**
- `"daily"` → generates `lunch,dinner for 1 day`
- `"weekly"` → generates `lunch,dinner for 7 days`

**How it works:**
1. `MealPlanningHelper::preparePrompt()` builds CSV-structured few-shot prompt (columns: `day,type,recipe,time,cals`)
2. `MealPlanningHelper::format()` parses CSV response and groups meals by day:

```
GPT-3 returns:
1,lunch,Grilled Salmon,25,380
1,dinner,Pasta Primavera,30,520
2,lunch,Caesar Salad,10,280
...

Parsed into:
{
  "1": { "lunch": {...}, "dinner": {...} },
  "2": { "lunch": {...}, "dinner": {...} },
  ...
}
```

---

## Laravel Actions Pattern

All business logic lives in action classes (`app/Actions/Api/Recipe/`), not controllers:

```php
class GenerateCustomRecipe
{
    use AsAction;

    public function rules(): array
    {
        return [
            'difficulty'   => 'required|string',
            'cuisine'      => 'required|string',
            'max_calories' => 'required|integer',
            // ...
        ];
    }

    public function handle(string $difficulty, string $cuisine, int $maxCalories, ...): array
    {
        $prompt = CustomRecipeHelper::preparePrompt(...);
        $response = OpenAIService::completition($prompt);
        OpenAiResponse::create([...]);  // save to audit log
        return $response->getFormattedTextAttribute();
    }

    public function asController(Request $request): JsonResponse
    {
        return response()->json(
            $this->handle(...$request->validated())
        );
    }
}
```

Same action class works as: **controller** (via route) + **queued job** (via `dispatch()`) + **artisan command** (via `php artisan generate:recipe`).

---

## Few-Shot Prompt Engineering

Training examples are stored in `config/generator.php` — not hardcoded in action classes. `CustomRecipeHelper::preparePrompt()` injects these into every request:

```php
// config/generator.php
'custom_recipe_examples' => [
    [
        'input'  => 'difficulty=easy@cuisine=Italian@...',
        'output' => '[recipe]: Simple Pasta
[ingredients]: ...',
    ],
    // ... more examples
],
```

The model sees real examples before the actual request, dramatically improving output structure consistency for GPT-3 completions (before JSON mode existed in OpenAI).

---

## Laravel Horizon Config

Redis queue `default`, supervisor `supervisor-1`:
```php
'production' => ['maxProcesses' => 10, 'balanceMaxShift' => 1, 'balanceCooldown' => 3],
'local'      => ['maxProcesses' => 3],
```

Job retention: recent/pending/completed kept 60 min; failed jobs kept 7 days.

---

## Getting Started

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

# Or with Docker:
./vendor/bin/sail up

php artisan horizon    # Queue monitoring
php artisan serve
```

**Required environment variables:**
```env
OPENAI_API_KEY=

REDIS_HOST=
QUEUE_CONNECTION=redis
```
