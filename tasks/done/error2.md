# Symfony\Component\Routing\Exception\RouteNotFoundException - Internal Server Error

Route [bartender.orders.walkin.create] not defined.

PHP 8.4.20
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - vendor/laravel/framework/src/Illuminate/Routing/UrlGenerator.php:528
1 - vendor/laravel/framework/src/Illuminate/Foundation/helpers.php:870
2 - resources/views/bartender/inbox.blade.php:29
3 - vendor/laravel/framework/src/Illuminate/Filesystem/Filesystem.php:123
4 - vendor/laravel/framework/src/Illuminate/Filesystem/Filesystem.php:124
5 - vendor/laravel/framework/src/Illuminate/View/Engines/PhpEngine.php:57
6 - vendor/laravel/framework/src/Illuminate/View/Engines/CompilerEngine.php:76
7 - vendor/laravel/framework/src/Illuminate/View/View.php:208
8 - vendor/laravel/framework/src/Illuminate/View/View.php:191
9 - vendor/laravel/framework/src/Illuminate/View/View.php:160
10 - vendor/laravel/framework/src/Illuminate/Http/Response.php:78
11 - vendor/laravel/framework/src/Illuminate/Http/Response.php:34
12 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:939
13 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:906
14 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
15 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
16 - app/Http/Middleware/RoleMiddleware.php:37
17 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
18 - app/Http/Middleware/SetLocale.php:31
19 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
20 - vendor/laravel/framework/src/Illuminate/Routing/Middleware/SubstituteBindings.php:50
21 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
22 - vendor/laravel/framework/src/Illuminate/Auth/Middleware/Authenticate.php:63
23 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
24 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php:87
25 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
26 - vendor/laravel/framework/src/Illuminate/View/Middleware/ShareErrorsFromSession.php:48
27 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
28 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:120
29 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:63
30 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
31 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/AddQueuedCookiesToResponse.php:36
32 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
33 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/EncryptCookies.php:74
34 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
35 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
36 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
37 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:800
38 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:764
39 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:753
40 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:200
41 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
42 - app/Http/Middleware/SecurityHeaders.php:18
43 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
44 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
45 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php:31
46 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
47 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
48 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php:51
49 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
50 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePostSize.php:27
51 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
52 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php:109
53 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
54 - vendor/laravel/framework/src/Illuminate/Http/Middleware/HandleCors.php:61
55 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
56 - vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php:58
57 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
58 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/InvokeDeferredCallbacks.php:22
59 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
60 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePathEncoding.php:26
61 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
62 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
63 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:175
64 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:144
65 - vendor/laravel/framework/src/Illuminate/Foundation/Application.php:1220
66 - public/index.php:20
67 - vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php:23

## Request

GET /bartender/orders

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (X11; Linux x86_64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/bartender/pos
* **connection**: keep-alive
* **cookie**: laravel-session=eyJpdiI6ImZWMFVvMEx2R2o2eUNoc240QStRemc9PSIsInZhbHVlIjoiL0RuaTdlZHgrYzlLVHJENlBHTmJVdDVMdWIzSGFjbzhNQnRQY215WUxCdjJLdnh3VGFKaVdZTGo3aXlMVS9wODBRRURocGExV3hDZDVLaVpLVEh6SS9tOGJGTlBuS0J6T0ZHcDB3K3BvQjdzT1QrU3IzTFh4bmUzbG5ESzNvZkEiLCJtYWMiOiJjOGMxZDhmMDRlNTU5ZTkzNDU4Mzc1YTUzYTFiNjliODFkNjM5MDlmMDViYzgyOGQ5MTk3YmU3MzJhNjliOTViIiwidGFnIjoiIn0%3D; XSRF-TOKEN=eyJpdiI6IlZ3b1pUVlozMVo1ZmpWVlZOUG02TlE9PSIsInZhbHVlIjoiMFMvQnIzTFczMDBYcElYWkdXTGRucU1uckZId2ZsSU5McE4wdklud25IemYzdjVCd3FyTWgvaTBjMHFVZVhhQVh1NUJMTklmclRqUFE3SVNGUmVWd2NGUGpyMXdXY0RzRVRTejBKTDdDU1JOZjFKM3BQVFJnOXh1LzRBdEYrL20iLCJtYWMiOiI0M2JmMzQxMzJlZmZhMjQxYmQ1M2I3MTdjYjJhZGQxOTc0MDliMDZlMmI1NDZlYWUwMjVlZDkzYWI3YzQ3NjhhIiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\Bartender\BartenderController@inbox
route name: bartender.inbox
middleware: web, auth, role:bar_tender,manager,admin

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = 'uKuhbyH7UBgY7v87P0GaUJfSVhWdMW5HClKwtuAy' limit 1 (2.08 ms)
* sqlite - select * from "users" where "id" = '5d05ecef-fade-4090-b635-b330fe41d332' limit 1 (0.1 ms)
* sqlite - select * from "roles" where "roles"."id" = 'd857b1ff-6361-4b7b-88aa-cfaad7260169' limit 1 (0.06 ms)
* sqlite - select * from "stock_locations" where "code" = 'bar' limit 1 (0.07 ms)
* sqlite - select count(*) as aggregate from "orders" where "location_id" = '4a9d6e95-7d03-4a10-91cd-43ad365de003' and "order_source" is not null (0.08 ms)
* sqlite - select * from "orders" where "location_id" = '4a9d6e95-7d03-4a10-91cd-43ad365de003' and "order_source" is not null order by "created_at" desc limit 20 offset 0 (0.09 ms)
* sqlite - select * from "order_items" where "order_items"."order_id" in ('3598eab3-e94d-43d0-94f5-ddf4d30ea405') (0.07 ms)
* sqlite - select * from "menu_items" where "menu_items"."id" in ('bdc62500-24de-4430-80d4-109d57263734') (0.06 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.08 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.04 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.03 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.1 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.06 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.06 ms)
