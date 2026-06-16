<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('goal', 30)->nullable();
            $table->string('financial', 30)->nullable();
            $table->string('body_type', 30)->nullable();
            $table->smallInteger('height')->nullable();
            $table->smallInteger('weight')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['goal', 'financial', 'body_type', 'height', 'weight']);
        });
    }
};
