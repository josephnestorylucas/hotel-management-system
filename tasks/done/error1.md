# Illuminate\Database\QueryException - Internal Server Error

SQLSTATE[HY000]: General error: 20 datatype mismatch (Connection: sqlite, Database: C:\Users\DADY\Desktop\projects\hotel-management-system\database\database.sqlite, SQL: insert into "receipts" ("module", "receiptable_type", "receiptable_id", "customer_name", "customer_phone", "items_snapshot", "subtotal", "discount", "tax", "total", "amount_paid", "balance", "currency", "payment_method", "payment_status", "transaction_reference", "cashier_id", "cashier_name", "notes", "issued_at", "id", "receipt_number", "print_count", "updated_at", "created_at") values (laundry, App\Models\LaundryOrder, 109b542a-02b9-4d23-8e56-7260e2a0cea3, mmm, 99999999, [{"name":"Underwear","details":"Wash Only","quantity":1,"unit_price":"1000.00","amount":"1000.00"}], 1000, 0, 0, 1000, 0, 1000, TZS, ?, unpaid, ?, efcdd719-57e9-4015-82c6-04f8db24eac6, Front Desk Officer, iiiiiiiiiiiiiiiiiii, 2026-04-03 02:27:52, 254134d1-b870-4f1e-bcf6-0f8f3b44a986, HMS-2026-000001, 0, 2026-04-03 02:39:52, 2026-04-03 02:39:52))

PHP 8.2.12
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - vendor\laravel\framework\src\Illuminate\Database\Connection.php:838
1 - vendor\laravel\framework\src\Illuminate\Database\Connection.php:794
2 - vendor\laravel\framework\src\Illuminate\Database\Connection.php:573
3 - vendor\laravel\framework\src\Illuminate\Database\Connection.php:537
4 - vendor\laravel\framework\src\Illuminate\Database\Query\Builder.php:4121
5 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Builder.php:2237
6 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Model.php:1412
7 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Model.php:1240
8 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Builder.php:1219
9 - vendor\laravel\framework\src\Illuminate\Support\helpers.php:393
10 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Builder.php:1218
11 - vendor\laravel\framework\src\Illuminate\Support\Traits\ForwardsCalls.php:23
12 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Model.php:2540
13 - vendor\laravel\framework\src\Illuminate\Database\Eloquent\Model.php:2556
14 - app\Services\ReceiptService.php:46
15 - app\Services\ReceiptService.php:35
16 - app\Http\Controllers\ReceiptController.php:55
17 - vendor\laravel\framework\src\Illuminate\Routing\ControllerDispatcher.php:46
18 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:265
19 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:211
20 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:822
21 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
22 - app\Http\Middleware\SetLocale.php:31
23 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
24 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:50
25 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
26 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
27 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
28 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken.php:87
29 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
30 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
31 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
32 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
33 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
34 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
35 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
36 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
37 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
38 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
39 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
40 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
41 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
42 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
43 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
44 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
45 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
46 - app\Http\Middleware\SecurityHeaders.php:18
47 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
48 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
49 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
50 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
51 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
52 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
53 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
54 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
55 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
56 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
57 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
58 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
59 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
60 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
61 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
62 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
63 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
64 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:26
65 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
66 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
67 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
68 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
69 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
70 - public\index.php:20
71 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23

## Request

GET /receipts/laundry/109b542a-02b9-4d23-8e56-7260e2a0cea3

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/laundry/orders/109b542a-02b9-4d23-8e56-7260e2a0cea3
* **connection**: keep-alive
* **cookie**: XSRF-TOKEN=eyJpdiI6IitFcldGSnNVUHB5Mkd2eXJqSDFSa3c9PSIsInZhbHVlIjoiRmNabjBXRko3WDlKWjZaSHMxOWxpWWFsUWtTYll0TEtNV1FwUEhlYmpFWHMzWWR4QVlSYjE2Zjd1SXorQzU1MG02RDdMNVV5MFp5VnhGdjVpVXRSNS94eFNXQWUxQ0I2KzkySEpvbkVacnhIdjBEVlRiM1duaW50ak0rR2tZckQiLCJtYWMiOiJiZjE2MjIxZDQzYzZkYWY0YWYzYzI5NzE2YzFhZWIyNjg5YWUyOTRiZWZlNzQ4ZjA2ZGY4MzczMzJhYjU3YjkzIiwidGFnIjoiIn0%3D; laravel-session=eyJpdiI6IjFXeHVvK2hBV2dzSWtyYjcrMFVydVE9PSIsInZhbHVlIjoiQTlsVGIrcWY3YnA1YjBxTVQ3QUhTaDU0eGtlVWI4ZEtMNmRiMFRpaUdXcWYwNnM0b2tCbzVveHlGTnFGR3czQzF0R0pNdXpSaHp2ZWZvYzdmdmhxVWNVZkh5U2Q1OVV5OERpNnVBcjJlWmJYWjErcUs0YklxWUJHZEh5cm1TRm0iLCJtYWMiOiI1ZjE3M2Q1OWNiZjRhNmZlMTk4MTQwMTEzM2FkMzMxOGEzYzVhOTMzNDcyYmVkODg0MzI1ZGY3MWUyMTYyYjNjIiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\ReceiptController@laundry
route name: receipts.laundry
middleware: web, auth

