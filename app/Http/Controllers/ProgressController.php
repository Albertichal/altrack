<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgressController extends Controller
{
    public function index(Request $request): View
    {
        $userId = auth()->id();

        $lifts = Exercise::query()
            ->join('workouts', 'exercises.workout_id', '=', 'workouts.id')
            ->where('workouts.user_id', $userId)
            ->select('exercises.name')
            ->distinct()
            ->orderBy('exercises.name')
            ->pluck('exercises.name')
            ->values();

        $selectedLift = null;
        if ($lifts->isNotEmpty()) {
            $q = $request->query('lift');
            if (is_string($q) && $q !== '' && $lifts->contains($q)) {
                $selectedLift = $q;
            } else {
                $selectedLift = $lifts->first();
            }
        }

        $liftHistory = collect();
        $prWeight = null;
        $lastWeight = null;
        $lastReps = null;
        $weightChange = null;
        $repsChange = null;

        if ($selectedLift !== null) {
            $rows = Exercise::query()
                ->join('workouts', 'exercises.workout_id', '=', 'workouts.id')
                ->where('workouts.user_id', $userId)
                ->where('exercises.name', $selectedLift)
                ->orderBy('workouts.date')
                ->orderBy('exercises.id')
                ->selectRaw('workouts.date as workout_date, exercises.weight, exercises.reps, exercises.sets')
                ->get();

            $liftHistory = $rows->map(function ($r) {
                $d = $r->workout_date;
                if ($d instanceof Carbon) {
                    $d = $d->format('Y-m-d');
                } elseif (is_string($d)) {
                    $d = Carbon::parse($d)->format('Y-m-d');
                } else {
                    $d = (string) $d;
                }

                return [
                    'date' => $d,
                    'weight' => $r->weight !== null ? (float) $r->weight : null,
                    'reps' => (int) $r->reps,
                    'sets' => (int) $r->sets,
                ];
            });

            $weights = $rows->pluck('weight')->filter(fn ($w) => $w !== null)->map(fn ($w) => (float) $w);
            $prWeight = $weights->isNotEmpty() ? $weights->max() : null;

            $last = $rows->last();
            if ($last) {
                $lastWeight = $last->weight !== null ? (float) $last->weight : null;
                $lastReps = (int) $last->reps;
            }

            if ($rows->count() >= 2) {
                $lastTwo = $rows->slice(-2)->values();
                $prev = $lastTwo[0];
                $lastRow = $lastTwo[1];
                if ($lastRow->weight !== null && $prev->weight !== null) {
                    $weightChange = round((float) $lastRow->weight - (float) $prev->weight, 2);
                }
                $repsChange = (int) $lastRow->reps - (int) $prev->reps;
            }
        }

        return view('progress', [
            'lifts' => $lifts,
            'selectedLift' => $selectedLift,
            'liftHistory' => $liftHistory,
            'prWeight' => $prWeight,
            'lastWeight' => $lastWeight,
            'lastReps' => $lastReps,
            'weightChange' => $weightChange,
            'repsChange' => $repsChange,
        ]);
    }
}
