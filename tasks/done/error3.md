# Illuminate\Database\QueryException - Internal Server Error

SQLSTATE[23000]: Integrity constraint violation: 19 CHECK constraint failed: payment_method (Connection: sqlite, Database: /home/dadi-utenga/projects/hotel-management-system/database/database.sqlite, SQL: insert into "orders" ("location_id", "order_type", "order_source", "customer_name", "customer_phone", "bartender_status", "bartender_status_updated_at", "status", "payment_method", "notes", "created_by", "id", "order_number", "updated_at", "created_at") values (4a9d6e95-7d03-4a10-91cd-43ad365de003, walkin, walkin, Walk-in Guest, ?, prepared, 2026-05-02 13:00:25, open, mobile, ?, 5d05ecef-fade-4090-b635-b330fe41d332, 7a581c3c-29bd-4ee8-8928-315984f92605, BAR-20260502-0002, 2026-05-02 13:00:25, 2026-05-02 13:00:25))

PHP 8.4.20
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:838
1 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:794
2 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:573
3 - vendor/laravel/framework/src/Illuminate/Database/Connection.php:537
4 - vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php:4121
5 - vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php:2237
6 - vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php:1412
7 - vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php:1240
8 - vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php:1219
9 - vendor/laravel/framework/src/Illuminate/Support/helpers.php:393
10 - vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php:1218
11 - vendor/laravel/framework/src/Illuminate/Support/Traits/ForwardsCalls.php:23
12 - vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php:2540
13 - vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php:2556
14 - app/Http/Controllers/Bartender/BartenderController.php:212
15 - vendor/laravel/framework/src/Illuminate/Database/Concerns/ManagesTransactions.php:35
16 - vendor/laravel/framework/src/Illuminate/Database/DatabaseManager.php:491
17 - vendor/laravel/framework/src/Illuminate/Support/Facades/Facade.php:363
18 - app/Http/Controllers/Bartender/BartenderController.php:208
19 - vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php:46
20 - vendor/laravel/framework/src/Illuminate/Routing/Route.php:265
21 - vendor/laravel/framework/src/Illuminate/Routing/Route.php:211
22 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:822
23 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
24 - app/Http/Middleware/RoleMiddleware.php:37
25 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
26 - app/Http/Middleware/SetLocale.php:31
27 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
28 - vendor/laravel/framework/src/Illuminate/Routing/Middleware/SubstituteBindings.php:50
29 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
30 - vendor/laravel/framework/src/Illuminate/Auth/Middleware/Authenticate.php:63
31 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
32 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php:87
33 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
34 - vendor/laravel/framework/src/Illuminate/View/Middleware/ShareErrorsFromSession.php:48
35 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
36 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:120
37 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:63
38 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
39 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/AddQueuedCookiesToResponse.php:36
40 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
41 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/EncryptCookies.php:74
42 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
43 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
44 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
45 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:800
46 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:764
47 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:753
48 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:200
49 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
50 - app/Http/Middleware/SecurityHeaders.php:18
51 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
52 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
53 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php:31
54 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
55 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
56 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php:51
57 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
58 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePostSize.php:27
59 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
60 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php:109
61 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
62 - vendor/laravel/framework/src/Illuminate/Http/Middleware/HandleCors.php:61
63 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
64 - vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php:58
65 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
66 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/InvokeDeferredCallbacks.php:22
67 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
68 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePathEncoding.php:26
69 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
70 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
71 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:175
72 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:144
73 - vendor/laravel/framework/src/Illuminate/Foundation/Application.php:1220
74 - public/index.php:20
75 - vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php:23

## Request

POST /bartender/pos

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (X11; Linux x86_64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/bartender/pos
* **content-type**: application/x-www-form-urlencoded
* **content-length**: 210
* **origin**: http://127.0.0.1:8000
* **connection**: keep-alive
* **cookie**: laravel-session=eyJpdiI6Iko0NWpZM2FoWkY1SGE0OGdjbTUvZlE9PSIsInZhbHVlIjoiZEFESUROcmhoNmZGazZsOHNnd09SMjlkdW83Y2JiblplSitQY09HVmRQSEN5VGQwTHNIWVRGUnA5K2ZtQmJtOUt3bjhhcENBZlZPUUk4UmVnMGoycXd5bDdhTDBFT3hTdzhzN2FWczZreGtyMGRTbHZ2S05LelhDQm5xa1ZWaSsiLCJtYWMiOiIyNDhmMDM2MTFkZGE0ZGU4YTljZDhiMDJjMDZjMDJmYjM0NzNlMGViODk0ZDk2MDc2MWZhYThkYmRhM2YyOTVkIiwidGFnIjoiIn0%3D; XSRF-TOKEN=eyJpdiI6IjRnK1dYcWppa0Q3TGsrN29HYjFIbUE9PSIsInZhbHVlIjoiQ1VVZVBiV1dwc01pbnhuVEd1N0F2R0lGQ0pwWWlOb0crVVF6WEVRSzltbzI3SkVyOXNqb2NWcWlGbGh4NGxhelFXUWllbUJEV2lRRUFMdS9hZTJFWXlySVYzRUYvTDk5bVJEaWNQcFdDMURxa2x4Q25MVDJZRXdWY1JaWnl1ZUciLCJtYWMiOiIzN2E4NmU0MTgyNTE2YTQ5NDQ3YjRiOWZlOWVjZTVkNmEzMmZlMjU4M2EwMzIzN2ZjYTkwY2U5OTNlNjA1NWEyIiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\Bartender\BartenderController@storePos
route name: bartender.pos.store
middleware: web, auth, role:bar_tender,manager,admin

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = 'Fzga94jTBDpp0L8LvSo6cIOTlixJYQHEieRR1PUp' limit 1 (7.24 ms)
* sqlite - select * from "users" where "id" = '5d05ecef-fade-4090-b635-b330fe41d332' limit 1 (0.35 ms)
* sqlite - select * from "roles" where "roles"."id" = 'd857b1ff-6361-4b7b-88aa-cfaad7260169' limit 1 (0.35 ms)
* sqlite - select * from "stock_locations" where "code" = 'bar' limit 1 (0.27 ms)
* sqlite - select count(*) as aggregate from "menu_items" where "id" = 'bdc62500-24de-4430-80d4-109d57263734' (0.27 ms)
* sqlite - select * from "stock_locations" where "stock_locations"."id" = '4a9d6e95-7d03-4a10-91cd-43ad365de003' limit 1 (0.33 ms)
* sqlite - select count(*) as aggregate from "orders" where strftime('%Y-%m-%d', "created_at") = cast('2026-05-02' as text) (0.19 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.31 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.17 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.14 ms)



i  cant  enter  the  customer  details   please  look  for  the  how  laundry  settles   payment  and  implement  this  again 
