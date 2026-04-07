<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    use HasFactory;

    public const SPLITS = [
        'PUSH',
        'PULL',
        'LEGS',
        'FULL',
        'UPPER',
        'LOWER',
    ];

    protected $fillable = [
        'user_id',
        'split',
        'date',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercises()
    {
        return $this->hasMany(Exercise::class);
    }

    public function cardioLog()
    {
        return $this->hasOne(CardioLog::class);
    }

    public function getDayLabelAttribute(): string
    {
        return match($this->split) {
            'PUSH'  => 'Push Day',
            'PULL'  => 'Pull Day',
            'LEGS'  => 'Leg Day',
            'FULL'  => 'Full Day',
            'UPPER' => 'Upper Day',
            'LOWER' => 'Lower Day',
            default => $this->split,
        };
    }
}