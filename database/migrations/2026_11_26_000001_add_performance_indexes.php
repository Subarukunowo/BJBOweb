<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postingans', function (Blueprint $table) {
            $table->index(['status', 'is_auto_blocked', 'id'], 'idx_postingans_feed');
            $table->index(['status', 'is_auto_blocked', 'category', 'id'], 'idx_postingans_cat_feed');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->index(['user_id', 'postingan_id'], 'idx_carts_user_prod');
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->index(['user_id', 'postingan_id'], 'idx_wishlists_user_prod');
        });
    }

    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropIndex('idx_wishlists_user_prod');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex('idx_carts_user_prod');
        });

        Schema::table('postingans', function (Blueprint $table) {
            $table->dropIndex('idx_postingans_cat_feed');
            $table->dropIndex('idx_postingans_feed');
        });
    }
};
