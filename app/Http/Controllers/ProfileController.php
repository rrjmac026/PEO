<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateEmployee(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            abort(403);
        }

        $request->validate([
            'first_name'           => 'nullable|string|max:255',
            'middle_name'          => 'nullable|string|max:255',
            'last_name'            => 'nullable|string|max:255',
            'email_address'        => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'date_of_birth'        => 'nullable|date',
            'blood_type'           => 'nullable|string|max:10',
            'height_cm'            => 'nullable|numeric|min:0',
            'weight_kg'            => 'nullable|numeric|min:0',
            'home_address'         => 'nullable|string|max:500',
            'phone_number'         => 'nullable|string|max:20',
            'emergency_contact_no' => 'nullable|string|max:20',
            'position_title'       => 'nullable|string|max:255',
            'eligibility'          => 'nullable|string|max:255',
            'licence_number'       => 'nullable|string|max:255',
            'id_number'            => 'nullable|string|max:255',
            'tin'                  => 'nullable|string|max:255',
            'pagibig_no'           => 'nullable|string|max:255',
            'philhealth'           => 'nullable|string|max:255',
            'gsis_no'              => 'nullable|string|max:255',
            'hmo_organization'     => 'nullable|string|max:255',
            'hmo_number'           => 'nullable|string|max:255',
        ]);

        $user->employee()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only([
                'first_name', 'middle_name', 'last_name', 'email_address',
                'date_of_birth', 'blood_type', 'height_cm', 'weight_kg',
                'home_address', 'phone_number', 'emergency_contact_no',
                'position_title', 'eligibility', 'licence_number',
                'id_number', 'tin', 'pagibig_no', 'philhealth', 'gsis_no',
                'hmo_organization', 'hmo_number',
            ])
        );

        // Sync User name from employee name parts
        $nameParts = array_filter([
            trim($request->first_name ?? ''),
            trim($request->middle_name ?? ''),
            trim($request->last_name ?? ''),
        ]);

        $updates = [];
        if (!empty($nameParts)) {
            $updates['name'] = implode(' ', $nameParts);
        }
        if ($request->filled('email_address')) {
            $updates['email'] = $request->email_address;
        }
        if (!empty($updates)) {
            $user->update($updates);
        }

        return back()->with('status', 'employee-updated');
    }
}
