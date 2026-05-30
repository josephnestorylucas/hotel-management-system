<?php
namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Building;
use Illuminate\Http\Request;

class FloorController extends Controller {
    public function index() {
        $floors = Floor::with('building')->withCount('rooms')->latest()->paginate(15);
        return view('floors.index', compact('floors'));
    }

    public function create() {
        $buildings = Building::where('is_active', true)->get();
        return view('floors.create', compact('buildings'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'building_id' => 'required|uuid|exists:buildings,id',
            'name' => 'required|max:255',
            'floor_number' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        Floor::create($validated);
        return redirect()->route('floors.index')->with('success', 'Floor created successfully.');
    }

    public function edit(Floor $floor) {
        $buildings = Building::where('is_active', true)->get();
        return view('floors.edit', compact('floor', 'buildings'));
    }

    public function update(Request $request, Floor $floor) {
        $validated = $request->validate([
            'building_id' => 'required|uuid|exists:buildings,id',
            'name' => 'required|max:255',
            'floor_number' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $floor->update($validated);
        return redirect()->route('floors.index')->with('success', 'Floor updated successfully.');
    }

    public function destroy(Floor $floor) {
        if ($floor->rooms()->exists()) {
            return redirect()->route('floors.index')
                ->with('error', 'Cannot delete "' . $floor->name . '" because it has assigned rooms. Remove the rooms first.');
        }

        $this->softDelete($floor);
        return redirect()->route('floors.index')->with('success', 'Floor archived successfully.');
    }

    public function archived() {
        $records = Floor::onlyDeleted()->with('building')->latest('deleted_at')->paginate(20);
        return view('floors.archived', compact('records'));
    }

    public function restore(Floor $floor) {
        $this->restoreModel($floor);
        return redirect()->route('floors.index')->with('success', 'Floor restored successfully.');
    }
}