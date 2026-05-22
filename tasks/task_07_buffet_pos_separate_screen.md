# Task 07 — Buffet POS — Separate Screen & Sales Flow

Use this task as an agent prompt only. Do not include implementation examples or code blocks in the response.

## Goal
Create a dedicated buffet POS flow that is fully separate from the regular POS.

## Requirements
- Buffet must live on its own screen at `/buffet` and must not be mixed with regular menu items.
- Regular POS remains item-based: item selection, quantity, add to order, pay.
- Buffet POS is count-based: select session, enter adults, enter children, auto-calculate total, pay.
- Show only active buffet sessions that are valid for the current time.
- Each sale must snapshot the session prices at the time of sale.
- Never recalculate a completed sale using the current session price.
- Payment flow must collect payment method and amount tendered.
- Generate a receipt after payment.
- Buffet sales must be tracked separately from regular orders.

## UI Expectations
- Provide a dedicated buffet screen with a clear way to return to the regular POS.
- Allow session selection from active buffet sessions only.
- Use adults and children counts with plus and minus controls.
- Update the live total immediately when session or counts change.
- Show the adult total, child total, and grand total.

## Navigation
- Add a sidebar link for Regular POS.
- Add a sidebar link for Buffet POS.

## Files Expected
- Controller for buffet POS flow
- Buffet sale model
- Buffet sale item model
- Database migration for buffet sales
- Database migration for buffet sale items
- Buffet POS view
- Buffet payment view
- Buffet receipt view

## Done When
- Buffet POS works independently from `/pos`.
- Active sessions are filtered correctly.
- Adults and children counts drive the total.
- Price snapshots are saved at sale time.
- Payment and receipt flow work end to end.
- Sidebar contains both POS links.
- Buffet sales are stored separately from regular orders.
