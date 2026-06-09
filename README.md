# BrightChef

AI-powered **recipe generation and meal planning API** backend. Sends prompts to OpenAI (GPT) to generate custom recipes or weekly/daily meal plans based on user dietary constraints.

![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=flat&logo=php)
![Laravel](https://img.shields.io/badge/Laravel-9.x-FF2D20?style=flat&logo=laravel)
![Laravel Sanctum](https://img.shields.io/badge/Sanctum-3.0-FF2D20?style=flat&logo=laravel)
![Laravel Horizon](https://img.shields.io/badge/Horizon-5.14-FF2D20?style=flat&logo=laravel)
![Redis](https://img.shields.io/badge/Redis-predis_2.x-DC382D?style=flat&logo=redis)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=flat&logo=mysql)

## Features

- **Custom Recipe Generation** (`GET /api/custom-recipe`) — generates a recipe given difficulty, cuisine, max calories, cook time, servings, and optional ingredients. Validated and saved to `open_ai_responses`.
- **Meal Planning** (`POST /api/meals`) — generates a full meal plan (daily/weekly) from cuisine, diet, calorie/time constraints, and exclusions.
- **History Endpoints** — `GET /api/meals` and `GET /api/fetch-custom-recipes` return stored AI responses.
- **OpenAI Response Caching** — every prompt/response pair (with token counts) is stored for audit and replay.
- **laravel-actions pattern** — all business logic in single-responsibility `Action` classes (`lorisleiva/laravel-actions`), not controllers.
- **Laravel Horizon + Redis** — background job queue monitoring.
- **Sanctum Auth** — API token authentication.

## Database Schema

| Table | Key Columns | Purpose |
|---|---|---|
| `users` | `id` (ULID), `name`, `email`, `password` | API users |
| `open_ai_responses` | `id`, `prompt`, `generated_text`, `type`, `prompt_tokens`, `completion_tokens`, `total_tokens` | AI prompt/response audit log |

## Architecture

```
GET /api/custom-recipe  →  GenerateCustomRecipe (Action)
                               └── OpenAIService::completition()
                                       └── OpenAiResponse::create()

POST /api/meals         →  MealPlanningAction
                               └── OpenAIService::completition()
                                       └── OpenAiResponse::create()
```

Prompt builders live in `app/Support/Helpers/` (`CustomRecipeHelper`, `MealPlanningHelper`).

## Getting Started

```bash
composer install
cp .env.example .env && php artisan key:generate
# Set DB_*, REDIS_*, OPENAI_API_KEY
php artisan migrate
php artisan horizon
php artisan serve
```

## Environment Variables

| Variable | Purpose |
|---|---|
| `OPENAI_API_KEY` | OpenAI API key |
| `QUEUE_CONNECTION` | Set to `redis` for Horizon |
| `REDIS_HOST` / `REDIS_PORT` | Redis connection |
| `DB_*` | MySQL connection |

## License
MIT
