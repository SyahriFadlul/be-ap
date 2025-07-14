<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Produk</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        h1, h3 { margin: 0; padding: 0; }
    </style>
</head>
<body>
    <h1>Laporan Produk</h1>
    <p><strong>Dicetak oleh:</strong> {{ $printed_by }}</p>
    <p><strong>Tanggal:</strong> {{ $date }}</p>
    <p><strong>Filter Kategori:</strong> {{ $category ?? 'Semua' }}</p>

    <table>
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->category->name ?? '-' }}</td>
                    <td>{{ $p->stock }}</td>
                    <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
