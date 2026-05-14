<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\UserCredentialsMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    // 📌 Display all users
    private array $roles = [
        'admin'               => 'Admin',
        'contractor'          => 'Contractor',
        'site_inspector'      => 'Site Inspector',
        'surveyor'            => 'Surveyor',
        'mtqa'                => 'MTQA',
        'resident_engineer'   => 'Resident Engineer',
        'engineeriv'          => 'Engineer IV',
        'engineeriii'         => 'Engineer III',
        'provincial_engineer' => 'Provincial Engineer',
    ];
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // 📌 Show create form
    public function create()
    {
        return view('admin.users.create', ['roles' => $this->roles]);
    }

    // 📌 Store new user
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role'  => 'required|string',
        ]);

        // Generate a random password — no manual entry needed
        $plainPassword = Str::password(12); // e.g. "aB3$xZq1!mPw"

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($plainPassword),
        ]);

        Mail::to($user->email)->send(new UserCredentialsMail($user, $plainPassword));

        return redirect()->route('admin.users.index')
                        ->with('success', 'User created and credentials sent via email.');
    }

    // 📌 Show single user
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    // 📌 Show edit form
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user') + ['roles' => $this->roles]);
    }

    // 📌 Update user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = $request->only('name', 'email', 'role');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    // 📌 Delete user
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully.');
    }
    
    public function resendCredentials(User $user)
    {
        $plainPassword = Str::password(12);

        $user->update(['password' => Hash::make($plainPassword)]);

        Mail::to($user->email)->send(new UserCredentialsMail($user, $plainPassword));

        return back()->with('success', 'New credentials sent to ' . $user->email);
    }
}
