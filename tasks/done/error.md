# Illuminate\Database\Eloquent\RelationNotFoundException - Internal Server Error

Call to undefined relationship [creator] on model [App\Models\LaundryOrder].

PHP 8.2.12
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\RelationNotFoundException.php:35
1 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Builder.php:969
2 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Relations\Relation.php:119
3 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Builder.php:965
4 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Builder.php:939
5 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Builder.php:919
6 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Builder.php:885
7 - app\Http\Controllers\DashboardController.php:495
8 - app\Http\Controllers\DashboardController.php:45
9 - vendor\laravel\framework\src\Illuminate\Routing\ControllerDispatcher.php:46
10 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:265
11 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:211
12 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:822
13 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
14 - app\Http\Middleware\SetLocale.php:31
15 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
16 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:50
17 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
18 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
19 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
20 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken.php:87
21 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
22 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
23 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
24 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
25 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
26 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
27 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
28 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
29 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
30 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
31 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
32 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
33 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
34 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
35 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
36 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
37 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
38 - app\Http\Middleware\SecurityHeaders.php:18
39 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
40 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
41 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
42 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
43 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
44 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
45 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
46 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
47 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
48 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
49 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
50 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
51 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
52 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
53 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
54 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
55 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
56 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:26
57 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
58 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
59 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
60 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
61 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
62 - public\index.php:20
63 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23

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
* **cookie**: csrftoken=4pobf2ybfXEO83a8kLeppj8zCgeZecVl; laravel-session=eyJpdiI6IkhLZWlZQldRa04wVGZkZVNIWnVKaEE9PSIsInZhbHVlIjoibGlzd2VscXVLdVZ4MWhkS3BWM0pBL1Zhd0U2QzNmUXltWXNBTFpZb24reDJpSGtBRUk1bDhoZTAvVlNpOHd2bTI2dVZtaHd4dzA0VENPWnRaTG1MZWl1TkU4aXVvOG81UWNleGg3dE1NeVRxN2JsWFovODJKaEExVkNLNFZUT3QiLCJtYWMiOiI4MTgxZjAwMWMyNzQ0ZDM2OTk5MTc1Nzg1MmQ3ZWY4MjIwNmZmZjIzZTcwNzUyYjNmMmY2ZDlhOTJjNDdmMjQ0IiwidGFnIjoiIn0%3D; session=.eJyrViouSUxLi89MUbJScnTx9fQzMDBU0oGK5iXmpgLFgyuLS1JzFRxTcjPzMotLihJL8ovgaoryc1JhepVqASZpGnk.aaVojg.lrCb-1mjIl51Lnw0gT-weLDNDOo; XSRF-TOKEN=eyJpdiI6IlN1R0F6RllReVJTdVJxMDdWVUlNenc9PSIsInZhbHVlIjoiZGNoSk04MWNSTWM4R0pxZWxmWlRFM0poVkFXWkUrcitGbm5SRlBlRjJiam9hbTQyQXoyRCtLdk54eGxOYXNUb21FZ0xVWW5UZWxaMWh4eE9OTUd3a1JNaTR4YmI4WWI0bWg3bkhZMldzZXZNSUFVSzlDSDN1VGRzWUN0VENqb0giLCJtYWMiOiIyZGVjN2RlOTZmMGU3MTBmYWExOGNlODlkZWM1MmNhZTUxYTg4ZTlmODcwNzRjNDE4YWY1OTdlYTNlOWIzMzQxIiwidGFnIjoiIn0%3D
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

* sqlite - select * from "sessions" where "id" = '3hEcU8T2hKrL0D6zE3Jdb1vLrRyYLLGyfq01SdYh' limit 1 (8.43 ms)
* sqlite - select * from "users" where "id" = '7fedd150-7d40-4c1b-ac78-ffb9b67bbf8e' limit 1 (0.52 ms)
* sqlite - select * from "roles" where "roles"."id" = '3378b278-fefd-4551-a2ac-164d23949d24' limit 1 (0.5 ms)
* sqlite - select count(*) as aggregate from "rooms" (0.49 ms)
* sqlite - select count(*) as aggregate from "rooms" where "status" = 'occupied' (1.26 ms)
* sqlite - select count(*) as aggregate from "rooms" where "status" = 'available' and "is_active" = 1 (0.45 ms)
* sqlite - select count(*) as aggregate from "rooms" where "status" = 'dirty' (0.42 ms)
* sqlite - select count(*) as aggregate from "rooms" where "status" = 'out_of_order' (0.35 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where "status" = 'pending' (0.91 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where "status" = 'in_progress' (0.43 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where "status" = 'completed' (0.41 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where "status" = 'delivered' (0.38 ms)
* sqlite - select count(*) as aggregate from "laundry_orders" where strftime('%Y-%m-%d', "created_at") = cast('2026-04-11' as text) (0.37 ms)
* sqlite - select "status", count(*) as count from "laundry_orders" group by "status" (0.44 ms)
* sqlite - select * from "laundry_orders" order by "created_at" desc limit 10 (0.59 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.4 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.39 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.37 ms)
