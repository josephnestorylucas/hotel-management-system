<?php
namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller {
    public function index() {
        $roomTypes = RoomType::withCount('rooms')->latest()->paginate(15);
        return view('room-types.index', compact('roomTypes'));
    }

    public function create() {
        return view('room-types.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|unique:room_types|max:255',
            'base_rate' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,TZS',
            'max_occupancy' => 'required|integer|min:1',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Remove file fields from validated data
        unset($validated['image'], $validated['gallery']);

        $roomType = RoomType::create($validated);

        // Handle main image upload
        if ($request->hasFile('image')) {
            $roomType->addMediaFromRequest('image')
                ->toMediaCollection('room_type_image');
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $roomType->addMedia($image)
                    ->toMediaCollection('room_type_gallery');
            }
        }

        return redirect()->route('room-types.index')->with('success', 'Room type created successfully.');
    }

    public function show(RoomType $roomType) {
        $roomType->load('media');
        return view('room-types.show', compact('roomType'));
    }

    public function edit(RoomType $roomType) {
        $roomType->load('media');
        return view('room-types.edit', compact('roomType'));
    }

    public function update(Request $request, RoomType $roomType) {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|unique:room_types,code,'.$roomType->id.'|max:255',
            'base_rate' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,TZS',
            'max_occupancy' => 'required|integer|min:1',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_image' => 'nullable|boolean',
            'remove_gallery' => 'nullable|array',
            'remove_gallery.*' => 'integer',
        ]);

        // Remove file and removal fields from validated data
        unset($validated['image'], $validated['gallery'], $validated['remove_image'], $validated['remove_gallery']);

        $roomType->update($validated);

        // Handle main image removal
        if ($request->boolean('remove_image')) {
            $roomType->clearMediaCollection('room_type_image');
        }

        // Handle main image upload/replacement (singleFile() handles automatic replacement)
        if ($request->hasFile('image')) {
            $roomType->addMediaFromRequest('image')
                ->toMediaCollection('room_type_image');
        }

        // Handle gallery image removal
        if ($request->has('remove_gallery') && is_array($request->remove_gallery)) {
            foreach ($request->remove_gallery as $mediaId) {
                $media = $roomType->media()->find($mediaId);
                if ($media) {
                    $media->delete();
                }
            }
        }

        // Handle new gallery images upload
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $roomType->addMedia($image)
                    ->toMediaCollection('room_type_gallery');
            }
        }

        return redirect()->route('room-types.index')->with('success', 'Room type updated successfully.');
    }

    public function destroy(RoomType $roomType) {
        // Media files are automatically deleted by Spatie Media Library
        $this->softDelete($roomType);
        return redirect()->route('room-types.index')->with('success', 'Room type archived successfully.');
    }

    public function archived() {
        $records = RoomType::onlyDeleted()->latest('deleted_at')->paginate(20);
        return view('room-types.archived', compact('records'));
    }

    public function restore(RoomType $room_type) {
        $this->restoreModel($room_type);
        return redirect()->route('room-types.index')->with('success', 'Room type restored successfully.');
    }

    /**
     * Remove a specific media item from a room type.
     */
    public function removeMedia(RoomType $roomType, $mediaId)
    {
        $media = $roomType->media()->find($mediaId);
        
        if (!$media) {
            return back()->with('error', 'Media not found.');
        }

        $media->delete();

        return back()->with('success', 'Image removed successfully.');
    }
}