<?php

namespace App\Http\Controllers;

use App\Exports\IncomingGoodsExport;
use App\Models\GoodsBatch;
use App\Models\IncomingGoods;
use App\Models\IncomingGoodsItems;
use Illuminate\Http\Request;
use App\Http\Resources\IncomingGoodsResource;
use App\Services\IncomingGoodsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class IncomingGoodsController extends Controller
{   
    public function __construct(
        protected IncomingGoodsService $service
    ) {}

    public function index(Request $request)
    {   
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        // $data = IncomingGoods::whereDate('received_date', '>=', Carbon::parse($startDate)->startOfDay())
        //     ->whereDate('received_date', '<=', Carbon::parse($endDate)->endOfDay())
        //     ->paginate(10);
        // return $data;

        $query = IncomingGoods::with([
            'supplier:id,company_name,contact_person_name',
            'items',
            'items.goods:id,name', 
            'items.batch:id,incoming_goods_item_id,batch_number,expiry_date',
            'items.unit:id,name,status',
            'createdBy:id,username'
        ]);
        if ($startDate && $endDate) {
            $query->whereDate('received_date', '>=', Carbon::parse($startDate)->startOfDay())
                ->whereDate('received_date', '<=', Carbon::parse($endDate)->endOfDay());
        }
        $data = $query->orderBy('received_date', 'desc')
            ->paginate(10);

        return IncomingGoodsResource::collection($data);
    }

    // public function getFilteredData(Request $request)
    // {
    //         'items.unit:id,name,status',
    //         'createdBy:id,username'])
    //         ->orderBy('received_date', 'desc')
    //         ->paginate(10);

    //     return IncomingGoodsResource::collection($data);
    // }

    public function getFilteredData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        return $request->all();
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
        return response()->json([
            'data' => $request->all()
        ],500);
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

    public function exportIncomingGoodsToPDF(Request $request)
    {
        $dataRequest = $request->all();

        $data = $dataRequest['data'];
        $filters = $dataRequest['filters'];
        $filters['start_date'] = Carbon::parse($filters['start_date'])->format('d-m-Y');
        $filters['end_date'] = Carbon::parse($filters['end_date'])->format('d-m-Y');

        $pdf = Pdf::loadView('pdf.incoming_goods_report', compact('data', 'filters'))
              ->setPaper('A4', 'landscape');

        return $pdf->download('laporan-barang-masuk.pdf');
    }

//     public function downloadIncomingGoods(Request $request)
// {
//     $query = IncomingGoods::with([
//         'supplier', 
//         'items.goods', 
//         'items.unit', 
//         'items.batch',
//         'createdBy'
//     ]);
//     $startDate = '2024-10-01';
//     $endDate   = '2025-08-09';

//     $query->whereDate('received_date', '>=', $startDate);
//     $query->whereDate('received_date', '<=', $endDate);

//     // Filter berdasarkan tanggal
//     if ($request->filled('start_date')) {
//         $query->whereDate('received_date', '>=', $request->start_date);
//     }

//     if ($request->filled('end_date')) {
//         $query->whereDate('received_date', '<=', $request->end_date);
//     }

//     // Sorting (opsional)
//     switch ($request->input('sort')) {
//         case 'date-asc':
//             $query->orderBy('received_date', 'asc');
//             break;
//         case 'date-desc':
//             $query->orderBy('received_date', 'desc');
//             break;
//         default:
//             $query->latest();
//     }

//     $incomingGoods = $query->get();

//     // Data tambahan untuk laporan
//     $data = [
//         'incomingGoods' => $incomingGoods,
//         'printed_by' => auth()->user()->name ?? 'Guest',
//         'date' => now()->format('d-m-Y H:i'),
//         'filters' => $request->all(),
//     ];

//     // Load PDF view
//     $pdf = Pdf::loadView('pdf.incoming_goods_report', $data)
//               ->setPaper('A4', 'landscape');

//     return $pdf->download('laporan-barang-masuk.pdf');
// }
    public function downloadIncomingGoods(Request $request)
    {
        $query = IncomingGoods::with([
            'supplier', 
            'items.goods', 
            'items.unit', 
            'items.batch',
            'createdBy'
        ]);

        // Mode testing (hardcode tanggal)
        $startDate = '2024-10-01';
        $endDate   = '2025-08-09';

        $query->whereDate('received_date', '>=', $startDate);
        $query->whereDate('received_date', '<=', $endDate);

        // Kalau mau tetap bisa pakai filter dari request
        if ($request->filled('start_date')) {
            $query->whereDate('received_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('received_date', '<=', $request->end_date);
        }

        // Sorting (opsional)
        switch ($request->input('sort')) {
            case 'date-asc':
                $query->orderBy('received_date', 'asc');
                break;
            case 'date-desc':
                $query->orderBy('received_date', 'desc');
                break;
            default:
                $query->latest();
        }

        $incomingGoods = $query->get();

        // Data tambahan untuk laporan
        $data = [
            'incomingGoods' => $incomingGoods,
            'printed_by' => auth()->user()->name ?? 'Guest',
            'date' => now()->format('d-m-Y H:i'),
            'filters' => $request->all(),
        ];

        // Load PDF view
        $pdf = Pdf::loadView('pdf.incoming_goods_report', $data)
                ->setPaper('A4', 'landscape');

        return $pdf->download('laporan-barang-masuk.pdf');
    }

    public function exportIncomingGoodsExcel(Request $request)
    {
        return Excel::download(new IncomingGoodsExport($request), 'laporan-barang-masuk.xlsx');
    }

    public function search(Request $request)
    {
        $query = $request->query('query');
        $incomingGoods = IncomingGoods::where('invoice', 'like', "%{$query}%")
            ->with(['supplier:id,company_name,contact_person_name',
                'items',
                'items.goods:id,name', 
                'items.batch:id,incoming_goods_item_id,batch_number,expiry_date',
                'items.unit:id,name,status',
                'createdBy:id,username'])
            ->orderBy('received_date', 'desc')
            ->paginate(10);
        return $incomingGoods->toResourceCollection();
    }

}
