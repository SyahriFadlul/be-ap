<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Barang Keluar</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 6px;
            text-align: left;
        }
        h2 {
            text-align: center;
            margin-bottom: 0;
        }
        .small {
            font-size: 12px;
            margin-top: 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Laporan Barang Keluar</h2>
    <p class="small">Periode: 
        <strong>{{ $filters['start_date'] ?? '-' }}</strong> s/d 
        <strong>{{ $filters['end_date'] ?? '-' }}</strong></p>
    <p class="small">Dicetak: {{ now()->format('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Invoice</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Qty</th>
                <th>Harga Satuan (Rp)</th>
                <th>Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($data as $row)
                @foreach($row['items'] as $item)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $row['date'] }}</td>
                        <td>{{ $row['invoice'] }}</td>
                        <td>{{ $item['goods'] }}</td>
                        <td>{{ $item['unit']['name'] }}</td>
                        <td>{{ $item['qty'] }}</td>
                        <td>{{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['unit_price'] * $item['qty'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
