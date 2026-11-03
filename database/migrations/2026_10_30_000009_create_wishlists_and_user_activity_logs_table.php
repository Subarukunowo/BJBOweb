<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('postingan_id')->constrained('postingans')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'postingan_id']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('user_search_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('query');
            $table->string('category')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('query');
        });

        Schema::create('user_product_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('postingan_id')->constrained('postingans')->onDelete('cascade');
            $table->unsignedInteger('view_count')->default(1);
            $table->timestamp('last_viewed_at');
            $table->timestamps();

            $table->index(['user_id', 'postingan_id']);
            $table->index(['user_id', 'last_viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_product_views');
        Schema::dropIfExists('user_search_logs');
        Schema::dropIfExists('wishlists');
    }
};
