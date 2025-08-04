<?php

namespace App\Models;

use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutgoingGoods extends Model
{   
    use SoftDeletes;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->invoice = IdGenerator::generate([
                'table' => 'outgoing_goods', 
                'field' => 'invoice', 
                'length' => 12, 
                'prefix' => 'INV-' . date('ym'),
                'reset_on_prefix_change' => true
            ]);
        });
    }

    public function items()
    {
        return $this->hasMany(OutgoingGoodsItems::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }



}
