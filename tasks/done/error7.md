# ParseError - Internal Server Error

syntax error, unexpected end of file

PHP 8.2.12
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - resources\views\dashboards\manager.blade.php:535
1 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:124
2 - vendor\laravel\framework\src\Illuminate\View\Engines\PhpEngine.php:57
3 - vendor\laravel\framework\src\Illuminate\View\Engines\CompilerEngine.php:76
4 - vendor\laravel\framework\src\Illuminate\View\View.php:208
5 - vendor\laravel\framework\src\Illuminate\View\View.php:191
6 - vendor\laravel\framework\src\Illuminate\View\View.php:160
7 - vendor\laravel\framework\src\Illuminate\Http\Response.php:78
8 - vendor\laravel\framework\src\Illuminate\Http\Response.php:34
9 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:939
10 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:906
11 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
12 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
13 - app\Http\Middleware\SetLocale.php:31
14 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
15 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:50
16 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
17 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
18 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
19 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken.php:87
20 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
21 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
22 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
23 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
24 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
25 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
26 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
27 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
28 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
29 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
30 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
31 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
32 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
33 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
34 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
35 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
36 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
37 - app\Http\Middleware\SecurityHeaders.php:18
38 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
39 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
40 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
41 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
42 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
43 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
44 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
45 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
46 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
47 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
48 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
49 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
50 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
51 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
52 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
53 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
54 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
55 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:26
56 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
57 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
58 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
59 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
60 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
61 - public\index.php:20
62 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23

## Request

