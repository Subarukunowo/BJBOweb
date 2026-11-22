<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CategoryAnalyticsService
{
    /**
     * Color palette mapping for categories (Shadcn UI inspired zinc and vibrant accents).
     *
     * @var array<string, string>
     */
    protected array $categoryColors = [
        'Elektronik' => '#10b981',       // Emerald
        'Perabot' => '#3b82f6',          // Blue
        'Fashion' => '#f59e0b',          // Amber
        'Otomotif' => '#8b5cf6',         // Violet
        'Gadget' => '#06b6d4',           // Cyan
        'Rumah Tangga' => '#ec4899',     // Pink
        'Olahraga & Hobi' => '#f97316',  // Orange
        'Lainnya' => '#64748b',          // Slate
    ];

    /**
     * Get aggregated sold items and revenue by category across all 4 timeframes.
     * Cached for 60 seconds to satisfy performance and query optimization rules.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAllPeriodsData(): array
    {
        return Cache::remember('admin_category_sales_all_periods', 60, function () {
            $periods = collect([
                '1_minggu' => now()->subDays(7),
                '1_bulan' => now()->subDays(30),
                '1_tahun' => now()->subDays(365),
                'all_time' => null,
            ]);

            return $periods->map(function (?Carbon $startDate, string $periodKey) {
                return $this->getCategorySalesData($periodKey, $startDate);
            })->all();
        });
    }

    /**
     * Get aggregated sold items by category for a specific date range.
     * Pure collection pipeline: 100% loop-free.
     *
     * @return array<string, mixed>
     */
    public function getCategorySalesData(string $periodKey, ?Carbon $startDate = null): array
    {
        $query = DB::table('pesanans')
            ->join('postingans', 'pesanans.postingan_id', '=', 'postingans.id')
            ->whereNotIn('pesanans.status', ['cancelled']);

        if ($startDate) {
            $query->where('pesanans.created_at', '>=', $startDate);
        }

        $records = $query->select(
            'postingans.category',
            DB::raw('COUNT(pesanans.id) as total_sold'),
            DB::raw('SUM(COALESCE(pesanans.final_price, pesanans.total_price)) as total_revenue')
        )
            ->groupBy('postingans.category')
            ->orderByDesc('total_sold')
            ->get();

        $totalUnits = (int) $records->sum('total_sold');
        $totalRevenue = (int) $records->sum('total_revenue');

        $items = $records->map(function ($record) use ($totalUnits) {
            $categoryName = (string) ($record->category ?: 'Lainnya');
            $sold = (int) $record->total_sold;
            $revenue = (int) $record->total_revenue;
            $percentage = $totalUnits > 0 ? round(($sold / $totalUnits) * 100, 1) : 0.0;
            $color = $this->categoryColors[$categoryName] ?? '#64748b';

            return [
                'category' => $categoryName,
                'total_sold' => $sold,
                'total_revenue' => $revenue,
                'formatted_revenue' => 'Rp '.number_format($revenue, 0, ',', '.'),
                'percentage' => $percentage,
                'color' => $color,
            ];
        })->values();

        $topCategory = $items->first();

        return [
            'period' => $periodKey,
            'period_label' => $this->getPeriodLabel($periodKey),
            'total_sold' => $totalUnits,
            'total_revenue' => $totalRevenue,
            'formatted_revenue' => 'Rp '.number_format($totalRevenue, 0, ',', '.'),
            'top_category' => $topCategory ? $topCategory['category'] : 'Belum Ada',
            'top_category_sold' => $topCategory ? $topCategory['total_sold'] : 0,
            'categories' => $items->pluck('category')->all(),
            'counts' => $items->pluck('total_sold')->all(),
            'revenues' => $items->pluck('total_revenue')->all(),
            'percentages' => $items->pluck('percentage')->all(),
            'colors' => $items->pluck('color')->all(),
            'items' => $items->all(),
        ];
    }

    /**
     * Human-readable label for each period key.
     */
    protected function getPeriodLabel(string $periodKey): string
    {
        return match ($periodKey) {
            '1_minggu' => '1 Minggu Terakhir',
            '1_bulan' => '1 Bulan Terakhir',
            '1_tahun' => '1 Tahun Terakhir',
            default => 'Semua Waktu',
        };
    }
}
