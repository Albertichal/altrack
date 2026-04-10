<?php

namespace App\Http\Controllers;

use App\Models\CardioLog;
use App\Models\CustomExercise;
use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkoutController extends Controller
{
    private function getSplitsData($user): array
    {
        return $user->customSplits()
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get()
            ->map(fn($s) => [
                'id'         => $s->id,
                'name'       => $s->name,
                'is_default' => (bool) $s->is_default,
            ])
            ->values()
            ->all();
    }

    private function getExercisesBySplit($user, array $splits): array
    {
        $exercisesBySplit = [];
        foreach ($splits as $split) {
            $exercisesBySplit[$split['name']] = $user->customExercises()
                ->where('muscle_group', $split['name'])
                ->orderBy('name')
                ->pluck('name')
                ->all();
        }
        return $exercisesBySplit;
    }

    public function index()
    {
        $user   = auth()->user();
        $splits = $this->getSplitsData($user);

        return view('log', [
            'splits'           => $splits,
            'exercisesBySplit' => $this->getExercisesBySplit($user, $splits),
            'todayYmd'         => now()->format('Y-m-d'),
            'todayLabel'       => now()->format('j M Y'),
        ]);
    }

    public function store(Request $request)
    {
        $hasExercises = $request->has('exercises') && count($request->input('exercises', [])) > 0;
        $hasCardio    = $request->filled('cardio_duration');

        if (!$hasExercises && !$hasCardio) {
            return back()->withErrors(['error' => 'Tambah minimal satu exercise atau isi durasi cardio.'])->withInput();
        }

        $userSplits = auth()->user()->customSplits()->pluck('name')->all();

        $rules = [
            'date'            => 'required|date_format:Y-m-d|before_or_equal:today',
            'notes'           => 'nullable|string|max:5000',
            'cardio_duration' => 'nullable|integer|min:1|max:600',
            'cardio_speed'    => 'nullable|numeric|min:0|max:30',
            'cardio_incline'  => 'nullable|numeric|min:0|max:15',
        ];

        if ($hasExercises) {
            $rules['split']              = ['required', Rule::in($userSplits)];
            $rules['exercises']          = 'required|array|min:1';
            $rules['exercises.*.name']   = 'required|string|max:255';
            $rules['exercises.*.sets']   = 'required|integer|min:1|max:500';
            $rules['exercises.*.reps']   = 'required|integer|min:1|max:500';
            $rules['exercises.*.weight'] = 'required|numeric|min:1|max:999999';
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
                $split = $request->string('split')->toString();

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
                        'weight' => $row['weight'],
                    ]);
                }

                foreach ($request->input('exercises', []) as $row) {
                    $name = trim((string) $row['name']);
                    if ($name === '') continue;
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

        $row = \App\Models\Exercise::query()
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

    public function edit(Workout $workout)
    {
        if ($workout->user_id !== auth()->id()) abort(403);

        $workout->load('exercises', 'cardioLog');

        $user   = auth()->user();
        $splits = $this->getSplitsData($user);

        $currentSplit = old('split', $workout->split);

        $exercisesBySplit = $this->getExercisesBySplit($user, $splits);

        $splitExists = collect($splits)->contains('name', $currentSplit);
        if (!$splitExists) {
            $exercisesBySplit[$currentSplit] = $user->customExercises()
                ->where('muscle_group', $currentSplit)
                ->orderBy('name')
                ->pluck('name')
                ->values()
                ->all();
        }

        $existingExercises = old('exercises')
            ? collect(old('exercises'))->map(fn($e) => [
                'name'   => $e['name'] ?? '',
                'sets'   => (int) ($e['sets'] ?? 1),
                'reps'   => (int) ($e['reps'] ?? 1),
                'weight' => isset($e['weight']) && $e['weight'] !== '' ? $e['weight'] : '',
              ])->values()->all()
            : $workout->exercises->map(fn($e) => [
                'name'   => $e->name,
                'sets'   => (int) $e->sets,
                'reps'   => (int) $e->reps,
                'weight' => $e->weight !== null ? (string) $e->weight : '',
              ])->values()->all();

        $cardioDuration = old('cardio_duration', $workout->cardioLog?->duration_minutes);
        $cardioSpeed    = old('cardio_speed',    $workout->cardioLog?->speed);
        $cardioIncline  = old('cardio_incline',  $workout->cardioLog?->incline);

        return view('workouts.edit', [
            'workout'           => $workout,
            'splits'            => $splits,
            'exercisesBySplit'  => $exercisesBySplit,
            'todayYmd'          => now()->format('Y-m-d'),
            'currentSplit'      => $currentSplit,
            'currentDate'       => old('date', $workout->date->format('Y-m-d')),
            'currentNotes'      => old('notes', $workout->notes ?? ''),
            'existingExercises' => $existingExercises,
            'cardioDuration'    => $cardioDuration,
            'cardioSpeed'       => $cardioSpeed,
            'cardioIncline'     => $cardioIncline,
            'initCardioOpen'    => !empty($cardioDuration),
            'splitExists'       => $splitExists,
        ]);
    }

    public function update(Request $request, Workout $workout)
    {
        if ($workout->user_id !== auth()->id()) abort(403);

        $hasExercises = $request->has('exercises') && count($request->input('exercises', [])) > 0;
        $hasCardio    = $request->filled('cardio_duration');

        if (!$hasExercises && !$hasCardio) {
            return back()->withErrors(['error' => 'Tambah minimal satu exercise atau isi durasi cardio.'])->withInput();
        }

        $userSplits = auth()->user()->customSplits()->pluck('name')->all();

        $rules = [
            'date'            => 'required|date_format:Y-m-d|before_or_equal:today',
            'notes'           => 'nullable|string|max:5000',
            'cardio_duration' => 'nullable|integer|min:1|max:600',
            'cardio_speed'    => 'nullable|numeric|min:0|max:30',
            'cardio_incline'  => 'nullable|numeric|min:0|max:15',
        ];

        if ($hasExercises) {
            $validSplits                 = array_unique(array_merge($userSplits, [$workout->split]));
            $rules['split']              = ['required', Rule::in($validSplits)];
            $rules['exercises']          = 'required|array|min:1';
            $rules['exercises.*.name']   = 'required|string|max:255';
            $rules['exercises.*.sets']   = 'required|integer|min:1|max:500';
            $rules['exercises.*.reps']   = 'required|integer|min:1|max:500';
            $rules['exercises.*.weight'] = 'required|numeric|min:1|max:999999';
        }

        $request->validate($rules, [
            'date.required'        => 'Tanggal wajib diisi.',
            'date.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
            'split.required'       => 'Pilih split terlebih dahulu.',
        ]);

        $userId = $request->user()->id;

        DB::transaction(function () use ($request, $workout, $hasExercises, $hasCardio, $userId) {
            $split = $hasExercises ? $request->string('split')->toString() : $workout->split;

            $workout->update([
                'split' => $split,
                'date'  => $request->date('date'),
                'notes' => $request->input('notes'),
            ]);

            $workout->exercises()->delete();

            if ($hasExercises) {
                foreach ($request->input('exercises', []) as $row) {
                    $workout->exercises()->create([
                        'name'   => $row['name'],
                        'sets'   => (int) $row['sets'],
                        'reps'   => (int) $row['reps'],
                        'weight' => $row['weight'],
                    ]);
                }

                foreach ($request->input('exercises', []) as $row) {
                    $name = trim((string) $row['name']);
                    if ($name === '') continue;
                    CustomExercise::firstOrCreate(
                        ['user_id' => $userId, 'name' => $name, 'muscle_group' => $split],
                        ['description' => null]
                    );
                }
            }

            if ($hasCardio) {
                CardioLog::updateOrCreate(
                    ['workout_id' => $workout->id],
                    [
                        'user_id'          => $userId,
                        'date'             => $request->input('date'),
                        'duration_minutes' => (int) $request->input('cardio_duration'),
                        'speed'            => $request->filled('cardio_speed') ? $request->input('cardio_speed') : null,
                        'incline'          => $request->filled('cardio_incline') ? $request->input('cardio_incline') : null,
                    ]
                );
            } else {
                CardioLog::where('workout_id', $workout->id)->delete();
            }
        });

        return redirect()->route('dashboard')->with('success', 'Workout berhasil diperbarui.');
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
