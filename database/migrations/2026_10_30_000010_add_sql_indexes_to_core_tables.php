<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postingans', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
            $table->index(['category', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('price');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index(['sender_id', 'receiver_id']);
            $table->index(['receiver_id', 'is_read']);
            $table->index(['postingan_id', 'created_at']);
        });

        Schema::table('pesanans', function (Blueprint $table) {
            $table->index(['user_id', 'status']);
            $table->index(['penjual_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['penjual_id', 'status']);
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['sender_id', 'receiver_id']);
            $table->dropIndex(['receiver_id', 'is_read']);
            $table->dropIndex(['postingan_id', 'created_at']);
        });

        Schema::table('postingans', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['category', 'status']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['price']);
        });
    }
};
