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
        // 1. Ensure product_variants has all required size and color columns
        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'product_id')) {
                $table->foreignId('product_id')->after('id')->constrained('products')->onDelete('cascade');
            }
            if (!Schema::hasColumn('product_variants', 'product_color_id')) {
                $table->foreignId('product_color_id')->nullable()->after('product_id')->constrained('product_colors')->nullOnDelete();
            }
            if (!Schema::hasColumn('product_variants', 'color_name')) {
                $table->string('color_name', 100)->nullable()->after('product_color_id');
            }
            if (!Schema::hasColumn('product_variants', 'color_hex')) {
                $table->string('color_hex', 20)->nullable()->after('color_name');
            }
            if (!Schema::hasColumn('product_variants', 'size')) {
                $table->string('size', 20)->nullable()->after('color_hex');
            }
            if (!Schema::hasColumn('product_variants', 'stock')) {
                $table->integer('stock')->default(0)->after('size');
            }
            if (!Schema::hasColumn('product_variants', 'sku')) {
                $table->string('sku', 100)->nullable()->after('stock');
            }

            $table->index(['product_id', 'size', 'color_name']);
        });

        // 2. Add product_variant_id to order_items for granular order line tracking
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'product_variant_id')) {
                $table->foreignId('product_variant_id')->nullable()->after('product_id')->constrained('product_variants')->nullOnDelete();
            }
        });

        // 3. Create stock_movements table for complete auditability and traceability
        if (!Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('type', 50); // initial_entry, manual_adjustment, sale, cancellation_restore, return
                $table->integer('quantity_before')->default(0);
                $table->integer('quantity_delta'); // + or -
                $table->integer('quantity_after')->default(0);
                $table->string('size', 20)->nullable();
                $table->string('color_name', 100)->nullable();
                $table->string('reason')->nullable();
                $table->timestamps();

                $table->index(['product_id', 'created_at']);
                $table->index(['product_variant_id', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'product_variant_id')) {
                $table->dropForeign(['product_variant_id']);
                $table->dropColumn('product_variant_id');
            }
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'size', 'color_name']);
            $table->dropColumn(['product_color_id', 'color_name', 'color_hex', 'size']);
        });
    }
};
