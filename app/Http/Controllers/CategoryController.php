<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        $data = Category::all();
        return $data->toResourceCollection();
    }

    public function show($id){
        $category = Category::find($id);
        return response($category);
    }

    public function edit(){

    }

    public function destroy(){ #delete

    }
    
}
