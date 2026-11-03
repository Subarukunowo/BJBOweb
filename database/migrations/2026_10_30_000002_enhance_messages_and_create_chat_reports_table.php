<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('postingan_id')->nullable()->after('receiver_id')->constrained('postingans')->nullOnDelete();
            $table->boolean('is_reported')->default(false)->after('message');
            $table->boolean('is_read')->default(false)->after('is_reported');
        });

        Schema::create('chat_laporans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('messages')->onDelete('cascade');
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reported_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('postingan_id')->nullable()->constrained('postingans')->nullOnDelete();
            $table->string('alasan');
            $table->text('keterangan')->nullable();
            $table->string('status')->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_laporans');

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['postingan_id']);
            $table->dropColumn(['postingan_id', 'is_reported', 'is_read']);
        });
    }
};
