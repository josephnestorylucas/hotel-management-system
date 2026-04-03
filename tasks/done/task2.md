# RuntimeException - Internal Server Error

Room is no longer available for the selected dates.

PHP 8.2.12
Laravel 12.49.0
127.0.0.1:8000

## Stack Trace

0 - app\Models\Booking.php:261
1 - vendor\laravel\framework\src\Illuminate\Database\Concerns\ManagesTransactions.php:35
2 - vendor\laravel\framework\src\Illuminate\Database\DatabaseManager.php:491
3 - vendor\laravel\framework\src\Illuminate\Support\Facades\Facade.php:363
4 - app\Models\Booking.php:258
5 - app\Http\Controllers\ReservationController.php:259
6 - vendor\laravel\framework\src\Illuminate\Routing\ControllerDispatcher.php:46
7 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:265
8 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:211
9 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:822
10 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
11 - app\Http\Middleware\RoleMiddleware.php:34
12 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
13 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:50
14 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
15 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
16 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
17 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken.php:87
18 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
19 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
20 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
21 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
22 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
23 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
24 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
25 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
26 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
27 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
28 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
29 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
30 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
31 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
32 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
33 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
34 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
35 - app\Http\Middleware\SecurityHeaders.php:18
36 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
37 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
38 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
39 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
40 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
41 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
42 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
43 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
44 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
45 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
46 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
47 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
48 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
49 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
50 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
51 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
52 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
53 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:26
54 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
55 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
56 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
57 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
58 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
59 - public\index.php:20
60 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23

## Request

POST /reservations/3294d4ca-72e9-4268-bed5-192296805158/check-in

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/reservations
* **content-type**: application/x-www-form-urlencoded
* **content-length**: 47
* **origin**: http://127.0.0.1:8000
* **connection**: keep-alive
* **cookie**: XSRF-TOKEN=eyJpdiI6IlJBajE3aTkyOG8wbmVLZ011ZHhndUE9PSIsInZhbHVlIjoidWNPeVNBdFg2NDZEYXRHaUZlZ0R2NmlIc2JJaUlvbHkyYmI3MURzUXJLWDhmREVYbm1YRk43RW1adDlGVExwWjIwVkp6ZHFWcTVwbk5ZaThSUnFOcUFteFlrWmVVNk9IeXE2ZElWaXBMYkNZTzFwVVZmUEQ0NHhVSUsyS3ZMVGoiLCJtYWMiOiIwZDc4MzU0MTk4ZmUxMjc3Y2Y4MmI4MTY5YTNiMTZlYTVkN2I0NWIwOWI3ZjJmNzQ2OTliZjE5MGMyNmY3YTdlIiwidGFnIjoiIn0%3D; laravel-session=eyJpdiI6InNxbDNFTktMOHBwa0h4ZjZjZE1OUHc9PSIsInZhbHVlIjoiNVIxc3JnbHJXZnN6Z0FqVDdZMXFja1dCeWtvNFNncVhtTSs1RXQ5TS9BZzA4VkNQZXZvSys5bWZEOHB1b0VDT3N2SWQvYnRFMHR3Z2ltQnlWVlowY1d3T2FSdFFIRm83UjBoM3dTMDRxRWxHNHBiSEVLZGxHd1RiaE90VkRkcS8iLCJtYWMiOiJiY2Q4NWQxMGM4NmU1MDVjYjE3MGY3ZGQzZGVlYTk5YjczMDI0NTBlNzQ5ODI2NmY1YjhjMGY3OTFiNWJjZjhkIiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\ReservationController@checkIn
route name: reservations.check-in
middleware: web, auth, role:admin,supervisor,front_desk,manager

## Route Parameters

{
    "reservation": {
        "id": "3294d4ca-72e9-4268-bed5-192296805158",
        "reservation_number": "RES-69865C9C41EE4",
        "room_id": "cd952176-3ab6-472d-b98a-86d67468891c",
        "guest_name": "Emily Davis",
        "guest_phone": "+1-555-0104",
        "guest_email": "emily.davis@email.com",
        "check_in_date": "2026-02-01T00:00:00.000000Z",
        "check_out_date": "2026-02-04T00:00:00.000000Z",
        "number_of_guests": 3,
        "status": "confirmed",
        "estimated_amount": "660.00",
        "created_by": "01c5525b-1ea0-4a0f-9cd9-293a70e13574",
        "created_at": "2026-02-06T21:26:52.000000Z",
        "updated_at": "2026-03-29T18:00:01.000000Z",
        "guest_id": null,
        "booking_id": null
    }
}

## Database Queries

* sqlite - select * from "sessions" where "id" = 'VsNmlapYpDfzQ2qhu7C6Et4yAWoW7NzUhQs0fmME' limit 1 (12.2 ms)
* sqlite - select * from "users" where "id" = '01c5525b-1ea0-4a0f-9cd9-293a70e13574' limit 1 (0.63 ms)
* sqlite - select * from "reservations" where "id" = '3294d4ca-72e9-4268-bed5-192296805158' limit 1 (0.68 ms)
* sqlite - select * from "roles" where "roles"."id" = 'b45852e1-5af0-413e-87be-65926dc42e0a' limit 1 (0.57 ms)
* sqlite - select exists(select * from "bookings" where "room_id" = 'cd952176-3ab6-472d-b98a-86d67468891c' and "status" not in ('cancelled', 'checked_out') and "check_in_date" < '2026-02-04 00:00:00' and "check_out_date" > '2026-02-01 00:00:00') as "exists" (0.9 ms)
* sqlite - select exists(select * from "reservations" where "room_id" = 'cd952176-3ab6-472d-b98a-86d67468891c' and "status" in ('pending', 'confirmed') and "check_in_date" < '2026-02-04 00:00:00' and "check_out_date" > '2026-02-01 00:00:00') as "exists" (0.15 ms)
