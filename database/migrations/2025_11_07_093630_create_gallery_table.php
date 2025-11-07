<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Таблица галереи фото для объявлений
     * 
     * Поля:
     * - id_post: ID объявления
     * - original_path: путь к оригинальному фото
     * - thumb_path: путь к миниатюре (193x193)
     * - webp_path: путь к webp версии миниатюры (для быстрой загрузки)
     */
	public function up(): void
	{
		Schema::create('gallery', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('id_post');
			$table->string('original_webp', 255); // оригинал в WebP
			$table->string('thumb_webp', 255);    // миниатюра 193x193 в WebP
			$table->timestamps();
			
			$table->index('id_post');
			$table->foreign('id_post')->references('id')->on('posts')->onDelete('cascade');
		});
	}

    public function down(): void
    {
        Schema::dropIfExists('gallery');
    }
};