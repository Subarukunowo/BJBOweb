<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('store_name');
            $table->string('store_slug')->unique();
            $table->enum('store_type', ['perorangan', 'official'])->default('perorangan');
            $table->string('phone_number');
            $table->string('email');

            // Persyaratan Perorangan (KTP, No HP, Email)
            $table->string('ktp_number')->nullable();
            $table->string('ktp_image')->nullable();

            // Persyaratan Official Store (NIB, NPWP Perusahaan, Sertifikat HAKI)
            $table->string('nib_number')->nullable();
            $table->string('npwp_company')->nullable();
            $table->string('npwp_image')->nullable();
            $table->string('haki_certificate_number')->nullable();
            $table->string('haki_certificate_file')->nullable();

            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();

            $table->enum('status', ['menunggu_verifikasi', 'aktif', 'ditolak', 'dinonaktifkan'])->default('menunggu_verifikasi');
            $table->text('rejected_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['store_type', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
