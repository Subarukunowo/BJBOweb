<?php

namespace App\Services;

class EmailSecurityService
{
    /**
     * Daftar domain email sementara (disposable / dummy email) populer.
     */
    protected const DISPOSABLE_DOMAINS = [
        'mailinator.com', 'tempmail.com', 'guerrillamail.com', '10minutemail.com',
        'throwawaymail.com', 'yopmail.com', 'sharklasers.com', 'getnada.com',
        'dispostable.com', 'trashmail.com', 'fakeinbox.com', 'mohmal.com',
        'maildrop.cc', 'mytemp.email', 'inboxkitten.com', 'guerrillamailblock.com',
        'crazymailing.com', 'temp-mail.org', 'fakemail.net', 'emailondeck.com',
        'generator.email', 'tempail.com', 'nada.ltd', 'trashmail.net',
        'burnermail.io', 'dropmail.me', 'fakemailgenerator.com', 'emailfake.com',
        'tmailor.com', 'armyspy.com', 'cuvox.de', 'dayrep.com', 'einrot.com',
        'fleckens.hu', 'gustr.com', 'jourrapide.com', 'rhyta.com', 'superrito.com',
        'teleworm.us', 'example.com', 'test.com', 'dummy.com', 'fake.com',
        'sample.com', 'localhost.com', 'asdf.com', 'invalid.com', 'tempmail.net',
        'tmpmail.net', 'tmpmail.org', 'trashmail.de', 'mailcatch.com',
        'trashmail.org', 'disposablemail.com', 'spam4.me', 'bupkis.org',
    ];

    /**
     * Memeriksa apakah email merupakan email dummy atau disposable.
     * Menggunakan paradigma fungsional murni tanpa looping.
     */
    public function isDummyEmail(string $email): bool
    {
        $cleanEmail = strtolower(trim($email));
        $atPos = strrpos($cleanEmail, '@');
        if ($atPos === false) {
            return true;
        }

        $domain = substr($cleanEmail, $atPos + 1);

        // 1. Cek exact match daftar blacklist domain disposable
        if (in_array($domain, self::DISPOSABLE_DOMAINS, true)) {
            return true;
        }

        // 2. Cek keyword mencurigakan pada nama domain (e.g. temp, throwaway, fake, trash)
        if (preg_match('/(tempmail|dispos|throwaway|fakemail|trashmail|burnermail|guerrilla|10minute)/i', $domain)) {
            return true;
        }

        // 3. Validasi DNS MX record jika koneksi internet tersedia
        if (function_exists('checkdnsrr') && ! in_array($domain, ['localhost', '127.0.0.1'], true)) {
            $hasMx = @checkdnsrr($domain, 'MX');
            $hasA = @checkdnsrr($domain, 'A');
            if (! $hasMx && ! $hasA) {
                return true;
            }
        }

        return false;
    }
}
