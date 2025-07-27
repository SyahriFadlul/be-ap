<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    protected $guarded = [];

    public static function getCategoryDistribution()
    {
        return self::selectRaw('categories.name as category, COUNT(goods.id) as total')
            ->join('categories', 'goods.category_id', '=', 'categories.id')
            ->groupBy('category')
            ->get();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function batches()
    {
        return $this->hasMany(GoodsBatch::class);
    }

    public function incomingGoodsItems()
    {
        return $this->hasMany(IncomingGoodsItems::class);
    }

    public function outgoingGoodsItems()
    {
        return $this->hasMany(OutgoingGoodsItems::class);
    }

    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id', 'id');
    }

    public function mediumUnit()
    {
        return $this->belongsTo(Unit::class, 'medium_unit_id', 'id');
    }

    public function largeUnit()
    {
        return $this->belongsTo(Unit::class, 'large_unit_id', 'id');
    }

}
