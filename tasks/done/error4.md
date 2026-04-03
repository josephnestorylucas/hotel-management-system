# TypeError - Internal Server Error

App\Events\NotificationCreated::__construct(): Argument #1 ($userId) must be of type int, string given, called in C:\Users\DADY\Desktop\projects\hotel-management-system\app\Services\NotificationService.php on line 94

PHP 8.2.12
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - app\Events\NotificationCreated.php:24
1 - app\Services\NotificationService.php:94
2 - app\Services\NotificationService.php:146
3 - app\Http\Controllers\Laundry\LaundryOrderController.php:139
4 - vendor\laravel\framework\src\Illuminate\Routing\ControllerDispatcher.php:46
5 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:265
6 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:211
7 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:822
8 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
9 - app\Http\Middleware\RoleMiddleware.php:34
10 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
11 - app\Http\Middleware\SetLocale.php:31
12 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
13 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:50
14 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
15 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
16 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
17 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken.php:87
18 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
19 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
20 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
21 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
22 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
23 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
24 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
25 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
26 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
27 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
28 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
29 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
30 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
31 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
32 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
33 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
34 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
35 - app\Http\Middleware\SecurityHeaders.php:18
36 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
37 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
38 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
39 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
40 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
41 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
42 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
43 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
44 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
45 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
46 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
47 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
48 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
49 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
50 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
51 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
52 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
53 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:26
54 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
55 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
56 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
57 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
58 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
59 - public\index.php:20
60 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23

## Request

