<?php

namespace App\Services;

class ContentModerationService
{
    /**
     * Memeriksa apakah konten postingan memuat barang terlarang
     * (Narkoba, Barang Ilegal/Senjata/Bahan Peledak, Perdagangan Manusia).
     * Murni fungsional dan bebas perulangan (loop-free).
     */
    public function inspect(string $name, string $category, string $description): array
    {
        $combinedText = strtolower($name.' '.$category.' '.$description);

        // 1. Pola Narkotika & Obat Terlarang
        $narkobaRegex = '/\b(sabu|shabu|ganja|ekstasi|inex|pil\s*koplo|tramadol|sinte|tembakau\s*sintetis|gorilla|heroin|kokain|morfin|psikotropika|narkoba|narkotika|obat\s*bius|alprazolam|dumolid)\b/i';
        if (preg_match($narkobaRegex, $combinedText, $matches)) {
            return [
                'is_blocked' => true,
                'category' => 'Narkotika & Zat Terlarang',
                'matched' => $matches[0],
                'reason' => 'Terdeteksi memuat zat/narkotika terlarang ('.$matches[0].') yang dilarang undang-undang.',
            ];
        }

        // 2. Pola Barang Terlarang, Senjata, Bahan Peledak, & Pemalsuan
        $barangTerlarangRegex = '/\b(senjata\s*api|senpi|pistol|senapan|amunisi|peluru|bom|bahan\s*peledak|dinamit|celurit\s*begal|sianida|organ\s*tubuh|jual\s*ginjal|satwa\s*langka|gading\s*gajah|trenggiling|uang\s*palsu|upal|ijazah\s*palsu|dokumen\s*palsu|sim\s*tembak|stnk\s*bodong|bpkb\s*bodong|barang\s*curian|curanmor|hasil\s*begal)\b/i';
        if (preg_match($barangTerlarangRegex, $combinedText, $matches)) {
            return [
                'is_blocked' => true,
                'category' => 'Barang Ilegal & Berbahaya',
                'matched' => $matches[0],
                'reason' => 'Terdeteksi memuat senjata, bahan peledak, dokumen palsu, atau barang ilegal ('.$matches[0].').',
            ];
        }

        // 3. Pola Perdagangan Manusia & Eksploitasi
        $perdaganganManusiaRegex = '/\b(perdagangan\s*manusia|human\s*trafficking|jual\s*anak|jual\s*bayi|sewa\s*rahim|jual\s*organ|prostitusi|open\s*bo|vcs|lendir|eksploitasi\s*anak|trafficking|perbudakan|jual\s*orang)\b/i';
        if (preg_match($perdaganganManusiaRegex, $combinedText, $matches)) {
            return [
                'is_blocked' => true,
                'category' => 'Perdagangan Manusia & Eksploitasi',
                'matched' => $matches[0],
                'reason' => 'Terdeteksi memuat indikasi perdagangan orang, eksploitasi, atau konten terlarang ('.$matches[0].').',
            ];
        }

        return [
            'is_blocked' => false,
            'category' => null,
            'matched' => null,
            'reason' => null,
        ];
    }
}
