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
        Schema::create('reservations', function (Blueprint $table) {
            $table->primary(['user_id', 'book_id', 'start']);
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('book_id')->references('book_id')->on('books');
            $table->date('start')->default(now());
            $table->boolean('message')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
