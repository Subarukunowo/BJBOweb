<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type')->default('diskon_persen'); // banner_iklan, diskon_persen, diskon_nominal, flash_sale
            $table->string('code')->nullable()->unique();
            $table->unsignedInteger('discount_percentage')->nullable();
            $table->bigInteger('discount_amount')->nullable();
            $table->bigInteger('min_purchase')->default(0);
            $table->string('banner_image')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'type']);
            $table->index(['code', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