## Route Parameters

{
    "laundryOrder": {
        "id": "109b542a-02b9-4d23-8e56-7260e2a0cea3",
        "order_number": "LND-20260403-0001",
        "customer_type": "walkin",
        "booking_id": null,
        "room_number": null,
        "customer_name": "mmm",
        "customer_phone": "99999999",
        "status": "received",
        "special_instructions": "iiiiiiiiiiiiiiiiiii",
        "subtotal": "1000.00",
        "discount": "0.00",
        "total": "1000.00",
        "payment_method": null,
        "expected_ready_at": "2026-04-03T14:27:52.000000Z",
        "ready_at": null,
        "delivered_at": null,
        "collected_at": null,
        "settled_at": null,
        "received_by": "efcdd719-57e9-4015-82c6-04f8db24eac6",
        "processed_by": null,
        "delivered_by": null,
        "settled_by": null,
        "created_at": "2026-04-03T02:27:52.000000Z",
        "updated_at": "2026-04-03T02:27:52.000000Z"
    }
}

## Database Queries

* sqlite - select * from "sessions" where "id" = '8KFh1AgDf7gA8UmQK7MbwoR0Po5rvHgnxU2B0NZ2' limit 1 (8.58 ms)
* sqlite - select * from "users" where "id" = 'efcdd719-57e9-4015-82c6-04f8db24eac6' limit 1 (0.44 ms)
* sqlite - select * from "laundry_orders" where "id" = '109b542a-02b9-4d23-8e56-7260e2a0cea3' limit 1 (0.55 ms)
* sqlite - select * from "roles" where "roles"."id" = 'edc84afd-bb55-484d-9fbb-9939f1c771eb' limit 1 (0.53 ms)
* sqlite - select * from "receipts" where "receiptable_type" = 'App\Models\LaundryOrder' and "receiptable_id" = '109b542a-02b9-4d23-8e56-7260e2a0cea3' limit 1 (0.64 ms)
* sqlite - select * from "laundry_order_items" where "laundry_order_items"."laundry_order_id" in ('109b542a-02b9-4d23-8e56-7260e2a0cea3') (0.44 ms)
* sqlite - select * from "laundry_service_items" where "laundry_service_items"."id" in ('539525cb-47f9-4c90-b42b-5d6e3ad7b599') (0.63 ms)
* sqlite - select * from "laundry_services" where "laundry_services"."id" = '821fbb75-8738-45b5-a6ee-e4c1a885ac47' limit 1 (0.59 ms)
* sqlite - select * from "receipts" where "receipt_number" like 'HMS-2026-%' order by "receipt_number" desc limit 1 (0.84 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.42 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-system_currency', 'laravel-cache-illuminate:cache:flexible:created:system_currency') and "expiration" <= 1775183993 (9.62 ms)
* sqlite - select "value" from "system_settings" where "key" = 'default_currency' limit 1 (0.55 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1775184053, 'laravel-cache-system_currency', 's:3:"USD";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (9.74 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.43 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.54 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-tzs_exchange_rate', 'laravel-cache-illuminate:cache:flexible:created:tzs_exchange_rate') and "expiration" <= 1775183993 (8.13 ms)
* sqlite - select "value" from "system_settings" where "key" = 'tzs_exchange_rate' limit 1 (0.31 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1775184053, 'laravel-cache-tzs_exchange_rate', 's:4:"2500";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (8.07 ms)
