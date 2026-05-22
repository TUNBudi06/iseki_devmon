<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $connection = 'dbrifa';

    protected $fillable = ['nama', 'nik', 'team', 'password', 'division_id', 'status', 'deleted_at'];
}
