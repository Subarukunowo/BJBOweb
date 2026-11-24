<?php

namespace App\Services;

class ContentModerationService
{
    /**
     * Memeriksa apakah konten postingan memuat barang terlarang
     * (Narkoba, Barang Ilegal/Senjata/Bahan Peledak, Perdagangan Manusia).
     * Murni fungsional dan bebas perulangan (loop-free).
     *
     * @SuppressWarnings("php:S1142")
     */
    public function inspect(string $name, string $category, string $description): array
    {
        $combinedText = strtolower($name.' '.$category.' '.$description);

        if ($violation = $this->detectViolation($combinedText)) {
            return $violation;
        }

        return [
            'is_blocked' => false,
            'category' => null,
            'matched' => null,
            'reason' => null,
        ];
    }

    /**
     * Deteksi pelanggaran hukum berdasarkan pola regex.
     *
     * @SuppressWarnings("php:S1142")
     */
    protected function detectViolation(string $combinedText): ?array
    {
        $patterns = [
            'Narkotika & Zat Terlarang' => [
                'regex' => '/\b(sabu|shabu|ganja|ekstasi|inex|pil\s*koplo|tramadol|sinte|tembakau\s*sintetis|gorilla|heroin|kokain|morfin|psikotropika|narkoba|narkotika|obat\s*bius|alprazolam|dumolid)\b/i',
                'msg' => 'Terdeteksi memuat zat/narkotika terlarang (:match) yang dilarang undang-undang.',
            ],
            'Barang Ilegal & Berbahaya' => [
                'regex' => '/\b(senjata\s*api|senpi|pistol|senapan|amunisi|peluru|bom|bahan\s*peledak|dinamit|celurit\s*begal|sianida|organ\s*tubuh|jual\s*ginjal|satwa\s*langka|gading\s*gajah|trenggiling|uang\s*palsu|upal|ijazah\s*palsu|dokumen\s*palsu|sim\s*tembak|stnk\s*bodong|bpkb\s*bodong|barang\s*curian|curanmor|hasil\s*begal)\b/i',
                'msg' => 'Terdeteksi memuat senjata, bahan peledak, dokumen palsu, atau barang ilegal (:match).',
            ],
            'Perdagangan Manusia & Eksploitasi' => [
                'regex' => '/\b(perdagangan\s*manusia|human\s*trafficking|jual\s*anak|jual\s*bayi|sewa\s*rahim|jual\s*organ|prostitusi|open\s*bo|vcs|lendir|eksploitasi\s*anak|trafficking|perbudakan|jual\s*orang)\b/i',
                'msg' => 'Terdeteksi memuat indikasi perdagangan orang, eksploitasi, atau konten terlarang (:match).',
            ],
        ];

        return collect($patterns)->map(function ($config, $category) use ($combinedText) {
            if (preg_match($config['regex'], $combinedText, $matches)) {
                return [
                    'is_blocked' => true,
                    'category' => $category,
                    'matched' => $matches[0],
                    'reason' => str_replace(':match', $matches[0], $config['msg']),
                ];
            }

            return null;
        })->filter()->first();
    }
}
