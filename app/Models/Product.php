<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = ['sku','name','description','price','stock','visible','images','category_id'];

    protected $casts = [
        'images' => 'array',
        'visible' => 'boolean',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getPrimaryImageUrlAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            return asset('storage/' . $this->images[0]);
        }
        return null;
    }
}
