<?php

namespace App\Http\Controllers;

use App\Http\Resources\GoodsCollection;
use App\Http\Resources\GoodsResource;
use App\Models\Goods;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
    
        return GoodsResource::collection($data);
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
            'name' => 'string|required',
            'category_id' => 'required|integer',
            'base_unit_id' => 'integer',
            'medium_unit_id' => 'integer|nullable',
            'large_unit_id' => 'integer|nullable',
            'shelf_location' => 'string|max:3|nullable',
            'conversion_medium_to_base' => 'integer|nullable',
            'conversion_large_to_medium' => 'integer|nullable',
        ]);
        return $validated;
        // $data = Goods::create($validated);
        // return response($data, 201);
    }

    public function update(Request $request, $id){
        $goods = Goods::findOrFail($id);
        $validated = $request->validate([
            'name' => 'string',
            'category_id' => 'integer',
            'base_unit_id' => 'integer',
            'medium_unit_id' => 'integer|nullable',
            'large_unit_id' => 'integer|nullable',
            'shelf_location' => 'string|max:3|nullable',
            'conversion_medium_to_base' => 'integer|nullable',
            'conversion_large_to_medium' => 'integer|nullable',
        ]);
        $goods->update($validated);
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

}
