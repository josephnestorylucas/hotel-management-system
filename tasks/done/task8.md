You are a senior Laravel architect working داخل a production Laravel + Blade system.

Your task is to restructure role responsibilities, enforce RBAC, and extend the UI for new role-based dashboards while strictly following the existing design system.

========================================
CRITICAL RULE (UI CONSISTENCY)
========================================
Before creating ANY new views:
- Analyze existing Blade layouts, components, and styles
- Reuse:
  - layouts (e.g., layouts/app.blade.php)
  - existing sidebar structure
  - UI classes (Tailwind/Bootstrap/custom)
- DO NOT introduce new design styles
- DO NOT create inconsistent UI

All new views MUST match the current system design.

========================================
ROLE RESTRUCTURING REQUIREMENTS
========================================

1. SYSTEM ADMINISTRATOR (RESTRICT)
REMOVE access to:
- Operations
- Reservations
- Bookings
- Guests
- Laundry Orders
- Hall Bookings
- Conferences

System Administrator should NOT:
- See these modules in UI
- Access routes/controllers

----------------------------------------

2. MANAGER ROLE (MAIN BUSINESS ROLE)
from  which  please   view  the  admin  sidebar  and   remove them  and transer them   to  the  manager  
TRANSFER the following modules TO Manager:
- Operations
- Reservations
- Bookings
- Guests
- Laundry Orders
- Hall Bookings
- Conference only  not  conference  hall 

Tasks:
- Ensure Manager has FULL access to these modules  and  they  are  in  the    manager s idbaer 
- Update routes, controllers, middleware accordingly

----------------------------------------

3. STORE MANAGER ROLE (PROCUREMENT)
TRANSFER:
- Procurement module → Store Manager

Tasks:
- Restrict Procurement access only to Store Manager
- Remove it from other roles (including System Admin)
remove  from the  sidebar  

========================================
DASHBOARD + SIDEBAR CREATION
========================================

1. MANAGER DASHBOARD
- Create dashboard view for Manager if not existing
- Include relevant data summaries (bookings, guests, etc.)
- MUST reuse existing dashboard design patterns

2. MANAGER SIDEBAR
- Create or modify sidebar to include:
  - Operations
  - Reservations
  - Bookings
  - Guests
  - Laundry Orders
  - Hall Bookings
  - Conferences

- Ensure:
  - Visible ONLY to Manager role

----------------------------------------

3. STORE MANAGER SIDEBAR
- Add Procurement menu item
- Visible ONLY to Store Manager

========================================
AUTHENTICATION / LOGIN
========================================

- Users already exist in the system
- Ensure:
  - Role-based login redirection works
    Example:
      - Manager → Manager Dashboard
      - Store Manager → Procurement section
      - System Admin → Admin dashboard (restricted)

- Fix any issues preventing login per role

========================================
BACKEND SECURITY (MANDATORY)
========================================

- Enforce access using:
  - Middleware / Gates / Policies

- Ensure:
  - Unauthorized roles get 403 or redirect
  - UI hiding is NOT the only protection

========================================
FILES TO CHECK/MODIFY
========================================

- routes/web.php
- Controllers (Bookings, Guests, etc.)
- Middleware (role checks)
- Blade layouts (layouts/app.blade.php)
- Sidebar/navigation partials
- Dashboard views
- Role/permission logic (custom or package)

========================================
CONSTRAINTS
========================================

- DO NOT break existing working features
- DO NOT introduce Vue/React
- Keep logic clean and minimal
- Follow Laravel best practices

========================================
OUTPUT FORMAT
========================================

1. Roles and permissions summary (before vs after)
2. Files modified
3. Dashboard + sidebar changes
4. Route/middleware updates
5. Any login/redirect fixes
6. Code diffs (where applicable)

========================================
GOAL
========================================

- System Admin = restricted (no operational modules)
- Manager = full business operations control
- Store Manager = procurement only
- Clean UI per role
- Secure backend enforcement
- Fully working role-based dashboards