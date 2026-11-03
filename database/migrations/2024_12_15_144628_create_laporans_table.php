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
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_laporan'); // Jenis laporan
            $table->text('teks_laporan'); // Teks deskripsi laporan
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Menghubungkan ke pengguna yang membuat laporan
            $table->string('status')->default('Pending'); // Status laporan default Pending
            $table->foreignId('postingan_id')->nullable()->constrained('postingans')->onDelete('cascade'); // Menghubungkan ke postingan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
