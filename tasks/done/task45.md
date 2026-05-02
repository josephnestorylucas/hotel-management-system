Task: Reception Drink Requests to Rooms With Orders Inbox

Objective:
Allow reception to request drinks for rooms. Requests should appear in a dedicated drink orders inbox for bar staff to fulfill.

This task must:
- Add a reception-facing UI to create drink requests for rooms
- Create a drink orders inbox for bar staff
- Link requests to room/booking/guest
- Reuse existing layouts and styles

Do not clear or reset the database.
Do not add new dependencies without approval.

---

### 1) Discovery First: Find Existing Request/Order Patterns

Before implementation, identify:
- Any existing room service or request flows
- Existing bar order or kitchen order inbox patterns
- How bookings/rooms are referenced in the system
- Notification or status update patterns

Implementation rule:
- Follow existing architecture and naming conventions
- Reuse existing inbox or ticketing components

---

### 2) Data Contract

Required behavior:
- Define a drink request record

Required fields (adjust to existing naming conventions):
- request_number
- room_id or booking_id
- guest_name (if available)
- requested_by (reception user)
- items: product_id, quantity, notes
- status (new, accepted, in_progress, completed, cancelled)
- requested_at

---

### 3) Reception UI

Required behavior:
- Provide a simple request form for reception
- Allow selecting room/booking and items
- Allow optional notes or delivery time
- Validate quantities and availability

---

### 4) Drink Orders Inbox (Bar)

Required behavior:
- Show incoming requests in an inbox list
- Allow status updates (accept, in progress, completed)
- Show room and guest details in each request
- Keep inbox updates consistent with existing order flows

---

### 5) Access Control

Required behavior:
- Reception roles can create requests
- Bar roles can view and update requests
- Other roles cannot access the inbox

---

### 6) Migration Plan (No Data Loss)

Required behavior:
- Create new tables/columns without destructive changes
- Provide safe rollback

---

### 7) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Reception can create a drink request for a room
2) Request appears in the bar inbox immediately
3) Bar staff can update request status
4) Requests are linked to the correct room/booking
5) No DB resets or data loss

---

Expected Outcome:

- Reception can request drinks to rooms
- Bar has a live inbox for drink orders
- Requests track status and room details

Priority:
HIGH - Guest service workflow
