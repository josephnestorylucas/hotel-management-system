# ParseError - Internal Server Error

syntax error, unexpected end of file

PHP 8.4.20
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - resources/views/bookings/create.blade.php:289
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

GET /bookings/create

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (X11; Linux x86_64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/laundry/orders
* **connection**: keep-alive
* **cookie**: laravel-session=eyJpdiI6IlhvcEZ6TGdWb0RNNjl5Tm5jNHdNYVE9PSIsInZhbHVlIjoieWJGdDlrVXlRb1EwMjVlWFo0dXU2V1VGYVh0YXRNRzlPdmIxRjkxOGdXRzFLS01OUlB4YVV1eU50NFNuS09tVUVDd1lHbWFBYmF0ZUY5a2JwbmFpNkZXazg5cGtFL0dwdiszZFVzeDZwcTdKdzVhMzBZNGYxdTNLdzdETVNhRXUiLCJtYWMiOiIxOTU2ZGRmMWFmOWFiYjBjODliNDRiZTExYThhODczY2Q3M2I2NGI3ODYxOGYyZjMyY2M1ZjQ2NTgzMDUwNWM5IiwidGFnIjoiIn0%3D; hms-session=eyJpdiI6IjVUQWxqZ2I2ZmJmT0NCVEFHd2p5OVE9PSIsInZhbHVlIjoiMzYwS1lucGwvbTJ4bkdCOVFOZUtndmVtZGtlalVnVTZCWFVHOFY4TEJUNlI5eHN3K1l1ZDIxdER2RGJBUmpuUmRaL2VhMDNEcW9yTW1Rc0t1L0tIQ1ZJUHE0RWdmT3c5aG9PZXdPd2RIL0czclczQ05kT3BxWXZIUWJJc1RYZWYiLCJtYWMiOiI4ODVjYzA2YmM4YmQyZTg4MzkyMmRhZGY3YzBiYzUyOTNmZTEwZTBmY2JjYzU3YzdkNjA2OTVlMTUzYWNjODI0IiwidGFnIjoiIn0%3D; XSRF-TOKEN=eyJpdiI6Ik56Rk5nZzNXQWhDQlE3bmt5eUtCMGc9PSIsInZhbHVlIjoiakc3VERzSjVMYU5tNlhnSjNpc05PMFpZbFBCa0pRYUx5c2JON3BnYmVTQUpVZ2NLYlFCSjJqWW9HRklLdS93ZFVXTktFVmJwNWJJbzB2VUd3cCtZaWpsa21JSE1DSUdPdklJS0g2em9OZ05PK2Y1b1FlY2JaVkFkdGthczhOZ3MiLCJtYWMiOiI3NWU4YzQ1NTllM2U5NzQ5ZGFkZGMyZmU5MGRlZDc4Y2E1ODRlY2VkYmRjY2NiZjY4MGMwYmM2NTM5Y2U0NjFkIiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\BookingController@create
route name: bookings.create
middleware: web, auth, role:front_desk,manager

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = '3ObCsYqqEvkRY7rERouL4kklaFvpft9yXPw9qLmI' limit 1 (6.9 ms)
* sqlite - select * from "users" where "id" = '2f86aabc-8872-48f3-a4dd-dac8a223c061' limit 1 (0.26 ms)
* sqlite - select * from "roles" where "roles"."id" = 'a100306b-a9b7-4f07-a9b1-837d3924f3fe' limit 1 (0.34 ms)
* sqlite - select * from "guests" order by "first_name" asc (0.63 ms)
* sqlite - select * from "room_types" (0.74 ms)
* sqlite - select * from "cache" where "key" in ('hms-cache-system_currency') (0.3 ms)
* sqlite - select * from "cache" where "key" in ('hms-cache-system_currency') (0.21 ms)
* sqlite - select * from "cache" where "key" in ('hms-cache-tzs_exchange_rate') (0.22 ms)
* sqlite - select * from "cache" where "key" in ('hms-cache-system_currency') (0.19 ms)
* sqlite - select * from "cache" where "key" in ('hms-cache-system_currency') (0.11 ms)
* sqlite - select * from "cache" where "key" in ('hms-cache-tzs_exchange_rate') (0.12 ms)
