<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Таблица пользователей (спонсоры и содержанки)
     * 
     * Поля:
     * - device_key: уникальный ключ устройства для идентификации
     * - email: email пользователя
     * - password: хешированный пароль
     * - ip: IP адрес при регистрации
     * - date: дата и время регистрации
     * - activate: статус активации аккаунта (0 - не активирован, 1 - активирован)
     * - status: статус пользователя (0 - обычный, возможно другие статусы для VIP и т.д.)
     * - activate_code: код для активации аккаунта
     * - phone: номер телефона
     * - fio: ФИО пользователя
     * - restore_code: код для восстановления пароля
     * - sex: пол (предположительно: 1 - мужчина/спонсор, 2 - женщина/содержанка)
     * - prov: статус проверки/верификации (0 - не проверен, 1 - проверен)
     * - prov_date: дата проверки/верификации
     * - confirm: подтверждение (например email или телефона)
     * - telegram_id: ID пользователя в Telegram
     * - telegram_username: username в Telegram
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // int(11) auto_increment
            $table->string('device_key', 150)->default('');
            $table->string('email', 255);
            $table->string('password', 255);
            $table->string('ip', 255);
            $table->dateTime('date');
            $table->integer('activate')->default(0);
            $table->integer('status')->default(0);
            $table->string('activate_code', 255)->default('');
            $table->string('phone', 255)->default('');
            $table->string('fio', 255)->default('');
            $table->string('restore_code', 255)->default('');
            $table->integer('sex');
            $table->integer('prov')->default(0);
            $table->dateTime('prov_date')->nullable();
            $table->boolean('confirm')->default(false);
            $table->string('telegram_id', 255)->nullable();
            $table->string('telegram_username', 255)->nullable();
            
            // Индексы для быстрого поиска
            $table->index('email');
            $table->index('phone');
            $table->index('sex');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};