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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_id')->nullable()->after('address_info');
            $table->string('shipping_service_name')->nullable()->after('shipping_id');
            $table->string('shipping_tracking_url')->nullable()->after('shipping_service_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_id', 'shipping_service_name', 'shipping_tracking_url']);
        });
    }
};
