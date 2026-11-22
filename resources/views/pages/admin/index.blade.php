@extends('layouts.admin.main')

@section('title', 'Dashboard Administrator - BJBO Sedagho')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-bjbo-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Ikhtisar Dashboard</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Pemantauan data pasar, moderasi barang terlarang, dan keamanan komunitas.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.postings') }}" class="bjbo-btn-primary px-3.5 py-2 text-xs font-semibold rounded-xl inline-flex items-center gap-1.5 shadow-2xs">
                <i class="bi bi-box-seam"></i> Kelola Postingan
            </a>
            <a href="{{ route('admin.laporans') }}" class="px-3.5 py-2 text-xs font-semibold rounded-xl bg-white border border-bjbo-border hover:bg-gray-50 text-gray-700 inline-flex items-center gap-1.5 shadow-2xs">
                <i class="bi bi-shield-exclamation text-red-500"></i> Aduan Komunitas
            </a>
        </div>
    </div>

    <!-- Stat Cards Grid (6 Metrik) -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">
        <!-- Pengguna -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">Warga Terdaftar</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-bjbo-primary flex items-center justify-center">
                    <i class="bi bi-people-fill text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 font-mono">{{ $users }}</div>
            <span class="text-[11px] text-gray-400 mt-1 block">Akun aktif</span>
        </div>

        <!-- Postingan Aktif -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">Postingan Aktif</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="bi bi-check-circle-fill text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600 font-mono">{{ $activePostings }}</div>
            <span class="text-[11px] text-gray-400 mt-1 block">Tayang di pasar</span>
        </div>

        <!-- Auto-Blocked (Terlarang) -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-red-600">Auto-Blocked</span>
                <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                    <i class="bi bi-slash-circle-fill text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-red-600 font-mono">{{ $blockedPostings }}</div>
            <span class="text-[11px] text-red-500 mt-1 block">Barang terlarang</span>
        </div>

        <!-- Total Laporan Postingan -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">Laporan Postingan</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="bi bi-flag-fill text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 font-mono">{{ $laporans }}</div>
            <span class="text-[11px] text-gray-400 mt-1 block">Dari warga sedagho</span>
        </div>

        <!-- Total Laporan Chat -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">Laporan Chat</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i class="bi bi-chat-square-dots-fill text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 font-mono">{{ $chatReports }}</div>
            <span class="text-[11px] text-gray-400 mt-1 block">Aduan obrolan</span>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white border border-bjbo-border rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">Total Transaksi</span>
                <div class="w-8 h-8 rounded-lg bg-orange-50 text-bjbo-accent flex items-center justify-center">
                    <i class="bi bi-receipt text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 font-mono">{{ $transactions }}</div>
            <span class="text-[11px] text-gray-400 mt-1 block">Pesanan terdaftar</span>
        </div>
    </div>

    <!-- Shadcn UI Analytics: Diagram Batang & Lingkaran Kategori Barang Laku -->
    <div class="bg-white border border-bjbo-border rounded-2xl p-5 sm:p-6 shadow-2xs relative">
        <!-- Header Card & Toolbar Filter (Shadcn UI Segmented Tabs & Type Toggle) -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-5 border-b border-bjbo-border">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <i class="bi bi-graph-up-arrow"></i> Analisis Pasar
                    </span>
                    <span class="text-xs text-gray-400">•</span>
                    <span class="text-xs text-gray-500 font-medium">Shadcn UI Chart</span>
                </div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight flex items-center gap-2">
                    <i class="bi bi-pie-chart text-bjbo-primary"></i> Analisis Kategori Barang Terlaris
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Statistik volume barang laku dan perputaran omzet berdasarkan kategori produk di BJBO Sedagho.</p>
            </div>

            <!-- Controls: Filter Waktu (1 Minggu, 1 Bulan, 1 Tahun, Semua Waktu) & Toggle Tipe Diagram -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Toggle Tipe Diagram (Batang vs Lingkaran) -->
                <div class="inline-flex items-center bg-gray-100 p-1 rounded-xl border border-gray-200 text-xs font-semibold">
                    <button type="button" id="btnChartTypeBar" onclick="setChartType('bar')" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition bg-white text-gray-900 shadow-2xs">
                        <i class="bi bi-bar-chart-fill text-bjbo-primary"></i> Batang
                    </button>
                    <button type="button" id="btnChartTypePie" onclick="setChartType('pie')" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition text-gray-500 hover:text-gray-900">
                        <i class="bi bi-pie-chart-fill"></i> Lingkaran
                    </button>
                </div>

                <!-- Tabs Filter Rentang Waktu -->
                <div class="inline-flex items-center bg-gray-100 p-1 rounded-xl border border-gray-200 text-xs font-semibold">
                    <button type="button" class="tab-period-btn px-2.5 sm:px-3 py-1.5 rounded-lg transition bg-white text-gray-900 shadow-2xs" data-period="1_minggu" onclick="setPeriod('1_minggu')">
                        1 Minggu
                    </button>
                    <button type="button" class="tab-period-btn px-2.5 sm:px-3 py-1.5 rounded-lg transition text-gray-500 hover:text-gray-900" data-period="1_bulan" onclick="setPeriod('1_bulan')">
                        1 Bulan
                    </button>
                    <button type="button" class="tab-period-btn px-2.5 sm:px-3 py-1.5 rounded-lg transition text-gray-500 hover:text-gray-900" data-period="1_tahun" onclick="setPeriod('1_tahun')">
                        1 Tahun
                    </button>
                    <button type="button" class="tab-period-btn px-2.5 sm:px-3 py-1.5 rounded-lg transition text-gray-500 hover:text-gray-900" data-period="all_time" onclick="setPeriod('all_time')">
                        All Time
                    </button>
                </div>
            </div>
        </div>

        <!-- 3 KPI Summary Pills -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 my-4">
            <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-[11px] text-gray-500 block">Total Barang Laku</span>
                    <span id="kpiTotalSold" class="text-lg font-extrabold text-gray-900 font-mono">0 Barang</span>
                </div>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm">
                    <i class="bi bi-bag-check-fill"></i>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-[11px] text-gray-500 block">Total Omzet Transaksi</span>
                    <span id="kpiTotalRevenue" class="text-lg font-extrabold text-bjbo-accent font-mono">Rp 0</span>
                </div>
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-[11px] text-gray-500 block">Kategori Terlaris</span>
                    <span id="kpiTopCategory" class="text-lg font-extrabold text-bjbo-primary truncate block max-w-[150px]">-</span>
                </div>
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-sm">
                    <i class="bi bi-trophy-fill"></i>
                </div>
            </div>
        </div>

        <!-- Main Graphic Visualization (Grid: Chart on Left, Breakdown List on Right) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center pt-2">
            <!-- Left Area: SVG Chart Container (7 Cols) -->
            <div class="lg:col-span-7 bg-gray-50/50 border border-gray-100 rounded-xl p-4 min-h-[320px] flex flex-col justify-center relative overflow-hidden">
                <!-- Floating Tooltip Container -->
                <div id="chartTooltip" class="hidden absolute pointer-events-none z-30 bg-gray-900 text-white text-xs rounded-xl py-2 px-3 shadow-xl border border-gray-700 transform -translate-x-1/2 -translate-y-full transition-opacity duration-150">
                    <div id="tooltipTitle" class="font-bold text-emerald-400 text-[11px] mb-0.5"></div>
                    <div id="tooltipSold" class="text-gray-200 text-[11px]"></div>
                    <div id="tooltipRevenue" class="text-gray-300 text-[10px] font-mono"></div>
                </div>

                <!-- View 1: Diagram Batang (Bar Chart) -->
                <div id="chartViewBar" class="w-full flex flex-col items-center justify-center">
                    <svg id="svgBarChart" viewBox="0 0 540 260" class="w-full h-auto max-h-[280px] overflow-visible">
                        <!-- Diisi secara dinamis oleh JavaScript -->
                    </svg>
                </div>

                <!-- View 2: Diagram Lingkaran (Pie / Donut Chart) -->
                <div id="chartViewPie" class="hidden w-full flex flex-col items-center justify-center">
                    <svg id="svgPieChart" viewBox="0 0 300 300" class="w-full max-w-[280px] h-auto overflow-visible">
                        <!-- Diisi secara dinamis oleh JavaScript -->
                    </svg>
                </div>

                <!-- Empty State -->
                <div id="chartEmptyState" class="hidden py-12 text-center text-gray-400">
                    <i class="bi bi-inbox text-3xl mb-2 block"></i>
                    <p class="text-xs">Tidak ada data transaksi barang laku pada rentang waktu ini.</p>
                </div>
            </div>

            <!-- Right Area: Detailed Breakdown Table & Progress Bars (5 Cols) -->
            <div class="lg:col-span-5 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-bjbo-border">
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Distribusi Kategori</span>
                    <span id="periodBadgeInfo" class="text-[11px] font-semibold text-gray-500 font-mono">1 Minggu Terakhir</span>
                </div>

                <div id="breakdownList" class="space-y-2.5 max-h-[290px] overflow-y-auto pr-1">
                    <!-- Diisi secara dinamis oleh JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Two-Column Section: Postingan Terbaru & Laporan Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Kolom Kiri: Postingan Terbaru (8 Kolom) -->
        <div class="lg:col-span-8 bg-white border border-bjbo-border rounded-2xl p-5 shadow-2xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-bjbo-border">
                <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <i class="bi bi-box-seam text-bjbo-primary"></i> Postingan Terbaru
                </h2>
                <a href="{{ route('admin.postings') }}" class="text-xs font-semibold text-bjbo-primary hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-bjbo-border overflow-hidden">
                @forelse($recentPostings as $item)
                    <div class="py-3 flex items-center justify-between gap-3 group">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-11 h-11 rounded-xl object-cover border border-bjbo-border shrink-0">
                            <div class="min-w-0">
                                <h3 class="text-xs font-bold text-gray-900 truncate group-hover:text-bjbo-primary transition">
                                    {{ $item->name }}
                                </h3>
                                <div class="flex items-center gap-2 mt-0.5 text-[11px] text-gray-500">
                                    <span class="font-mono font-semibold text-bjbo-accent">{{ $item->formatted_price }}</span>
                                    <span>•</span>
                                    <span>Penjual: {{ $item->username ?? ($item->user->name ?? 'Warga') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if($item->is_auto_blocked)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200" title="{{ $item->block_reason }}">
                                    <i class="bi bi-slash-circle mr-1"></i> Auto-Blocked
                                </span>
                            @elseif($item->status === 'disetujui')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="bi bi-check-circle mr-1"></i> Aktif
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    Menunggu
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-6 text-center">Belum ada postingan barang.</p>
                @endforelse
            </div>
        </div>

        <!-- Kolom Kanan: Laporan Komunitas Terbaru (4 Kolom) -->
        <div class="lg:col-span-4 bg-white border border-bjbo-border rounded-2xl p-5 shadow-2xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-bjbo-border">
                <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <i class="bi bi-shield-exclamation text-red-500"></i> Laporan Terbaru
                </h2>
                <a href="{{ route('admin.laporans') }}" class="text-xs font-semibold text-bjbo-primary hover:underline">
                    Lihat Semua
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentLaporans as $lap)
                    <div class="p-3 rounded-xl bg-bjbo-surface-subtle border border-bjbo-border text-xs">
                        <div class="flex items-center justify-between gap-1 mb-1">
                            <span class="font-bold text-gray-900 truncate">{{ $lap->user->name ?? 'Warga' }}</span>
                            <span class="text-[10px] text-gray-400 font-mono">{{ $lap->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[11px] text-red-700 font-semibold mb-1">
                            <i class="bi bi-exclamation-circle-fill mr-1"></i> {{ $lap->alasan }}
                        </p>
                        @if($lap->postingan)
                            <p class="text-[11px] text-gray-500 truncate">Produk: {{ $lap->postingan->name }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-6 text-center">Tidak ada laporan pelanggaran baru.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    const categoryAnalyticsData = @json($categoryAnalyticsData);
    let currentPeriod = '1_minggu';
    let currentChartType = 'bar';

    function setPeriod(period) {
        currentPeriod = period;
        document.querySelectorAll('.tab-period-btn').forEach(btn => {
            if (btn.getAttribute('data-period') === period) {
                btn.className = 'tab-period-btn px-2.5 sm:px-3 py-1.5 rounded-lg transition bg-white text-gray-900 shadow-2xs font-semibold';
            } else {
                btn.className = 'tab-period-btn px-2.5 sm:px-3 py-1.5 rounded-lg transition text-gray-500 hover:text-gray-900 font-semibold';
            }
        });
        renderDashboardCharts();
    }

    function setChartType(type) {
        currentChartType = type;
        const btnBar = document.getElementById('btnChartTypeBar');
        const btnPie = document.getElementById('btnChartTypePie');
        const viewBar = document.getElementById('chartViewBar');
        const viewPie = document.getElementById('chartViewPie');

        if (type === 'bar') {
            btnBar.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition bg-white text-gray-900 shadow-2xs';
            btnPie.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition text-gray-500 hover:text-gray-900';
            viewBar.classList.remove('hidden');
            viewPie.classList.add('hidden');
        } else {
            btnPie.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition bg-white text-gray-900 shadow-2xs';
            btnBar.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition text-gray-500 hover:text-gray-900';
            viewPie.classList.remove('hidden');
            viewBar.classList.add('hidden');
        }
        renderDashboardCharts();
    }

    function showTooltip(event, title, sold, revenue) {
        const tooltip = document.getElementById('chartTooltip');
        const rect = event.currentTarget.getBoundingClientRect();
        const containerRect = tooltip.parentElement.getBoundingClientRect();
        
        document.getElementById('tooltipTitle').textContent = title;
        document.getElementById('tooltipSold').textContent = sold + ' barang laku';
        document.getElementById('tooltipRevenue').textContent = 'Omzet: ' + revenue;
        
        tooltip.style.left = (rect.left + rect.width / 2 - containerRect.left) + 'px';
        tooltip.style.top = (rect.top - containerRect.top - 10) + 'px';
        tooltip.classList.remove('hidden');
    }

    function hideTooltip() {
        const tooltip = document.getElementById('chartTooltip');
        if (tooltip) {
            tooltip.classList.add('hidden');
        }
    }

    function renderDashboardCharts() {
        const data = categoryAnalyticsData[currentPeriod] || {
            items: [],
            total_sold: 0,
            total_revenue: 0,
            formatted_revenue: 'Rp 0',
            top_category: 'Belum Ada',
            period_label: '1 Minggu Terakhir'
        };

        // 1. Update KPI Widgets
        document.getElementById('kpiTotalSold').textContent = data.total_sold + ' Barang';
        document.getElementById('kpiTotalRevenue').textContent = data.formatted_revenue;
        document.getElementById('kpiTopCategory').textContent = data.top_category + (data.total_sold > 0 && data.items.length > 0 ? ' (' + data.items[0].percentage + '%)' : '');
        document.getElementById('periodBadgeInfo').textContent = data.period_label;

        const emptyState = document.getElementById('chartEmptyState');
        const viewBar = document.getElementById('chartViewBar');
        const viewPie = document.getElementById('chartViewPie');

        if (!data.items || data.items.length === 0 || data.total_sold === 0) {
            emptyState.classList.remove('hidden');
            viewBar.classList.add('hidden');
            viewPie.classList.add('hidden');
            document.getElementById('breakdownList').innerHTML = '<p class="text-xs text-gray-400 py-4 text-center">Belum ada transaksi di periode ini.</p>';
            return;
        }

        emptyState.classList.add('hidden');
        if (currentChartType === 'bar') {
            viewBar.classList.remove('hidden');
            viewPie.classList.add('hidden');
        } else {
            viewPie.classList.remove('hidden');
            viewBar.classList.add('hidden');
        }

        // 2. Render Diagram Batang (SVG Bar Chart)
        const svgBar = document.getElementById('svgBarChart');
        const maxVal = Math.max(...data.items.map(i => i.total_sold), 4);
        const barHeightMax = 170;
        const baselineY = 210;
        const leftPadding = 50;
        const availableW = 540 - leftPadding - 20;
        const slotW = availableW / data.items.length;
        const barW = Math.min(Math.max(slotW * 0.55, 24), 48);

        let gridLinesSvg = '';
        const gridSteps = [0, 0.25, 0.5, 0.75, 1];
        gridSteps.forEach(step => {
            const y = baselineY - (step * barHeightMax);
            const val = Math.round(step * maxVal);
            gridLinesSvg += `
                <line x1="${leftPadding}" y1="${y}" x2="530" y2="${y}" stroke="#f3f4f6" stroke-dasharray="4,4" stroke-width="1" />
                <text x="${leftPadding - 8}" y="${y + 4}" text-anchor="end" font-size="10" font-family="monospace" fill="#9ca3af">${val}</text>
            `;
        });

        let barsSvg = '';
        data.items.forEach((item, idx) => {
            const h = (item.total_sold / maxVal) * barHeightMax;
            const x = leftPadding + (idx * slotW) + ((slotW - barW) / 2);
            const y = baselineY - h;

            barsSvg += `
                <g class="bar-group" onmouseenter="showTooltip(event, '${item.category}', '${item.total_sold}', '${item.formatted_revenue}')" onmouseleave="hideTooltip()">
                    <rect x="${x}" y="${y}" width="${barW}" height="${h}" rx="6" fill="${item.color}" class="transition-all duration-200 cursor-pointer hover:opacity-85" opacity="0.95"></rect>
                    <text x="${x + barW / 2}" y="${y - 6}" text-anchor="middle" font-size="11" font-weight="700" fill="#374151" font-family="monospace">${item.total_sold}</text>
                    <text x="${x + barW / 2}" y="${baselineY + 18}" text-anchor="middle" font-size="10" font-weight="600" fill="#6b7280">${item.category}</text>
                </g>
            `;
        });

        svgBar.innerHTML = gridLinesSvg + barsSvg;

        // 3. Render Diagram Lingkaran (SVG Donut Chart)
        const svgPie = document.getElementById('svgPieChart');
        const circumference = 596.9026; // 2 * PI * 95
        let cumulativeOffset = 0;
        let pieSlicesSvg = '';

        data.items.forEach(item => {
            const dash = (item.percentage / 100) * circumference;
            const gap = circumference - dash;
            const offset = cumulativeOffset;

            pieSlicesSvg += `
                <circle cx="150" cy="150" r="95" fill="none" stroke="${item.color}" stroke-width="36"
                    stroke-dasharray="${dash} ${gap}" stroke-dashoffset="${-offset}"
                    transform="rotate(-90 150 150)" class="transition-all duration-300 hover:opacity-80 cursor-pointer"
                    onmouseenter="showTooltip(event, '${item.category}', '${item.total_sold} (${item.percentage}%)', '${item.formatted_revenue}')"
                    onmouseleave="hideTooltip()"></circle>
            `;
            cumulativeOffset += dash;
        });

        const centerTextSvg = `
            <circle cx="150" cy="150" r="74" fill="#ffffff" />
            <text x="150" y="138" text-anchor="middle" font-size="11" font-weight="600" fill="#9ca3af">Total Laku</text>
            <text x="150" y="164" text-anchor="middle" font-size="24" font-weight="800" font-family="monospace" fill="#111827">${data.total_sold}</text>
            <text x="150" y="180" text-anchor="middle" font-size="10" font-weight="500" fill="#6b7280">Unit Barang</text>
        `;

        svgPie.innerHTML = pieSlicesSvg + centerTextSvg;

        // 4. Render Breakdown Table & Progress Bars (Shadcn style)
        const breakdownContainer = document.getElementById('breakdownList');
        let breakdownHtml = '';
        data.items.forEach(item => {
            breakdownHtml += `
                <div class="p-2.5 rounded-xl bg-gray-50/80 border border-gray-100 hover:bg-gray-100/70 transition space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: ${item.color}"></span>
                            <span class="font-bold text-gray-800">${item.category}</span>
                            <span class="text-[10px] font-semibold text-gray-400 font-mono">(${item.total_sold} terjual)</span>
                        </div>
                        <div class="flex items-center gap-1.5 font-mono">
                            <span class="font-bold text-gray-900">${item.formatted_revenue}</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-white text-gray-600 border border-gray-200">${item.percentage}%</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200/80 rounded-full h-1.5 overflow-hidden">
                        <div class="h-1.5 rounded-full transition-all duration-500" style="width: ${item.percentage}%; background-color: ${item.color}"></div>
                    </div>
                </div>
            `;
        });
        breakdownContainer.innerHTML = breakdownHtml;
    }

    // Jalankan render awal saat DOM siap
    document.addEventListener('DOMContentLoaded', function() {
        renderDashboardCharts();
    });
</script>
@endsection