GET /dashboard

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/login
* **connection**: keep-alive
* **cookie**: laravel-session=eyJpdiI6IlEway9XNGtYK2hGejIrbjNmQmhBV1E9PSIsInZhbHVlIjoiV1Jxa0F3Z2dEUExCak4xYnl1SlJCOUJVRndOeU5VRTZ6Y3F6dkdOK2IwOUZEK1IvNEhTdE1jVExDTXdNbytBQkkxOU8wWDZjcFhJNFM4dlU4ZVpkUVEwNmlvY2VWOVdrZWhkbHRzVk1tR2xMS1VSNXVBdGtJSTluS01aSWM5OVEiLCJtYWMiOiJiMjM0ZDMzYThlYTA1NTQzZWQyMWIyNjZiNmRlNmNiODkxNWQ2NzBiNDkyNTQxNDEyODBlYmI5NDVhM2IyNDMyIiwidGFnIjoiIn0%3D; XSRF-TOKEN=eyJpdiI6IjdQVEkwTThzb2VsZ09uOG9Ra2hlTEE9PSIsInZhbHVlIjoiUlluVUk4R0NnZGNRYW94aFE4SnpVWi9NcmQ3NW4rTkt2M2Foa1BjSDdBd3VQbG9KRVNwc2lJODZQMjZRVmJrK2xCNGdrY05namxqNVJPVnY2eEZ3bFVaaTlVc2ZoTi95d2syTlc2bmIwYU1nWXNweE9HdzV3Qm9Cbmx0K3lFd1oiLCJtYWMiOiIxNjc1NTBjNzA3OGU1ZDAzMjY1ODI1NTViNzUzMmE3Mjg4MDE2MDk3YmYwNGQyMTc0YTc5MjA2ZWE2MjY5MDY0IiwidGFnIjoiIn0%3D; remember_web_59ba36addc2b2f9401580f014c7f58ea4e30989d=eyJpdiI6ImhUbnRMYjlLaTN6OE10TUhmQ0h4bnc9PSIsInZhbHVlIjoidjB3dkwyL1JxVmtJeS9teHlGVGJqSnJEWDZzWitSWDM4Qkk4cXlsSFl0WFA4ZzZOVk5SUHBEZjNCQjkrK1ZyZlVTTWVJVnpONlFNbit6eTUvRUY3UUwzamR4Y3JnTnJYbXVqWFpEa1BXbm42blp5QzFWcE15Y2ZtTE5nZnZveWV5OFZ6V2tCb3hYZW14WDl5dUZoWDhjVWh0WVk1UzBraVkxMzJKZXA3ZllPWDdqM1QybllTb0Y0djkzbXVCRDBtOVdRbXVOODdDQkZMSXJOcmVYNkxBL0dtYStkazNmOGdacE9oRFdhT2E0ZzF1YjU0eXdJUmxtYWJvc1IxMUVYVHJ5alB0dytQV2RNYU1zZHdvZzdQN3c9PSIsIm1hYyI6ImVhMGViYTE5ODA2ZTE1NzRjYjhjYWYyYWFkNDAwNTY3NzA0YTc2MmUwNjNiYjA2ODMyOGQ3NDJhMDY3Y2E5OWIiLCJ0YWciOiIifQ%3D%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\DashboardController@index
route name: dashboard
middleware: web, auth

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = 'z8WFjvuvERtiOoyICj2q0Cts5KVHX7QVYW9iiY0P' limit 1 (10.52 ms)
* sqlite - select * from "users" where "id" = '9638fcd4-d29e-4a8a-af67-dc1192692b1d' limit 1 (0.47 ms)
* sqlite - select * from "roles" where "roles"."id" = '50d9df04-bf91-4870-83de-ba4dec3a6a4b' limit 1 (0.38 ms)
* sqlite - select count(*) as aggregate from "buildings" (0.37 ms)
* sqlite - select count(*) as aggregate from "rooms" (0.4 ms)
* sqlite - select count(*) as aggregate from "rooms" where "is_active" = 1 (0.51 ms)
* sqlite - select count(*) as aggregate from "users" where "is_active" = 1 (0.32 ms)
* sqlite - select count(*) as aggregate from "rooms" where "status" = 'occupied' (0.47 ms)
* sqlite - select count(*) as aggregate from "rooms" where "status" = 'available' and "is_active" = 1 (0.42 ms)
* sqlite - select count(*) as aggregate from "rooms" where "status" = 'reserved' (0.38 ms)
* sqlite - select count(*) as aggregate from "rooms" where "status" = 'dirty' (0.49 ms)
* sqlite - select count(*) as aggregate from "reservations" where strftime('%Y-%m-%d', "check_in_date") = cast('2026-04-13' as text) and "status" in ('confirmed', 'pending') (0.5 ms)
* sqlite - select count(*) as aggregate from "bookings" where strftime('%Y-%m-%d', "check_out_date") = cast('2026-04-13' as text) and "status" = 'checked_in' (0.42 ms)
* sqlite - select count(*) as aggregate from "reservations" (0.32 ms)
* sqlite - select count(*) as aggregate from "reservations" where "status" = 'pending' (0.44 ms)
* sqlite - select count(*) as aggregate from "bookings" where "status" = 'checked_in' (0.31 ms)
* sqlite - select sum("total_amount") as aggregate from "bookings" where strftime('%Y-%m-%d', "created_at") = cast('2026-04-13' as text) and "status" in ('checked_in', 'checked_out') (0.42 ms)
* sqlite - select sum("total_amount") as aggregate from "bookings" where "check_in_date" between '2026-04-13 00:00:00' and '2026-04-19 23:59:59' and "status" in ('checked_in', 'checked_out') (0.49 ms)
* sqlite - select sum("total_amount") as aggregate from "bookings" where strftime('%m', "check_in_date") = cast('04' as text) and strftime('%Y', "check_in_date") = cast(2026 as text) and "status" in ('checked_in', 'checked_out') (0.42 ms)
* sqlite - select sum("total_amount") as aggregate from "bookings" where "status" in ('checked_in', 'checked_out') (0.35 ms)
* sqlite - select count(*) as aggregate from "internal_usage_requests" where "status" = 'pending' (0.43 ms)
* sqlite - select "status", count(*) as count from "rooms" where "is_active" = 1 group by "status" (0.43 ms)
* sqlite - select "status", count(*) as count from "reservations" group by "status" (0.47 ms)
* sqlite - select * from "reservations" order by "created_at" desc limit 10 (0.49 ms)
* sqlite - select * from "rooms" where "rooms"."id" in ('43966e43-5ee8-43b1-b9bf-9a33cedcfcca', '7b43dbf6-02cf-497c-ae39-5b8f736d37b2', '8da67b8d-b520-402b-a96c-dfea93990705') (0.72 ms)
* sqlite - select * from "users" where "users"."id" in ('efcdd719-57e9-4015-82c6-04f8db24eac6') (0.42 ms)
* sqlite - select "buildings".*, (select count(*) from "floors" where "buildings"."id" = "floors"."building_id") as "floors_count", (select count(*) from "rooms" inner join "floors" on "floors"."id" = "rooms"."floor_id" where "buildings"."id" = "floors"."building_id") as "rooms_count" from "buildings" (0.59 ms)
* sqlite - select * from "users" where "is_active" = 1 (0.37 ms)
* sqlite - select * from "roles" where "roles"."id" in ('3378b278-fefd-4551-a2ac-164d23949d24', '451c3da4-5c93-45c3-80a5-f4989d217673', '50d9df04-bf91-4870-83de-ba4dec3a6a4b', '834794bf-3d0d-4d79-96e3-305fad5b7839', 'ba5ef85c-54c9-49b2-a2d8-1cbedf3559f4', 'd857b1ff-6361-4b7b-88aa-cfaad7260169', 'e89fd0bc-1454-49a7-a10e-1280958fa30e', 'edc84afd-bb55-484d-9fbb-9939f1c771eb') (0.5 ms)
* sqlite - select * from "internal_usage_requests" where "status" = 'pending' order by "created_at" desc limit 10 (0.48 ms)
* sqlite - select * from "local_purchase_orders" where "status" = 'pending_approval' order by "order_date" desc limit 6 (0.55 ms)
* sqlite - select * from "suppliers" where "suppliers"."id" in ('6d8b3ccb-1c32-4b22-9b6c-ab04a645529a') (0.5 ms)
* sqlite - select * from "users" where "users"."id" in ('bb89326f-2556-41e5-9079-17822a6f1705') (0.48 ms)
* sqlite - select "stock_levels".* from "stock_levels" inner join "products" on "stock_levels"."product_id" = "products"."id" where "products"."is_active" = 1 and ("stock_levels"."quantity" <= 0 or "stock_levels"."quantity" <= "products"."reorder_level") order by "stock_levels"."quantity" asc limit 8 (0.65 ms)
* sqlite - select * from "products" where "products"."id" in ('e7448160-e47e-47bd-9f82-6a37fcccf312') (0.45 ms)
* sqlite - select * from "stock_locations" where "stock_locations"."id" in ('09b2f008-0110-4a39-b2e0-e3548c12f2f1', '3b77c6eb-f9de-4630-9760-d57691205007') (0.55 ms)
* sqlite - select * from "stock_movements" order by "created_at" desc limit 8 (0.56 ms)
* sqlite - select * from "products" where "products"."id" in ('e7448160-e47e-47bd-9f82-6a37fcccf312') (0.44 ms)
* sqlite - select * from "stock_locations" where "stock_locations"."id" in ('4a9d6e95-7d03-4a10-91cd-43ad365de003') (0.45 ms)
* sqlite - select * from "users" where "users"."id" in ('7fedd150-7d40-4c1b-ac78-ffb9b67bbf8e', 'bb89326f-2556-41e5-9079-17822a6f1705') (0.48 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.3 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-system_currency', 'laravel-cache-illuminate:cache:flexible:created:system_currency') and "expiration" <= 1776092357 (8.93 ms)
* sqlite - select "value" from "system_settings" where "key" = 'default_currency' limit 1 (0.47 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1776092417, 'laravel-cache-system_currency', 's:3:"TZS";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (10.65 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.31 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.28 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-tzs_exchange_rate', 'laravel-cache-illuminate:cache:flexible:created:tzs_exchange_rate') and "expiration" <= 1776092357 (6.82 ms)
* sqlite - select "value" from "system_settings" where "key" = 'tzs_exchange_rate' limit 1 (0.37 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1776092417, 'laravel-cache-tzs_exchange_rate', 's:4:"2500";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (9.13 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.37 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.32 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.37 ms)
