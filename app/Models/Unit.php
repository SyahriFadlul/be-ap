<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $guarded = [];

    public function outgoingGoodsItems()
    {
        return $this->hasMany(outgoingGoodsItems::class);
    }
}
