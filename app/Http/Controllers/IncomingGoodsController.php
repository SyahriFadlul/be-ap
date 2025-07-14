<?php

namespace App\Http\Controllers;

use App\Models\GoodsBatch;
use App\Models\IncomingGoods;
use App\Models\IncomingGoodsItems;
use Illuminate\Http\Request;
use App\Http\Resources\IncomingGoodsResource;

class IncomingGoodsController extends Controller
{
    public function index()
    {
        $data = IncomingGoods::with([
            'supplier:id,name',
            'items',
            'items.goods' => function ($query) {
                $query->select('id', 'name');
            }, 
            'items.batch' => function ($query) {
                $query->select('id', 'incoming_goods_item_id', 'batch_number');
            },
            'items.unit' => function ($query) {
                $query->select('id', 'name');
            },
            'createdBy:id,username'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return IncomingGoodsResource::collection($data);
    }

    public function create()
    {
        // Logic to show form for creating new incoming goods
    }

    public function store(Request $request)
    {
        // Logic to store new incoming goods data
        // Validate and save the incoming goods data
    }

    public function show($id)
    {
        // Logic to show details of a specific incoming goods item
    }

    public function edit($id)
    {
        // Logic to show form for editing an existing incoming goods item
    }

    public function update(Request $request, $id)
    {
        // Logic to update an existing incoming goods item
    }

    public function destroy($id)
    {
        // Logic to delete an incoming goods item
    }
}
