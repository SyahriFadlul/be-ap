<?php

namespace App\Http\Controllers;

use App\Exports\OutgoingGoodsExport;
use App\Http\Resources\OutgoingGoodsResource;
use App\Models\Goods;
use App\Models\OutgoingGoods;
use App\Services\OutgoingGoodsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class OutgoingGoodsController extends Controller
{
    public function __construct(
        protected OutgoingGoodsService $service
    ) {}

    public function index( Request $request )
    {   
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = OutgoingGoods::with([
            'items',
            'items.goods:id,name',
            'items.batch:id,batch_number',
            'items.unit:id,name,status',
            'createdBy:id,username',
        ]);

        if ($startDate && $endDate) {
            $query->whereDate('date', '>=', Carbon::parse($startDate)->startOfDay())
                ->whereDate('date', '<=', Carbon::parse($endDate)->endOfDay());
        }
        $data = $query->orderBy('date', 'desc')
            ->paginate(10);
            
        return OutgoingGoodsResource::collection($data);
        
        // $data = OutgoingGoods::with([
        //     'items',
        //     'items.goods' => function ($query) {
        //         $query->select('id', 'name');
        //     }, 
        //     'items.batch' => function ($query) {
        //         $query->select('id', 'batch_number');
        //     },
        //     'items.unit' => function ($query) {
        //         $query->select('id', 'name', 'status');
        //     },
        //     'createdBy:id,username'])
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(10);
            
    }

    public function store(Request $request)
    {   
        // return response()->json([
        //     'data' => $request->all(),
        // ],500);
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
        $data = OutgoingGoods::findOrFail($id);
        $data->delete();
        return response(null,204);
    }

    public function exportOutgoingGoodsToPDF(Request $request)
    {
        $dataRequest = $request->all();
        
        $data = $dataRequest['data'];
        $filters = $dataRequest['filters'];
        $filters['start_date'] = Carbon::parse($filters['start_date'])->format('Y-m-d');
        $filters['end_date'] = Carbon::parse($filters['end_date'])->format('Y-m-d');

        $pdf = Pdf::loadView('pdf.outgoing_goods_report', compact('data', 'filters'))
              ->setPaper('A4', 'landscape');

        return $pdf->download('laporan-barang-keluar.pdf');
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

    public function search(Request $request)
    {
        $query = $request->query('query');
        $outgoingGoods = OutgoingGoods::where('invoice', 'like', "%{$query}%")
            ->with([
                'items',
                'items.goods:id,name',
                'items.batch:id,batch_number',
                'items.unit:id,name,status',
                'createdBy:id,username',
            ])
            ->orderBy('date', 'desc')
            ->paginate(10);

        return $outgoingGoods->toResourceCollection();
    }

    public function exportOutgoingGoodsExcel(Request $request)
    {
        return Excel::download(new OutgoingGoodsExport($request), 'laporan-barang-keluar.xlsx');
    }
}
