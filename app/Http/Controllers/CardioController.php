<?php

namespace App\Http\Controllers;

use App\Models\CardioLog;
use Illuminate\Http\Request;

class CardioController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'date'            => 'required|date_format:Y-m-d|before_or_equal:today',
            'cardio_duration' => 'required|integer|min:1|max:600',
            'cardio_speed'    => 'nullable|numeric|min:0|max:30',
            'cardio_incline'  => 'nullable|numeric|min:0|max:15',
        ], [
            'cardio_duration.required' => 'Durasi cardio wajib diisi.',
            'cardio_duration.min'      => 'Durasi minimal 1 menit.',
        ]);

        CardioLog::create([
            'user_id'          => auth()->id(),
            'workout_id'       => null,
            'date'             => $request->input('date'),
            'duration_minutes' => (int) $request->input('cardio_duration'),
            'speed'            => $request->filled('cardio_speed') ? $request->input('cardio_speed') : null,
            'incline'          => $request->filled('cardio_incline') ? $request->input('cardio_incline') : null,
        ]);

        return redirect()->route('log')->with('success', 'Cardio berhasil disimpan.');
    }
}