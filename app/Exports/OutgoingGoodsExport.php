<?php

namespace App\Exports;

use App\Models\OutgoingGoods;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class OutgoingGoodsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = OutgoingGoods::with(['items.goods', 'createdBy']);

        if ($this->request->filled('filters.start_date')) {
            $query->whereDate('date', '>=', $this->request->filters['start_date']);
        }

        if ($this->request->filled('filters.end_date')) {
            $query->whereDate('date', '<=', $this->request->filters['end_date']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Keluar',
            'Nomor Invoice',
            'Tipe',
            'Nama Barang',
            'Qty',
            'Satuan',
            'Harga Satuan',
            'Total',
            'Dibuat Oleh',
        ];
    }

    public function map($outgoingGoods): array
    {
        $rows = [];

        foreach ($outgoingGoods->items as $item) {
            $rows[] = [
                $outgoingGoods->date,
                $outgoingGoods->invoice,
                $outgoingGoods->type,
                $item->goods->name ?? '-',
                $item->final_qty,
                $item->unit->name ?? '-',
                $item->unit_price,
                $item->line_total,
                $outgoingGoods->createdBy->username ?? '-',
            ];
        }

        return count($rows) ? $rows[0] : [];
    }
}
