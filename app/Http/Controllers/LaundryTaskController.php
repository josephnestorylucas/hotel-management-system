<?php
// app/Http/Controllers/LaundryTaskController.php

namespace App\Http\Controllers;

use App\Models\LaundryTask;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class LaundryTaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // House Help sees only their assigned tasks
        if ($user->isHouseHelp()) {
            $tasks = LaundryTask::with(['reservation', 'assignedTo', 'creator'])
                ->where('assigned_to', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            // Supervisor and Admin see all tasks
            $tasks = LaundryTask::with(['reservation', 'assignedTo', 'creator'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return view('laundry.index', compact('tasks'));
    }

    public function create()
    {
        // Only supervisor and admin can create tasks
        if (!auth()->user()->isSupervisor() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Get active reservations (checked in or confirmed)
        $reservations = Reservation::with(['room', 'guest'])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->whereNotNull('room_id')
            ->orderBy('check_in_date', 'desc')
            ->get();

        // Get house help staff
        $houseHelpRole = Role::where('name', Role::HOUSE_HELP)->first();
        $houseHelpStaff = User::where('role_id', $houseHelpRole->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('laundry.create', compact('reservations', 'houseHelpStaff'));
    }

    public function store(Request $request)
    {
        // Only supervisor and admin can create tasks
        if (!auth()->user()->isSupervisor() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'reservation_id' => 'required|uuid|exists:reservations,id',
            'assigned_to' => 'required|uuid|exists:users,id',
            'description' => 'nullable|string|max:1000',
            'is_amenity' => 'required|boolean',
            'cost' => 'required_if:is_amenity,0|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Get reservation details
        $reservation = Reservation::with('room')->findOrFail($validated['reservation_id']);

        $task = LaundryTask::create([
            'reservation_id' => $validated['reservation_id'],
            'assigned_to' => $validated['assigned_to'],
            'created_by' => auth()->id(),
            'guest_name' => $reservation->guest_display_name,
            'room_number' => $reservation->room->room_number,
            'description' => $validated['description'],
            'is_amenity' => $validated['is_amenity'],
            'cost' => $validated['is_amenity'] ? 0 : $validated['cost'],
            'notes' => $validated['notes'],
            'status' => 'pending',
        ]);

        return redirect()->route('laundry.index')
            ->with('success', 'Laundry task created and assigned successfully.');
    }

    public function edit(LaundryTask $laundryTask)
    {
        // Only supervisor and admin can edit tasks
        if (!auth()->user()->isSupervisor() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Get active reservations
        $reservations = Reservation::with(['room', 'guest'])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->whereNotNull('room_id')
            ->orderBy('check_in_date', 'desc')
            ->get();

        // Get house help staff
        $houseHelpRole = Role::where('name', Role::HOUSE_HELP)->first();
        $houseHelpStaff = User::where('role_id', $houseHelpRole->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('laundry.edit', compact('laundryTask', 'reservations', 'houseHelpStaff'));
    }

    public function update(Request $request, LaundryTask $laundryTask)
    {
        // Only supervisor and admin can update tasks
        if (!auth()->user()->isSupervisor() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'reservation_id' => 'required|uuid|exists:reservations,id',
            'assigned_to' => 'required|uuid|exists:users,id',
            'description' => 'nullable|string|max:1000',
            'is_amenity' => 'required|boolean',
            'cost' => 'required_if:is_amenity,0|numeric|min:0',
            'status' => 'required|in:pending,in_progress,completed,returned',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Get reservation details if changed
        $reservation = Reservation::with('room')->findOrFail($validated['reservation_id']);

        $laundryTask->update([
            'reservation_id' => $validated['reservation_id'],
            'assigned_to' => $validated['assigned_to'],
            'guest_name' => $reservation->guest_display_name,
            'room_number' => $reservation->room->room_number,
            'description' => $validated['description'],
            'is_amenity' => $validated['is_amenity'],
            'cost' => $validated['is_amenity'] ? 0 : $validated['cost'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('laundry.index')
            ->with('success', 'Laundry task updated successfully.');
    }

    public function destroy(LaundryTask $laundryTask)
    {
        // Only supervisor and admin can delete tasks
        if (!auth()->user()->isSupervisor() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $this->softDelete($laundryTask);

        return redirect()->route('laundry.index')
            ->with('success', 'Laundry task deleted successfully.');
    }

    public function markAsInProgress(LaundryTask $laundryTask)
    {
        if (!$laundryTask->canBeMarkedAsInProgress()) {
            return back()->with('error', 'Task cannot be marked as in progress.');
        }

        $laundryTask->markAsInProgress();

        return back()->with('success', 'Task marked as in progress.');
    }

    public function markAsCompleted(LaundryTask $laundryTask)
    {
        if (!$laundryTask->canBeMarkedAsCompleted()) {
            return back()->with('error', 'Task cannot be marked as completed.');
        }

        $laundryTask->markAsCompleted();

        return back()->with('success', 'Laundry completed successfully.');
    }

    public function markAsReturned(LaundryTask $laundryTask)
    {
        // House help or supervisor can mark as returned
        if (!auth()->user()->isHouseHelp() && !auth()->user()->isSupervisor() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // House help can only mark their own tasks
        if (auth()->user()->isHouseHelp() && $laundryTask->assigned_to !== auth()->id()) {
            abort(403, 'You can only mark your own tasks as returned.');
        }

        if (!$laundryTask->canBeMarkedAsReturned()) {
            return back()->with('error', 'Only completed tasks can be marked as returned.');
        }

        $laundryTask->markAsReturned();

        return back()->with('success', 'Clothes returned to guest successfully.');
    }
}