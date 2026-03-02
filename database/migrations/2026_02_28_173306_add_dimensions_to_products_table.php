<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('weight', 8, 3)->nullable()->after('stock')->comment('Peso em kg');
            $table->decimal('height', 8, 2)->nullable()->after('weight')->comment('Altura em cm');
            $table->decimal('width', 8, 2)->nullable()->after('height')->comment('Largura em cm');
            $table->decimal('length', 8, 2)->nullable()->after('width')->comment('Comprimento em cm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
