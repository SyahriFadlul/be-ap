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
            'name' => 'required|string|unique:suppliers,name',
            'contact' => 'nullable',
            'note' => 'nullable',
        ],
        [
            'name.required' => 'Nama Supplier wajib diisi',
            'name.unique' => 'Nama Supplier sudah ada',
        ]);
        // return $validated;
        $data = Supplier::create($validated);
        return response($data, 201);
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|unique:suppliers,name,' . $supplier->id,
            'contact' => 'nullable',
            'note' => 'nullable',
        ],
        [
            'name.required' => 'Nama Supplier wajib diisi',
            'name.unique' => 'Nama Supplier sudah ada',
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
