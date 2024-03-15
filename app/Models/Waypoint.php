<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Waypoint extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'latitude', 'longitude', 'journey_attempt_id', 'user_id'];

    public function journeyAttempt()
    {
        return $this->belongsTo(JourneyAttempt::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
