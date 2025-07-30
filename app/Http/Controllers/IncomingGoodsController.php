<?php

namespace App\Http\Controllers;

use App\Models\GoodsBatch;
use App\Models\IncomingGoods;
use App\Models\IncomingGoodsItems;
use Illuminate\Http\Request;
use App\Http\Resources\IncomingGoodsResource;
use App\Services\IncomingGoodsService;

class IncomingGoodsController extends Controller
{   
    public function __construct(
        protected IncomingGoodsService $service
    ) {}

    public function index()
    {
        $data = IncomingGoods::with([
            'supplier:id,name',
            'items',
            'items.goods' => function ($query) {
                $query->select('id', 'name');
            }, 
            'items.batch' => function ($query) {
                $query->select('id', 'incoming_goods_item_id', 'batch_number', 'expiry_date');
            },
            'items.unit' => function ($query) {
                $query->select('id', 'name');
            },
            'createdBy:id,username'])
            ->orderBy('received_date', 'desc')
            ->paginate(10);

        return IncomingGoodsResource::collection($data);
    }

    public function store(Request $request)
    {   
        // return response(auth()->user()->id,500);
        try {
            $incoming = $this->service->store($request->all(),auth()->user());

            return response()->json([
                'message' => 'Barang masuk berhasil disimpan',
                'data' => $incoming
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function show($id)
    {
        // Logic to show details of a specific incoming goods item
    }

    public function update(Request $request, $id)
    {
        try {
            $incoming = $this->service->update($request->all(), $id, auth()->user());

            return response()->json([
                'message' => 'Barang masuk berhasil dirubah',
                'data' => $incoming
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal merubah',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy($id)
    {
        $data = IncomingGoods::findOrFail($id);
        $data->delete();
        return response(null,204);
    }
}
