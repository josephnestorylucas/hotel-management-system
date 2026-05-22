# Task 02 — Bind Conference to Conference Hall (Building) on Creation

## Problem
When creating a conference/event, the hall/building selection is not properly bound or saved. The conference is created without a linked ConferenceHall.

---

## Expected Behavior
When a staff member creates a conference:
1. They select a ConferenceHall (building/venue)
2. The system validates hall availability for the chosen time slot
3. The conference is saved with `conference_hall_id` linked
4. Hall booking (ConferenceBooking) is created or linked automatically

---

## Database Relationship

```
conference_halls
    ↓
conference_bookings (hall_id FK)
    ↓
conferences (booking_id FK)  ←── THIS LINK MUST BE ENFORCED
```

---

## Fix Checklist

### Step 1 — Confirm Migration Has the FK
```php
// create_conferences_table migration

Schema::create('conferences', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('conference_booking_id')
          ->constrained('conference_bookings')
          ->cascadeOnDelete(); // ← MUST EXIST
    // ...
});
```

- [ ] Confirm `conference_booking_id` FK exists on `conferences` table
- [ ] If missing: create alter migration

```bash
php artisan make:migration add_booking_id_to_conferences_table
```

```php
Schema::table('conferences', function (Blueprint $table) {
    $table->foreignUuid('conference_booking_id')
          ->nullable()
          ->constrained('conference_bookings');
});
```

---

### Step 2 — Fix Conference Creation Form

The form must include a hall/booking selector:

```blade
{{-- resources/views/conferences/create.blade.php --}}

<div class="form-group">
    <label>Select Conference Hall</label>
    <select name="conference_hall_id" id="hall_select" required>
        <option value="">-- Select Hall --</option>
        @foreach($halls as $hall)
            <option value="{{ $hall->id }}">
                {{ $hall->name }} (Capacity: {{ $hall->capacity }})
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Date</label>
    <input type="date" name="date" required>
</div>

<div class="form-group">
    <label>Start Time</label>
    <input type="time" name="start_time" required>
</div>

<div class="form-group">
    <label>End Time</label>
    <input type="time" name="end_time" required>
</div>
```

- [ ] Hall dropdown populates from `conference_halls` where status = available
- [ ] Date + start/end time fields present
- [ ] Form submits all fields to controller

---

### Step 3 — Fix ConferenceController@store

```php
// app/Http/Controllers/ConferenceController.php

public function store(Request $request)
{
    $request->validate([
        'conference_hall_id' => 'required|exists:conference_halls,id',
        'date'               => 'required|date|after_or_equal:today',
        'start_time'         => 'required',
        'end_time'           => 'required|after:start_time',
        'title'              => 'required|string|max:255',
        'description'        => 'nullable|string',
    ]);

    // Step 1: Check hall availability (30-min buffer)
    $conflict = ConferenceBooking::where('conference_hall_id', $request->conference_hall_id)
        ->where('date', $request->date)
        ->where('status', '!=', 'cancelled')
        ->where(function ($query) use ($request) {
            $bufferEnd = Carbon::parse($request->end_time)->addMinutes(30)->format('H:i');
            $query->whereBetween('start_time', [$request->start_time, $bufferEnd])
                  ->orWhereBetween('end_time', [$request->start_time, $bufferEnd])
                  ->orWhere(function ($q) use ($request, $bufferEnd) {
                      $q->where('start_time', '<=', $request->start_time)
                        ->where('end_time', '>=', $bufferEnd);
                  });
        })
        ->exists();

    if ($conflict) {
        return back()->withErrors([
            'conference_hall_id' => 'This hall is not available for the selected time slot (including 30-min cleanup buffer).'
        ])->withInput();
    }

    // Step 2: Calculate cost
    $hall = ConferenceHall::findOrFail($request->conference_hall_id);
    $hours = Carbon::parse($request->start_time)
                   ->diffInMinutes(Carbon::parse($request->end_time)) / 60;
    $cost = round($hours * $hall->hourly_rate, 2);

    // Step 3: Create the booking first
    $booking = ConferenceBooking::create([
        'booking_number'      => 'BK-' . strtoupper(Str::random(8)),
        'conference_hall_id'  => $request->conference_hall_id,
        'guest_id'            => $request->guest_id ?? null,
        'date'                => $request->date,
        'start_time'          => $request->start_time,
        'end_time'            => $request->end_time,
        'cost'                => $cost,
        'status'              => 'confirmed',
    ]);

    // Step 4: Create conference linked to booking
    $conference = Conference::create([
        'conference_booking_id' => $booking->id,  // ← BIND HERE
        'title'                 => $request->title,
        'description'           => $request->description,
        'start_datetime'        => $request->date . ' ' . $request->start_time,
        'end_datetime'          => $request->date . ' ' . $request->end_time,
        'status'                => 'draft',
    ]);

    return redirect()
        ->route('conferences.show', $conference)
        ->with('success', 'Conference created and hall booked successfully.');
}
```

- [ ] Validate all inputs
- [ ] Check availability before creating
- [ ] Create `ConferenceBooking` first
- [ ] Create `Conference` with `conference_booking_id` set
- [ ] Return redirect with success message

---

### Step 4 — Pass Halls to Create View (Controller@create)

```php
public function create()
{
    $halls = ConferenceHall::where('status', 'available')
                            ->orderBy('name')
                            ->get();

    return view('conferences.create', compact('halls'));
}
```

- [ ] Confirm `$halls` is passed to view
- [ ] Confirm only `status = available` halls shown

---

### Step 5 — Update Conference Model

```php
// app/Models/Conference.php

class Conference extends Model
{
    protected $fillable = [
        'conference_booking_id',
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'status',
    ];

    public function booking()
    {
        return $this->belongsTo(ConferenceBooking::class, 'conference_booking_id');
    }

    public function hall()
    {
        return $this->hasOneThrough(
            ConferenceHall::class,
            ConferenceBooking::class,
            'id',              // FK on conference_bookings
            'id',              // FK on conference_halls
            'conference_booking_id', // local key on conferences
            'conference_hall_id'     // local key on conference_bookings
        );
    }
}
```

- [ ] `booking()` relationship defined
- [ ] `hall()` through booking defined
- [ ] `conference_booking_id` in `$fillable`

---

### Step 6 — Show Hall Info on Conference View

```blade
{{-- resources/views/conferences/show.blade.php --}}

<div class="info-card">
    <h3>Venue</h3>
    <p>{{ $conference->booking->hall->name }}</p>
    <p>{{ $conference->booking->date }} | 
       {{ $conference->booking->start_time }} – {{ $conference->booking->end_time }}</p>
    <p>Capacity: {{ $conference->booking->hall->capacity }}</p>
</div>
```

- [ ] Booking info shown on conference detail page
- [ ] Hall name, date, time visible

---

## Files to Modify

| File | Change |
|------|--------|
| `database/migrations/..._conferences_table.php` | Add `conference_booking_id` FK if missing |
| `app/Models/Conference.php` | Add `booking()` and `hall()` relationships |
| `app/Http/Controllers/ConferenceController.php` | Fix `create()` + `store()` methods |
| `resources/views/conferences/create.blade.php` | Add hall dropdown + date/time fields |
| `resources/views/conferences/show.blade.php` | Display venue/hall info |

---

## Done When
- [ ] Conference creation form shows available halls
- [ ] Hall + time selected on form
- [ ] ConferenceBooking is auto-created on submit
- [ ] Conference saved with `conference_booking_id` linked
- [ ] Conference detail page shows hall name, date, time
- [ ] Conflict detection works (cannot double-book same hall)
