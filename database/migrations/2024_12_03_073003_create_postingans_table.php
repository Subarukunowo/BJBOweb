<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('postingans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('price');
            $table->string('category');
            $table->text('description');
            $table->string('image');
            $table->text('lokasi');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('username')->nullable();
            $table->enum('status', ['disetujui', 'belum disetujui'])->default('belum disetujui');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postingans');
    }
};
