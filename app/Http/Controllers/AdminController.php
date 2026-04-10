<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')
            ->withCount('workouts')
            ->orderBy('name')
            ->get();

        return view('admin', compact('users'));
    }

    public function showCreateUser()
    {
        return view('admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:50',
            'username' => 'required|string|min:3|max:30|unique:users|alpha_dash',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,user',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.min' => 'Nama minimal 2 karakter.',
            'name.max' => 'Nama maksimal 50 karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username maksimal 30 karakter.',
            'username.unique' => 'Username sudah dipakai, pilih yang lain.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, strip, dan underscore.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
            'expires_at' => $request->role === 'admin' ? null : now()->addDays(30),
        ]);

        return redirect()->route('admin')->with('success', "Akun @{$request->username} berhasil dibuat.");
    }

    public function userProgress(Request $request, User $user)
    {
        $recentWorkouts = Workout::with(['exercises', 'cardioLog'])
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return view('admin.user-progress', [
            'member'         => $user,
            'recentWorkouts' => $recentWorkouts,
        ]);
    }

    public function toggleActive(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin')->withErrors([
                'error' => 'Tidak bisa mengubah status akun admin.',
            ]);
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin')->with('success', "Akun @{$user->username} berhasil {$status}.");
    }

    public function addAccess(Request $request, User $user)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ], [
            'days.required' => 'Jumlah hari wajib diisi.',
            'days.integer' => 'Jumlah hari harus berupa angka.',
            'days.min' => 'Minimal 1 hari.',
            'days.max' => 'Maksimal 365 hari.',
        ]);

        $days = (int) $request->days;

        $base = ($user->expires_at && Carbon::now()->isBefore($user->expires_at))
            ? $user->expires_at->copy()
            : Carbon::now();

        $user->expires_at = $base->addDays($days);
        $user->is_active = true;
        $user->save();

        return redirect()->route('admin')->with(
            'success',
            "Akses {$days} hari ditambahkan ke @{$user->username}. Expired: {$user->expires_at->format('d M Y')}."
        );
    }

    public function resetAccess(Request $request, User $user)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ], [
            'days.required' => 'Jumlah hari wajib diisi.',
            'days.integer' => 'Jumlah hari harus berupa angka.',
            'days.min' => 'Minimal 1 hari.',
            'days.max' => 'Maksimal 365 hari.',
        ]);

        $days = (int) $request->days;

        $user->expires_at = Carbon::now()->addDays($days);
        $user->is_active = true;
        $user->save();

        return redirect()->route('admin')->with(
            'success',
            "Akses direset ke {$days} hari untuk @{$user->username}. Expired: {$user->expires_at->format('d M Y')}."
        );
    }

    public function deleteAvatar(User $user)
    {
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->avatar = null;
        $user->save();

        return redirect()->route('admin')->with('success', "Foto profil @{$user->username} berhasil dihapus.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('admin')->with('success', "Password @{$user->username} berhasil direset.");
    }

    public function updateName(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:50',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.min' => 'Nama minimal 2 karakter.',
            'name.max' => 'Nama maksimal 50 karakter.',
        ]);

        $user->name = $request->name;
        $user->save();

        return redirect()->route('admin')->with('success', "Nama @{$user->username} berhasil diubah menjadi \"{$user->name}\".");
    }

    public function deleteUser(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin')->withErrors([
                'error' => 'Tidak bisa menghapus akun admin',
            ]);
        }

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $username = $user->username;

        $user->workouts()->delete();

        $user->delete();

        return redirect()->route('admin')->with('success', "Akun @{$username} berhasil dihapus");
    }
}
