<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('saldo_rekber')->default(0)->after('profile_picture');
        });

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('bank_name'); // BCA, Mandiri, BRI, BNI, BSI, Bank Riau Kepri Syariah
            $table->string('account_number');
            $table->string('account_name');
            $table->boolean('is_verified')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'is_verified']);
        });

        Schema::create('pencairan_danas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->bigInteger('amount');
            $table->string('status')->default('menunggu_proses'); // menunggu_proses, diproses, selesai, ditolak
            $table->string('reference_number')->unique();
            $table->timestamp('estimated_completion_at')->nullable(); // 2x24 jam kerja
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pencairan_danas');
        Schema::dropIfExists('bank_accounts');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('saldo_rekber');
        });
    }
};
