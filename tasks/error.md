http://127.0.0.1:8000/notifications/e1d9e686-7e23-4bdb-8ac7-1b97728ea9f9/read

# Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException - Method Not Allowed

The GET method is not supported for route notifications/e1d9e686-7e23-4bdb-8ac7-1b97728ea9f9/read. Supported methods: POST.

PHP 8.4.20
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - vendor/laravel/framework/src/Illuminate/Routing/AbstractRouteCollection.php:130
1 - vendor/laravel/framework/src/Illuminate/Routing/AbstractRouteCollection.php:115
2 - vendor/laravel/framework/src/Illuminate/Routing/AbstractRouteCollection.php:41
3 - vendor/laravel/framework/src/Illuminate/Routing/RouteCollection.php:184
4 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:777
5 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:764
6 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:753
7 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:200
8 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
9 - app/Http/Middleware/SecurityHeaders.php:18
10 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
11 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
12 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php:31
13 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
14 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php:21
15 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php:51
16 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
17 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePostSize.php:27
18 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
19 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php:109
20 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
21 - vendor/laravel/framework/src/Illuminate/Http/Middleware/HandleCors.php:61
22 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
23 - vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php:58
24 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
25 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/InvokeDeferredCallbacks.php:22
26 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
27 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePathEncoding.php:26
28 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
29 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
30 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:175
31 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:144
32 - vendor/laravel/framework/src/Illuminate/Foundation/Application.php:1220
33 - public/index.php:20
34 - vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php:23

## Request

GET /notifications/e1d9e686-7e23-4bdb-8ac7-1b97728ea9f9/read

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (X11; Linux x86_64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/laundry/services
* **connection**: keep-alive
* **cookie**: laravel-session=eyJpdiI6ImFMVzR0cysyNWg5K29IbnJrbTgvVEE9PSIsInZhbHVlIjoiSi8xN01FMVhsR3piLzF6YktQaFJmRllPSjRNVW1HOVJSMWZkRk1xTmJIazlsODBRNVVGS3dyVjJmUm1jUXhuVElTWnBlendSMmYzMTRKdVRZK1FNUnF3bFpXUUlpalJORWtScWhYWGxVaktPa3BSWG9EQVAwTUhZU3NrZmE0eGUiLCJtYWMiOiIzN2ZhYjM0ZThlYzExYTZkYzVjYWY0OTQwNzM3MTNjNWZkZTNlOGI2ZTY1MzFkODQyMjViNWJlMzFlYmU5NWU1IiwidGFnIjoiIn0%3D; XSRF-TOKEN=eyJpdiI6InpHYjYzSVR5MDZzOVd3dTNEZzFqZ0E9PSIsInZhbHVlIjoiZXRPTlora1dsdFp3emhieHVHSmRWNXJ0SFV1Wk8xblVTNFNCMnVGeXRZWDIwQ3dmdzBML2sweFRaaXJidmF5OXd6NHFnTmZ6MWxuOXlYY2FnaElWb0ZPN25ZUFVtYW1JQkJSUm1aY0NySUNJU3RnSXNBUS9QNE1rWENoMHFhWDAiLCJtYWMiOiJmMTY5YmUwZDk4YTM5YTUxODliNTc3ODA5YTk1MDM3MTVjODA1NDdiZDA1Njc5YmE5NzQ1NDFhODJjYWYwY2IyIiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

No routing data available.

## Route Parameters

No route parameter data available.

## Database Queries

* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (2.93 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.07 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.07 ms)
