<?php
namespace App\Imports;

use App\Models\Goods;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ObatImport implements OnEachRow, WithHeadingRow
{
    protected $kataPertamaDipakai = [];
    protected $jumlahDiambil = 0;
    protected $maksimal = 30;
    protected $limit = 0;

    public function onRow(Row $row)
    {
        if ($this->jumlahDiambil >= $this->maksimal) {
            return;
        }

        $data = $row->toArray();

        // Ambil data dasar
        $name = trim($data['nama_obat'] ?? '');
        $unit = strtolower(trim($data['satuan'] ?? ''));
        $stock = (int) ($data['stok'] ?? 0);
        $shelf = $data['letak_penyimpanan_obatalkes'] ?? '';
        $expDateRaw = $data['exp_date'] ?? '';
        
        // Cek syarat
        $unitValid = ['strip'];
        if (!in_array($unit, $unitValid)) {
            return;
        }
        //  elseif ($unit === 'strip') {         
        //     $this->limit++;
        //     if ($this->limit > 10) {
        //         return;
        //     }
        // }
        switch ($unit) {
            case 'pcs':
                $unit = 1; //base        
                $unit2 = null; //medium
                $unit3 = null; //large
                break;
            case 'botol':
                $unit = 2;
                $unit2 = null;
                $unit3 = null;
                break;
            case 'tube':
                $unit = 5;
                $unit2 = null;
                $unit3 = null;
                break;
            case 'strip': //jadiin tablet
                $unit = 7;
                $unit2 = 4;
                $unit3 = 8;
                break;
        }

        if ($stock < 4) {
            return;
        }
        
        if ($shelf === '') {
            return;
        }
        
        if ($expDateRaw === '') {
            return;
        }
        // Cek format tanggal
        $expDate = null;
        if(is_numeric($expDateRaw)){
            try {
                $expDate = Date::excelToDateTimeObject($expDateRaw);
                $expDate->setDate('2029', $expDate->format('m'), $expDate->format('d'));
                $expDate = Date::excelToDateTimeObject($expDateRaw);
                if($expDate){
                    $expDate->setDate('2029', $expDate->format('m'), $expDate->format('d'));
                } else {
                    return; // Format tanggal tidak valid
                }
            } catch (\Exception $e) {
                return;
            }
        } else {
            return;
        }

        // Cek kata pertama nama (case-insensitive)
        $kataPertama = strtolower(strtok($name, ' '));
        if (isset($this->kataPertamaDipakai[$kataPertama])) {
            return;
        }

        // Simpan data ke DB
        Goods::create([
            'name' => $name,
            // 'kategori' => $data['kategori'] ?? null,
            // 'stock' => $stock,
            // 'price' => $data['harga_jual'] ?? 0,
            'base_unit_id' => $unit,
            'medium_unit_id' => $unit2,
            'large_unit_id' => $unit3,
            'shelf_location' => $data['letak_penyimpanan_obatalkes'],
            // 'expiry_date' => $expDate->format('Y-m-d'),
        ]);

        $this->kataPertamaDipakai[$kataPertama] = true;
        $this->jumlahDiambil++;
    }
}
