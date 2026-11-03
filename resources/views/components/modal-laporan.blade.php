<!-- Modal Laporan Pelanggaran (Pure Tailwind CSS) -->
<div id="laporanModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-bjbo-border max-w-md w-full overflow-hidden animate-fade-in">
        <div class="border-b border-bjbo-border px-6 py-4 flex items-center justify-between">
            <h5 class="font-bold text-gray-900 text-base">Laporkan Postingan</h5>
            <button type="button" class="close-laporan-modal text-gray-400 hover:text-gray-700 transition" aria-label="Tutup">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>
        <form action="{{ route('laporan.submit') }}" method="POST">
            @csrf
            <input type="hidden" name="postingan_id" id="modalPostinganId" value="">
            <div class="p-6 space-y-4">
                <div>
                    <label for="jenis_laporan" class="block text-xs font-semibold text-gray-700 mb-1">Alasan Laporan</label>
                    <select name="jenis_laporan" id="jenis_laporan" class="w-full px-3.5 py-2 text-sm border border-bjbo-border-strong rounded-lg focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" required>
                        <option value="">Pilih Alasan Pelanggaran</option>
                        <option value="Postingan Palsu">Barang Palsu / Tidak Sesuai</option>
                        <option value="Penipuan">Indikasi Penipuan / Spam</option>
                        <option value="Konten Melanggar">Konten Tidak Pantas / Dilarang</option>
                        <option value="Sudah Terjual">Barang Sudah Tidak Tersedia</option>
                    </select>
                </div>
                <div>
                    <label for="teks" class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi Laporan</label>
                    <textarea name="teks" id="teks" class="w-full px-3.5 py-2 text-sm border border-bjbo-border-strong rounded-lg focus:border-bjbo-primary focus:ring-2 focus:ring-bjbo-primary/20 focus:outline-none transition" rows="3" placeholder="Jelaskan alasan laporan Anda dengan jujur..." required></textarea>
                </div>
            </div>
            <div class="border-t border-bjbo-border px-6 py-3 flex justify-end gap-2 bg-gray-50">
                <button type="button" class="close-laporan-modal bjbo-btn-outline text-xs">Batal</button>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 font-semibold text-xs rounded-lg bg-red-600 text-white hover:bg-red-700 transition">Kirim Laporan</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('laporanModal');
        const modalInput = document.getElementById('modalPostinganId');
        const openBtns = document.querySelectorAll('.btn-report');
        const closeBtns = document.querySelectorAll('.close-laporan-modal');

        if (modal && modalInput) {
            openBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    modalInput.value = this.getAttribute('data-id');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
            });

            closeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                });
            });

            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            });
        }
    });
</script>
