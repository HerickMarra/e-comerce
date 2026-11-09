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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('media_type')->default('video'); // 'video' or 'image'
            $table->text('video_url')->nullable();
            $table->string('video_file')->nullable();
            $table->text('image_url')->nullable();
            $table->string('image_file')->nullable();
            $table->string('btn_text')->nullable()->default('Ver Catálogo');
            $table->string('btn_link')->nullable()->default('/categorias');
            $table->boolean('is_active')->default(true);
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
