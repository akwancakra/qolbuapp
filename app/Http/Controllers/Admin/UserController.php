<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        // $users = User::all();
        $users = [
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@example.com'
            ],
            [
                'id' => 2,
                'name' => 'Bobby Marshal',
                'email' => 'bobby@example.com'
            ],
            [
                'id' => 3,
                'name' => 'Jane Doe',
                'email' => 'jane@example.com'
            ],
            [
                'id' => 4,
                'name' => 'Alice Brown',
                'email' => 'alice@example.com'
            ],
            [
                'id' => 5,
                'name' => 'Bob Smith',
                'email' => 'bob@example.com'
            ],
        ];

        return Inertia::render('Admin/User/Index', ['users' => $users]);
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return Redirect::route('users.index');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/User/Edit', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            // 'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            // 'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return Redirect::route('users.index');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return Redirect::route('users.index');
    }
}
