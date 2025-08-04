<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutgoingGoodsItems extends Model
{   
    use SoftDeletes;
    protected $guarded = [];

    public function outgoingGoods()
    {
        return $this->belongsTo(OutgoingGoods::class);
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    public function batch()
    {
        return $this->belongsTo(GoodsBatch::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
