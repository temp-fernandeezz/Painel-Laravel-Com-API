<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    public function orders()
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('quantity', 'unit_price')
            ->withTimestamps();
    }

}
