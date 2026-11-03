<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postingans', function (Blueprint $table) {
            $table->boolean('is_auto_blocked')->default(false)->after('status');
            $table->string('block_reason')->nullable()->after('is_auto_blocked');
        });
    }

    public function down(): void
    {
        Schema::table('postingans', function (Blueprint $table) {
            $table->dropColumn(['is_auto_blocked', 'block_reason']);
        });
    }
};
