<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
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

    /**
     * Update Philippine payment settings (GCash, Maya, Bank Transfer), Farm info, and Messenger/Facebook links.
     */
    public function updatePayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'farm_name' => 'nullable|string|max:100',
            'farm_location' => 'nullable|string|max:150',
            'messenger_username' => 'nullable|string|max:100',
            'facebook_page' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'gcash_name' => 'nullable|string|max:100',
            'gcash_number' => 'nullable|string|max:50',
            'gcash_qr' => 'nullable|image|max:2048',
            'maya_name' => 'nullable|string|max:100',
            'maya_number' => 'nullable|string|max:50',
            'bank_details' => 'nullable|string|max:500',
            'shipping_notes' => 'nullable|string|max:500',
        ]);

        $user = $request->user();

        if ($request->hasFile('gcash_qr')) {
            if ($user->gcash_qr_path) {
                Storage::disk('public')->delete($user->gcash_qr_path);
            }
            $validated['gcash_qr_path'] = $request->file('gcash_qr')->store('qrcodes', 'public');
        }

        unset($validated['gcash_qr']);
        $user->update($validated);

        return Redirect::route('profile.edit')->with('success', 'Farm profile, Facebook Messenger, and payment settings updated successfully.');
    }
}
