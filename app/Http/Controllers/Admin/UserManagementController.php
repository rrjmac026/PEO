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
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Split search into individual words for better partial matching
                $terms = array_filter(explode(' ', $search));
                foreach ($terms as $term) {
                    $q->where(function ($inner) use ($term) {
                        $inner->where('name', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%");
                    });
                }
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

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
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|string',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $plainPassword = $request->filled('password')
            ? $request->password
            : Str::random(12);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($plainPassword),
        ]);

        try {
            Mail::to($user->email)->send(new UserCredentialsMail($user, $plainPassword));
            $message = 'User created and credentials sent via email.';
        } catch (\Exception $e) {
            \Log::error('Failed to send credentials: ' . $e->getMessage());
            $message = 'User created but email failed: ' . $e->getMessage();
        }

        return redirect()->route('admin.users.index')->with('success', $message);
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

    public function resendCredentials(Request $request, User $user)
    {
        $request->validate([
            'password' => 'nullable|min:8|confirmed',
        ]);

        $plainPassword = $request->filled('password')
            ? $request->password
            : Str::random(12);

        $user->update(['password' => Hash::make($plainPassword)]);

        try {
            Mail::to($user->email)->send(new UserCredentialsMail($user, $plainPassword, isResend: true));
            $message = 'New credentials sent to ' . $user->email;
        } catch (\Exception $e) {
            \Log::error('Failed to resend credentials: ' . $e->getMessage());
            $message = 'Password reset but email failed: ' . $e->getMessage();
        }

        return back()->with('success', $message);
    }
}
