# Illuminate\Database\QueryException - Internal Server Error

SQLSTATE[HY000]: General error: 1 no such column: updated_by (Connection: sqlite, Database: C:\Users\DADY\Desktop\projects\hotel-management-system\database\database.sqlite, SQL: update "system_settings" set "value" = TZS, "updated_by" = 393abd66-b29f-477e-b27e-99362cde59f3, "updated_at" = 2026-04-02 08:51:06 where "key" = default_currency)

PHP 8.2.12
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - vendor\laravel\framework\src\Illuminate\Database\Connection.php:838
1 - vendor\laravel\framework\src\Illuminate\Database\Connection.php:794
2 - vendor\laravel\framework\src\Illuminate\Database\Connection.php:597
3 - vendor\laravel\framework\src\Illuminate\Database\Connection.php:549
4 - vendor\laravel\framework\src\Illuminate\Database\Query\Builder.php:4234
5 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Builder.php:1266
6 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Model.php:1316
7 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Model.php:1233
8 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Model.php:1090
9 - app\Models\SystemSetting.php:40
10 - app\Http\Controllers\Admin\SettingsController.php:42
11 - vendor\laravel\framework\src\Illuminate\Routing\ControllerDispatcher.php:46
12 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:265
13 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:211
14 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:822
15 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
16 - app\Http\Middleware\RoleMiddleware.php:34
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

POST /admin/settings

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/admin/settings
* **content-type**: application/x-www-form-urlencoded
* **content-length**: 91
* **origin**: http://127.0.0.1:8000
* **connection**: keep-alive
* **cookie**: XSRF-TOKEN=eyJpdiI6IkV6NE9CcHNuL0k3L1JDZG5YdjBsV3c9PSIsInZhbHVlIjoiK0l0bTUyc0I2ZWdWejY3ZGRLN0tTSkxuNmxETW5GZStnQXZKQW9DVHRRVzlNeDJ3MVkxMHRhai9MV3ZrenVRb1psaFBqZ3RTaHhxQ0FLL0RGZjBNU2xsTmpkVjM2TzJ5QVBIVHlpRHVIYlUyczAxc3BqdytMM2dhdlhkeWovdWQiLCJtYWMiOiJiNDdiZDc1MzM5ZGE2YjdmY2M1ZjYwOGU3NWQ5MDIzMDllNzM2YjAwY2IxNGEyYjhhZDRiZGRiYWEwZTgwY2Q1IiwidGFnIjoiIn0%3D; laravel-session=eyJpdiI6Ik8xaFREZkZYSk01TU9MQ1F5V0Q1TFE9PSIsInZhbHVlIjoidFpjNGR2dVU4c1VzU2VnMnpOVGhET1RzTEd1cjBrd214empuYjF0ak1FSW1zNU5qZ3pPeTlLTFg2RDhkWVJWUk5KNnZDME9rQWRKVWhaSlV4RmVnRVBWRTViWHhnYzBLcnNkTnBteElUemhjWVdmV25DSUdqUm40bkFiMmp4TE8iLCJtYWMiOiI0MWZmMWU0OTQ1NjZmZTA5NDExM2E0Mjg0ZTI0M2IxNjk5MzJmN2FkZThkNzk2Y2QwODQwZjRmZjNmYTIxMGYxIiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\Admin\SettingsController@updateSettings
route name: admin.settings.update
middleware: web, auth, role:admin

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = 'T6jyKYSrh0NJ5HvTNq3AAYqvLBW89Z6dRT4kjIu4' limit 1 (10.26 ms)
* sqlite - select * from "users" where "id" = '393abd66-b29f-477e-b27e-99362cde59f3' limit 1 (0.61 ms)
* sqlite - select * from "roles" where "roles"."id" = 'da835b85-463c-409d-9e00-f8a80c87e7e4' limit 1 (0.62 ms)
* sqlite - select * from "system_settings" where "key" = 'default_currency' limit 1 (0.57 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.36 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.49 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.41 ms)
