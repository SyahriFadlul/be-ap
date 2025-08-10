<?php

namespace App\Http\Controllers;

use App\Http\Resources\OutgoingGoodsResource;
use App\Models\Goods;
use App\Models\OutgoingGoods;
use App\Services\OutgoingGoodsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan',
                'error' => $e->getMessage()
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

    public function download(Request $request)
    {
        // Ambil tanggal awal & akhir dari request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Query join outgoing_goods & outgoing_goods_items
        $items = DB::table('outgoing_goods_items as i')
            ->join('outgoing_goods as g', 'g.id', '=', 'i.outgoing_goods_id')
            ->join('goods as p', 'p.id', '=', 'i.goods_id')
            ->join('units as u', 'u.id', '=', 'i.unit_id')
            ->whereBetween('g.date', [$startDate, $endDate])
            ->select(
                'g.date',
                'g.invoice',
                'p.name as goods_name',
                'u.name as unit_name',
                'i.initial_qty',
                'i.final_qty',
                'i.unit_price',
                'i.line_total'
            )
            ->orderBy('g.date', 'asc')
            ->get();

        // Data untuk PDF
        $data = [
            'items' => $items,
            'date_range' => "$startDate s/d $endDate",
            'printed_at' => now()->format('d-m-Y H:i'),
        ];

        $pdf = Pdf::loadView('pdf.outgoing_goods_report', $data);
        return $pdf->download('laporan-barang-keluar.pdf');
    }
}
