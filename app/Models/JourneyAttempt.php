<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JourneyAttempt extends Model {
    use SoftDeletes;
    protected $casts = [
        'nearest_route' => 'json',
        'farthest_route' => 'json',
    ];
    protected $fillable = [ 'name', 'nearest_route', 'farthest_route', 'user_id', 'start_waypoint_id', 'calculated' ];

    public function user() {
        return $this->belongsTo( User::class );
    }

    public function waypoints() {
        return $this->hasMany( Waypoint::class );
    }

    public function startWaypoint() {
        return $this->hasOne( Waypoint::class, 'id', 'start_waypoint_id' );
    }
}
