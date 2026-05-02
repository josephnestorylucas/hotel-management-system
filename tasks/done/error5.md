# ParseError - Internal Server Error

Unclosed '[' on line 135 does not match ')'

PHP 8.4.20
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - resources/views/reception/drinks/create.blade.php:115
1 - vendor/laravel/framework/src/Illuminate/Filesystem/Filesystem.php:124
2 - vendor/laravel/framework/src/Illuminate/View/Engines/PhpEngine.php:57
3 - vendor/laravel/framework/src/Illuminate/View/Engines/CompilerEngine.php:76
4 - vendor/laravel/framework/src/Illuminate/View/View.php:208
5 - vendor/laravel/framework/src/Illuminate/View/View.php:191
6 - vendor/laravel/framework/src/Illuminate/View/View.php:160
7 - vendor/laravel/framework/src/Illuminate/Http/Response.php:78
8 - vendor/laravel/framework/src/Illuminate/Http/Response.php:34
9 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:939
10 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:906
11 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
12 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
13 - app/Http/Middleware/RoleMiddleware.php:37
14 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
15 - app/Http/Middleware/SetLocale.php:31
16 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
17 - vendor/laravel/framework/src/Illuminate/Routing/Middleware/SubstituteBindings.php:50
18 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
19 - vendor/laravel/framework/src/Illuminate/Auth/Middleware/Authenticate.php:63
20 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
21 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php:87
22 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
23 - vendor/laravel/framework/src/Illuminate/View/Middleware/ShareErrorsFromSession.php:48
24 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
25 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:120
26 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:63
27 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
28 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/AddQueuedCookiesToResponse.php:36
29 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
30 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/EncryptCookies.php:74
31 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
32 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
33 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
34 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:800
35 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:764
36 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:753
37 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:200
38 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
39 - app/Http/Middleware/SecurityHeaders.php:18
40 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
41 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
42 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php:31
43 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
44 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
45 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php:51
46 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
47 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePostSize.php:27
48 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
49 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php:109
50 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
51 - vendor/laravel/framework/src/Illuminate/Http/Middleware/HandleCors.php:61
52 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
53 - vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php:58
54 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
55 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/InvokeDeferredCallbacks.php:22
56 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
57 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePathEncoding.php:26
58 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
59 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
60 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:175
61 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:144
62 - vendor/laravel/framework/src/Illuminate/Foundation/Application.php:1220
63 - public/index.php:20
64 - vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php:23

## Request

GET /drinks/request

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (X11; Linux x86_64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/conference-halls
* **connection**: keep-alive
* **cookie**: laravel-session=eyJpdiI6IkxGUENLeVd2aGtRZnNaMVFoWThPNWc9PSIsInZhbHVlIjoiWHc5VUpIVEkyUW8yYjlzdXlOZE92Y2hIWXZZTTBXYlZuSG1PSVoyaExDbU9SeE1MeU51RlBZT1ZLb0Jyd3VINXJiNGQ2WFN5dzl0dmVrOWltQVhGVjBHZjVYSk0yakczazNjUFdjdHE0bDFBc0JqRWI1WXZEb3BveEdHdG5QcDEiLCJtYWMiOiIxNDRhZThiNGM3YzZmMDc1OWM0YzgzNjBhZmExNDg1ZThhMzQzMDA5NWUzMDViMTliYTQ0YzU3NmYxMjBkNTM0IiwidGFnIjoiIn0%3D; XSRF-TOKEN=eyJpdiI6InZkdUVWSDVQWWF4MHVsRThKZmxHZWc9PSIsInZhbHVlIjoiZTFxY2drS3ppMFFncEsxc3E4RnpNOGZ3eTJNcTNwaHB2V2ZENG1ybklvRk9UQ0hsb2w0Mkt2UkhnTHBIV3Y3dlJjeHdOZDZPbitwbTVlYVpKWUpzS0N6cjJpd3NUekNkUHVseVd2Z0ZRUkYrNHhMR1dmZTlESmVmSmN4eVBaeDYiLCJtYWMiOiIxMmNkMTM5ZDI5N2RhYzI0Zjc1OGYwOTg1MWY2ZWY1ZWMzZjg5NWU4YWUyOTgwNDYzNTFiYWEyOGYyZTI3YTgwIiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\Reception\DrinkRequestController@create
route name: reception.drinks.create
middleware: web, auth, role:supervisor,front_desk,manager

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = 'pg0adBcWi1pWb70DBvEorc1mMBHmAGR0R6H749VC' limit 1 (10.19 ms)
* sqlite - select * from "users" where "id" = 'efcdd719-57e9-4015-82c6-04f8db24eac6' limit 1 (0.4 ms)
* sqlite - select * from "roles" where "roles"."id" = 'edc84afd-bb55-484d-9fbb-9939f1c771eb' limit 1 (0.28 ms)
* sqlite - select * from "stock_locations" where "code" = 'bar' limit 1 (0.21 ms)
* sqlite - select * from "bookings" where "status" = 'checked_in' order by "guest_name" asc (0.29 ms)
* sqlite - select * from "rooms" where "rooms"."id" in ('7b43dbf6-02cf-497c-ae39-5b8f736d37b2') (0.29 ms)
* sqlite - select * from "products" where "product_type" = 'bar' and "is_active" = 1 order by "name" asc (0.46 ms)
* sqlite - select * from "stock_levels" where "location_id" = '4a9d6e95-7d03-4a10-91cd-43ad365de003' (0.36 ms)
* sqlite - select * from "products" where "products"."id" in ('1a81cc4a-0913-481f-916e-9e85983cdaf3', '78a11c81-cb8a-4bc3-a7d0-46dadd12f1bf', 'cae297f8-8311-4a09-8177-da2fdae4254a', 'e7448160-e47e-47bd-9f82-6a37fcccf312') (0.38 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.29 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-system_currency', 'laravel-cache-illuminate:cache:flexible:created:system_currency') and "expiration" <= 1777727762 (9.82 ms)
* sqlite - select "value" from "system_settings" where "key" = 'default_currency' limit 1 (0.27 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1777727822, 'laravel-cache-system_currency', 's:3:"TZS";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (9.54 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.29 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.23 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-tzs_exchange_rate', 'laravel-cache-illuminate:cache:flexible:created:tzs_exchange_rate') and "expiration" <= 1777727762 (10.14 ms)
* sqlite - select "value" from "system_settings" where "key" = 'tzs_exchange_rate' limit 1 (0.21 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1777727822, 'laravel-cache-tzs_exchange_rate', 's:4:"2500";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (9.76 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.32 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.22 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.19 ms)
