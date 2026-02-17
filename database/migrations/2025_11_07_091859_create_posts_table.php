<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Таблица объявлений
     * 
     * Поля:
     * - title: заголовок объявления
     * - slug: уникальный URL (генерируется из title)
     * - email: email автора
     * - phone: телефон
     * - photo_view: показывать ли фото (0 - нет, 1 - да)
     * - whats: WhatsApp
     * - telegram: Telegram
     * - date: дата создания
     * - fio: ФИО автора
     * - sex: пол (1 - мужчина, 2 - женщина)
     * - who: кто ищет (1 - спонсор, 2 - содержанка)
     * - city: город (название города)
     * - discription: описание объявления
     * - view: количество просмотров
     * - ip: IP адрес
     * - del: удалено ли (0 - нет, 1 - да)
     * - from_telegram: добавлено из телеграма (0 - нет, 1 - да)
     * - telegram_id: ID пользователя в Telegram
     * - telegram_username: username в Telegram
     */
    public function up(): void
    {
        Schema::create('post', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->string('email', 255);
            $table->string('phone', 255);
            $table->boolean('photo_view')->default(false);
            $table->string('whats', 255)->default('');
            $table->string('telegram', 255)->default('');
            $table->dateTime('date');
            $table->string('fio', 255);
            $table->integer('sex');
            $table->integer('who');
            $table->string('city', 255);
            $table->text('discription');
            $table->integer('view')->default(10);
            $table->string('ip', 255);
            $table->boolean('del')->default(false);
            $table->integer('from_telegram')->default(0);
            $table->string('telegram_id', 255)->nullable();
            $table->string('telegram_username', 255)->nullable();
            
            // Индексы для быстрого поиска
            $table->index('slug');
            $table->index('who');
            $table->index('city');
            $table->index('sex');
            $table->index('del');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post');
    }
};