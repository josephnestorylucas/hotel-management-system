# Symfony\Component\Routing\Exception\RouteNotFoundException - Internal Server Error

Route [laundry.show] not defined.

PHP 8.2.12
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - vendor\laravel\framework\src\Illuminate\Routing\UrlGenerator.php:528
1 - vendor\laravel\framework\src\Illuminate\Foundation\helpers.php:870
2 - resources\views\dashboards\store-keeper.blade.php:185
3 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:123
4 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:124
5 - vendor\laravel\framework\src\Illuminate\View\Engines\PhpEngine.php:57
6 - vendor\laravel\framework\src\Illuminate\View\Engines\CompilerEngine.php:76
7 - vendor\laravel\framework\src\Illuminate\View\View.php:208
8 - vendor\laravel\framework\src\Illuminate\View\View.php:191
9 - vendor\laravel\framework\src\Illuminate\View\View.php:160
10 - vendor\laravel\framework\src\Illuminate\Http\Response.php:78
11 - vendor\laravel\framework\src\Illuminate\Http\Response.php:34
12 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:939
13 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:906
14 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
15 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
16 - app\Http\Middleware\SetLocale.php:31
17 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
18 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:50
19 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
20 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
21 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
22 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken.php:87
23 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
24 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
25 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
26 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
27 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
28 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
29 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
30 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
31 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
32 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
33 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
34 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
35 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
36 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
37 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
38 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
39 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
40 - app\Http\Middleware\SecurityHeaders.php:18
41 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
42 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
43 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
44 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
45 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
46 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
47 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
48 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
49 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
50 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
51 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
52 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
53 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
54 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
55 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
56 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
57 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
58 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:26
59 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
60 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
61 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
62 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
63 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
64 - public\index.php:20
65 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23

## Request

GET /dashboard

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/login
* **sec-gpc**: 1
* **connection**: keep-alive
* **cookie**: csrftoken=4pobf2ybfXEO83a8kLeppj8zCgeZecVl; laravel-session=eyJpdiI6IkpSSTBsWTVDQXBpZ0NxNXozNG80Nmc9PSIsInZhbHVlIjoicm5vTHU4N0ZoOWJLUzJGc2hCNHltRWdhZmhBalZGbXVZdktGOUlhdU1uWVpQL1hBUytlMVV2Um9QRy93MUcyOFprQnBwbkdCZXkrSzlRQzJmN0xlUExsbXJkNEVqYWdYTzlWVXI5RTdsN3ZzRGozckV1VmhoMzNNQW1USC92azkiLCJtYWMiOiIwOWNlYjJhNDVhNWQ2N2MxODJkNWY0OTQxNjgzNmE2ZGFlMGNlYjg0NWJiYjQ4NzBlZGJkM2U0MTljOWY0NjBmIiwidGFnIjoiIn0%3D; session=.eJyrViouSUxLi89MUbJScnTx9fQzMDBU0oGK5iXmpgLFgyuLS1JzFRxTcjPzMotLihJL8ovgaoryc1JhepVqASZpGnk.aaVojg.lrCb-1mjIl51Lnw0gT-weLDNDOo; XSRF-TOKEN=eyJpdiI6IlZVTWcybEw2MGRmY3Z4b0I4Vlc0ZlE9PSIsInZhbHVlIjoibGNMVUlSTjdFcHNFZVAyZi9GVVh6MGZxek5PZjl5cnY3a0JYaGtyU05memFNR0wza3Q1QnpOZ1dpLzUvV3RHejYzaUZzeTIyOU5jcmhWT1IvME14WFp3OXpnMnVuU1dlUENUVnQ4UjFuNjM0STkyRmNSWVBpQkRLN3FKSUdPVGUiLCJtYWMiOiIyMmZiYzE1NjgyMTY5MTFiOGIxYmEwM2ExZGJjNDQ5MWE0NDFhYzljOTdkYjExN2EzZTg2ZjQzMjcyODcyZjQ4IiwidGFnIjoiIn0%3D
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

* sqlite - select * from "sessions" where "id" = '3hEcU8T2hKrL0D6zE3Jdb1vLrRyYLLGyfq01SdYh' limit 1 (8.76 ms)
* sqlite - select * from "users" where "id" = '7fedd150-7d40-4c1b-ac78-ffb9b67bbf8e' limit 1 (0.37 ms)
* sqlite - select * from "roles" where "roles"."id" = '3378b278-fefd-4551-a2ac-164d23949d24' limit 1 (0.56 ms)
* sqlite - select count(*) as aggregate from "rooms" (0.42 ms)
* sqlite - select count(*) as aggregate from "rooms" where "status" = 'occupied' (0.47 ms)
* sqlite - select count(*) as aggregate from "rooms" where "status" = 'available' and "is_active" = 1 (0.41 ms)
* sqlite - select count(*) as aggregate from "rooms" where "status" = 'dirty' (0.34 ms)
* sqlite - select count(*) as aggregate from "rooms" where "status" = 'out_of_order' (0.55 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where "status" = 'pending' (0.49 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where "status" = 'in_progress' (0.38 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where "status" = 'completed' (0.28 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where "status" = 'delivered' (0.3 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where strftime('%Y-%m-%d', "created_at") = cast('2026-04-11' as text) (0.3 ms)
* sqlite - select "status", count(*) as count from "laundry_orders" group by "status" (0.45 ms)
* sqlite - select * from "laundry_orders" order by "created_at" desc limit 10 (0.62 ms)
* sqlite - select * from "users" where "users"."id" in ('efcdd719-57e9-4015-82c6-04f8db24eac6') (0.44 ms)
* sqlite - select * from "rooms" where "status" in ('dirty', 'out_of_order') (0.38 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.42 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.36 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.31 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.46 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.42 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.41 ms)
