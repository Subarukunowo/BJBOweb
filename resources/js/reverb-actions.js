/**
 * Real-time asynchronous actions and Laravel Reverb integration for BJBOweb
 * Handles non-refresh interactive buttons: Favorit, Toggle Sold Status, and Ulasan
 */

function showToast(message, type = 'success') {
    const existingToast = document.getElementById('bjbo-live-toast');
    if (existingToast) {
        existingToast.remove();
    }

    const toast = document.createElement('div');
    toast.id = 'bjbo-live-toast';
    const bgClass = type === 'error' ? 'bg-red-600' : 'bg-gray-900';
    toast.className = `fixed bottom-20 sm:bottom-6 right-4 z-50 flex items-center gap-2 px-4 py-3 rounded-xl text-white text-xs font-semibold shadow-2xl transition-all duration-300 transform translate-y-4 opacity-0 ${bgClass}`;

    const iconClass = type === 'error' ? 'bi-exclamation-circle-fill' : 'bi-check-circle-fill text-green-400';
    toast.innerHTML = `<i class="bi ${iconClass} text-sm"></i><span>${message}</span>`;

    document.body.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
    });

    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, 2800);
}

function updateFavoriteUI(postinganId, favorited) {
    const forms = document.querySelectorAll(`form[data-favorite-form][data-postingan-id="${postinganId}"]`);
    forms.forEach((form) => {
        const heartIcons = form.querySelectorAll('i.bi-heart, i.bi-heart-fill');
        heartIcons.forEach((icon) => {
            if (favorited) {
                icon.className = 'bi bi-heart-fill text-red-500 text-sm transition-transform duration-150 scale-125';
                setTimeout(() => icon.classList.remove('scale-125'), 200);
            } else {
                icon.className = 'bi bi-heart text-gray-600 text-sm transition-transform duration-150';
            }
        });

        const textSpan = form.querySelector('.fav-text');
        if (textSpan) {
            textSpan.textContent = favorited ? 'Tersimpan di Favorit' : 'Tambah ke Favorit';
        }
    });

    if (!favorited && window.location.pathname.includes('/favorit')) {
        const card = document.querySelector(`[data-postingan-card="${postinganId}"]`);
        if (card) {
            card.classList.add('transition-all', 'duration-300', 'opacity-0', 'scale-95');
            setTimeout(() => card.remove(), 320);
        }
    }
}

function updateSoldStatusUI(postinganId, isSold) {
    const overlays = document.querySelectorAll(`[data-sold-overlay="${postinganId}"]`);
    overlays.forEach((el) => {
        if (isSold) {
            el.classList.remove('hidden');
            el.classList.add('flex');
        } else {
            el.classList.add('hidden');
            el.classList.remove('flex');
        }
    });

    const images = document.querySelectorAll(`[data-product-img="${postinganId}"]`);
    images.forEach((img) => {
        if (isSold) {
            img.classList.add('grayscale');
        } else {
            img.classList.remove('grayscale');
        }
    });

    const sellerOverlay = document.querySelector(`[data-seller-overlay="${postinganId}"]`);
    if (sellerOverlay) {
        if (isSold) {
            sellerOverlay.classList.remove('hidden');
        } else {
            sellerOverlay.classList.add('hidden');
        }
    }

    const sellerBadgeContainer = document.querySelector(`[data-seller-badge="${postinganId}"]`);
    if (sellerBadgeContainer) {
        if (isSold) {
            sellerBadgeContainer.innerHTML = '<span class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-600 bg-gray-200 px-2 py-0.5 rounded"><i class="bi bi-bag-check-fill"></i> Sudah Terjual</span>';
        } else {
            sellerBadgeContainer.innerHTML = '<span class="inline-flex items-center gap-1 text-[10px] font-bold text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded"><i class="bi bi-check-circle-fill"></i> Tayang Aktif</span>';
        }
    }

    const toggleBtn = document.querySelector(`[data-toggle-sold-btn="${postinganId}"]`);
    if (toggleBtn) {
        if (isSold) {
            toggleBtn.className = 'inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg border transition bg-green-50 text-green-700 border-green-200 hover:bg-green-100';
            toggleBtn.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i><span>Aktifkan</span>';
            toggleBtn.title = 'Kembalikan ke status aktif';
        } else {
            toggleBtn.className = 'inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg border transition bg-gray-50 text-gray-700 border-gray-300 hover:bg-gray-100';
            toggleBtn.innerHTML = '<i class="bi bi-check-all"></i><span>Tandai Terjual</span>';
            toggleBtn.title = 'Tandai sebagai sudah terjual';
        }
    }

    const sellerItem = document.querySelector(`[data-seller-item="${postinganId}"]`);
    if (sellerItem) {
        if (isSold) {
            sellerItem.classList.add('bg-gray-50/70', 'opacity-80', 'product-sold');
            sellerItem.classList.remove('bg-white', 'product-active');
        } else {
            sellerItem.classList.remove('bg-gray-50/70', 'opacity-80', 'product-sold');
            sellerItem.classList.add('bg-white', 'product-active');
        }
    }
}

