# TypeError - Internal Server Error

App\Services\NotificationService::markAllAsRead(): Argument #1 ($userId) must be of type int, string given, called in C:\Users\DADY\Desktop\projects\hotel-management-system\app\Http\Controllers\NotificationController.php on line 27

PHP 8.2.12
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - app\Services\NotificationService.php:123
1 - app\Http\Controllers\NotificationController.php:27
2 - vendor\laravel\framework\src\Illuminate\Routing\ControllerDispatcher.php:46
3 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:265
4 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:211
5 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:822
6 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
7 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:50
8 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
9 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
10 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
11 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken.php:87
12 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
13 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
14 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
15 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
16 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
17 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
18 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
19 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
20 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
21 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
22 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
23 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
24 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
25 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
26 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
27 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
28 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
29 - app\Http\Middleware\SecurityHeaders.php:18
30 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
31 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
32 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
33 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
34 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
35 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
36 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
37 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
38 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
39 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
40 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
41 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
42 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
43 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
44 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
45 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
46 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
47 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:26
48 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
49 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
50 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
51 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
52 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
53 - public\index.php:20
54 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23

## Request

GET /notifications

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/dashboard
* **connection**: keep-alive
* **cookie**: XSRF-TOKEN=eyJpdiI6ImRVaUhzb3V2dzdybjRSMzFGdno2WkE9PSIsInZhbHVlIjoieWVUZDJlZHVpSS94S05qYUVCbVd6VkdKNnNPMHdGQXZYQU5WeVI4QUFHUmFsazdGRFFFT2lHY2E0cCtxeGY3YnFBSzRyOE1vMFlFM2U0R2hyckt4VmZmMWU2cnhZcUtiQXVqUzVBandUMHJxZUV0dVNacmhYaTNETEpYeUxVRy8iLCJtYWMiOiJiMDc4NDhiYmNlYmQ3OTk4ZWNhZDU5NzBjZmQzMmQ2MjkyOGIzNjMzZTFmNGMxYTJjZmYwOTU2YmI0YjBjNjQ2IiwidGFnIjoiIn0%3D; laravel-session=eyJpdiI6InBGbUpMUnpjanMrWnFhaTNwcnVJWHc9PSIsInZhbHVlIjoiRnpuSWlZakxnMzNnenlFN2IrWUlzWHNGUXY5cnhQT3dwSDRneXJIZEJRZlRsVVpCSHFXTnU1cFRCVWpKa0NsVVpuS0tIcHJVQi95VTN3ZnBHVmlRcEVYRjV1dXVzOHd1NEZlZkZPSUtjNWdwVUtqVzhkcUdlNkQzWEdxcjg0VG0iLCJtYWMiOiIwYjNkMGNiYTQ5MWMyYmRhZGJlMTYyYzBkODA4YjRlNzZhMmJmZGYyMzA1MGJmMWFkZWVmNjMwZWYyNGFhNjAyIiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\NotificationController@index
route name: notifications.index
middleware: web, auth

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "sessions" where "id" = 'mVfCKgy4PZLW4VYwFWPpaR0EoJBkyPQwQjJqPecn' limit 1 (12.08 ms)
* sqlite - select * from "users" where "id" = '393abd66-b29f-477e-b27e-99362cde59f3' limit 1 (0.81 ms)
* sqlite - select count(*) as aggregate from "store_notifications" where "user_id" = '393abd66-b29f-477e-b27e-99362cde59f3' (0.81 ms)
