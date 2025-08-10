<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $data = Supplier::paginate(10);

        return $data->toResourceCollection();
    }

    public function show($id){
        $supplier = Supplier::find($id);
        return response($supplier);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|unique:suppliers,company_name',
            'company_phone' => 'required|numeric|digits:20',
            'contact_person_name' => 'required',
            'contact_person_phone' => 'required|numeric|digits:20',
            'note' => 'nullable',
        ],
        [
            'company_name.required' => 'Nama Instansi wajib diisi',
            'company_name.unique' => 'Nama Instansi sudah ada',
            'company_phone.required' => 'Nomor telepon instansi wajib diisi',
            'company_phone.numeric' => 'Nomor telepon instansi harus berupa angka',
            'company_phone.digits' => 'Nomor telepon instansi harus terdiri dari 20 digit',
            'contact_person_name.required' => 'Nama kontak person wajib diisi',
            'contact_person_phone.required' => 'Nomor telepon kontak person wajib diisi',
        ]);
        // return $validated;
        $data = Supplier::create($validated);
        return response($data, 201);
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'company_name' => 'required|string|unique:suppliers,company_name,' . $supplier->id,
            'company_phone' => 'required',
            'contact_person_name' => 'required',
            'contact_person_phone' => 'required',
            'note' => 'nullable',
        ],
        [
            'name.required' => 'Nama Supplier wajib diisi',
            'name.unique' => 'Nama Supplier sudah ada',
            'company_phone.required' => 'Nomor telepon instansi wajib diisi',
            'contact_person_name.required' => 'Nama kontak person wajib diisi',
            'contact_person_phone.required' => 'Nomor telepon kontak person wajib diisi',
        ]);

        $supplier->update($validated);
        return response($supplier, 200);
    }

    public function destroy($id)
    { 
        try {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return response()->json(['message' => 'Supplier berhasil dihapus.']);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'Tidak dapat menghapus supplier karena masih digunakan pada data barang masuk.'
                ], 409); //konflik
            }

            return response()->json(['message' => 'Terjadi kesalahan saat menghapus supplier.'], 500);
        }
    }
}
