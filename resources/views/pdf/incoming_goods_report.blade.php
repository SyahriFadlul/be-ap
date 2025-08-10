<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Barang Masuk</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .meta {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Laporan Barang Masuk</h2>
</div>

<div class="meta">
    Dicetak oleh: <strong>Admin</strong><br>
    Tanggal cetak: <strong>{{ $date }}</strong><br>
    @if(!empty($filters['start_date']) || !empty($filters['end_date']))
        Periode:
        <strong>{{ $filters['start_date'] ?? '-' }}</strong> s/d 
        <strong>{{ $filters['end_date'] ?? '-' }}</strong>
    @endif
</div>

<table>
    <thead>
        <tr>
            <th>Tanggal Terima</th>
            <th>No. Invoice</th>
            <th>Supplier</th>
            <th>Nama Barang</th>
            <th>Qty</th>
            <th>Harga Satuan</th>
            <th>Total</th>
            <th>Dibuat Oleh</th>
        </tr>
    </thead>
    <tbody>
        @forelse($incomingGoods as $goods)
            @foreach($goods->items as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($goods->received_date)->format('d-m-Y') }}</td>
                    <td>{{ $goods->invoice }}</td>
                    <td>{{ $goods->supplier->company_name ?? '-' }}</td>
                    <td>{{ $item->goods->name ?? '-' }}</td>
                    <td>{{ $item->final_qty }}</td>
                    <td>{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->line_total, 0, ',', '.') }}</td>
                    <td>{{ $goods->createdBy->name ?? '-' }}</td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="8" style="text-align: center;">Tidak ada data</td>
            </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
