<?php

namespace App\Http\Controllers;

use App\Http\Resources\GoodsResource;
use App\Models\Goods;
use App\Models\GoodsBatch;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GoodsController extends Controller
{
    public function index(){
        $data = Goods::with([
                'category:id,name',
                'baseUnit:id,name',
                'mediumUnit:id,name',
                'largeUnit:id,name',
                'batches:id,goods_id,qty'])
                ->paginate(10);
    
        $totalStock = GoodsBatch::sum('qty');

        $categoryDistribution = Goods::getCategoryDistribution();

        return response()->json([
            'data' => GoodsResource::collection($data),
            'meta' => [
                'total_stock' => intval($totalStock),
                'category_distribution' => $categoryDistribution,
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                ],
            ]
        ]);
    }

    public function show($id){
        $data = Goods::findOrFail($id)->with(['category:id,name',
                'baseUnit:id,name',
                'mediumUnit:id,name',
                'largeUnit:id,name',
                'batches:id,goods_id,qty'])->first();
        return $data->toResource();
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|integer',
            'base_unit_id' => 'integer',
            'medium_unit_id' => 'integer|nullable',
            'large_unit_id' => 'integer|nullable',
            'shelf_location' => 'string|max:3|nullable',
            'conversion_medium_to_base' => 'integer|nullable',
            'conversion_large_to_medium' => 'integer|nullable',
        ],[
            'name.required' => 'Nama wajib diisi',
            'category_id.required' => 'Kategori wajib dipilih',
            'base_unit_id.required' => 'Unit terkecil wajib dipilih',
        ]);
        // return $validated;
        $data = Goods::create($validated);
        return response($data, 201);
    }

    public function update(Request $request, $id){
        $goods = Goods::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|integer',
            'base_unit_id' => 'required|integer',
            'medium_unit_id' => 'integer|nullable',
            'large_unit_id' => 'integer|nullable',
            'shelf_location' => 'string|max:3|nullable',
            'conversion_medium_to_base' => 'integer|nullable',
            'conversion_large_to_medium' => 'integer|nullable',
        ]);
        $goods->update($validated);
        return $request;
        return response($goods);
    }

    public function destroy($id){
        $goods = Goods::findOrFail($id);
        $goods->delete();
        return response(null, 204);
    }

    public function download(Request $request)
    {   
        // return response($request->all());
        $query = Goods::query();

        if ($request->filled('stock_min')) {
            $query->where('stock', '>=', $request->stock_min);
        }

        if ($request->filled('stock_max')) {
            $query->where('stock', '<=', $request->stock_max);
        }

    // 💡 SORTING DINAMIS
        switch ($request->input('sort')) {
            case 'name-asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name-desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price-asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price-desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest(); // fallback
        }

        $products = $query->get();

        // Data tambahan untuk PDF
        $data = [
            'products' => $products,
            'printed_by' => auth()->user()->name ?? 'Guest',
            'date' => now()->format('d-m-Y H:i'),
            'filters' => $request->all(),
        ];

        $pdf = Pdf::loadView('pdf.report', $data);
        return $pdf->download('laporan-produk.pdf');
    }

    public function selectSuggestions(Request $request) //buat vue-select,return all id nama goods
    {   
        $search = $request->query('search');
        $data = Goods::select('id as goods_id','name as goods')
            ->where('name', 'like', '%' . $search . '%')
            ->limit(10)
            ->get()
            ->toArray();
        
        $results = array_column($data, 'name');
        
            // $result =collect()

        return $data;
    }

    public function getAvailableGoodsBatches(Request $request, $id)
    {   
        $isFifo = $request->fifo === true;
        $batches = Goods::select('id', 'name')
            ->with(['batches' => function ($q) use ($isFifo){
                // $q->select('id', 'goods_id', 'batch_number', 'expiry_date');
                $q->where('qty', '>', 0);
                $q->orderBy('expiry_date', 'asc');
                if($isFifo){
                    $q->limit(1);
                }
            }])
            ->findOrFail($id);
        
        return response($batches);
    }

    public function getGoodsUnit($id)
    {
        $units = Goods::
            select('id', 'name', 'base_unit_id', 'medium_unit_id', 'large_unit_id')
            ->findOrFail($id);

        $data = Goods::with(['baseUnit' => function ($query) {
                $query->select('id', 'name', 'status');
            }, 
            'mediumUnit' => function ($query) {
                $query->select('id', 'name', 'status');
            },
            'largeUnit' => function ($query) {
                $query->select('id', 'name', 'status');
            },
            'batches' => function ($query) {
                $query->select('id', 'goods_id', 'selling_price');
            }
            ])->select('id', 'name', 'base_unit_id', 'medium_unit_id', 'large_unit_id','conversion_medium_to_base', 'conversion_large_to_medium')
            ->findOrFail($id);
        

        return $data;
    }

    public function updatebatchestobase() //migrasi qty goods batches dari medium/strip ke base/tablet
    {
        $goodsList = Goods::with('batches')->get();

        foreach ($goodsList as $goods) {
            $conversion = $goods->conversion_medium_to_base;

            foreach ($goods->batches as $batch) {
                $batch->qty *= $conversion;
                $batch->save();
            }
        }

        return response()->json([
            'message' => 'Berhasil mengupdate qty semua batch sesuai konversi ke satuan dasar.'
        ]);
    }
    public function updatepricegoodsbatches() //migrasi harga dari medium ke base
    {
       $batches = GoodsBatch::with('goods')->get();

        foreach ($batches as $batch) {
            $goods = $batch->goods;

            if (!$goods || !$goods->conversion_medium_to_base || !$batch->purchase_price) {
                continue;
            }

            $baseConversion = $goods->conversion_medium_to_base ?: 1;


            $purchasePricePerBase = $batch->purchase_price / $baseConversion;
            $purchasePricePerBase = round($purchasePricePerBase, 2); // bulatkan ke 2 digit
            $sellingPricePerBase = $batch->selling_price / $baseConversion;
            $sellingPricePerBase = ceil($sellingPricePerBase / 100) * 100; // bulatkan ke atas

            $batch->purchase_price = $purchasePricePerBase;
            $batch->selling_price = $sellingPricePerBase;
            $batch->save();
        }

        return response()->json(['message' => 'Price Converted']);
    }

    public function expiringGoods()
    {
        $today = Carbon::now();
        $limitDate = $today->copy()->addDays(7);

        // $goods = Goods::whereHas('batches',function ($q) use($limitDate) {
        //     $q->whereDate('expiry_date', '<=', $limitDate);
        //     // }])
        //     // ->with(['batches' => function ($q){
        //         $q->select(['id','goods_id','batch_number','expiry_date']);
        //     })
        //     ->get(['id', 'name',]);
        
        $goods = Goods::whereHas('batches', function ($q) use ($limitDate) {
            $q->whereDate('expiry_date', '<=', $limitDate);
            })->with(['batches' => function ($q) use ($limitDate) {
            $q->whereDate('expiry_date', '<=', $limitDate)
            ->select(['id', 'goods_id', 'batch_number', 'expiry_date']);
            }])->get(['id', 'name']);

        return response()->json($goods);
    }

    public function lowStockGoods()
    {
        $lowStockCount = Goods::whereHas('batches', function ($q) {
            $q->select('goods_id', DB::raw('SUM(stock) / goods.conversion_medium_to_base AS stock_in_medium'))
            ->join('goods', 'goods.id', '=', 'goods_batches.goods_id')
            ->groupBy('goods_id', 'goods.conversion_medium_to_base')
            ->havingRaw('stock_in_medium < 5');
        })->count();

        return $lowStockCount;
    }

    public function search(Request $request)
    {
        $query = $request->query('query');

        $goods = Goods::where('name', 'like', "%{$query}%")
            ->with([
                'category:id,name',
                'baseUnit:id,name',
                'mediumUnit:id,name',
                'largeUnit:id,name',
                'batches:id,goods_id,qty'])
            ->paginate(10);

        return $goods->toResourceCollection();
    }
}
