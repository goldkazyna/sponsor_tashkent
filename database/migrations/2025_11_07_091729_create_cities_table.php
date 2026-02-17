<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Таблица городов Казахстана
     */
    public function up(): void
    {
        Schema::create('city', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255); // Название города
        });

        DB::table('city')->insert([
            ['title' => 'Алматы'],
            ['title' => 'Астана'],
            ['title' => 'Шымкент'],
            ['title' => 'Караганда'],
            ['title' => 'Актобе'],
            ['title' => 'Тараз'],
            ['title' => 'Павлодар'],
            ['title' => 'Усть-Каменогорск'],
            ['title' => 'Семей'],
            ['title' => 'Атырау'],
            ['title' => 'Костанай'],
            ['title' => 'Петропавловск'],
            ['title' => 'Актау'],
            ['title' => 'Уральск'],
            ['title' => 'Кызылорда'],
            ['title' => 'Талдыкорган'],
            ['title' => 'Экибастуз'],
            ['title' => 'Кокшетау'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('city');
    }
};