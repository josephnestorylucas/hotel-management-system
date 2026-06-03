<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CleaningController extends Controller
{
    const ATTENTION_STATUSES = ['dirty', 'out_of_order', 'occupied'];

    /**
     * Supervisor: list rooms needing cleaning or maintenance.
     */
    public function index(): View
    {
        $roomsNeedingAttention = Room::with(['floor.building', 'roomType', 'cleaningAssignee', 'outOfOrderBy'])
            ->whereIn('status', self::ATTENTION_STATUSES)
            ->orderBy('room_number')
            ->get();

        $roomsAssigned = $roomsNeedingAttention->whereNotNull('cleaning_assigned_to');

        $houseHelpers = User::whereHas('role', fn($q) => $q->where('name', 'house_help'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('cleaning.supervisor', compact(
            'roomsNeedingAttention',
            'roomsAssigned',
            'houseHelpers'
        ));
    }

    /**
     * Supervisor: assign house help to a room.
     */
    public function assign(Request $request, Room $room): RedirectResponse
    {
        abort_unless(in_array($room->status, self::ATTENTION_STATUSES), 422, 'Only rooms needing attention can be assigned.');

        $data = $request->validate([
            'house_help_id' => 'required|uuid|exists:users,id',
        ]);

        $room->update([
            'cleaning_assigned_to' => $data['house_help_id'],
            'cleaning_assigned_at' => now(),
        ]);

        return redirect()->route('cleaning.index')
            ->with('success', "Room {$room->room_number} assigned.");
    }

    /**
     * Supervisor: confirm cleaning/maintenance and return room to available.
     */
    public function confirm(Room $room): RedirectResponse
    {
        abort_unless(in_array($room->status, self::ATTENTION_STATUSES), 422, 'Only rooms needing attention can be confirmed.');
        abort_if(!$room->cleaning_completed_at, 422, 'House help must mark the task as done before confirmation.');

        // Occupied rooms stay occupied — guest is still in the room
        $newStatus = $room->status === 'occupied' ? 'occupied' : 'available';

        $room->update([
            'status' => $newStatus,
            'cleaning_confirmed_by' => (string) Auth::id(),
            'cleaning_confirmed_at' => now(),
        ]);

        $statusText = $newStatus === 'available' ? 'returned to available' : 'confirmed (guest still in room)';
        return redirect()->route('cleaning.index')
            ->with('success', "Room {$room->room_number} {$statusText}.");
    }

    /**
     * House Help: view assigned rooms (cleaning + maintenance).
     */
    public function myRooms(): View
    {
        $assignedRooms = Room::with(['floor.building', 'roomType', 'outOfOrderBy'])
            ->whereIn('status', self::ATTENTION_STATUSES)
            ->where('cleaning_assigned_to', (string) Auth::id())
            ->orderBy('room_number')
            ->get();

        return view('cleaning.house-help', compact('assignedRooms'));
    }

    /**
     * House Help: mark cleaning/maintenance as done.
     */
    public function markDone(Room $room): RedirectResponse
    {
        abort_unless(in_array($room->status, self::ATTENTION_STATUSES), 422, 'Only rooms needing attention can be marked done.');
        abort_if($room->cleaning_assigned_to !== (string) Auth::id(), 403, 'You are not assigned to this room.');

        $room->update([
            'cleaning_completed_at' => now(),
        ]);

        return redirect()->route('cleaning.my-rooms')
            ->with('success', "Room {$room->room_number} marked as done. Awaiting supervisor confirmation.");
    }

    /**
     * Front desk / Supervisor / Manager: view all out_of_order rooms and their maintenance status.
     */
    public function maintenanceIndex(): View
    {
        $maintenanceRooms = Room::with(['floor.building', 'roomType', 'cleaningAssignee', 'outOfOrderBy'])
            ->where('status', 'out_of_order')
            ->orderBy('room_number')
            ->get();

        return view('cleaning.maintenance', compact('maintenanceRooms'));
    }

    /**
     * Front desk: mark room as out of order with a reason.
     */
    public function markOutOfOrder(Request $request, Room $room): RedirectResponse
    {
        abort_if(!in_array($room->status, ['available', 'dirty']), 422, 'Only available or dirty rooms can be marked out of order.');

        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Clear cleaning assignment if transitioning from dirty
        $updates = [
            'status' => 'out_of_order',
            'out_of_order_reason' => $data['reason'],
            'out_of_order_set_by' => (string) Auth::id(),
            'out_of_order_set_at' => now(),
        ];

        if ($room->status === 'dirty') {
            $updates['cleaning_assigned_to'] = null;
            $updates['cleaning_assigned_at'] = null;
            $updates['cleaning_completed_at'] = null;
        }

        $room->update($updates);

        return redirect()->route('cleaning.maintenance')
            ->with('success', "Room {$room->room_number} marked as out of order.");
    }
}
