<?php

namespace App\Http\Controllers;

use App\Http\Resources\OutgoingGoodsResource;
use App\Models\Goods;
use App\Models\OutgoingGoods;
use App\Services\OutgoingGoodsService;
use Illuminate\Http\Request;

class OutgoingGoodsController extends Controller
{
    public function __construct(
        protected OutgoingGoodsService $service
    ) {}

    public function index()
    {
        $data = OutgoingGoods::with([
            'items',
            'items.goods' => function ($query) {
                $query->select('id', 'name');
            }, 
            'items.batch' => function ($query) {
                $query->select('id', 'batch_number');
            },
            'items.unit' => function ($query) {
                $query->select('id', 'name', 'status');
            },
            'createdBy:id,username'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return OutgoingGoodsResource::collection($data);
    }

    public function store(Request $request)
    {   
        return response()->json([
            'data' => $request->all(),
        ],500);
        try {
            $outgoing = $this->service->store($request->all(),auth()->user());

            return response()->json([
                'message' => 'Barang keluar berhasil disimpan',
                'data' => $outgoing
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
        // return response()->json([
        //     'data' => $request->all(),
        //     'id' => $id
        // ],500);
        try {
            $outgoing = $this->service->update($request->all(), $id, auth()->user());

            return response()->json([
                'message' => 'Barang keluar berhasil disimpan',
                'data' => $outgoing
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Gagal menyimpan',
                'error' => $e
            ], 422);
        }
    }

    public function destroy($id)
    {
        // Logic to delete an incoming goods item
    }

    public function getAvailableBatches($goodsId)
    {
        $batches = Batch::where('goods_id', $goodsId)
            ->where('stock', '>', 0)
            ->orderBy('expiry_date') // misal pakai FIFO
            ->get(['id', 'batch_number', 'expiry_date', 'stock']);

        return response()->json($batches);
    }
}
