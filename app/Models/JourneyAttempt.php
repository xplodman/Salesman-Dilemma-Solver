<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JourneyAttempt extends Model
{
    use SoftDeletes;

    protected $casts = [
        'shortest_path'  => 'json',
        'longest_path' => 'json',
    ];
    protected $fillable = [ 'name', 'shortest_path', 'shortest_path_distance', 'longest_path', 'longest_path_distance', 'user_id', 'start_waypoint_id', 'calculated' ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function waypoints()
    {
        return $this->hasMany(Waypoint::class);
    }

    public function startWaypoint()
    {
        return $this->hasOne(Waypoint::class, 'id', 'start_waypoint_id');
    }
}
