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
    <p class="small">Periode: {{ $date_range }}</p>
    <p class="small">Dicetak: {{ $printed_at }}</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Invoice</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Qty</th>
                <th>Harga Satuan</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $row)
                <tr>
                    <td>{{ $row->date }}</td>
                    <td>{{ $row->invoice }}</td>
                    <td>{{ $row->goods_name }}</td>
                    <td>{{ $row->unit_name }}</td>
                    <td>{{ $row->final_qty }}</td>
                    <td>{{ number_format($row->unit_price, 0, ',', '.') }}</td>
                    <td>{{ number_format($row->line_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
