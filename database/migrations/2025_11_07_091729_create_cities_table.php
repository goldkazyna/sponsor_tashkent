<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Таблица городов Узбекистана
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255); // Название города
        });

        // Добавляем города Узбекистана
        DB::table('cities')->insert([
            ['name' => 'Ташкент'],
            ['name' => 'Самарканд'],
            ['name' => 'Бухара'],
            ['name' => 'Андижан'],
            ['name' => 'Наманган'],
            ['name' => 'Фергана'],
            ['name' => 'Коканд'],
            ['name' => 'Нукус'],
            ['name' => 'Карши'],
            ['name' => 'Термез'],
            ['name' => 'Гулистан'],
            ['name' => 'Ургенч'],
            ['name' => 'Навои'],
            ['name' => 'Джизак'],
            ['name' => 'Маргилан'],
            ['name' => 'Чирчик'],
            ['name' => 'Ангрен'],
            ['name' => 'Алмалык'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};