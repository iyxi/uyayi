<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['sku','name','description','price','stock','visible'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
