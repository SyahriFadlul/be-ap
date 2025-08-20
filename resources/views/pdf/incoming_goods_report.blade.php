<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Barang Masuk</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #444;
            padding: 6px;
            text-align: left;
        }
        .meta {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h2>Laporan Barang Masuk</h2>

    <div class="meta">
        Dicetak oleh: <strong>Admin</strong><br>
        Tanggal cetak: <strong>{{ now()->format('d-m-Y H:i') }}</strong><br>
        @if(!empty($filters['start_date']) || !empty($filters['end_date']))
            Periode:
            <strong>{{ $filters['start_date'] ?? '-' }}</strong> s/d 
            <strong>{{ $filters['end_date'] ?? '-' }}</strong>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Terima</th>
                <th>Supplier</th>
                <th>No. Invoice</th>
                <th>Barang</th>
                <th>No. Batch</th>
                <th>Expired</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>Harga (Rp)</th>
                <th>Subtotal (Rp)</th>
                <th>Dibuat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($data as $row)
                @foreach($row['items'] as $item)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $row['received_date'] }}</td>
                        <td>{{ $row['supplier'] }}</td>
                        <td>{{ $row['invoice'] }}</td>
                        <td>{{ $item['goods'] }}</td>
                        <td>{{ $item['batch_number'] }}</td>
                        <td>{{ $item['expiry_date'] }}</td>
                        <td>{{ $item['qty'] }}</td>
                        <td>{{ $item['unit']['name'] }}</td>
                        <td>{{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['line_total'], 0, ',', '.') }}</td>
                        <td>{{ $row['created_by'] }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
