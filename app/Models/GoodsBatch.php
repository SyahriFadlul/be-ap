<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsBatch extends Model
{
    protected $guarded = [];

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    public function incomingGoodsItems()
    {
        return $this->belongsTo(IncomingGoodsItems::class, 'incoming_goods_item_id', 'id');
    }

    public function outgoingGoodsItems()
    {
        return $this->hasMany(OutgoingGoodsItems::class);
    }
}
