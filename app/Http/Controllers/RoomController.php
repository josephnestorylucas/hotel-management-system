<?php
namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Floor;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomController extends Controller {
    public function index() {
        $rooms = Room::with(['floor.building', 'roomType'])->latest()->paginate(20);

        $statusCounts = Room::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('rooms.index', compact('rooms', 'statusCounts'));
    }

    public function show(Room $room) {
        $room->load(['floor.building', 'roomType', 'cleaningAssignee', 'outOfOrderBy', 'cleaningConfirmer']);
        return view('rooms.show', compact('room'));
    }

    public function create() {
        $floors = Floor::with('building')->where('is_active', true)->get();
        $roomTypes = RoomType::all();
        return view('rooms.create', compact('floors', 'roomTypes'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'floor_id' => 'required|uuid|exists:floors,id',
            'room_type_id' => 'required|uuid|exists:room_types,id',
            'room_number' => 'required|max:255',
            'status' => 'required|in:available,reserved,occupied,dirty,out_of_order',
            'is_active' => 'boolean',
        ]);

        Room::create($validated);
        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
    }

    public function edit(Room $room) {
        $floors = Floor::with('building')->where('is_active', true)->get();
        $roomTypes = RoomType::all();
        return view('rooms.edit', compact('room', 'floors', 'roomTypes'));
    }

    public function update(Request $request, Room $room) {
        $validated = $request->validate([
            'floor_id' => 'required|uuid|exists:floors,id',
            'room_type_id' => 'required|uuid|exists:room_types,id',
            'room_number' => 'required|max:255',
            'status' => 'required|in:available,reserved,occupied,dirty,out_of_order',
            'is_active' => 'boolean',
        ]);

        $room->update($validated);
        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room) {
        $this->softDelete($room);
        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }

    public function archived()
    {
        $rooms = Room::onlyDeleted()->with(['floor.building', 'roomType'])->latest('deleted_at')->paginate(20);

        return view('rooms.archived', compact('rooms'));
    }

    public function restore(Room $room)
    {
        $this->restoreModel($room);

        return redirect()->route('rooms.index')->with('success', 'Room restored successfully.');
    }

    public function toggleStatus(Request $request, Room $room) {
        $validated = $request->validate([
            'status' => 'required|in:available,reserved,occupied,dirty,out_of_order',
        ]);

        $room->update(['status' => $validated['status']]);
        return back()->with('success', 'Room status updated successfully.');
    }
}