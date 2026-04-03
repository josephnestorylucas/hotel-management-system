Refactor the notification system to eliminate inefficient polling of the `/notifications/unread-count` endpoint and improve overall performance and scalability.

Current problem:

* The frontend is sending repeated interval-based requests (~30 seconds) to fetch unread notification counts.
* This causes unnecessary load on the backend and database, especially as the number of users increases.

Required improvements:

1. Replace polling with a real-time solution:

   * Implement WebSockets (preferred) or Server-Sent Events (SSE) to push notification updates from the server to the client only when changes occur.

2. Backend optimization:

   * Cache unread notification counts using Redis or in-memory caching.
   * Avoid querying the database on every request.
   * Invalidate or update cache only when notifications are created or marked as read.

3. Frontend optimization (if polling must remain temporarily):

   * Increase polling interval to at least 2 minutes.
   * Stop polling when the browser tab is inactive.
   * Resume polling when the user becomes active again.

4. Ensure scalability:

   * The system should handle increased user load without a linear increase in request frequency.
   * Minimize redundant network calls and database queries.

Deliverables:

* Updated backend logic for efficient unread count retrieval
* Real-time notification delivery mechanism
* Optimized frontend logic to reduce unnecessary requests
