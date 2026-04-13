http://127.0.0.1:8000/procurement/suppliers

# InvalidArgumentException - Internal Server Error

View [procurement.suppliers.index] not found.

PHP 8.2.12
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - vendor\laravel\framework\src\Illuminate\View\FileViewFinder.php:138
1 - vendor\laravel\framework\src\Illuminate\View\FileViewFinder.php:78
2 - vendor\laravel\framework\src\Illuminate\View\Factory.php:150
3 - vendor\laravel\framework\src\Illuminate\Foundation\helpers.php:1100
4 - app\Http\Controllers\Procurement\SupplierController.php:20
5 - vendor\laravel\framework\src\Illuminate\Routing\ControllerDispatcher.php:46
6 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:265
7 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:211
8 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:822
9 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
10 - app\Http\Middleware\RoleMiddleware.php:34
11 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
12 - app\Http\Middleware\SetLocale.php:31
13 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
14 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:50
15 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
16 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
17 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
18 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken.php:87
19 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
20 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
21 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
22 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
23 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
24 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
25 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
26 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
27 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
28 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
29 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
30 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
31 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
32 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
33 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
34 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
35 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
36 - app\Http\Middleware\SecurityHeaders.php:18
37 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
38 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
39 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
40 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
41 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
42 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
43 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
44 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
45 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
46 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
47 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
48 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
49 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
50 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
51 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
52 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
53 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
54 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:26
55 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
56 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
57 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
58 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
59 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
60 - public\index.php:20
61 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23

## Request

GET /procurement/suppliers

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/dashboard
* **connection**: keep-alive
* **cookie**: laravel-session=eyJpdiI6ImdWdUdzM0x2dGxHbUF4QzhzaGtsYVE9PSIsInZhbHVlIjoieHhBV3VQdUg3Uk1MbnRramx2Unl1WmRlSjdFMzhLcDg2OFV0Z3NtbFZ2cWJZbllnb0dBMitrU1k2cVFVazE3UDk2ZU9oeFNJZ2hqRlJGWjVCcXhtZmRBOURLQ01ZWGdNR0VOb2k0YVFqR1RtYW5ReWR0V0tCWnRwZkV6N3lSTEwiLCJtYWMiOiI5NWU2NzA2NDlmMWFjY2I0M2Y2MjBlYjQxNTE3ZTJlNjdlZmM5NzU2NzllYzNkMzEyM2FlZjQxY2ZlMGVmM2M2IiwidGFnIjoiIn0%3D; XSRF-TOKEN=eyJpdiI6IitkdWZlNDloL3FyQWl5VFdFMFd4QXc9PSIsInZhbHVlIjoiVnZsaXRkWXBlTUEvTDRSUnlpU0lubGhPVmgrb2xXdHE4dStLVW9qR2VjcU94OEN1cTdXcTlUdmpzM2t2WWtNVW1sRnpXMXJ4cjVpVTFJMVFlM0tLc015dVM2eUhMQ3JKUzVyZkdFaWVvelFiM096M3paUmtaWTU5Ty84c05LWG4iLCJtYWMiOiJjNDA5NTJkOTM4NWI5ZjEyN2ZiOTIwYzNjYjNjY2IxNTFlM2RjMjg0ZmZlMzM5YmM2ZjUzOGVjYzgxZGQ3NTUwIiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\Procurement\SupplierController@index
route name: procurement.suppliers.index
middleware: web, auth, role:store_manager,store_keeper

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = 'GAOsJhphxJly4K0I6h5Aa7Bu0tEQbHfBnWnrxxD0' limit 1 (12.39 ms)
* sqlite - select * from "users" where "id" = 'bb89326f-2556-41e5-9079-17822a6f1705' limit 1 (0.66 ms)
* sqlite - select * from "roles" where "roles"."id" = 'e89fd0bc-1454-49a7-a10e-1280958fa30e' limit 1 (0.64 ms)
* sqlite - select count(*) as aggregate from "suppliers" (0.6 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.63 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-system_currency', 'laravel-cache-illuminate:cache:flexible:created:system_currency') and "expiration" <= 1776083610 (10.71 ms)
* sqlite - select "value" from "system_settings" where "key" = 'default_currency' limit 1 (0.61 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1776083670, 'laravel-cache-system_currency', 's:3:"USD";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (9.97 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.56 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.52 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-tzs_exchange_rate', 'laravel-cache-illuminate:cache:flexible:created:tzs_exchange_rate') and "expiration" <= 1776083610 (10.47 ms)
* sqlite - select "value" from "system_settings" where "key" = 'tzs_exchange_rate' limit 1 (0.62 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1776083670, 'laravel-cache-tzs_exchange_rate', 's:4:"2500";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (9.52 ms)
