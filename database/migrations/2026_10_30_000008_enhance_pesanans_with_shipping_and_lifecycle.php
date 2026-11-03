<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->string('shipping_status')->default('menunggu_konfirmasi')->after('status');
            // Statuses: 'menunggu_konfirmasi', 'diproses', 'dikirim', 'sampai_tujuan', 'ditolak_penjual'

            $table->string('kurir_name')->nullable()->after('shipping_status');
            $table->string('nomor_resi')->nullable()->after('kurir_name');
            $table->timestamp('penjual_confirmation_at')->nullable()->after('nomor_resi');
            $table->text('alasan_penolakan')->nullable()->after('penjual_confirmation_at');

            // Lokasi asal dan tujuan untuk kurir tracking di OpenStreetMap Leaflet
            $table->decimal('origin_lat', 10, 7)->nullable()->after('alasan_penolakan');
            $table->decimal('origin_lng', 10, 7)->nullable()->after('origin_lat');
            $table->string('origin_address')->nullable()->after('origin_lng');
            $table->decimal('destination_lat', 10, 7)->nullable()->after('origin_address');
            $table->decimal('destination_lng', 10, 7)->nullable()->after('destination_lat');
            $table->string('destination_address')->nullable()->after('destination_lng');
            $table->decimal('current_lat', 10, 7)->nullable()->after('destination_address');
            $table->decimal('current_lng', 10, 7)->nullable()->after('current_lat');
            $table->string('last_checkpoint')->nullable()->after('current_lng');

            // Promo and Rekber escrow release
            $table->foreignId('promo_id')->nullable()->after('last_checkpoint')->constrained('promos')->nullOnDelete();
            $table->bigInteger('discount_amount')->default(0)->after('promo_id');
            $table->bigInteger('final_price')->nullable()->after('discount_amount');
            $table->timestamp('delivered_at')->nullable()->after('final_price');
            $table->boolean('is_funds_released')->default(false)->after('delivered_at');

            // Indexes for fast lookup avoiding N+1 / slow table scans
            $table->index(['penjual_id', 'shipping_status']);
            $table->index(['user_id', 'shipping_status']);
            $table->index('nomor_resi');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropForeign(['promo_id']);
            $table->dropIndex(['penjual_id', 'shipping_status']);
            $table->dropIndex(['user_id', 'shipping_status']);
            $table->dropIndex(['nomor_resi']);

            $table->dropColumn([
                'shipping_status',
                'kurir_name',
                'nomor_resi',
                'penjual_confirmation_at',
                'alasan_penolakan',
                'origin_lat',
                'origin_lng',
                'origin_address',
                'destination_lat',
                'destination_lng',
                'destination_address',
                'current_lat',
                'current_lng',
                'last_checkpoint',
                'promo_id',
                'discount_amount',
                'final_price',
                'delivered_at',
                'is_funds_released',
            ]);
        });
    }
};
