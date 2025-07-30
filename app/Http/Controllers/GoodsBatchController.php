<?php

namespace App\Http\Controllers;

use App\Models\GoodsBatch;
use Illuminate\Http\Request;

class GoodsBatchController extends Controller
{
    public function index()
    {
        $data = GoodsBatch::paginate(10);

        return $data->toResourceCollection();
    }
}
