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
        'imei',
        'mac_address',
        'registered',
        'approved',
        'hash_token',
        'thumbnail',
        'list_photos',
    ];

    protected function casts(): array
    {
        return [
            'registered' => 'boolean',
            'approved' => 'boolean',
            'list_photos' => 'array',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * Generate model_id following the manual register format: dev-{type}{code}-{next}
     * Example: dev-py02-001 (Phone Y02, first device)
     */
    public static function generateModelId(string $modelName, string $modelType): string
    {
        $type = $modelType === 'Tablet' ? 't' : 'p';

        $words = preg_split('/\s+/', trim($modelName));
        $code = '';
        foreach ($words as $word) {
            $first = strtolower($word[0]);
            preg_match('/\d+/', $word, $matches);
            $digits = $matches[0] ?? '';
            $code .= $first.$digits;
        }

        $last = static::where('model_id', 'like', 'dev-'.$type.$code.'-%')
            ->orderByRaw('CAST(SUBSTRING(model_id, -3) AS UNSIGNED) DESC')
            ->first();
        $lastIncrement = $last ? (int) substr($last->model_id, -3) : 0;
        $next = str_pad($lastIncrement + 1, 3, '0', STR_PAD_LEFT);

        return "dev-{$type}{$code}-{$next}";
    }
}