POST /laundry/orders

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/laundry/orders/create
* **content-type**: application/x-www-form-urlencoded
* **content-length**: 689
* **origin**: http://127.0.0.1:8000
* **connection**: keep-alive
* **cookie**: XSRF-TOKEN=eyJpdiI6Ikl3enYvaFNXZVo1NDIrWTA2ems5T0E9PSIsInZhbHVlIjoidHhqc0dRb0Q2QUpid1FBNGhUVGY2WXkxcmwyT25KYjZJQi96eGtMaGk2V3VNdXZSaHVlSTFTSlU5UlF4dWxxYlJkL3BFaUZxdFlRLy8xMkxCRUkrSW94cTVpRTZIS1A1RkFHTXZvTkhzakRTamEvZFdlL0lmdHU3ZERTVkZ0N0giLCJtYWMiOiJlZDZjNzMxZGJhYmRlNTllNTNkYTNkZmRkOTFiOTBlNzg5ZDNmYTZiZDE4YjFiZjgyNjUxYWEwYzU3YTE5MjRjIiwidGFnIjoiIn0%3D; laravel-session=eyJpdiI6Img2eC83Vk84LzkxZjJXZVd0b0Q4Ymc9PSIsInZhbHVlIjoiRHIzcEQ3djNoNUl0SStvZ2NKTXlUcjRHZEhCQjc0dE00dTBFeXFNRnY3T0xreGY1VTBFTGUzeHVrUG5ZYk5WL2UrSHJRTGtoQzZNQ0F2VGpuTFNieHdSWnF6d2lXdnJYb2JsY0J5VXRNS0p6eDdmdHlFeXZXai9NeDVRcWM2TFoiLCJtYWMiOiJjY2VjYWY1ZWMyYzYwY2RiNWI1ZWJmN2QxMWQwYmFkZGU2MDQxZDdmYzlkNGU2ZGFmYzA2YjIxYWRlYzllNDIxIiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\Laundry\LaundryOrderController@store
route name: laundry.orders.store
middleware: web, auth, role:house_help,front_desk,supervisor,laundry_manager,manager

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = 'iNp6NBtua4yryQwFwpgUFwu8OdTR8kC2jAAFOGMv' limit 1 (10.35 ms)
* sqlite - select * from "users" where "id" = '01c5525b-1ea0-4a0f-9cd9-293a70e13574' limit 1 (0.54 ms)
* sqlite - select * from "roles" where "roles"."id" = 'b45852e1-5af0-413e-87be-65926dc42e0a' limit 1 (0.37 ms)
* sqlite - select count(*) as aggregate from "laundry_service_items" where "id" = '3ccccb7d-8b3f-4935-99f0-0613846934f3' (0.6 ms)
* sqlite - select count(*) as aggregate from "laundry_service_items" where "id" = '580a65b9-2f7e-4b88-922e-9a7cf6c80016' (0.55 ms)
* sqlite - select count(*) as aggregate from "laundry_service_items" where "id" = '4c97859d-bfc6-4836-9f01-c5f92ca7af32' (0.56 ms)
* sqlite - select count(*) as aggregate from "laundry_service_items" where "id" = '92dc878b-807e-42e0-a755-6630927944f6' (0.44 ms)
* sqlite - select * from "bookings" where "bookings"."id" = '194badd1-28cd-409e-b030-52e77b437914' limit 1 (0.65 ms)
* sqlite - select * from "rooms" where "rooms"."id" in ('5a280237-2b22-4d18-ae8e-e9af5eb03161') (0.58 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '3ccccb7d-8b3f-4935-99f0-0613846934f3' limit 1 (0.72 ms)
* sqlite - select * from "laundry_services" where "laundry_services"."id" in ('f7f3747c-151b-4dc2-ab3b-1fb21b3906cf') (0.26 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '580a65b9-2f7e-4b88-922e-9a7cf6c80016' limit 1 (0.15 ms)
* sqlite - select * from "laundry_services" where "laundry_services"."id" in ('f7f3747c-151b-4dc2-ab3b-1fb21b3906cf') (0.1 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '4c97859d-bfc6-4836-9f01-c5f92ca7af32' limit 1 (0.1 ms)
* sqlite - select * from "laundry_services" where "laundry_services"."id" in ('f7f3747c-151b-4dc2-ab3b-1fb21b3906cf') (0.11 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '92dc878b-807e-42e0-a755-6630927944f6' limit 1 (0.18 ms)
* sqlite - select * from "laundry_services" where "laundry_services"."id" in ('e8ffb097-a434-4a1e-9740-9056acfa99d2') (0.13 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where strftime('%Y-%m-%d', "created_at") = cast('2026-04-02' as text) (0.22 ms)
* sqlite - insert into "laundry_orders" ("customer_type", "booking_id", "room_number", "customer_name", "customer_phone", "special_instructions", "status", "expected_ready_at", "received_by", "id", "order_number", "updated_at", "created_at") values ('guest', '194badd1-28cd-409e-b030-52e77b437914', 'MAIN-001', NULL, NULL, NULL, 'received', '2026-04-04 19:37:29', '01c5525b-1ea0-4a0f-9cd9-293a70e13574', '47de73a9-f5c1-48b1-8c86-ec01aed96a3c', 'LND-20260402-0002', '2026-04-02 19:37:29', '2026-04-02 19:37:29') (3.31 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '3ccccb7d-8b3f-4935-99f0-0613846934f3' limit 1 (0.13 ms)
* sqlite - insert into "laundry_order_items" ("laundry_order_id", "laundry_service_item_id", "quantity", "unit_price", "subtotal", "notes", "id", "updated_at", "created_at") values ('47de73a9-f5c1-48b1-8c86-ec01aed96a3c', '3ccccb7d-8b3f-4935-99f0-0613846934f3', '1', '2000.00', 2000, NULL, '28dde579-6eba-4954-9f63-e08cddab653a', '2026-04-02 19:37:29', '2026-04-02 19:37:29') (0.33 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '580a65b9-2f7e-4b88-922e-9a7cf6c80016' limit 1 (0.11 ms)
* sqlite - insert into "laundry_order_items" ("laundry_order_id", "laundry_service_item_id", "quantity", "unit_price", "subtotal", "notes", "id", "updated_at", "created_at") values ('47de73a9-f5c1-48b1-8c86-ec01aed96a3c', '580a65b9-2f7e-4b88-922e-9a7cf6c80016', '1', '4000.00', 4000, NULL, '81674596-5b4a-40b1-be0f-6b9644eb5683', '2026-04-02 19:37:29', '2026-04-02 19:37:29') (0.09 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '4c97859d-bfc6-4836-9f01-c5f92ca7af32' limit 1 (0.09 ms)
* sqlite - insert into "laundry_order_items" ("laundry_order_id", "laundry_service_item_id", "quantity", "unit_price", "subtotal", "notes", "id", "updated_at", "created_at") values ('47de73a9-f5c1-48b1-8c86-ec01aed96a3c', '4c97859d-bfc6-4836-9f01-c5f92ca7af32', '1', '2000.00', 2000, NULL, '14deb12b-7dfe-421f-821c-15b18689956e', '2026-04-02 19:37:29', '2026-04-02 19:37:29') (0.09 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '92dc878b-807e-42e0-a755-6630927944f6' limit 1 (0.09 ms)
* sqlite - insert into "laundry_order_items" ("laundry_order_id", "laundry_service_item_id", "quantity", "unit_price", "subtotal", "notes", "id", "updated_at", "created_at") values ('47de73a9-f5c1-48b1-8c86-ec01aed96a3c', '92dc878b-807e-42e0-a755-6630927944f6', '1', '7000.00', 7000, NULL, 'fc59cc0c-1b85-47f6-b790-3243b5a5a447', '2026-04-02 19:37:29', '2026-04-02 19:37:29') (0.08 ms)
* sqlite - select * from "laundry_order_items" where "laundry_order_items"."laundry_order_id" in ('47de73a9-f5c1-48b1-8c86-ec01aed96a3c') (0.23 ms)
* sqlite - update "laundry_orders" set "subtotal" = 15000, "total" = 15000, "updated_at" = '2026-04-02 19:37:29' where "id" = '47de73a9-f5c1-48b1-8c86-ec01aed96a3c' (0.29 ms)
* sqlite - select "id" from "users" where exists (select * from "roles" where "users"."role_id" = "roles"."id" and "name" in ('supervisor', 'laundry_manager')) (0.35 ms)
* sqlite - insert into "store_notifications" ("type", "title", "body", "reference_type", "reference_id", "action_url", "user_id", "created_at", "id") values ('new_laundry_order', 'New Laundry Order Received', 'Order LND-20260402-0002 — Room MAIN-001 — 4 item(s). Ready by: 04 Apr 19:37', 'laundry_order', '47de73a9-f5c1-48b1-8c86-ec01aed96a3c', 'http://127.0.0.1:8000/laundry/orders/47de73a9-f5c1-48b1-8c86-ec01aed96a3c', '14ebc4b4-daec-4692-92cd-770b3bdda04f', '2026-04-02 19:37:29', '0ba7a18f-abb4-49be-b12e-e344c52fb1b2') (8.95 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-notifications:unread_count:14ebc4b4-daec-4692-92cd-770b3bdda04f') (0.48 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-notifications:unread_count:14ebc4b4-daec-4692-92cd-770b3bdda04f') (0.4 ms)
* sqlite - select count(*) as aggregate from "store_notifications" where "user_id" = '14ebc4b4-daec-4692-92cd-770b3bdda04f' and "is_read" = 0 (0.34 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1775162249, 'laravel-cache-notifications:unread_count:14ebc4b4-daec-4692-92cd-770b3bdda04f', 'i:1;') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (7.9 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.57 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-system_currency', 'laravel-cache-illuminate:cache:flexible:created:system_currency') and "expiration" <= 1775158650 (11.73 ms)
* sqlite - select "value" from "system_settings" where "key" = 'default_currency' limit 1 (0.51 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1775158710, 'laravel-cache-system_currency', 's:3:"USD";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (8.87 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.5 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.42 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-tzs_exchange_rate', 'laravel-cache-illuminate:cache:flexible:created:tzs_exchange_rate') and "expiration" <= 1775158650 (7.49 ms)
* sqlite - select "value" from "system_settings" where "key" = 'tzs_exchange_rate' limit 1 (0.47 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1775158710, 'laravel-cache-tzs_exchange_rate', 's:4:"2500";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (9.72 ms)
