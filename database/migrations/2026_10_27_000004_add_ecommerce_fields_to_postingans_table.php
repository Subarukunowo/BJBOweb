<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postingans', function (Blueprint $table) {
            $table->string('kondisi')->default('Bekas (Pre-loved)')->after('category');
            $table->string('tipe_penawaran')->default('Bisa Nego')->after('price');
            $table->boolean('is_sold')->default(false)->after('status');
            $table->unsignedInteger('views_count')->default(0)->after('is_sold');
        });
    }

    public function down(): void
    {
        Schema::table('postingans', function (Blueprint $table) {
            $table->dropColumn(['kondisi', 'tipe_penawaran', 'is_sold', 'views_count']);
        });
    }
};
