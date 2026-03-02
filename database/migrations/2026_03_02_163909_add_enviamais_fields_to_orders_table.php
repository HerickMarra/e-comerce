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
            $table->string('shipping_simulacao_id')->nullable()->after('shipping_tracking_url');
            $table->string('shipping_modalidade')->nullable()->after('shipping_simulacao_id');
            $table->string('shipping_descricao_conteudo')->nullable()->after('shipping_modalidade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_simulacao_id', 'shipping_modalidade', 'shipping_descricao_conteudo']);
        });
    }
};
