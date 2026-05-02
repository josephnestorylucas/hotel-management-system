Task: Centralized Bar POS With Sidebar Entry and Unified Guest/Booking Binding

Objective:
Recreate and modify the current Bar UI into a centralized POS entry that appears in the sidebar for bartenders. The POS should handle walk-in sales as the primary flow, support creating bar sales directly, and allow binding a sale to a booking or in-house guest from one place.

This task must:
- Add a dedicated Bar POS entry in the sidebar for bartender role(s)
- Provide a centralized POS screen for conducting bar sales
- Recreate and modernize the POS UI to replace current bar sale entry screens
- Create new POS views and remove the separate walk-in views (merge into one flow)
- Support walk-in sales as the default/primary flow
- Allow creating bar sales directly from the POS
- Provide one unified option to bind a sale to a booking or in-house guest
- Reuse existing bar sales, booking, and guest data sources
- Provide clear UI states for draft vs completed sales
- Support fast keyboard/mouse flow for counter use
- Ensure totals and taxes are always visible and updated live
- Keep print/receipt behavior consistent with existing bar flows
- Reuse existing app layouts and styling tokens (no new visual system)
- Ensure the new POS screens match current theme and component styles

Do not break existing bar sales flows.
Do not change pricing or tax logic without approval.
Do not add new dependencies without approval.

---

### 1) Discovery First: Find Existing Bar Sales and POS Patterns

Before implementation, identify:
- Current bar sales creation screens and controllers
- Existing POS or sales screens in other modules
- How walk-in sales are handled today (if any)
- How bookings and in-house guests are referenced for billing
- Sidebar/menu definitions and role-based visibility logic
- Any existing walk-in-specific views and routes that should be retired
- Any existing JS components or Livewire/Alpine patterns used for POS-like screens
- Current receipt/print flow for bar sales
- Existing stock deduction and accounting hooks on bar sales

Implementation rule:
- Follow current architecture and naming conventions
- Avoid duplicating existing POS components

---

### 2) Sidebar Entry and Access Control

Required behavior:
- Add a Bar POS link in the sidebar for bartender role(s)
- Restrict access to authorized roles
- Ensure other roles do not see the POS entry unless already allowed
- Keep URLs protected with server-side authorization

---

### 3) Centralized POS Screen

Required behavior:
- Provide a single POS screen for bar sales
- Support selecting products, quantities, and pricing
- Support primary walk-in sales flow with minimal steps
- Keep the UI fast and optimized for counter use
- Replace old bar sale entry UI with the new POS screen
- Include a searchable product list with categories (if existing)
- Provide cart-style line items with qty +/- controls
- Show per-line subtotal and overall totals
- Show tax/discount lines only if used in the current bar flow
- Include cashier/bartender name and timestamp in the POS header
- Provide clear action buttons: Save Draft, Complete Sale, Print

---

### 4) Walk-In Sales (Primary Flow)

Required behavior:
- Walk-in sale is the default selection
- Walk-in customers can proceed without booking or guest binding
- Sales are still recorded with cashier/bartender and timestamp
- Remove separate walk-in views and routes once the new POS flow is live
- The walk-in path must not require extra fields beyond existing mandatory data

---

### 5) Unified Binding to Booking or In-House Guest

Required behavior:
- One unified control to bind a sale to either a booking or an in-house guest
- Allow searching or selecting the target
- Ensure only valid/active bookings or guests are selectable
- Store the binding in the existing bar sale record structure
- Allow clearing or changing the binding before completing a sale
- Display selected booking/guest details (name, room, booking code) in the POS

---

### 6) Data and Integration

Required behavior:
- Reuse existing product, pricing, and tax data
- Reuse existing booking/guest lookup APIs or models
- Ensure sales still integrate with accounting and inventory logic
- Preserve existing bar sale numbering and audit logging
- Ensure stock deductions match existing behavior

---

### 7) Routing and Views

Required behavior:
- Create a dedicated POS route and controller action for Bar POS
- Create new POS views under the bar module views path
- Retire old walk-in routes/views once the new POS is live
- Update any menu links or redirects to point to the new POS

Implementation rule:
- Keep route names consistent with current naming patterns

---

### 8) UI Migration Plan (No Breaks)

Required behavior:
- Introduce new POS UI alongside existing screens until validated
- Switch sidebar entry to the new POS only after validation
- Remove or archive the old walk-in views and routes when confirmed
- Ensure no broken links remain in bar module pages

---

### 7) Testing and Manual Acceptance Criteria

Validate end-to-end:

1) Bartender sees Bar POS in the sidebar
2) POS allows creating a walk-in sale quickly
3) Sale can be bound to booking or in-house guest from one place
4) Sales totals match existing pricing and tax rules
5) Old walk-in views are removed and replaced by the new POS flow
6) Existing bar sales logic remains intact
7) Stock deductions and accounting entries are unchanged
8) Print/receipt output matches existing bar requirements
9) Access control prevents unauthorized POS access

---

Expected Outcome:

- A centralized Bar POS accessible from the sidebar
- Walk-in sales flow is the default and fastest path
- Unified binding to booking or in-house guest
- No regressions in bar sales or accounting

Priority:
HIGH - Bar operations speed and clarity
