<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id'); // ID отправителя
            $table->unsignedBigInteger('receiver_id'); // ID получателя
            $table->unsignedBigInteger('post_id')->nullable(); // ID объявления (если сообщение связано с объявлением)
            $table->text('message'); // Текст сообщения
            $table->boolean('is_read')->default(0); // Прочитано ли сообщение
            $table->timestamp('read_at')->nullable(); // Когда прочитано
            $table->timestamps();
            
            // Индексы для быстрого поиска
            $table->index(['sender_id', 'receiver_id']);
            $table->index(['receiver_id', 'is_read']);
            $table->index('post_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};