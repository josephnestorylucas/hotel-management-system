# Task 04 — Fix Menu Selection & Remove Mixed Elements

## Problem
The POS menu selection screen is not organized clearly enough for daily cashier use. Items from different categories and service types are currently appearing together in one mixed view, which makes the screen hard to scan and creates a risk of selecting the wrong product during checkout. Regular items, buffet items, bar items, and kitchen items should not all be treated as if they belong to the same display path. The experience needs to be made cleaner, more predictable, and easier to operate quickly during busy service periods.

The goal of this task is to separate the menu experience into the correct logical sections, make the regular POS grid display only the items that belong there, and ensure that item options are handled in a clear follow-up interaction instead of being mixed into the initial selection grid. This prompt should guide a careful cleanup of the menu query, the display structure, and the supporting item logic so that the screen no longer shows confusing or duplicate entries.

---

## Expected Behavior
- Menu items must be displayed in a clean category-based layout so that each category is visually separated from the next and the cashier can move through the list without guessing where one group ends and another begins.
- Buffet items must never appear in the regular POS menu grid. They need their own dedicated screen or dedicated flow and should be completely excluded from the standard selection interface.
- Bar items and kitchen items must be filtered according to the current POS context so that the user only sees items relevant to the station or workflow they are working with.
- If a menu item has selectable options, those options must appear in a popup or modal after the item is clicked, rather than being shown alongside unrelated items in the main grid.
- The UI must not show duplicate items, ghost items, partially loaded elements, or stale cards left over from another category or filter state.
- The overall menu interaction should feel stable and intentional, with no mixing between item types and no ambiguity about which items are available at a given moment.

---

## Root Cause Areas to Check

1. The category filter may not be applied early enough in the menu query, which can cause items from different groups to be loaded into the same list.
2. Inactive items may still be passing through the query or the view layer, which would explain why products marked as unavailable are still visible to the cashier.
3. Buffet items may be included in the normal POS item set instead of being isolated into their own dedicated screen or workflow.
4. The time-based availability window may not be enforced consistently, allowing items to show even when they should be hidden outside their active hours.
5. The UI may not be distinguishing properly between normal items, option-based items, and buffet items, which would make the screen behave as if everything is the same kind of product.
6. The menu rendering may be reusing stale client-side state or old panel content, which can produce duplicate cards or ghost elements when switching categories.

---

## Fix Checklist

### Step 1 — Fix Menu Query (Controller)

The controller logic that builds the regular POS menu must be tightened so that only the correct records are sent to the view. The menu collection should be built from active categories only, and each category should only include active items that are valid for the regular POS flow. Buffet products must be excluded at the query level, not only hidden in the template, so they never become part of the standard grid by mistake.

The availability window also needs to be respected consistently. If an item has defined active hours, the query should only include it when the current time falls inside that valid range. Items that are not active right now should not appear just because they belong to a category or were previously cached. Categories that end up with no visible items after filtering should be removed from the final result so the UI stays compact and does not show empty sections.

- [ ] Inactive items are excluded before rendering.
- [ ] Buffet items are excluded from the regular POS flow.
- [ ] Time-restricted items only appear within their valid window.
- [ ] Empty categories are hidden from the screen.
- [ ] Items remain sorted in a consistent, readable order inside each category.
- [ ] The query should be predictable enough that the view layer only handles presentation, not business filtering.

---

### Step 2 — Fix POS Category Tabs UI

The POS screen should present categories as a clear tabbed or grouped interface, with only one category visible at a time unless the design intentionally supports a broader multi-panel layout. The important part is that each category remains visually isolated and that switching categories does not cause items to leak into neighboring sections. When a category is selected, only the items for that category should be visible.

Each menu item card should communicate the essential information quickly: item name, price, and image if available. The presentation should avoid clutter and should not mix buffet or other unrelated item types into the same list. If an item belongs to a special flow, it should not be shown in the regular grid unless the current screen is specifically designed for that flow.

- [ ] Category controls are visible and usable.
- [ ] Switching categories swaps the item display cleanly.
- [ ] Each card clearly shows the item name and price.
- [ ] Images appear only when available and should not break the layout.
- [ ] Buffet items are not shown in this screen.
- [ ] The category experience should feel deliberate, not like a loosely filtered dump of menu data.

---

### Step 3 — Options Popup (After Item Click)

Some menu items require an additional choice after the main card is selected. Those items should not be forced directly into the order without clarifying the chosen variant. Instead, clicking the item should open a modal or popup that presents the available options in a simple, readable list. This keeps the main grid clean and avoids cluttering the selection screen with secondary choices.

