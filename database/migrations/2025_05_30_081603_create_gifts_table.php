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
        Schema::create('gifts', function (Blueprint $table) {
            $table->id(); // Kolom ID auto-increment
            $table->string('slug')->unique(); // Untuk URL unik (misal: /g/aBcDeFgH)
            $table->text('message')->nullable();
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable(); // Untuk link YouTube
            $table->string('other_link')->nullable();
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gifts');
    }
};