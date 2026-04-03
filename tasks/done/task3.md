# Illuminate\Foundation\ViteManifestNotFoundException - Internal Server Error

Vite manifest not found at: C:\Users\DADY\Desktop\projects\hotel-management-system\public\build/manifest.json

PHP 8.2.12
Laravel 12.49.0
127.0.0.1:8000

## Stack Trace

0 - vendor\laravel\framework\src\Illuminate\Foundation\Vite.php:946
1 - vendor\laravel\framework\src\Illuminate\Foundation\Vite.php:384
2 - resources\views\laundry\layout.blade.php:7
3 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:123
4 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:124
5 - vendor\laravel\framework\src\Illuminate\View\Engines\PhpEngine.php:57
6 - vendor\laravel\framework\src\Illuminate\View\Engines\CompilerEngine.php:76
7 - vendor\laravel\framework\src\Illuminate\View\View.php:208
8 - vendor\laravel\framework\src\Illuminate\View\View.php:191
9 - vendor\laravel\framework\src\Illuminate\View\View.php:160
10 - resources\views\laundry\services\index.blade.php:77
11 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:123
12 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:124
13 - vendor\laravel\framework\src\Illuminate\View\Engines\PhpEngine.php:57
14 - vendor\laravel\framework\src\Illuminate\View\Engines\CompilerEngine.php:76
15 - vendor\laravel\framework\src\Illuminate\View\View.php:208
16 - vendor\laravel\framework\src\Illuminate\View\View.php:191
17 - vendor\laravel\framework\src\Illuminate\View\View.php:160
18 - vendor\laravel\framework\src\Illuminate\Http\Response.php:78
19 - vendor\laravel\framework\src\Illuminate\Http\Response.php:34
20 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:939
21 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:906
22 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
23 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
24 - app\Http\Middleware\RoleMiddleware.php:34
25 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
26 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:50
27 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
28 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
29 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
30 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken.php:87
31 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
32 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
33 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
34 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
35 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
36 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
37 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
38 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
39 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
40 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
41 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
42 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
43 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
44 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
45 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
46 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
47 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
48 - app\Http\Middleware\SecurityHeaders.php:18
49 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
50 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
51 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
52 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
53 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
54 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
55 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
56 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
57 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
58 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
59 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
60 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
61 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
62 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
63 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
64 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
65 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
66 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:26
67 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
68 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
69 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
70 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
71 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
72 - public\index.php:20
73 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23

## Request

GET /laundry/services

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/procurement
* **connection**: keep-alive
* **cookie**: XSRF-TOKEN=eyJpdiI6ImlKb3lxZkg0ckwrelFZL3ZXZVNOWFE9PSIsInZhbHVlIjoiYWN1MHR1em9CMzQ3VmlaTVFwMnlic2MvS0Zwb0s3MkxNM3pNaHF4azJaUTkwc2ZnZzhPc1RvenYzaHlFaXNvUG9DOGE3eHBMaHg0Sm8zeDFZSXhtRXcrTDF4WHMzT1Jxak9XSk1seVE5aS9USW00VnZZeGhINFl5WnpKcndSZmgiLCJtYWMiOiI3N2MxMWFjZGFmMTI5ZDYyZDliY2JkZWM1OTk0N2ZmYTZjYmVkZGFkMTc3OGQ5OGVhYTdiYjIwNjAzMmUwZGMyIiwidGFnIjoiIn0%3D; laravel-session=eyJpdiI6IkRGcHRpa1hmMm8vYjk0Q3hrQ1I5MEE9PSIsInZhbHVlIjoieElVTVpUcnBPU09iN0VUUXh2dG1NWmJvOWNqSGJUZ3ptR2FxYmFSRjlWOSs4dFJVS1JPbDA5Ui9FS1VPdHJlS2M2NSt5bjVQeU91eGRvbnNHWnNMMVltOXVWNmJpbnprUFBMN2tNMWRhTkNqYXlJZjJ5M2JpNjBJYVlHd1R0aVMiLCJtYWMiOiJhYWViMWZhNmJkN2IxYzU5MDhjYTM2NTRkMzhlM2Q0NTlhMWQ3MDk5ZmIyYTIzYjljZmZmYTZlYzQzMWI5NTFlIiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\Laundry\LaundryServiceController@index
route name: laundry.services.index
middleware: web, auth, role:laundry_manager,supervisor,store_manager,admin

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = 'mRmMkLNjDZ25JLtETzBMpXl81c8nzvF7eGcoatkS' limit 1 (7.73 ms)
* sqlite - select * from "users" where "id" = '393abd66-b29f-477e-b27e-99362cde59f3' limit 1 (0.62 ms)
* sqlite - select * from "roles" where "roles"."id" = 'da835b85-463c-409d-9e00-f8a80c87e7e4' limit 1 (0.5 ms)
* sqlite - select * from "laundry_services" where "is_active" = 1 (0.66 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."laundry_service_id" in ('e8ffb097-a434-4a1e-9740-9056acfa99d2', 'eb048cb5-e8d0-43bc-9245-0f7d04b0f76e', 'f55f3e03-5c08-43ef-bd07-2e2a6e707cdc', 'f7f3747c-151b-4dc2-ab3b-1fb21b3906cf') and "is_active" = 1 (1.63 ms)
