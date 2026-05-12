<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceManagement extends Model
{
    protected $table = 'device_management';

    protected $fillable = [
        'device_name',
        'device_id',
        'approved',
        'token',
        'last_seen_at',
    ];
}
