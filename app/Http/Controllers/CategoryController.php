<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        $data = Category::paginate(10);
        return $data->toResourceCollection();
    }

    public function show($id){
        $category = Category::find($id);
        return response($category);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:categories,name',
            'note' => 'nullable'
        ],
        [
            'name.required' => 'Nama Kategori wajib diisi',
            'name.unique' => 'Nama Kategori sudah ada',
        ]);
        // return $validated;
        $data = Category::create($validated);
        return response($data, 201);
    }

    public function edit()
    {

    }

    public function destroy($id)
    { 
        $category = Category::findOrFail($id);
        $category->delete();
        return response(null, 204);
    }
    
}
