Task: Enforce Default Swahili Translations for All New Views

Objective:
Ensure that every newly created view in the system includes Swahili (sw) translations by default, matching the existing  structure used in other parts of the application.

Requirements:

1. Translation Enforcement

* Any new UI view/component/page MUST include both:

  * English (en)
  * Swahili (sw)
* No hardcoded strings are allowed in views.

2. Translation File Updates

* For every new view:

  * Add corresponding keys in:

    * en.json
    * sw.json
* Maintain consistent key naming conventions.

3. Fallback Handling

* If a Swahili translation is missing, the system should:

  * Log a warning
  * Fallback to English
* However, missing Swahili entries should be treated as a development error and fixed immediately.

4. Developer Workflow Enforcement

* Add a checklist or lint rule:

  * ❌ Block PRs that introduce new strings without Swahili translations
* Optional: Add CI validation to compare en.json vs sw.json keys.

5. Reusable Pattern

* Create or update a base template for new views that:

  * Uses translation hooks/functions (e.g., t('key'))
  * Includes placeholder translation keys for both languages

6. Refactoring (if needed)

* Audit existing views:

  * Identify any without Swahili translations
  * Add missing entries to sw.json

Expected Outcome:

* 100% of views support Swahili by default
* No untranslated UI text in production
* Consistent bilingual experience across the system

Notes:

* Prioritize consistency with the current i18n implementation
* Avoid introducing new translation patterns unless necessary
