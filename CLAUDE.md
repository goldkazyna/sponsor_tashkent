# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Classified ads platform built with Laravel 12 and PHP 8.2+. UI is in Russian. Server-rendered Blade templates with Tailwind CSS v4 and vanilla JS (Axios for AJAX).

## Commands

```bash
# Full project setup (install deps, generate key, migrate, build assets)
composer setup

# Development (runs artisan serve + queue + pail + vite concurrently)
composer run dev

# Tests (clears config cache, runs PHPUnit with in-memory SQLite)
composer run test

# Run a single test file
./vendor/bin/phpunit tests/Feature/ExampleTest.php

# Code style (Laravel Pint)
./vendor/bin/pint

# Build frontend assets for production
npm run build
```

## Architecture

### Controllers (app/Http/Controllers/)

- **PostController** — Homepage listing with city filter + pagination, post creation with image upload, post detail page
- **ProfileController** — User dashboard: my posts CRUD, profile settings, password change, messaging system (AJAX-based chat)
- **AuthController** — Custom session-based auth (not Laravel's built-in Auth); uses `session(['user_id' => ...])` for login state
- **ContactController** — Contact form that sends messages via Telegram bot API

### Database Access Pattern

Uses `DB::table()` query builder (not Eloquent models) for almost everything. The `User` model exists but controllers query the `users` table directly via the DB facade. Auth checks are done with `session('user_id')` throughout ProfileController.

### Key Database Tables

- **users** — `sex` field: 1=sponsor, 2=kept woman. `prov` = verified status. Auth by email+password (bcrypt, 12 rounds)
- **posts** — Listings with `slug` (unique), `who` (1=sponsor seeking, 2=kept woman seeking), `del` (soft delete flag), `city` (references cities table by name, not ID)
- **gallery** — Post photos stored as WebP. Fields: `original_webp`, `thumb_webp`. Linked to posts via `id_post`
- **messages** — Direct messaging between users with `is_read`/`read_at` tracking. Optional `post_id` for context
- **cities** — 18 Uzbek cities, seeded in migration

### Image Processing

Uses Intervention/Image v3 with GD driver. Photos are converted to WebP (85% quality for originals, 80% for 193x193 thumbnails). Stored in `public/uploads/posts/{post_id}/`.

### Frontend

- Tailwind CSS v4 via `@tailwindcss/vite` plugin
- Vite for asset bundling (entry points: `resources/css/app.css`, `resources/js/app.js`)
- Blade templates with `layouts/app.blade.php` as main layout
- City filter is a reusable partial: `views/partials/city-filter.blade.php`

### External Integrations

- **Telegram Bot** — Contact form messages sent via bot API. Configured with `TELEGRAM_BOT_TOKEN` and `TELEGRAM_CHAT_ID` env vars.

### Routes

All routes in `routes/web.php`. Profile routes are grouped under `/profile/*` middleware group. Post detail pages use slug-based URLs (`/posts/{slug}`).
