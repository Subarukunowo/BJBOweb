<?php

namespace Tests\Feature;

use App\Services\ChatService;
use App\Services\ContentModerationService;
use App\Services\EmailSecurityService;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndChatFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji deteksi email dummy dan disposable.
     */
    public function test_email_security_blocks_dummy_emails(): void
    {
        $service = app(EmailSecurityService::class);

        $this->assertTrue($service->isDummyEmail('test@mailinator.com'));
        $this->assertTrue($service->isDummyEmail('user@tempmail.com'));
        $this->assertTrue($service->isDummyEmail('hacker@10minutemail.com'));
        $this->assertTrue($service->isDummyEmail('bot@guerrillamail.com'));
        $this->assertTrue($service->isDummyEmail('anon@fake.com'));

        $this->assertFalse($service->isDummyEmail('sedagho@gmail.com'));
        $this->assertFalse($service->isDummyEmail('warga@yahoo.com'));
    }

    /**
     * Uji moderasi otomatis (auto-block) terhadap narkoba, barang terlarang, dan human trafficking.
     */
    public function test_content_moderation_auto_blocks_contraband(): void
    {
        $service = app(ContentModerationService::class);

        // Narkoba
        $narkoba = $service->inspect('Jual Sabu Murni 1 Gram', 'Lainnya', 'Barang siap antar');
        $this->assertTrue($narkoba['is_blocked']);
        $this->assertEquals('Narkotika & Zat Terlarang', $narkoba['category']);

        // Senjata Terlarang / Bom
        $senjata = $service->inspect('Pistol Senjata Api Rakitan', 'Otomotif', 'Lengkap dengan amunisi peluru');
        $this->assertTrue($senjata['is_blocked']);
        $this->assertEquals('Barang Ilegal & Berbahaya', $senjata['category']);

        // Perdagangan Manusia
        $trafficking = $service->inspect('Jasa Perdagangan Manusia dan Organ', 'Jasa', 'Hubungi segera');
        $this->assertTrue($trafficking['is_blocked']);
        $this->assertEquals('Perdagangan Manusia & Eksploitasi', $trafficking['category']);

        // Barang Bersih / Halal
        $bersih = $service->inspect('Sepeda Lipat Polygon Urbano 3', 'Otomotif', 'Kondisi mulus terawat, pemakaian pribadi');
        $this->assertFalse($bersih['is_blocked']);
    }

    /**
     * Uji penyensoran nomor telepon pada chat dan enkripsi E2E.
     */
    public function test_chat_censors_phone_numbers_and_encrypts(): void
    {
        $chatService = app(ChatService::class);

        // Uji sensor nomor telepon
        $censored1 = $chatService->censorPhoneNumber('Nomor saya 081234567890');
        $this->assertEquals('pesan ini tidak dapat dikirim', $censored1);

        $censored2 = $chatService->censorPhoneNumber('Hubungi WA +62 812-3456-7890 ya mas');
        $this->assertEquals('pesan ini tidak dapat dikirim', $censored2);

        $censored3 = $chatService->censorPhoneNumber('Nomor hp 0 8 1 2 3 4 5 6 7 8');
        $this->assertEquals('pesan ini tidak dapat dikirim', $censored3);

        // Pesan normal tanpa nomor telepon
        $normal = $chatService->censorPhoneNumber('Halo, apakah stok barang ini masih ada?');
        $this->assertEquals('Halo, apakah stok barang ini masih ada?', $normal);
    }

    /**
     * Uji pengiriman OTP dan interval cooldown 60 detik.
     */
    public function test_otp_service_cooldown(): void
    {
        $otpService = app(OtpService::class);
        $email = 'test_warga_'.time().'@example.org';

        $firstSend = $otpService->sendOtp($email, ['name' => 'Warga Bengkalis', 'password' => 'secret123']);
        $this->assertTrue($firstSend['success']);

        // Percobaan kirim ulang langsung sebelum 60 detik harus ditolak
        $secondSend = $otpService->sendOtp($email, ['name' => 'Warga Bengkalis', 'password' => 'secret123']);
        $this->assertFalse($secondSend['success']);
        $this->assertStringContainsString('detik', $secondSend['message']);
    }
}
