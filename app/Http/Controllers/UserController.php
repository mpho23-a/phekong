<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:sales_rep,stock_admin,approval_admin',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with('success', "{$user->name} added as {$validated['role']}.");
    }

    public function edit(User $user)
    {
        $allRoles = ['sales_rep', 'stock_admin', 'approval_admin'];
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('users.edit', compact('user', 'allRoles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'required|array|min:1',
            'roles.*' => 'in:sales_rep,stock_admin,approval_admin',
        ]);

        $user->syncRoles($validated['roles']);

        return redirect()->route('users.index')->with('success', "{$user->name}'s roles updated.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', "You can't delete your own account.");
        }

        $user->delete();

        return back()->with('success', 'User removed.');
    }
}