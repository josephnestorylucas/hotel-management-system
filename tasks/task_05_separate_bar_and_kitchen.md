# Task 05 — Separate Bar and Kitchen Modules

Use this task as an agent prompt only. Do not include implementation examples or code blocks in the response.

## Goal
Separate bar and kitchen completely so they use different queues, different screens, different item destinations, and different staff access.

## Core Requirement
- Menu items must be routed by destination.
- Kitchen items go to the kitchen queue and kitchen screen.
- Bar items go to the bar queue and bar screen.
- Kitchen staff must never see bar tickets.
- Bartenders must never see kitchen tickets.
- Mixed orders must split correctly into separate kitchen and bar tickets.

## Rules
- Do not create any new user roles.
- Reuse the existing roles already in the system.
- Use role-based access to restrict the screens.
- Supervisor and admin can see both sides if the app already supports that pattern.
- Keep the separation strict end to end.

## UI Expectations
- Add a clear destination choice to menu item forms so items can be marked for kitchen or bar.
- Show only the correct tickets on each screen.
- Kitchen and bar screens must use different visual treatment if needed, but the main requirement is functional separation.
- The bar queue should be distinct from the kitchen queue.
- The bar tabs screen should remain separate if it already exists in the app.

## Dispatch Expectations
- When an order is confirmed, split its items by destination.
- Create a kitchen ticket only for kitchen items.
- Create a bar ticket only for bar items.
- Preserve item details, quantities, notes, and timestamps.
- Keep the order history tied to the originating order.

## Files Expected
- Migration to add destination to menu items
- Menu item model update for destination
- Kitchen ticket model
- Bar ticket model
- Kitchen controller
- Bar controller
- Order dispatch service
- Kitchen queue view
- Bar queue view
- Sidebar updates for role-based links
- Route updates for kitchen and bar screens

## Done When
- Menu items have a destination field.
- Bar items are assigned to bar.
- Food items are assigned to kitchen.
- Kitchen tickets contain only kitchen items.
- Bar tickets contain only bar items.
- Kitchen staff can access only kitchen screens.
- Bartenders can access only bar screens.
- No new roles were added.
- The order split works correctly for mixed orders.