function appendUlasanUI(ulasan) {
    const list = document.getElementById('ulasanReviewsList');
    const emptyState = document.getElementById('ulasanEmptyState');

    if (!list) return;

    if (emptyState) {
        emptyState.classList.add('hidden');
    }
    list.classList.remove('hidden');

    let starsHtml = '';
    const ratingNum = Number(ulasan.rating) || 5;
    for (let i = 1; i <= 5; i++) {
        if (i <= ratingNum) {
            starsHtml += '<i class="bi bi-star-fill"></i>';
        } else {
            starsHtml += '<i class="bi bi-star text-gray-300"></i>';
        }
    }

    const card = document.createElement('div');
    card.className = 'border border-bjbo-border rounded-xl p-3.5 bg-gray-50/50 transition-all duration-300 transform -translate-y-2 opacity-0';
    card.innerHTML = `
        <div class="flex items-center justify-between mb-1.5">
            <div class="flex items-center gap-2">
                <img src="${ulasan.user_avatar || ulasan.userAvatar}" alt="${ulasan.user_name || ulasan.userName}" class="w-6 h-6 rounded-full object-cover border border-bjbo-border">
                <span class="font-bold text-xs text-gray-800">${ulasan.user_name || ulasan.userName}</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="flex items-center text-xs text-amber-400 gap-0.5">
                    ${starsHtml}
                </div>
                <span class="text-[11px] font-bold text-gray-700 ml-1">${ratingNum}.0</span>
            </div>
        </div>
        <p class="text-xs text-gray-600 leading-relaxed">${ulasan.komentar}</p>
    `;

    list.prepend(card);

    requestAnimationFrame(() => {
        card.classList.remove('-translate-y-2', 'opacity-0');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('submit', async (e) => {
        const favoriteForm = e.target.closest('form[data-favorite-form]');
        if (favoriteForm) {
            e.preventDefault();
            const postinganId = favoriteForm.getAttribute('data-postingan-id');
            const submitBtn = favoriteForm.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            try {
                const formData = new FormData(favoriteForm);
                const response = await fetch(favoriteForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();
                if (data.success) {
                    updateFavoriteUI(postinganId, data.favorited);
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Gagal mengubah favorit.', 'error');
                }
            } catch (err) {
                showToast('Koneksi terputus saat memproses favorit.', 'error');
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
            return;
        }

        const soldForm = e.target.closest('form[data-toggle-sold-form]');
        if (soldForm) {
            e.preventDefault();
            const postinganId = soldForm.getAttribute('data-postingan-id');
            const submitBtn = soldForm.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            try {
                const formData = new FormData(soldForm);
                const response = await fetch(soldForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();
                if (data.success) {
                    updateSoldStatusUI(postinganId, data.is_sold);
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Gagal mengubah status produk.', 'error');
                }
            } catch (err) {
                showToast('Koneksi terputus saat mengubah status produk.', 'error');
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
            return;
        }

        const reviewForm = e.target.closest('#reviewForm');
        if (reviewForm) {
            e.preventDefault();
            const submitBtn = reviewForm.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            try {
                const formData = new FormData(reviewForm);
                const response = await fetch(reviewForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();
                if (data.success) {
                    appendUlasanUI(data.ulasan);
                    showToast(data.message, 'success');
                    const textarea = reviewForm.querySelector('textarea[name="komentar"]');
                    if (textarea) textarea.value = '';
                } else {
                    showToast(data.message || 'Gagal mengirim ulasan.', 'error');
                }
            } catch (err) {
                showToast('Koneksi terputus saat mengirim ulasan.', 'error');
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        }
    });

    if (window.Echo) {
        window.Echo.channel('postingan-favorit')
            .listen('.favorit.toggled', (event) => {
                updateFavoriteUI(event.postinganId, event.favorited);
            });

        window.Echo.channel('postingan-status')
            .listen('.status.changed', (event) => {
                updateSoldStatusUI(event.postinganId, event.isSold);
            });

        const ulasanSection = document.getElementById('ulasanSection');
        if (ulasanSection) {
            const currentPostinganId = ulasanSection.getAttribute('data-postingan-id');
            if (currentPostinganId) {
                window.Echo.channel(`postingan-ulasan.${currentPostinganId}`)
                    .listen('.ulasan.added', (event) => {
                        appendUlasanUI(event);
                    });
            }
        }
    }
});
