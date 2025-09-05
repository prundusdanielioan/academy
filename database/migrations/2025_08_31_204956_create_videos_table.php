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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('original_filename');
            $table->string('original_path');
            $table->string('hls_path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('duration')->nullable();
            $table->string('resolution')->nullable();
            $table->string('file_size')->nullable();
            $table->enum('status', ['uploading', 'processing', 'completed', 'failed'])->default('uploading');
            $table->text('processing_log')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
