<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Verifikasi Akun BJBO</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F8F9FA; font-family: 'Plus Jakarta Sans', Arial, sans-serif; color: #1F2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #F8F9FA; padding: 30px 10px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 520px; background-color: #FFFFFF; border: 1px solid #E2E6EA; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #1E3A8A; padding: 24px 32px; text-align: center;">
                            <h1 style="margin: 0; font-size: 20px; font-weight: 700; color: #FFFFFF; letter-spacing: -0.5px;">BJBO Sedagho</h1>
                            <p style="margin: 4px 0 0; font-size: 12px; color: #DBEAFE;">Pasar Komunitas Belanjo & Jual Barang Online</p>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding: 32px;">
                            <h2 style="margin: 0 0 12px; font-size: 18px; font-weight: 700; color: #111827;">Halo, {{ $name }}!</h2>
                            <p style="margin: 0 0 20px; font-size: 14px; line-height: 1.6; color: #4B5563;">
                                Terima kasih telah bergabung dengan komunitas BJBO. Untuk menyelesaikan proses pendaftaran akun Anda, silakan masukkan 6 digit kode OTP verifikasi berikut:
                            </p>
                            
                            <!-- OTP Box -->
                            <div style="background-color: #FFF7ED; border: 2px dashed #C2410C; border-radius: 12px; padding: 20px; text-align: center; margin: 24px 0;">
                                <span style="font-family: 'JetBrains Mono', Courier, monospace; font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #C2410C;">
                                    {{ $otp }}
                                </span>
                            </div>

                            <p style="margin: 0 0 16px; font-size: 12px; line-height: 1.5; color: #6B7280;">
                                • Kode ini berlaku selama <strong>10 menit</strong>.<br>
                                • Permintaan kirim ulang kode memiliki interval jeda <strong>60 detik</strong> demi keamanan.<br>
                                • Jangan pernah memberitahukan kode ini kepada siapa pun termasuk pihak yang mengatasnamakan BJBO.
                            </p>

                            <p style="margin: 24px 0 0; font-size: 13px; color: #9CA3AF; border-top: 1px solid #F3F4F6; padding-top: 16px;">
                                Jika Anda tidak merasa melakukan pendaftaran di BJBO, abaikan email ini dengan aman.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #F9FAFB; padding: 16px 32px; text-align: center; border-top: 1px solid #E5E7EB;">
                            <p style="margin: 0; font-size: 11px; color: #9CA3AF;">
                                &copy; {{ date('Y') }} BJBO Sedagho - Kabupaten Bengkalis. Seluruh hak cipta dilindungi.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
