# BrightChef - Chef & Food Delivery Platform

A **Laravel-powered platform** connecting customers with professional chefs. Features Stripe billing and background job queues.

## Tech Stack

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![Stripe](https://img.shields.io/badge/Stripe-008CDD?style=flat&logo=stripe&logoColor=white)
![Laravel Horizon](https://img.shields.io/badge/Laravel_Horizon-FF2D20?style=flat&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)

## Features

- Chef profiles with menu management
- Food ordering and cart system
- Stripe subscriptions and one-time payments
- Laravel Horizon for background job processing
- Laravel Sanctum API authentication
- Push notifications

## Getting Started

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan horizon   # start queue worker
php artisan serve
```

## License
MIT
