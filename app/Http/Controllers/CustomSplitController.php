<?php

namespace App\Http\Controllers;

use App\Models\CustomSplit;
use Illuminate\Http\Request;

class CustomSplitController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50']);

        $user = auth()->user();
        $name = strtoupper(trim($request->input('name')));

        $exists = $user->customSplits()
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Split sudah terdaftar.'], 422);
        }

        $split = $user->customSplits()->create([
            'name'       => $name,
            'is_default' => false,
        ]);

        return response()->json(['success' => true, 'split' => $split]);
    }

    public function destroy(CustomSplit $customSplit)
    {
        if ($customSplit->user_id !== auth()->id()) abort(403);

        if ($customSplit->is_default) {
            return back()->withErrors(['error' => 'Split default tidak bisa dihapus.']);
        }

        $name = $customSplit->name;
        $customSplit->delete();

        return back()->with('success', 'Split "' . $name . '" dihapus. Exercise tetap tersimpan.');
    }
}
