You are a senior Laravel architect working inside a production Laravel + Blade system.

Your task is to implement a FULL bilingual localization system (English + Swahili) across the entire application.

========================================
CORE GOAL
========================================
- The system must support:
  - English (EN)
  - Swahili (SW)
- Users must be able to switch language from the UI
- ALL views must support translation

========================================
CRITICAL RULE (NO HARD TEXT)
========================================
- REMOVE all hardcoded text from Blade views
- Replace with Laravel translation system:
  - __('text.key')

========================================
STEP 1: SETUP LOCALIZATION SYSTEM
========================================

1. Ensure Laravel localization is properly configured:
   - Default locale: 'en'
   - Add Swahili locale: 'sw'

2. Create language files:
   - resources/lang/en/
   - resources/lang/sw/

3. Organize translations:
   Example files:
   - messages.php
   - dashboard.php
   - bookings.php
   - auth.php
   - navigation.php

========================================
STEP 2: TRANSLATE ALL VIEWS
========================================

1. Scan ALL Blade files:
   - layouts/
   - dashboards/
   - modules (bookings, guests, etc.)

2. Replace text:
   Example:
     BEFORE:
       <h1>Welcome back</h1>

     AFTER:
       <h1>{{ __('dashboard.welcome') }}</h1>

3. Add corresponding translations:
   en/dashboard.php:
     'welcome' => 'Welcome back'

   sw/dashboard.php:
     'welcome' => 'Karibu tena'

========================================
STEP 3: LANGUAGE SWITCH BUTTON (UI)
========================================

1. Add language toggle in main layout:
   - File: layouts/app.blade.php

2. UI Requirements:
   - Two buttons or toggle:
     - EN (English)
     - SW (Swahili)

   Example:
     <a href="{{ route('lang.switch', 'en') }}">EN</a>
     <a href="{{ route('lang.switch', 'sw') }}">SW</a>

3. Style:
   - Must match existing UI design
   - Highlight active language

========================================
STEP 4: LANGUAGE SWITCH LOGIC
========================================

1. Create route:
   Route::get('/lang/{locale}', ...)

2. Controller logic:
   - Validate locale (en/sw only)
   - Store in session:
     session(['locale' => $locale])

3. Middleware:
   - Create or update middleware to:
     - Read session locale
     - Set app locale:
       app()->setLocale(session('locale', 'en'))

4. Register middleware globally

========================================
STEP 5: APPLY GLOBALLY
========================================

- Ensure ALL pages:
  - Respect selected language
  - Persist language across navigation

========================================
STEP 6: FORM + VALIDATION TRANSLATION
========================================

- Translate:
  - Form labels
  - Buttons
  - Validation messages

- Use:
  resources/lang/sw/validation.php

========================================
STEP 7: FALLBACK + SAFETY
========================================

- If translation missing:
  - Fallback to English

========================================
STEP 8: KEEP DESIGN INTACT
========================================

- DO NOT:
  - Break layouts
  - Change structure
  - Introduce new UI styles

- ONLY replace text content

========================================
OUTPUT FORMAT
========================================

1. Files created (lang files, middleware)
2. Files modified (Blade views)
3. Example translations (EN + SW)
4. Language switch implementation
5. Middleware explanation

========================================
GOAL
========================================

- Full bilingual system (EN + SW)
- Toggle button in layout
- Clean translated UI
- Works across ALL pages
- Feels native for Tanzanian users