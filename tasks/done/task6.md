# Spatie\Image\Exceptions\CouldNotLoadImage - Internal Server Error

Could not load image at path `C:\Users\DADY\Desktop\projects\hotel-management-system\storage\media-library/temp\Dty8p0EJOSGKM66VSMeOYIsAPeYScvYI/Un7tIDXFEH2PpPTzW3MzmAHEINX5J3Kithumb.png : Call to undefined function Spatie\Image\Drivers\Gd\imagecreatefromstring()`

PHP 8.2.12
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - vendor\spatie\image\src\Exceptions\CouldNotLoadImage.php:11
1 - vendor\spatie\image\src\Drivers\Gd\GdDriver.php:91
2 - vendor\spatie\image\src\Image.php:51
3 - vendor\spatie\laravel-medialibrary\src\Conversions\Actions\PerformManipulationsAction.php:38
4 - vendor\spatie\laravel-medialibrary\src\Conversions\Actions\PerformConversionAction.php:30
5 - vendor\spatie\laravel-medialibrary\src\Conversions\FileManipulator.php:80
6 - vendor\laravel\framework\src\Illuminate\Collections\Traits\EnumeratesValues.php:275
7 - vendor\spatie\laravel-medialibrary\src\Conversions\FileManipulator.php:80
8 - vendor\spatie\laravel-medialibrary\src\Conversions\FileManipulator.php:40
9 - vendor\spatie\laravel-medialibrary\src\MediaCollections\Filesystem.php:38
10 - vendor\spatie\laravel-medialibrary\src\MediaCollections\FileAdder.php:531
11 - vendor\spatie\laravel-medialibrary\src\MediaCollections\FileAdder.php:513
12 - vendor\spatie\laravel-medialibrary\src\MediaCollections\FileAdder.php:416
13 - app\Http\Controllers\RoomTypeController.php:89
14 - vendor\laravel\framework\src\Illuminate\Routing\ControllerDispatcher.php:46
15 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:265
16 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:211
17 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:822
18 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
19 - app\Http\Middleware\RoleMiddleware.php:34
20 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
21 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:50
22 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
23 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
24 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
25 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken.php:87
26 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
27 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
28 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
29 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
30 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
31 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
32 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
33 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
34 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
35 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
36 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
37 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
38 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
39 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
40 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
41 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
42 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
43 - app\Http\Middleware\SecurityHeaders.php:18
44 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
45 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
46 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
47 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
48 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
49 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
50 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
51 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
52 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
53 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
54 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
55 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
56 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
57 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
58 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
59 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
60 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
61 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:26
62 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
63 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
64 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
65 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
66 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
67 - public\index.php:20
68 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23

## Request

PUT /room-types/2283a469-57dd-49e9-8ba0-b3d8e7730a48

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/room-types/2283a469-57dd-49e9-8ba0-b3d8e7730a48/edit
* **content-type**: multipart/form-data; boundary=----geckoformboundary4565a8d2346e19a5b58da533516d62e9
* **content-length**: 387472
* **origin**: http://127.0.0.1:8000
* **connection**: keep-alive
* **cookie**: XSRF-TOKEN=eyJpdiI6Iklxem9yRGtTenBIWlZrdnlKaDBFUkE9PSIsInZhbHVlIjoibjJpM0dhdjdPTW1OM05rRHFqTHo1Zkw4TEpXY2FZT2c3aEI4Qk9tM0JoTFF6NUFkVjhCc2M5K0pjWVVjZFhtbkdyRnAzeFEzN1ZCSFdQOTc5RytqR3RqWkJ5V1NDcXZpbEdTTDZQdDZibEgyM3J3NzhMZ3dibE11Y3ZHUnFLR2kiLCJtYWMiOiJkN2QzMDk5YTljZDI1YWI3MDNjMWMzNGMwZTg3ZGMwZDhhODYzNjBjYmJiNjI0Y2M2NTAzZGI3ZjE1MDMyMWZjIiwidGFnIjoiIn0%3D; laravel-session=eyJpdiI6IlNOaXd6YWxVNHNvVy85WkxBZlRGRmc9PSIsInZhbHVlIjoiaWFCYU41ZDN6bFFITTltNG1zUXg2eUxqTnoyQmZ1a3hoYXpzcUY1V0J3OXBxdHZ0Qld1bkJRTmN0KzE5T2tvWWw2SXA0NURlRVJkb1IwVmh2L1EycmEwdnhCbnhJajRjSU95UHNqVUZLRFR3SThSSXJvaHlIdUxpVzJFd3dtNFIiLCJtYWMiOiJjZmZjMjI4NDMyNTlmMjk2OGFiYTcyNzlmNzE1ZmM3MDIxM2NmZTkyZjM1YWFjM2VlMjRlZmYxM2MwMjQ1ZDA5IiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\RoomTypeController@update
route name: room-types.update
middleware: web, auth, role:admin

## Route Parameters

{
    "room_type": {
        "id": "2283a469-57dd-49e9-8ba0-b3d8e7730a48",
        "name": "Standard Double",
        "code": "STD-DBL",
        "base_rate": "119.99",
        "max_occupancy": "4",
        "description": "Standard room with double bed",
        "created_at": "2026-02-06T21:26:51.000000Z",
        "updated_at": "2026-03-31T22:09:07.000000Z"
    }
}

## Database Queries

* sqlite - select * from "sessions" where "id" = 'mVfCKgy4PZLW4VYwFWPpaR0EoJBkyPQwQjJqPecn' limit 1 (13.36 ms)
* sqlite - select * from "users" where "id" = '393abd66-b29f-477e-b27e-99362cde59f3' limit 1 (0.76 ms)
* sqlite - select * from "room_types" where "id" = '2283a469-57dd-49e9-8ba0-b3d8e7730a48' limit 1 (0.74 ms)
* sqlite - select * from "roles" where "roles"."id" = 'da835b85-463c-409d-9e00-f8a80c87e7e4' limit 1 (0.73 ms)
* sqlite - select count(*) as aggregate from "room_types" where "code" = 'STD-DBL' and "id" <> '2283a469-57dd-49e9-8ba0-b3d8e7730a48' (0.81 ms)
* sqlite - update "room_types" set "base_rate" = '119.99', "max_occupancy" = '4', "updated_at" = '2026-03-31 22:09:07' where "id" = '2283a469-57dd-49e9-8ba0-b3d8e7730a48' (10.73 ms)
* sqlite - select max("order_column") as aggregate from "media" where "model_type" = 'App\Models\RoomType' and "model_id" = '2283a469-57dd-49e9-8ba0-b3d8e7730a48' (0.53 ms)
* sqlite - insert into "media" ("name", "file_name", "disk", "conversions_disk", "collection_name", "mime_type", "size", "custom_properties", "generated_conversions", "responsive_images", "manipulations", "model_id", "model_type", "uuid", "order_column", "updated_at", "created_at") values ('{640FDABE-6C13-48CC-9082-5B41F661D1D7}', '{640FDABE-6C13-48CC-9082-5B41F661D1D7}.png', 'public', 'public', 'room_type_image', 'image/png', 193083, '[]', '[]', '[]', '[]', '2283a469-57dd-49e9-8ba0-b3d8e7730a48', 'App\Models\RoomType', '3c20be71-88b7-45ef-a911-367d0fb06032', 1, '2026-03-31 22:09:07', '2026-03-31 22:09:07') (13.52 ms)
