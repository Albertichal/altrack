<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardioLog extends Model
{
    protected $fillable = [
        'user_id',
        'workout_id',
        'date',
        'duration_minutes',
        'speed',
        'incline',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }
}