<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** @return array<string, string> */
    public static function splitColors(): array
    {
        return [
            'PUSH' => '#f5c518',
            'PULL' => '#60a5fa',
            'LEGS' => '#a78bfa',
            'FULL' => '#4ade80',
            'UPPER' => '#fb923c',
            'LOWER' => '#f87171',
            'CARDIO' => '#2dd4bf',
        ];
    }

    public function dashboard(): View
    {
        $userId = auth()->id();
        $now = Carbon::now();

        $totalSessions = Workout::where('user_id', $userId)->count();

        $thisMonth = Workout::where('user_id', $userId)
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->count();

        $totalVolume = (float) (DB::table('exercises')
            ->join('workouts', 'exercises.workout_id', '=', 'workouts.id')
            ->where('workouts.user_id', $userId)
            ->selectRaw(
                'COALESCE(SUM(exercises.sets * exercises.reps * COALESCE(exercises.weight, 0)), 0) as v'
            )
            ->value('v') ?? 0);

        $recentWorkouts = Workout::query()
            ->with(['exercises', 'cardioLog'])
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $heatmapStart = $now->copy()->subDays(364)->startOfDay();
        $workoutCountsByDate = Workout::query()
            ->where('user_id', $userId)
            ->where('date', '>=', $heatmapStart->toDateString())
            ->get()
            ->groupBy(fn (Workout $w) => $w->date->format('Y-m-d'))
            ->map->count();

        $heatmapData = [];
        for ($i = 0; $i < 365; $i++) {
            $d = $heatmapStart->copy()->addDays($i)->format('Y-m-d');
            $heatmapData[$d] = (int) ($workoutCountsByDate[$d] ?? 0);
        }

        $allWorkouts = Workout::query()
            ->with(['exercises', 'cardioLog'])
            ->where('user_id', $userId)
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $volumePerSession = $allWorkouts->map(function (Workout $w) {
            $volume = $w->exercises->sum(function ($e) {
                $wgt = $e->weight !== null ? (float) $e->weight : 0.0;

                return (int) $e->sets * (int) $e->reps * $wgt;
            });

            return [
                'date' => $w->date->format('Y-m-d'),
                'split' => $w->split,
                'volume' => round($volume, 2),
            ];
        })->values()->all();

        $currentStreak = $this->computeStreak($userId, $now);

        return view('dashboard', [
            'totalSessions' => $totalSessions,
            'thisMonth' => $thisMonth,
            'totalVolume' => round($totalVolume, 2),
            'recentWorkouts' => $recentWorkouts,
            'heatmapData' => $heatmapData,
            'volumePerSession' => $volumePerSession,
            'currentStreak' => $currentStreak,
            'splitColors' => self::splitColors(),
        ]);
    }

    private function computeStreak(int $userId, Carbon $today): int
    {
        $streak = 0;
        $day = $today->copy()->startOfDay();

        for ($i = 0; $i < 400; $i++) {
            $has = Workout::where('user_id', $userId)
                ->whereDate('date', $day->toDateString())
                ->exists();

            if ($has) {
                $streak++;
                $day->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}
