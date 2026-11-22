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
        // 1. Tambah saldo_bjpay pada tabel users
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'saldo_bjpay')) {
                $table->bigInteger('saldo_bjpay')->default(0)->after('saldo_rekber');
            }
        });

        // 2. Tambah metode_pembayaran, ongkir, diskon_ongkir pada pesanans jika belum ada
        Schema::table('pesanans', function (Blueprint $table) {
            if (! Schema::hasColumn('pesanans', 'ongkir')) {
                $table->bigInteger('ongkir')->default(10000)->after('final_price');
            }
            if (! Schema::hasColumn('pesanans', 'diskon_ongkir')) {
                $table->bigInteger('diskon_ongkir')->default(0)->after('ongkir');
            }
            if (! Schema::hasColumn('pesanans', 'metode_pembayaran')) {
                $table->string('metode_pembayaran')->default('transfer')->after('diskon_ongkir');
            }
        });

        // 3. Buat tabel invoices untuk struk digital dan invoice resmi
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('pesanan_id')->constrained('pesanans')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->bigInteger('subtotal');
            $table->bigInteger('ongkir')->default(0);
            $table->bigInteger('diskon_voucher')->default(0);
            $table->bigInteger('diskon_ongkir')->default(0);
            $table->bigInteger('total_bayar');
            $table->string('metode_pembayaran')->default('transfer'); // 'transfer', 'cod', 'bjpay'
            $table->string('status')->default('menunggu_pembayaran'); // 'menunggu_pembayaran', 'lunas', 'batal', 'selesai'
            $table->timestamp('paid_at')->nullable();
            $table->string('receipt_token')->unique();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index('receipt_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');

        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn(['ongkir', 'diskon_ongkir', 'metode_pembayaran']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['saldo_bjpay']);
        });
    }
};
