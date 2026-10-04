<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use App\Models\Tank;
use App\Models\TankPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TankPhotoController extends Controller
{
    /**
     * Store a new photo for a specific tank (and optionally associate with a fish in this tank).
     */
    public function store(Request $request, Tank $tank): RedirectResponse
    {
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'caption' => 'nullable|string|max:255',
            'livestock_id' => 'nullable|exists:livestock,id',
            'set_as_avatar' => 'nullable|boolean',
        ]);

        $path = $request->file('photo')->store('tank_photos', 'public');

        $photo = $tank->photos()->create([
            'livestock_id' => $validated['livestock_id'] ?? null,
            'photo_path' => $path,
            'caption' => $validated['caption'] ?? null,
        ]);

        // If requested, also update the livestock's main profile photo
        if ($request->boolean('set_as_avatar') && !empty($validated['livestock_id'])) {
            $livestock = Livestock::find($validated['livestock_id']);
            if ($livestock && $livestock->tank_id === $tank->id) {
                $livestock->update(['photo_path' => $path]);
            }
        }

        return back()->with('success', 'Fish photo added to ' . $tank->tank_code . ' successfully.');
    }

    /**
     * Delete a tank photo.
     */
    public function destroy(TankPhoto $tankPhoto): RedirectResponse
    {
        if ($tankPhoto->photo_path) {
            Storage::disk('public')->delete($tankPhoto->photo_path);
        }

        $tankPhoto->delete();

        return back()->with('success', 'Photo removed successfully.');
    }
}
