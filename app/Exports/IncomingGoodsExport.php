<?php

namespace App\Exports;

use App\Models\IncomingGoods;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class IncomingGoodsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = IncomingGoods::with(['supplier', 'items.goods', 'createdBy']);

        if ($this->request->filled('filters.start_date')) {
            $query->whereDate('received_date', '>=', $this->request->filters['start_date']);
        }

        if ($this->request->filled('filters.end_date')) {
            $query->whereDate('received_date', '<=', $this->request->filters['end_date']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Terima',
            'Nomor Invoice',
            'Supplier',
            'Nama Barang',
            'Qty',
            'Harga Satuan',
            'Total',
            'Dibuat Oleh',
        ];
    }

    public function map($incomingGoods): array
    {
        $rows = [];

        foreach ($incomingGoods->items as $item) {
            $rows[] = [
                $incomingGoods->received_date,
                $incomingGoods->invoice,
                $incomingGoods->supplier->company_name ?? '-',
                $item->goods->name ?? '-',
                $item->final_qty,
                $item->unit_price,
                $item->line_total,
                $incomingGoods->createdBy->username ?? '-',
            ];
        }

        // Maatwebsite/Excel expects a flat array per row, 
        // jadi kita return row pertama saja (atau diubah ke FromArray untuk multi-row)
        return count($rows) ? $rows[0] : [];
    }
}