The option experience should explain the item clearly, show the option labels in a usable order, and make it obvious how each option affects the final price. Once the cashier selects a variant, the item should be added to the order with the chosen option attached, and the modal should close automatically. Items that do not have any options should continue to add directly to the order without forcing the cashier through an extra step.

- [ ] Clicking an item with options opens a popup.
- [ ] The popup shows only valid options for that item.
- [ ] Each option clearly displays the final price impact.
- [ ] Selecting an option adds the item to the order and closes the popup.
- [ ] Items without options skip the popup and add immediately.
- [ ] The modal should not leak stale option data from a previously selected item.

---

### Step 4 — Options API Endpoint

The backend needs a dedicated endpoint for loading the options that belong to a selected menu item. This endpoint should return a clean JSON response containing only the fields required for rendering the popup. Options that are inactive should be excluded so the cashier never sees something that should no longer be available.

The response should be lightweight, predictable, and easy for the frontend to consume without extra transformation. The intention is to keep the item click interaction fast while still allowing the modal to build a complete and correct option list.

- [ ] The route is defined and reachable from the POS screen.
- [ ] The response is JSON and contains only the data the UI needs.
- [ ] Only active options are returned.
- [ ] Each option includes a stable identifier, a readable name, and a price adjustment value.
- [ ] The endpoint should not include unrelated item data or hidden fields.

---

### Step 5 — Remove Buffet from Regular Menu Grid

Buffet items need explicit handling in the data model so that they can be separated from the standard menu experience. The regular POS grid should treat buffet as a distinct type of menu content, not just another label or category. If the buffet flag does not already exist on the item model or database table, it should be introduced in a way that supports clear filtering and future maintenance.

Once the buffet flag exists, any regular POS query must deliberately exclude buffet items. This is important because the UI alone is not a reliable safeguard if the underlying data set is already mixed. The buffet screen can then operate as its own dedicated flow without competing with the regular menu.

- [ ] The data model includes a buffet indicator.
- [ ] Buffet items are marked correctly in the database.
- [ ] The main POS menu excludes all buffet items.
- [ ] Buffet content is handled in its own screen or flow.
- [ ] Regular item selection remains strictly separate from buffet selection.

---

### Step 6 — Time-Based Availability Enforcement

Some menu items are only meant to appear during particular hours of the day. That logic should be enforced consistently so the POS only shows items that are actually available at the moment the cashier is taking the order. The availability rules should be part of the item query rather than a visual-only condition in the template.

If an item has no time restriction, it should still appear normally. If it does have a defined start and end time, then it should only be included when the current time falls inside that valid range. This prevents stale or unavailable items from appearing as selectable options and keeps the screen aligned with operational reality.

- [ ] A reusable availability scope or equivalent filter exists on the item model.
- [ ] The POS query uses that availability logic instead of duplicating it in multiple places.
- [ ] Items outside the valid time window do not appear in the menu.
- [ ] Items without time rules continue to behave normally.
- [ ] The screen should never show a product that the business rules say is currently unavailable.

---

## Files to Modify

| File | Change |
|------|--------|
| `app/Http/Controllers/PosController.php` | Tighten the menu query so only the correct items are loaded for the regular POS screen. |
| `resources/views/pos/index.blade.php` | Rework the category presentation so the grid is clearly separated and easier to navigate. |
| `resources/views/pos/partials/option-modal.blade.php` | Provide the popup used when an item needs the cashier to choose among variants or modifiers. |
| `public/js/pos.js` | Handle item clicks, option loading, and selection flow in the frontend. |
| `app/Models/MenuItem.php` | Add the time-availability behavior used to decide whether an item is currently visible. |
| `routes/web.php` | Register the endpoint that returns item options for the popup. |
| `database/migrations/...` | Add or confirm the buffet flag if the schema does not already support it. |

---

## Done When
- [ ] Category tabs are visible and switching between them updates the item list correctly.
- [ ] Only active items that are valid for the current time appear on the regular POS screen.
- [ ] Buffet items do not appear in the regular POS grid at all.
- [ ] Items that require a choice open a popup instead of being added immediately.
- [ ] Items without choices are added directly without unnecessary extra steps.
- [ ] No mixed, duplicate, or ghost elements remain visible anywhere in the menu area.
- [ ] The cashier can find items quickly because the screen is clearly grouped and easy to scan.
- [ ] The final implementation feels like a deliberate POS workflow rather than a partially filtered list of menu records.
