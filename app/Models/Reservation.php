<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Station;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'station_id',
        'start_time',
        'duration_minutes',
        'end_time',
        'status',
        'energy_delivered_kwh',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}