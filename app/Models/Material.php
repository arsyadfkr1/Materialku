<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'price',
        'sale_unit',
        'description',
        'image_path',
        'is_active',
        'availability_status',
        'calculator_enabled',
        'calculation_type',
        'coverage_per_box',
        'spread_per_liter_per_coat',
        'package_volume_liters',
        'default_coats',
        'waste_ratio',
        'calculation_note',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'calculator_enabled' => 'boolean',
        'coverage_per_box' => 'float',
        'spread_per_liter_per_coat' => 'float',
        'package_volume_liters' => 'float',
        'default_coats' => 'integer',
        'waste_ratio' => 'float',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
