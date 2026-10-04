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
            'farm_name' => 'required|string|max:100',
            'farm_location' => 'nullable|string|max:150',
            'farm_logo' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,gif,bmp,ico,jfif|max:20480',
            'remove_farm_logo' => 'nullable|boolean',
            'messenger_username' => 'nullable|string|max:100',
            'facebook_page' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'gcash_name' => 'nullable|string|max:100',
            'gcash_number' => 'nullable|string|max:50',
            'gcash_qr' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,gif,bmp|max:20480',
            'maya_name' => 'nullable|string|max:100',
            'maya_number' => 'nullable|string|max:50',
            'bank_details' => 'nullable|string|max:500',
            'shipping_notes' => 'nullable|string|max:500',
        ], [
            'farm_logo.mimes' => 'The logo must be an image file (PNG, JPG, SVG, WebP, GIF, or BMP).',
            'farm_logo.max' => 'The logo file size must not exceed 20MB.',
            'gcash_qr.max' => 'The GCash QR file size must not exceed 20MB.',
        ]);

        $user = $request->user();

        // Handle Farm Logo upload or removal
        if ($request->boolean('remove_farm_logo')) {
            if ($user->farm_logo_path) {
                Storage::disk('public')->delete($user->farm_logo_path);
            }
            $validated['farm_logo_path'] = null;
        } elseif ($request->hasFile('farm_logo')) {
            if ($user->farm_logo_path) {
                Storage::disk('public')->delete($user->farm_logo_path);
            }
            $validated['farm_logo_path'] = $request->file('farm_logo')->store('farm_logos', 'public');
        }

        if ($request->hasFile('gcash_qr')) {
            if ($user->gcash_qr_path) {
                Storage::disk('public')->delete($user->gcash_qr_path);
            }
            $validated['gcash_qr_path'] = $request->file('gcash_qr')->store('qrcodes', 'public');
        }

        unset($validated['farm_logo'], $validated['remove_farm_logo'], $validated['gcash_qr']);
        $user->update($validated);

        // Synchronize farm branding across all admin records in the system
        \App\Models\User::where('role', 'admin')->where('id', '!=', $user->id)->update([
            'farm_name' => $user->farm_name,
            'farm_location' => $user->farm_location,
            'farm_logo_path' => $user->farm_logo_path,
            'messenger_username' => $user->messenger_username,
            'facebook_page' => $user->facebook_page,
            'contact_number' => $user->contact_number,
            'gcash_name' => $user->gcash_name,
            'gcash_number' => $user->gcash_number,
            'gcash_qr_path' => $user->gcash_qr_path,
            'maya_name' => $user->maya_name,
            'maya_number' => $user->maya_number,
            'bank_details' => $user->bank_details,
            'shipping_notes' => $user->shipping_notes,
        ]);

        return Redirect::route('profile.edit')->with('success', 'Farm branding, logo, Messenger, and payment settings updated successfully.');
    }
}
