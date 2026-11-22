<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * Membuat atau memperbarui data invoice / struk digital untuk suatu pesanan.
     * Menerapkan pemrograman deklaratif tanpa looping.
     */
    public function generateInvoice(Pesanan $pesanan): Invoice
    {
        $existing = Invoice::where('pesanan_id', $pesanan->id)->first();
        if ($existing) {
            $isPaid = in_array($pesanan->status, ['paid', 'selesai'], true);
            $existing->update([
                'status' => $isPaid ? 'lunas' : ($pesanan->status === 'cancelled' ? 'batal' : 'menunggu_pembayaran'),
                'paid_at' => $isPaid && ! $existing->paid_at ? Carbon::now() : $existing->paid_at,
                'metode_pembayaran' => $pesanan->metode_pembayaran ?? 'transfer',
            ]);

            return $existing;
        }

        $subtotal = $pesanan->total_price;
        $ongkir = $pesanan->ongkir ?? 10000;
        $diskonVoucher = $pesanan->discount_amount ?? 0;
        $diskonOngkir = $pesanan->diskon_ongkir ?? 0;
        $totalBayar = $pesanan->final_price ?? max(0, $subtotal + $ongkir - $diskonVoucher - $diskonOngkir);

        $datePart = Carbon::now()->format('Ymd');
        $randomPart = strtoupper(Str::random(6));
        $invoiceNumber = "INV/{$datePart}/BJBO/{$pesanan->id}{$randomPart}";
        $receiptToken = hash('sha256', "RECEIPT-{$pesanan->id}-{$pesanan->order_number}-".microtime());

        $isPaid = in_array($pesanan->status, ['paid', 'selesai'], true);

        return Invoice::create([
            'invoice_number' => $invoiceNumber,
            'pesanan_id' => $pesanan->id,
            'user_id' => $pesanan->user_id,
            'seller_id' => $pesanan->penjual_id,
            'subtotal' => $subtotal,
            'ongkir' => $ongkir,
            'diskon_voucher' => $diskonVoucher,
            'diskon_ongkir' => $diskonOngkir,
            'total_bayar' => $totalBayar,
            'metode_pembayaran' => $pesanan->metode_pembayaran ?? 'transfer',
            'status' => $isPaid ? 'lunas' : 'menunggu_pembayaran',
            'paid_at' => $isPaid ? Carbon::now() : null,
            'receipt_token' => $receiptToken,
            'catatan' => $pesanan->catatan,
        ]);
    }

    /**
     * Mengambil invoice berdasarkan nomor invoice dengan relasi lengkap (eager loading anti N+1).
     */
    public function getInvoiceByNumber(string $invoiceNumber): ?Invoice
    {
        return Invoice::with([
            'pesanan.postingan:id,name,image,harga,lokasi,kondisi',
            'user:id,name,email,phone,avatar',
            'seller:id,name,email,phone,avatar',
            'seller.store:id,user_id,nama_toko,status,lokasi_toko',
        ])->where('invoice_number', $invoiceNumber)->first();
    }

    /**
     * Mengambil invoice berdasarkan token unik struk digital.
     */
    public function getInvoiceByToken(string $receiptToken): ?Invoice
    {
        return Invoice::with([
            'pesanan.postingan:id,name,image,harga,lokasi,kondisi',
            'user:id,name,email,phone,avatar',
            'seller:id,name,email,phone,avatar',
            'seller.store:id,user_id,nama_toko,status,lokasi_toko',
        ])->where('receipt_token', $receiptToken)->first();
    }

    /**
     * Mengambil invoice untuk pesanan tertentu.
     */
    public function getInvoiceByOrderId(int $orderId): ?Invoice
    {
        return Invoice::with([
            'pesanan.postingan:id,name,image,harga,lokasi,kondisi',
            'user:id,name,email,phone,avatar',
            'seller:id,name,email,phone,avatar',
            'seller.store:id,user_id,nama_toko,status,lokasi_toko',
        ])->where('pesanan_id', $orderId)->first();
    }

    /**
     * Menandai invoice sebagai lunas.
     */
    public function markInvoiceAsPaid(int $invoiceId): bool
    {
        $invoice = Invoice::find($invoiceId);
        if (! $invoice) {
            return false;
        }

        return $invoice->update([
            'status' => 'lunas',
            'paid_at' => Carbon::now(),
        ]);
    }
}
