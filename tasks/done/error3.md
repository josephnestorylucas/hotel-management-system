http://127.0.0.1:8000/store/reports/stock-snapshot

# ErrorException - Internal Server Error

Undefined variable $locations

PHP 8.2.12
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - resources\views\store\reports\stock-snapshot.blade.php:21
1 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:123
2 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:124
3 - vendor\laravel\framework\src\Illuminate\View\Engines\PhpEngine.php:57
4 - vendor\laravel\framework\src\Illuminate\View\Engines\CompilerEngine.php:76
5 - vendor\laravel\framework\src\Illuminate\View\View.php:208
6 - vendor\laravel\framework\src\Illuminate\View\View.php:191
7 - vendor\laravel\framework\src\Illuminate\View\View.php:160
8 - vendor\laravel\framework\src\Illuminate\Http\Response.php:78
9 - vendor\laravel\framework\src\Illuminate\Http\Response.php:34
10 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:939
11 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:906
12 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
13 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
14 - app\Http\Middleware\RoleMiddleware.php:34
15 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
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

GET /store/reports/stock-snapshot

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/dashboard
* **connection**: keep-alive
* **cookie**: laravel-session=eyJpdiI6Ik9EbDRTcUlYVnAwRFd3OFc0TmlKSXc9PSIsInZhbHVlIjoiMXBwdWlaM25pNllkM29ab0NoVm1TeFFNTGIycUZUTURMVzBmNEllb2g1aWZVZHA4d2hTNnpBcHhzZ0FNRCtLSVF1SVZQR2g1NWNKV2d5MWd4STA5YmU4NldOWkYwcDdSWkxaUEJzWnk3bXVMUHdpT2F2ZnpUZ1lWUHFWZjErUlMiLCJtYWMiOiI1ODYyY2IxOWRlMzBlZTA1MjZjYmQyMDc2ZDE4ZTFmZjlhM2IxMjhkNDYyNjI5MjNjZGQzMzE4NzZiYmIxNWE4IiwidGFnIjoiIn0%3D; XSRF-TOKEN=eyJpdiI6ImFGSVdSQkJmZXI5VGhYSWluS2RTNWc9PSIsInZhbHVlIjoiNk1XdjBCcDRQQW5sMW9OTkJxZWZjQy83ME1ZL3pGRG1lWVJ0M3l4UlBOUXRsMDdTQW5lZEJqd29CVlpQNXNmcGZZL0VYWTl5VlkwWUNLUEFsV2ROVFB2ZXhNRjV0QTJ4UU05ckhzSjQrenMydE81SVFoYkpjZlpJZEVGTUYrTi8iLCJtYWMiOiI1YzllY2IzZDUyOGZlMjZjYmRiNzE5ZWZhZmJkYjYyNjY4ODlmMTI0MTE1ZmQzMDI2ZDZlZTg5NWE0MjhjODA4IiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\Store\ReportController@stockSnapshot
route name: store.reports.stock-snapshot
middleware: web, auth, role:store_manager,store_keeper

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = 'J4aJL4NqJjYTvkb25HODbFgQ4vd0y9hTTy289JAS' limit 1 (13.2 ms)
* sqlite - select * from "users" where "id" = '7fedd150-7d40-4c1b-ac78-ffb9b67bbf8e' limit 1 (0.59 ms)
* sqlite - select * from "roles" where "roles"."id" = '3378b278-fefd-4551-a2ac-164d23949d24' limit 1 (0.43 ms)
* sqlite - select count(*) as aggregate from "stock_levels" inner join "products" on "stock_levels"."product_id" = "products"."id" where "products"."is_active" = 1 (0.85 ms)
* sqlite - select "stock_levels".* from "stock_levels" inner join "products" on "stock_levels"."product_id" = "products"."id" where "products"."is_active" = 1 limit 30 offset 0 (0.39 ms)
* sqlite - select * from "products" where "products"."id" in ('e7448160-e47e-47bd-9f82-6a37fcccf312') (0.47 ms)
* sqlite - select * from "stock_locations" where "stock_locations"."id" in ('09b2f008-0110-4a39-b2e0-e3548c12f2f1', '3b77c6eb-f9de-4630-9760-d57691205007', '4a9d6e95-7d03-4a10-91cd-43ad365de003') (0.64 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.62 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.95 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.44 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.56 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.42 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.3 ms)
