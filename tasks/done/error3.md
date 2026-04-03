# Illuminate\Database\QueryException - Internal Server Error

SQLSTATE[HY000]: General error: 1 table store_notifications has no column named action_url (Connection: sqlite, Database: C:\Users\DADY\Desktop\projects\hotel-management-system\database\database.sqlite, SQL: insert into "store_notifications" ("type", "title", "body", "reference_type", "reference_id", "action_url", "user_id", "created_at", "id") values (new_laundry_order, New Laundry Order Received, Order LND-20260402-0001 — Room MAIN-001 — 4 item(s). Ready by: 04 Apr 19:31, laundry_order, 735ef226-7b1e-41eb-bb78-79fe21a33cc8, http://127.0.0.1:8000/laundry/orders/735ef226-7b1e-41eb-bb78-79fe21a33cc8, 14ebc4b4-daec-4692-92cd-770b3bdda04f, 2026-04-02 19:31:05, 842f68ce-294a-45b7-94da-ab7c73090b21))

PHP 8.2.12
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - vendor\laravel\framework\src\Illuminate\Database\Connection.php:838
1 - vendor\laravel\framework\src\Illuminate\Database\Connection.php:794
2 - vendor\laravel\framework\src\Illuminate\Database\Connection.php:573
3 - vendor\laravel\framework\src\Illuminate\Database\Connection.php:537
4 - vendor\laravel\framework\src\Illuminate\Database\Query\Builder.php:4121
5 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Builder.php:2237
6 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Model.php:1412
7 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Model.php:1240
8 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Builder.php:1219
9 - vendor\laravel\framework\src\Illuminate\Support\helpers.php:393
10 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Builder.php:1218
11 - vendor\laravel\framework\src\Illuminate\Support\Traits\ForwardsCalls.php:23
12 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Model.php:2540
13 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Model.php:2556
14 - app\Services\NotificationService.php:86
15 - app\Services\NotificationService.php:146
16 - app\Http\Controllers\Laundry\LaundryOrderController.php:139
17 - vendor\laravel\framework\src\Illuminate\Routing\ControllerDispatcher.php:46
18 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:265
19 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:211
20 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:822
21 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
22 - app\Http\Middleware\RoleMiddleware.php:34
23 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
24 - app\Http\Middleware\SetLocale.php:31
25 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
26 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:50
27 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
28 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
29 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
30 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken.php:87
31 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
32 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
33 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
34 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
35 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
36 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
37 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
38 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
39 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
40 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
41 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
42 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
43 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
44 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
45 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
46 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
47 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
48 - app\Http\Middleware\SecurityHeaders.php:18
49 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
50 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
51 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
52 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
53 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
54 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
55 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
56 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
57 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
58 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
59 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
60 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
61 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
62 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
63 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
64 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
65 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
66 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:26
67 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
68 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
69 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
70 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
71 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
72 - public\index.php:20
73 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23

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
* **cookie**: XSRF-TOKEN=eyJpdiI6IkpWcVdoR2NNQWtobGhGbWQ4RG12Y3c9PSIsInZhbHVlIjoiMFYzOFk5QnFmSER0eHdrZFgrR2NheHdoeXFlRStXZm1OVlBPb1VQMVp4NVBXWjVRL2lsVWxOSThrV1pOeHVsd0IrMERUU1dXalhlSERrQWU3T0VhcHVQNWdJd3dkclRFYnNoWjdRRW9LOWh4c1VIZGhFanVHbUVGSTZsS3NZa2wiLCJtYWMiOiIzYmFlNGIwOTUxZTEyNjdkNjIzNjE1NjFkZmVjNGM5OTY2NzRjOTBhOTM1OGRkNWI1ZDJhNmI3NjljOTM2ZDJlIiwidGFnIjoiIn0%3D; laravel-session=eyJpdiI6ImZVNHRlSHlQQTFGYkJOamY5VEVxeFE9PSIsInZhbHVlIjoiTThzM3lTNHEvY24xdnNxLzRkcExUZFZ2czlKS2xUN2psZUNWdlZ2NEdjbUdDL2dFZlo2b2cvZHdWeEJ3NlMycGNsMzdSWExKZ2UwWWs0dG1PYlVyTDk2akQ4VWMxYW1CZG5kMDRaUEExK0k0NVdHbXR4RFJuWVExWTF2Nm9QZVoiLCJtYWMiOiJhOTM1NGQ1MjMzNDExMzVkNTljODg0ODUwM2EwZTQwYjA4NDc2OWU2NzcwMjgwMDRjNzc4YWY3NjUzYmRjZDBjIiwidGFnIjoiIn0%3D
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

* sqlite - select * from "sessions" where "id" = 'iNp6NBtua4yryQwFwpgUFwu8OdTR8kC2jAAFOGMv' limit 1 (8.32 ms)
* sqlite - select * from "users" where "id" = '01c5525b-1ea0-4a0f-9cd9-293a70e13574' limit 1 (0.68 ms)
* sqlite - select * from "roles" where "roles"."id" = 'b45852e1-5af0-413e-87be-65926dc42e0a' limit 1 (0.58 ms)
* sqlite - select count(*) as aggregate from "laundry_service_items" where "id" = '3ccccb7d-8b3f-4935-99f0-0613846934f3' (0.46 ms)
* sqlite - select count(*) as aggregate from "laundry_service_items" where "id" = '580a65b9-2f7e-4b88-922e-9a7cf6c80016' (0.49 ms)
* sqlite - select count(*) as aggregate from "laundry_service_items" where "id" = '4c97859d-bfc6-4836-9f01-c5f92ca7af32' (0.41 ms)
* sqlite - select count(*) as aggregate from "laundry_service_items" where "id" = '92dc878b-807e-42e0-a755-6630927944f6' (0.42 ms)
* sqlite - select * from "bookings" where "bookings"."id" = '194badd1-28cd-409e-b030-52e77b437914' limit 1 (0.63 ms)
* sqlite - select * from "rooms" where "rooms"."id" in ('5a280237-2b22-4d18-ae8e-e9af5eb03161') (0.49 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '3ccccb7d-8b3f-4935-99f0-0613846934f3' limit 1 (0.47 ms)
* sqlite - select * from "laundry_services" where "laundry_services"."id" in ('f7f3747c-151b-4dc2-ab3b-1fb21b3906cf') (0.32 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '580a65b9-2f7e-4b88-922e-9a7cf6c80016' limit 1 (0.1 ms)
* sqlite - select * from "laundry_services" where "laundry_services"."id" in ('f7f3747c-151b-4dc2-ab3b-1fb21b3906cf') (0.12 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '4c97859d-bfc6-4836-9f01-c5f92ca7af32' limit 1 (0.11 ms)
* sqlite - select * from "laundry_services" where "laundry_services"."id" in ('f7f3747c-151b-4dc2-ab3b-1fb21b3906cf') (0.09 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '92dc878b-807e-42e0-a755-6630927944f6' limit 1 (0.13 ms)
* sqlite - select * from "laundry_services" where "laundry_services"."id" in ('e8ffb097-a434-4a1e-9740-9056acfa99d2') (0.1 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where strftime('%Y-%m-%d', "created_at") = cast('2026-04-02' as text) (0.24 ms)
* sqlite - insert into "laundry_orders" ("customer_type", "booking_id", "room_number", "customer_name", "customer_phone", "special_instructions", "status", "expected_ready_at", "received_by", "id", "order_number", "updated_at", "created_at") values ('guest', '194badd1-28cd-409e-b030-52e77b437914', 'MAIN-001', NULL, NULL, NULL, 'received', '2026-04-04 19:31:05', '01c5525b-1ea0-4a0f-9cd9-293a70e13574', '735ef226-7b1e-41eb-bb78-79fe21a33cc8', 'LND-20260402-0001', '2026-04-02 19:31:05', '2026-04-02 19:31:05') (2.63 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '3ccccb7d-8b3f-4935-99f0-0613846934f3' limit 1 (0.15 ms)
* sqlite - insert into "laundry_order_items" ("laundry_order_id", "laundry_service_item_id", "quantity", "unit_price", "subtotal", "notes", "id", "updated_at", "created_at") values ('735ef226-7b1e-41eb-bb78-79fe21a33cc8', '3ccccb7d-8b3f-4935-99f0-0613846934f3', '1', '2000.00', 2000, NULL, 'fda512f1-6693-4344-9cd1-154b0734f833', '2026-04-02 19:31:05', '2026-04-02 19:31:05') (0.86 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '580a65b9-2f7e-4b88-922e-9a7cf6c80016' limit 1 (0.14 ms)
* sqlite - insert into "laundry_order_items" ("laundry_order_id", "laundry_service_item_id", "quantity", "unit_price", "subtotal", "notes", "id", "updated_at", "created_at") values ('735ef226-7b1e-41eb-bb78-79fe21a33cc8', '580a65b9-2f7e-4b88-922e-9a7cf6c80016', '1', '4000.00', 4000, NULL, '46c9174a-ff30-4475-9dda-e019bb728b6f', '2026-04-02 19:31:05', '2026-04-02 19:31:05') (0.1 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '4c97859d-bfc6-4836-9f01-c5f92ca7af32' limit 1 (0.1 ms)
* sqlite - insert into "laundry_order_items" ("laundry_order_id", "laundry_service_item_id", "quantity", "unit_price", "subtotal", "notes", "id", "updated_at", "created_at") values ('735ef226-7b1e-41eb-bb78-79fe21a33cc8', '4c97859d-bfc6-4836-9f01-c5f92ca7af32', '1', '2000.00', 2000, NULL, 'adbe5cc9-0689-4263-b31d-344675393dcb', '2026-04-02 19:31:05', '2026-04-02 19:31:05') (0.11 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" = '92dc878b-807e-42e0-a755-6630927944f6' limit 1 (0.13 ms)
* sqlite - insert into "laundry_order_items" ("laundry_order_id", "laundry_service_item_id", "quantity", "unit_price", "subtotal", "notes", "id", "updated_at", "created_at") values ('735ef226-7b1e-41eb-bb78-79fe21a33cc8', '92dc878b-807e-42e0-a755-6630927944f6', '1', '7000.00', 7000, NULL, 'b99024b2-cc24-4105-ab04-6c2431ace430', '2026-04-02 19:31:05', '2026-04-02 19:31:05') (0.11 ms)
* sqlite - select * from "laundry_order_items" where "laundry_order_items"."laundry_order_id" in ('735ef226-7b1e-41eb-bb78-79fe21a33cc8') (0.17 ms)
* sqlite - update "laundry_orders" set "subtotal" = 15000, "total" = 15000, "updated_at" = '2026-04-02 19:31:05' where "id" = '735ef226-7b1e-41eb-bb78-79fe21a33cc8' (0.13 ms)
* sqlite - select "id" from "users" where exists (select * from "roles" where "users"."role_id" = "roles"."id" and "name" in ('supervisor', 'laundry_manager')) (0.53 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.5 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-system_currency', 'laravel-cache-illuminate:cache:flexible:created:system_currency') and "expiration" <= 1775158266 (9.12 ms)
* sqlite - select "value" from "system_settings" where "key" = 'default_currency' limit 1 (0.6 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1775158326, 'laravel-cache-system_currency', 's:3:"USD";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (9.37 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.45 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.42 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-tzs_exchange_rate', 'laravel-cache-illuminate:cache:flexible:created:tzs_exchange_rate') and "expiration" <= 1775158266 (7.39 ms)
* sqlite - select "value" from "system_settings" where "key" = 'tzs_exchange_rate' limit 1 (0.55 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1775158326, 'laravel-cache-tzs_exchange_rate', 's:4:"2500";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (9.36 ms)
