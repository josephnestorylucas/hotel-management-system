http://127.0.0.1:8000/store/stock/damage

# Symfony\Component\HttpKernel\Exception\HttpException - Unprocessable Content

Insufficient stock. Available: 0, Requested: 12

PHP 8.2.12
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1440
1 - vendor\laravel\framework\src\Illuminate\Foundation\helpers.php:67
2 - app\Models\StockMovement.php:64
3 - vendor\laravel\framework\src\Illuminate\Database\Concerns\ManagesTransactions.php:35
4 - vendor\laravel\framework\src\Illuminate\Database\DatabaseManager.php:491
5 - vendor\laravel\framework\src\Illuminate\Support\Facades\Facade.php:363
6 - app\Models\StockMovement.php:40
7 - app\Http\Controllers\Store\StockController.php:94
8 - vendor\laravel\framework\src\Illuminate\Routing\ControllerDispatcher.php:46
9 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:265
10 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:211
11 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:822
12 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
13 - app\Http\Middleware\RoleMiddleware.php:34
14 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
15 - app\Http\Middleware\SetLocale.php:31
16 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
17 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:50
18 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
19 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
20 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
21 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken.php:87
22 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
23 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
24 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
25 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
26 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
27 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
28 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
29 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
30 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
31 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
32 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
33 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
34 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
35 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
36 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
37 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
38 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
39 - app\Http\Middleware\SecurityHeaders.php:18
40 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
41 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
42 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
43 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
44 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
45 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
46 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
47 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
48 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
49 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
50 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
51 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
52 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
53 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
54 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
55 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
56 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
57 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:26
58 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
59 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
60 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
61 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
62 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
63 - public\index.php:20
64 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23

## Request

POST /store/stock/damage

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/store/stock/damage
* **content-type**: application/x-www-form-urlencoded
* **content-length**: 181
* **origin**: http://127.0.0.1:8000
* **connection**: keep-alive
* **cookie**: laravel-session=eyJpdiI6Ink0TDIyMmJ0UWlxRW8zRlJFdDUwcHc9PSIsInZhbHVlIjoiNXliUGtDS1dPZ096THhFTEt4YzhxZWhjQ0h2Y2Q5Qnh5dnZDeGczSzJDQS9HU0RLTFR0U3ZMRVoyZmNUWjl3NjEyaVhnYUMzdG9BNlBMUEpPUktIQ0FGeDRiOEtxanppbDVQVlR2RU4rUFFSNGNGSmdlTmlJdVVVRUcvbHRJYkgiLCJtYWMiOiI5MzAxYjk4OWE3MGQ4YmU4OWE5ODFkMTk2MzJlMmM1NTc1ZWZlMDEzOTQzYjZlNDBkODg0MjQ5MzlmMjI4OWRhIiwidGFnIjoiIn0%3D; XSRF-TOKEN=eyJpdiI6IjRjTzhMTGgxbWprODF2eFp1SVlzNmc9PSIsInZhbHVlIjoiSUt4dS90a3hkV0pnU1llN2RycXd3Q24zZ3Zsc1hzcXVmQyttUnZQQW9OR3FqeFJaa29xRGpYaU43WHZvQXF5dUVlZExLMm94Z3Uyekt1NHJOSnNabmVWVmhNYXQ1Q2kvWWRxblVkeVlISlpSZHNFbjNnYzRjNkEyckloeUFnL0wiLCJtYWMiOiJiZWMzYmM5OTgzNDVjOGI2YTMzNDNhZjRiOTE2YTcxOTM1ZDgxMWRlNDE2MDBjNjQ3MDQ0YjUwZTcwOWYzN2U1IiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\Store\StockController@damage
route name: store.stock.damage
middleware: web, auth, role:store_keeper,store_manager,restaurant_manager

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = 'J4aJL4NqJjYTvkb25HODbFgQ4vd0y9hTTy289JAS' limit 1 (12.11 ms)
* sqlite - select * from "users" where "id" = '7fedd150-7d40-4c1b-ac78-ffb9b67bbf8e' limit 1 (0.57 ms)
* sqlite - select * from "roles" where "roles"."id" = '3378b278-fefd-4551-a2ac-164d23949d24' limit 1 (0.59 ms)
* sqlite - select count(*) as aggregate from "products" where "id" = 'e7448160-e47e-47bd-9f82-6a37fcccf312' (0.56 ms)
* sqlite - select count(*) as aggregate from "stock_locations" where "id" = '09b2f008-0110-4a39-b2e0-e3548c12f2f1' (0.53 ms)
* sqlite - select * from "stock_locations" where "stock_locations"."id" = '09b2f008-0110-4a39-b2e0-e3548c12f2f1' limit 1 (0.62 ms)
* sqlite - select * from "products" where "products"."id" = 'e7448160-e47e-47bd-9f82-6a37fcccf312' limit 1 (0.6 ms)
* sqlite - select * from "stock_levels" where "product_id" = 'e7448160-e47e-47bd-9f82-6a37fcccf312' and "location_id" = '09b2f008-0110-4a39-b2e0-e3548c12f2f1' limit 1 (0.63 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.59 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.57 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.43 ms)
