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
        Schema::table('products', function (Blueprint $table) {
            $table->json('sizes')->nullable()->after('stock');
            $table->string('brand')->nullable()->after('name');
            $table->string('model_line')->nullable()->after('brand');
            $table->string('gender')->nullable()->default('Unissex')->after('model_line');
            $table->decimal('compare_price', 10, 2)->nullable()->after('price');
            $table->string('material')->nullable()->after('technical_specifications');
            $table->string('sole_material')->nullable()->after('material');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('size')->nullable()->after('color_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'sizes',
                'brand',
                'model_line',
                'gender',
                'compare_price',
                'material',
                'sole_material',
            ]);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('size');
        });
    }
};
