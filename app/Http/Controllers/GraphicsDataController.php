<?php

namespace App\Http\Controllers;

use App\Models\Goods;
use App\Models\GoodsBatch;
use App\Models\IncomingGoods;
use App\Models\OutgoingGoods;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GraphicsDataController extends Controller
{
    public function dashboardData()
    {
        $data = [];

        $outOfStockGoods = Goods::whereDoesntHave('batches', function ($query) {
            $query->where('qty', '>', 0);
        })->get();

        $goodsBelowMinimum = Goods::whereHas('batches', function ($query) {
            $query->where('qty', '<=', 5);
        })->get();

        $expiringSoonCount = GoodsBatch::whereBetween('expiry_date', [now(), now()->addDays(30)])->count();

        $totalQty = GoodsBatch::sum('qty');

        $totalcurrentIncomingGoods = IncomingGoods::where('received_date', '>=', now()->subMonths(1)->startOfMonth())->count(); // menghitung jumlah barang masuk dalam 1 bulan terakhir
        
        $totalcurrentOut = OutgoingGoods::where('date', '>=', now()->subMonths(1)->startOfMonth())->count();
        
        $last6MonthsIncomingGoods = IncomingGoods::where('received_date', '>=', now()->subMonths(6)->startOfMonth())
            ->orderBy('received_date', 'asc')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->received_date)->format('Y-m'); // grouping by months
            });
        $last6MonthsOutgoingGoods = OutgoingGoods::where('date', '>=', now()->subMonths(6)->startOfMonth())
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->date)->format('Y-m'); // grouping by months
            });

        $supplierStats = IncomingGoods::select('suppliers.company_name', DB::raw('COUNT(incoming_goods_items.id) as total_items'))
            ->join('incoming_goods_items', 'incoming_goods.id', '=', 'incoming_goods_items.incoming_goods_id')
            ->join('suppliers', 'incoming_goods.supplier_id', '=', 'suppliers.id')
            // ->whereMonth('incoming_goods.received_date', now()->month)
            // ->whereYear('incoming_goods.received_date', now()->year)
            ->groupBy('suppliers.company_name')
            ->get();

        $legend = $supplierStats->pluck('company_name');
        $series = $supplierStats->map(function ($row) {
                    return [
                        'value' => $row->total_items,
                        'name'  => $row->company_name
                    ];
                });

        $startOfYear = now()->startOfYear(); // 1 Januari tahun ini
        $endOfYear = now()->endOfYear(); // 31 Desember tahun ini

        // Buat list bulan Januari - Desember
        $months = collect(range(0, 11))->map(function ($i) use ($startOfYear) {
            return $startOfYear->copy()->addMonths($i)->format('Y-m');
        });

        // Barang masuk per bulan tahun ini
        $incoming = DB::table('incoming_goods')
            ->selectRaw("DATE_FORMAT(received_date, '%Y-%m') as period, COUNT(*) as total")
            ->whereBetween('received_date', [$startOfYear, $endOfYear])
            ->groupBy('period')
            ->pluck('total', 'period');

        // Barang keluar per bulan tahun ini
        $outgoing = DB::table('outgoing_goods')
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as period, COUNT(*) as total")
            ->whereBetween('date', [$startOfYear, $endOfYear])
            ->groupBy('period')
            ->pluck('total', 'period');

        // Label bulan dan data
        $labels = $months->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->format('M'));
        $incomingData = $months->map(fn($m) => $incoming[$m] ?? 0);
        $outgoingData = $months->map(fn($m) => $outgoing[$m] ?? 0);
        
        
        $currentIncomingGoodsCount = IncomingGoods::whereMonth('received_date', now()->month)
            ->whereYear('received_date', now()->year)
            ->withCount('items')
            ->pluck('items_count')
            ->sum();
        
        $currentOutgoingGoodsCount = OutgoingGoods::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->withCount('items')
            ->pluck('items_count')
            ->sum();

        // Hitung total item bulan lalu
        $lastMonthIncomingItems = IncomingGoods::whereMonth('received_date', now()->subMonth()->month)
            ->whereYear('received_date', now()->subMonth()->year)
            ->withCount('items')
            ->pluck('items_count')
            ->sum();

        $lastMonthOutgoingItems = OutgoingGoods::whereMonth('date', now()->subMonth()->month)
            ->whereYear('date', now()->subMonth()->year)
            ->withCount('items')
            ->pluck('items_count')
            ->sum();

        // Hitung persentase perubahan
        $percentageIncomingChange = $lastMonthIncomingItems > 0
            ? round((($currentIncomingGoodsCount - $lastMonthIncomingItems) / $lastMonthIncomingItems) * 100, 2)
            : 0;

        $percentageOutgoingChange = $lastMonthOutgoingItems > 0
            ? round((($currentOutgoingGoodsCount - $lastMonthOutgoingItems) / $lastMonthOutgoingItems) * 100, 2)
            : 0;
        
        $months = collect(range(0, 5))->map(function ($i) {
            return now()->subMonths($i)->format('Y-m');
            })->reverse();
        
        $incoming = DB::table('incoming_goods')
            ->selectRaw("DATE_FORMAT(received_date, '%Y-%m') as period, COUNT(*) as total")
            ->where('received_date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('period')
            ->pluck('total', 'period');

        $data = $months->map(function ($month) use ($incoming) {
            return [
                'month' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                'incoming' => $incoming[$month] ?? 0,
            ];
        });

        // return $data;

        $totalGoods = Goods::count();

        $totalAvailableGoods = Goods::whereHas('batches', fn ($q) => $q->where('qty', '>', 0))->count();

        $totalAvailableGoods = Goods::has('batches')->count();

        return response()->json([
            'total_qty' => $totalQty,
            'total_current_incoming_goods' => $totalcurrentIncomingGoods,
            'current_incoming_goods' => $currentIncomingGoodsCount,
            'current_outgoing_goods' => $currentOutgoingGoodsCount,
            'incoming_goods_last_6_months' => $data,
            'total_goods' => $totalGoods,
            'total_available_goods' => $totalAvailableGoods,
            'bar_graph_data' => [
                'labels' => $labels,
                'incoming' => $incomingData,
                'outgoing' => $outgoingData
            ],
            'percentage_change' => [
                'incoming' => $percentageIncomingChange,
                'outgoing' => $percentageOutgoingChange
            ],
            'supplier_stats' => [
                'legend' => $legend,
                'series' => $series,                
            ],
            'expiring_soon_count' => $expiringSoonCount,
            'goods_below_min_stock' => $goodsBelowMinimum
        ], 200);
    }
}
