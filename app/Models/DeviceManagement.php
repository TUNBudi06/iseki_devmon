<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceManagement extends Model
{
    use SoftDeletes;

    protected $table = 'device_management';

    protected $fillable = [
        'device_name',
        'device_id',
        'approved',
        'token',
        'last_seen_at',
        'comment',
        'foto',
        'battery',
        'status',
    ];
}
