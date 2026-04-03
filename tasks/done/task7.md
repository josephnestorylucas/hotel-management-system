You are a senior Laravel developer working داخل an existing Laravel + Blade codebase.

Your task is to fix multiple related issues in the Room Types and Booking functionality.

========================================
PROBLEM 1: IMAGE UPLOAD NOT WORKING
========================================
- The GD library is already enabled in php.ini
- However, when uploading room images, they are NOT displaying  and they  are  not  well  structured 

Your tasks:
1. Locate:
   - RoomType model, controller, and Blade views
   - Image upload handling logic (store/update methods)

2. Fix:
   - Ensure images are correctly:
     - Validated (image, mime types)
     - Stored using Laravel storage (e.g., storage/app/public or public folder)
     - Linked using `php artisan storage:link` if needed
   - Ensure correct usage of:
     - asset()
     - Storage::url()
   - Fix incorrect paths causing images not to render

3. Ensure:
   - Images persist after upload
   - Correct URL is saved in database
   - Images display properly in Blade views

========================================
PROBLEM 2: REMOVE GENERIC CDN IMAGES
========================================
- The system currently uses placeholder/generic images from CDNs

Your tasks:
1. Find all usages of:
   - External image URLs (CDNs, placeholders)
2. Replace them with:
   - Locally uploaded images from storage
3. Add fallback logic:
   - If no image exists → show a local default placeholder (NOT CDN)

========================================
PROBLEM 3: BOOKING FILTER NOT WORKING (PUBLIC ROUTE)
========================================
- Booking page filtering is broken
- Users cannot see preferred room choices based on filters  all  they  see  is  the  all  rooms  avalaible  

Your tasks:
1. Locate:
   - Public booking route/controller
   - Filtering logic (likely query builder or request filters)

2. Fix:
   - Ensure filters (e.g., room type, price, capacity, availability) are:
     - Properly read from request
     - Applied to database queries correctly
   - Fix issues like:
     - Missing `where` conditions
     - Incorrect variable names
     - Filters not being passed from Blade forms

3. Ensure:
   - Filtering updates results dynamically on form submit
   - Only matching rooms are displayed

========================================
CONSTRAINTS
========================================
- Use Laravel best practices
- Do NOT introduce new frameworks (no Vue/React)
- Keep logic clean and minimal
- Modify ONLY relevant:
  - Controllers
  - Models (if needed)
  - Blade views
  - Routes (if needed)

========================================
OUTPUT FORMAT
========================================
1. List of files modified
2. Clear explanation per problem (1, 2, 3)
3. Code changes (diff-style preferred)
4. Any commands needed (e.g., storage:link)

========================================
GOAL
========================================
- Uploaded room images must display correctly
- No external CDN images should be used
- Booking filters must work and show correct results