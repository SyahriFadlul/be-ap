<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutgoingGoods extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(OutgoingGoodsItems::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

}
