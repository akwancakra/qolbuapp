<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $role = $request->input('role');

        if ($role === 'default') {
            $role = null;
        }

        $users = User::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($role, function ($query, $role) {
                return $query->where('role', $role);
            })
            ->paginate(10);

        return Inertia::render('Admin/User/Index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
                'role' => $role
            ],
        ]);
    }

    public function show(User $user): Response
    {
        return Inertia::render('Admin/User/Show', ['user' => $user]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/User/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:admin,pengurus,duta',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return to_route('admin.users.index')->with('message', 'Berhasil menambah akun');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/User/Edit', ['user' => $user]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:admin,pengurus,duta',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return to_route('admin.users.index')->with('message', 'Berhasil mengubah akun');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return to_route('admin.users.index')->with('message', 'Berhasil menghapus akun');
    }

    public function destroyMultiple(Request $request): RedirectResponse
    {
        $ids = $request->input('ids');
        User::whereIn('id', $ids)->delete();
        return to_route('admin.users.index')->with('message', 'Berhasil menghapus beberapa akun');
    }
}
