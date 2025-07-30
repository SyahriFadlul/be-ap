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

        $totalQty = GoodsBatch::sum('qty');

        $totalcurrentIncomingGoods = IncomingGoods::where('received_date', '>=', now()->subMonths(1)->startOfMonth())->count(); // menghitung jumlah barang masuk dalam 1 bulan terakhir
        $percentageDifferenceIncomingFromLastMonth = IncomingGoods::where('received_date', '>=', now()->subMonths(2)->startOfMonth())
            ->where('received_date', '<', now()->subMonths(1)->startOfMonth())
            ->count();
        $totalcurrentOut = OutgoingGoods::where('date', '>=', now()->subMonths(1)->startOfMonth())->count();
        $percentageDifferenceOutgoingFromLastMonth = OutgoingGoods::where('date', '>=', now()->subMonths(2)->startOfMonth())
            ->where('date', '<', now()->subMonths(1)->startOfMonth())
            ->count();

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

        $supplierIncomingGoods = IncomingGoods::selectRaw('suppliers.name as supplier, COUNT(incoming_goods.id) as total')
            ->join('suppliers', 'incoming_goods.supplier_id', '=', 'suppliers.id')
            ->groupBy('suppliers.name')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();
        return $supplierIncomingGoods;
        

        $currentIncomingGoods = IncomingGoods::all();
        $currentOutgoingGoods = OutgoingGoods::all();

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

        return $data;

        $totalGoods = Goods::count();

        $totalAvailableGoods = Goods::whereHas('batches', fn ($q) => $q->where('qty', '>', 0))->count();

        $totalAvailableGoods = Goods::has('batches')->count();

        return response()->json([
            'total_qty' => $totalQty,
            'total_current_incoming_goods' => $totalcurrentIncomingGoods,
            'current_incoming_goods' => $currentIncomingGoods->count(),
            'incoming_goods_last_6_months' => $data,
            'total_goods' => $totalGoods,
            'total_available_goods' => $totalAvailableGoods,
        ], 200);
    }
}
