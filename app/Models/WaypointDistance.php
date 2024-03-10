<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaypointDistance extends Model
{
    protected $fillable = ['origin_id', 'destination_id', 'distance'];

    public function origin()
    {
        return $this->belongsTo(Waypoint::class, 'id', 'origin_id');
    }

    public function destination()
    {
        return $this->belongsTo(Waypoint::class, 'id', 'destination_id');
    }
}
