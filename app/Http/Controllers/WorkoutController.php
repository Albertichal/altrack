<?php

namespace App\Http\Controllers;

use App\Models\CustomExercise;
use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkoutController extends Controller
{
    /**
     * @return array<string, list<string>>
     */
    private static function exercisesBySplit(): array
    {
        return [
            'PUSH' => [
                'Bench Press', 'Incline Dumbbell Press', 'Overhead Press', 'Tricep Pushdown',
                'Lateral Raise', 'Cable Fly', 'High to Low Cable Fly', 'Chest Dip', 'Arnold Press',
                'Close Grip Bench Press', 'Push Up',
            ],
            'PULL' => [
                'Deadlift', 'Barbell Row', 'Pull Up', 'Lat Pulldown', 'Seated Cable Row',
                'Bicep Curl', 'Hammer Curl', 'Face Pull', 'T-Bar Row', 'Single Arm Dumbbell Row',
            ],
            'LEGS' => [
                'Squat', 'Leg Press', 'Romanian Deadlift', 'Leg Curl', 'Leg Extension',
                'Calf Raise', 'Hip Thrust', 'Bulgarian Split Squat', 'Hack Squat', 'Sumo Deadlift',
            ],
            'UPPER' => [
                'Bench Press', 'Overhead Press', 'Pull Up', 'Barbell Row', 'Lateral Raise',
                'Bicep Curl', 'Tricep Pushdown', 'Incline Dumbbell Press', 'Face Pull',
            ],
            'LOWER' => [
                'Squat', 'Leg Press', 'Romanian Deadlift', 'Leg Curl', 'Leg Extension',
                'Calf Raise', 'Hip Thrust', 'Bulgarian Split Squat',
            ],
            'FULL' => [
                'Squat', 'Bench Press', 'Deadlift', 'Overhead Press', 'Pull Up', 'Barbell Row',
                'Bicep Curl', 'Tricep Pushdown', 'Leg Press', 'Calf Raise',
            ],
            'CARDIO' => [
                'Treadmill Run', 'Cycling', 'Jump Rope', 'Rowing Machine', 'Elliptical',
                'Stair Climber', 'HIIT Sprint', 'Battle Ropes',
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
            'customBySplit' => $customBySplit,
            'splits' => Workout::SPLITS,
            'todayYmd' => now()->format('Y-m-d'),
            'todayLabel' => now()->format('j M Y'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d|before_or_equal:today',
            'split' => ['required', Rule::in(Workout::SPLITS)],
            'notes' => 'nullable|string|max:5000',
            'exercises' => 'required|array|min:1',
            'exercises.*.name' => 'required|string|max:255',
            'exercises.*.sets' => 'required|integer|min:1|max:500',
            'exercises.*.reps' => 'required|integer|min:1|max:500',
            'exercises.*.weight' => 'nullable|numeric|min:0|max:999999',
        ], [
            'date.required' => 'Tanggal wajib diisi.',
            'date.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
            'split.required' => 'Pilih split terlebih dahulu.',
            'exercises.required' => 'Tambah minimal satu exercise.',
            'exercises.min' => 'Tambah minimal satu exercise.',
        ]);

        $split = $request->string('split')->toString();
        $canonical = self::exercisesBySplit()[$split] ?? [];
        $userId = $request->user()->id;

        DB::transaction(function () use ($request, $split, $canonical, $userId) {
            $workout = Workout::create([
                'user_id' => $userId,
                'split' => $split,
                'date' => $request->date('date'),
                'notes' => $request->input('notes'),
            ]);

            foreach ($request->input('exercises', []) as $row) {
                $workout->exercises()->create([
                    'name' => $row['name'],
                    'sets' => (int) $row['sets'],
                    'reps' => (int) $row['reps'],
                    'weight' => array_key_exists('weight', $row) && $row['weight'] !== '' && $row['weight'] !== null
                        ? $row['weight']
                        : null,
                ]);
            }

            foreach ($request->input('exercises', []) as $row) {
                $name = trim((string) $row['name']);
                if ($name === '') {
                    continue;
                }
                if (in_array($name, $canonical, true)) {
                    continue;
                }
                CustomExercise::firstOrCreate(
                    [
                        'user_id' => $userId,
                        'name' => $name,
                        'muscle_group' => $split,
                    ],
                    ['description' => null]
                );
            }
        });

        return redirect()->route('log')->with('success', 'Workout berhasil disimpan.');
    }

    public function getLastExercise(Request $request)
    {
        $request->validate([
            'exercise_name' => 'required|string|max:255',
        ]);

        $name = $request->query('exercise_name');

        $row = Exercise::query()
            ->join('workouts', 'workouts.id', '=', 'exercises.workout_id')
            ->where('workouts.user_id', $request->user()->id)
            ->where('exercises.name', $name)
            ->orderByDesc('workouts.date')
            ->orderByDesc('exercises.id')
            ->select('exercises.sets', 'exercises.reps', 'exercises.weight')
            ->first();

        if ($row === null) {
            return response()->json(null);
        }

        return response()->json([
            'sets' => (int) $row->sets,
            'reps' => (int) $row->reps,
            'weight' => $row->weight !== null ? (float) $row->weight : null,
        ]);
    }
}
