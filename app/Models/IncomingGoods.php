<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingGoods extends Model
{
    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    public function items()
    {
        return $this->hasMany(IncomingGoodsItems::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

}
