<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    public function goods()
    {
        return $this->hasMany(Goods::class);
    }
}
