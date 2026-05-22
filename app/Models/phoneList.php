<?php

namespace App\Models;

use Database\Factories\PhoneListFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhoneList extends Model
{
    /** @use HasFactory<PhoneListFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'phone_lists';

    protected $fillable = [
        'brand_id',
        'model_id',
        'model_name',
        'model_type',
        'buy_date',
        'price',
        'ram',
        'storage',
        'registered',
        'hash_token',
        'thumbnail',
        'list_photos',
    ];

    protected function casts(): array
    {
        return [
            'registered' => 'boolean',
            'list_photos' => 'array',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
