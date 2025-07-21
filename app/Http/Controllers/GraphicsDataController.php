<?php

namespace App\Http\Controllers;

use App\Models\Goods;
use App\Models\GoodsBatch;
use App\Models\IncomingGoods;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GraphicsDataController extends Controller
{
    public function dashboardData()
    {
        $data = [];

        $totalQty = GoodsBatch::sum('qty');
        
        $currentIncomingGoods = IncomingGoods::all();

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

        return $totalAvailableGoods;
    }
}
