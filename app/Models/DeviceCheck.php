<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceCheck extends Model
{
    protected $fillable = [
        'phone_list_id',
        'checked_by_name',
        'checked_by_username',
        'imei_ok',
        'mac_ok',
        'keterangan',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'imei_ok' => 'boolean',
            'mac_ok' => 'boolean',
            'foto' => 'array',
        ];
    }

    public function phoneList(): BelongsTo
    {
        return $this->belongsTo(PhoneList::class);
    }
}
