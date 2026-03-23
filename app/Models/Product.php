<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use SoftDeletes, Searchable;

    protected $fillable = ['sku','name','description','price','stock','visible','images','category_id','parent_id'];
    // Variants: all products that have this product as their parent
    public function variants()
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    // Parent: the main product this is a variant of
    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

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

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'description' => $this->description ?? '',
            'sku' => $this->sku,
            'visible' => (bool) $this->visible,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return $this->deleted_at === null;
    }

    public function getPrimaryImageUrlAttribute()
    {
        $path = null;

        if ($this->images && count($this->images) > 0) {
            $path = $this->images[0];
        } elseif (!empty($this->image)) {
            $path = $this->image;
        }

        if (!$path) {
            return null;
        }

        $raw = ltrim((string) $path, '/');

        if (preg_match('/^https?:\/\//i', $raw)) {
            return $raw;
        }

        if (str_starts_with($raw, 'storage/') || str_starts_with($raw, 'img/')) {
            return asset($raw);
        }

        if (str_starts_with($raw, 'public/')) {
            return asset('storage/' . substr($raw, 7));
        }

        if (str_contains($raw, '/')) {
            return asset('storage/' . $raw);
        }

        return asset('img/' . $raw);
    }
}
