<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomingGoodsItems extends Model
{   
    use SoftDeletes;
    protected $guarded = [];

    public function incomingGoods()
    {
        return $this->belongsTo(IncomingGoods::class);
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    public function batch()
    {
        return $this->hasOne(GoodsBatch::class, 'incoming_goods_item_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
