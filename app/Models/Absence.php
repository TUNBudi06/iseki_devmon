<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $fillable = [
        'device_id',
        'nik',
        'name',
        'catatan',
        'time_absence',
    ];

    protected function casts(): array
    {
        return [
            'time_absence' => 'datetime',
        ];
    }
}
