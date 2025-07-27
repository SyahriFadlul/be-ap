<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
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

    public function edit()
    {

    }

    public function destroy($id)
    { 
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return response(null, 204);
    }
}
