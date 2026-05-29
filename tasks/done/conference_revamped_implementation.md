# Conference Management System — New Prompt

Use this document as a prompt for implementation only. Do not include old conference booking logic, old single-attendee booking flow, or implementation examples in the response.

## Goal
Build a simplified conference flow where the conference itself carries the charge, not each attendee receipt.

## Core Changes
- Remove the old conference booking flow.
- Remove the single attendee booking flow for individual conference bookings.
- Remove all old conference booking behavior tied to attendee receipts.
- Conference pricing should be based on the conference rate already assigned to the conference.
- Attendee receipts must not carry the conference charge.
- The conference record should hold the charge, not the attendee receipt.
- Keep the process simple and clear.

## Required Behavior
- When a conference is created, use the conference rate as the charge basis.
- Do not add the conference fee again on attendee receipts.
- Print tickets or passes for attendees.
- Passes should be simple and easy to validate.
- Use attendee numbers on passes.
- Provide separate pass types for speakers and organizers.
- Keep attendee passes distinct from speaker and organizer passes.

## Scanning Portal
- Create a view or portal for scanning passes.
- The scanner must validate access and enforce restrictions correctly.
- The scanning flow should be simple for staff to use.
- The portal should clearly show whether a pass is valid, restricted, or already used.
- The portal should support attendee passes, speaker passes, and organizer passes.

## Access Rules
- Attendee passes should only allow attendee-level access.
- Speaker passes should only allow speaker-level access.
- Organizer passes should only allow organizer-level access.
- Scanning should block access when the pass type does not match the allowed area.
- Keep the restriction logic strict and easy to follow.

## Output Expectations
- Conference creation should be simpler than the old booking flow.
- No attendee receipt charge for the conference fee.
- Printable passes should be generated for all relevant people.
- The scan portal should be usable at entry points.
- The implementation should remove the old, confusing conference booking structure.

## Done When
- Old conference booking is removed.
- Single attendee booking for conference is removed.
- Conference charge lives on the conference itself.
- Attendee receipts are not charged for the conference fee.
- Attendee, speaker, and organizer passes can be printed.
- A pass scanning portal exists.
- Access restrictions are enforced correctly.
