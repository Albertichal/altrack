<?php

namespace App\Http\Controllers;

use App\Models\CardioLog;
use App\Models\CustomExercise;
use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkoutController extends Controller
{
    private static function exercisesBySplit(): array
    {
        return [
            'PUSH' => [
                'Incline Dumbell Press',
                'Barbell Bench Press',
                'High To Low Cable Fly',
                'Low To High Cable Fly',
                'Machine Chest Fly',
                'Shoulder Press',
                'Cable Lateral Raise',
                'Dumbell Lateral Raise',
                'Triceps Extension',
            ],
            'PULL' => [
                'Weighted Cable Row',
                'V Bar Cable Row',
                'Weighted Lat Pulldown',
                'V Bar Lat Pulldown',
                'Face Pull',
                'Rear Delt Raise',
                'Machine Bicep Curl',
                'Dumbell Bicep Curl',
            ],
            'LEGS' => [
                'Barbell Squat',
                'Leg Press',
                'Leg Extension',
                'Leg Curl',
                'Calf Raises',
            ],
            'UPPER' => [
                'Incline Dumbell Press',
                'High To Low Cable Fly',
                'Weighted Cable Row',
                'Weighted Lat Pulldown',
                'Cable Lateral Raise',
                'Triceps Extension',
                'Face Pull',
                'Machine Bicep Curl',
            ],
            'LOWER' => [
                'Barbell Squat',
                'Leg Press',
                'Leg Extension',
                'Leg Curl',
                'Calf Raises',
            ],
            'FULL' => [
                'Incline Dumbell Press',
                'High To Low Cable Fly',
                'Weighted Cable Row',
                'Weighted Lat Pulldown',
                'Cable Lateral Raise',
                'Triceps Extension',
                'Face Pull',
                'Machine Bicep Curl',
                'Barbell Squat',
                'Leg Extension',
                'Leg Curl',
            ],
        ];
    }

    public function index()
    {
        $exercisesBySplit = self::exercisesBySplit();
        $user = auth()->user();

        $customBySplit = [];
        foreach (Workout::SPLITS as $split) {
            $customBySplit[$split] = $user->customExercises()
                ->where('muscle_group', $split)
                ->orderBy('name')
                ->pluck('name')
                ->values()
                ->all();
        }

        return view('log', [
            'exercisesBySplit' => $exercisesBySplit,
            'customBySplit'    => $customBySplit,
            'splits'           => Workout::SPLITS,
            'todayYmd'         => now()->format('Y-m-d'),
            'todayLabel'       => now()->format('j M Y'),
        ]);
    }

    public function store(Request $request)
    {
        $hasExercises = $request->has('exercises') && count($request->input('exercises', [])) > 0;
        $hasCardio    = $request->filled('cardio_duration');

        // Minimal salah satu harus ada
        if (!$hasExercises && !$hasCardio) {
            return back()->withErrors(['error' => 'Tambah minimal satu exercise atau isi durasi cardio.'])->withInput();
        }

        $rules = [
            'date'  => 'required|date_format:Y-m-d|before_or_equal:today',
            'notes' => 'nullable|string|max:5000',
            'cardio_duration' => 'nullable|integer|min:1|max:600',
            'cardio_speed'    => 'nullable|numeric|min:0|max:30',
            'cardio_incline'  => 'nullable|numeric|min:0|max:15',
        ];

        if ($hasExercises) {
            $rules['split'] = ['required', Rule::in(Workout::SPLITS)];
            $rules['exercises']           = 'required|array|min:1';
            $rules['exercises.*.name']    = 'required|string|max:255';
            $rules['exercises.*.sets']    = 'required|integer|min:1|max:500';
            $rules['exercises.*.reps']    = 'required|integer|min:1|max:500';
            $rules['exercises.*.weight']  = 'nullable|numeric|min:0|max:999999';
        }

        $request->validate($rules, [
            'date.required'        => 'Tanggal wajib diisi.',
            'date.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
            'split.required'       => 'Pilih split terlebih dahulu.',
        ]);

        $userId = $request->user()->id;

        DB::transaction(function () use ($request, $hasExercises, $hasCardio, $userId) {
            $workout = null;

            if ($hasExercises) {
                $split     = $request->string('split')->toString();
                $canonical = self::exercisesBySplit()[$split] ?? [];

                $workout = Workout::create([
                    'user_id' => $userId,
                    'split'   => $split,
                    'date'    => $request->date('date'),
                    'notes'   => $request->input('notes'),
                ]);

                foreach ($request->input('exercises', []) as $row) {
                    $workout->exercises()->create([
                        'name'   => $row['name'],
                        'sets'   => (int) $row['sets'],
                        'reps'   => (int) $row['reps'],
                        'weight' => isset($row['weight']) && $row['weight'] !== '' ? $row['weight'] : null,
                    ]);
                }

                foreach ($request->input('exercises', []) as $row) {
                    $name = trim((string) $row['name']);
                    if ($name === '' || in_array($name, $canonical, true)) continue;
                    CustomExercise::firstOrCreate(
                        ['user_id' => $userId, 'name' => $name, 'muscle_group' => $split],
                        ['description' => null]
                    );
                }
            }

            if ($hasCardio) {
                CardioLog::create([
                    'user_id'          => $userId,
                    'workout_id'       => $workout?->id,
                    'date'             => $request->input('date'),
                    'duration_minutes' => (int) $request->input('cardio_duration'),
                    'speed'            => $request->filled('cardio_speed') ? $request->input('cardio_speed') : null,
                    'incline'          => $request->filled('cardio_incline') ? $request->input('cardio_incline') : null,
                ]);
            }
        });

        return redirect()->route('log')->with('success', 'Sesi berhasil disimpan.');
    }

    public function getLastExercise(Request $request)
    {
        $request->validate(['exercise_name' => 'required|string|max:255']);
        $name = $request->query('exercise_name');

        $row = Exercise::query()
            ->join('workouts', 'workouts.id', '=', 'exercises.workout_id')
            ->where('workouts.user_id', $request->user()->id)
            ->where('exercises.name', $name)
            ->orderByDesc('workouts.date')
            ->orderByDesc('exercises.id')
            ->select('exercises.sets', 'exercises.reps', 'exercises.weight')
            ->first();

        if ($row === null) return response()->json(null);

        return response()->json([
            'sets'   => (int) $row->sets,
            'reps'   => (int) $row->reps,
            'weight' => $row->weight !== null ? (float) $row->weight : null,
        ]);
    }

    public function destroy(Workout $workout)
    {
        if ($workout->user_id !== auth()->id()) abort(403);

        DB::transaction(function () use ($workout) {
            $workout->cardioLog()?->delete();
            $workout->exercises()->delete();
            $workout->delete();
        });

        return redirect()->route('dashboard')->with('success', 'Workout berhasil dihapus.');
    }
}