# Illuminate\Database\QueryException - Internal Server Error

SQLSTATE[23000]: Integrity constraint violation: 19 NOT NULL constraint failed: finance_payments.payment_type (Connection: sqlite, Database: C:\Users\DADY\Desktop\projects\hotel-management-system\database\database.sqlite, SQL: insert into "finance_payments" ("checkout_id", "order_id", "method", "currency", "amount_usd", "exchange_rate", "status", "created_by", "id", "payment_number", "updated_at", "created_at") values (?, ?, card, TZS, 6, 2500, completed, 01c5525b-1ea0-4a0f-9cd9-293a70e13574, 4d765915-c8aa-4ca6-bdc5-219952e5fea4, PAY-20260402-0001, 2026-04-02 19:49:24, 2026-04-02 19:49:24))

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
14 - app\Http\Controllers\Laundry\LaundryOrderController.php:324
15 - vendor\laravel\framework\src\Illuminate\Database\Concerns\ManagesTransactions.php:35
16 - vendor\laravel\framework\src\Illuminate\Database\DatabaseManager.php:491
17 - vendor\laravel\framework\src\Illuminate\Support\Facades\Facade.php:363
18 - app\Http\Controllers\Laundry\LaundryOrderController.php:271
19 - vendor\laravel\framework\src\Illuminate\Routing\ControllerDispatcher.php:46
20 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:265
21 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:211
22 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:822
23 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
24 - app\Http\Middleware\RoleMiddleware.php:34
25 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
26 - app\Http\Middleware\SetLocale.php:31
27 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
28 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:50
29 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
30 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
31 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
32 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken.php:87
33 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
34 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
35 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
36 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
37 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
38 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
39 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
40 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
41 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
42 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
43 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
44 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
45 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
46 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
47 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
48 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
49 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
50 - app\Http\Middleware\SecurityHeaders.php:18
51 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
52 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
53 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
54 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
55 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
56 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
57 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
58 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
59 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
60 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
61 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
62 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
63 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
64 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
65 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
66 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
67 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
68 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:26
69 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
70 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
71 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
72 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
73 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
74 - public\index.php:20
75 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23

## Request

POST /laundry/orders/47de73a9-f5c1-48b1-8c86-ec01aed96a3c/settle

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/laundry/orders/47de73a9-f5c1-48b1-8c86-ec01aed96a3c
* **content-type**: application/x-www-form-urlencoded
* **content-length**: 126
* **origin**: http://127.0.0.1:8000
* **connection**: keep-alive
* **cookie**: XSRF-TOKEN=eyJpdiI6Ilpha0RnK2Q2Zk1VTXB0bllmVG1RaVE9PSIsInZhbHVlIjoiQnIzWEpUcXA0cktLSmRHaFF2VG5FR01WMmFsQy85c3lpdW9KOFVlUTA0UWZqYW93VXkvYnlhckRkYmJ1RXhsYmZ2UysrMkJKcnVkVzc2UHZ1aTdQdlhRa095ZmFReUhNSUk0eGVOWDk0NHVhcnVrQlFMQkJMYnNObnpYUy95L08iLCJtYWMiOiJiMTNmOTI1NzkwNTBmOTE0NWJmMGY0NTQxMzBhYmExNjdjNzFjMzlhNTM3NThiMDIyNDZmNjQ2MmY4MDU4OTBjIiwidGFnIjoiIn0%3D; laravel-session=eyJpdiI6Ikw4aTMvOURCNEh3dFp6UXQ5WnpwQ2c9PSIsInZhbHVlIjoiL0llcnhlUU9aeTdMSVAvUHA0MTU4WWtEaHpjN2JyLzhrQlEvWTBGcFBNc2lFMzdmSEg3cGQrR1p4K2pneHgzK05vaWg3Ty9PcXB1Si91b01OcXZhRE8vTkU5aGErTU8zckdHektIeHBISGRsQ3ZhQ3lndUdOQjdSem5uZnFxMFIiLCJtYWMiOiI4NzAwODI0MTEyNjIxYWM3ZDM0ZDc4NjU0ZjllOWE1NDQ1OTU4NmRhZDA3Zjc2Nzk0ZDkzZjU1M2JkYWZkMjQ0IiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: App\Http\Controllers\Laundry\LaundryOrderController@settle
route name: laundry.orders.settle
middleware: web, auth, role:cashier,front_desk,laundry_manager,supervisor,manager

## Route Parameters

{
    "laundryOrder": {
        "id": "47de73a9-f5c1-48b1-8c86-ec01aed96a3c",
        "order_number": "LND-20260402-0002",
        "customer_type": "guest",
        "booking_id": "194badd1-28cd-409e-b030-52e77b437914",
        "room_number": "MAIN-001",
        "customer_name": null,
        "customer_phone": null,
        "status": "settled",
        "special_instructions": null,
        "subtotal": "15000.00",
        "discount": "0.00",
        "total": "15000.00",
        "payment_method": "card",
        "expected_ready_at": "2026-04-04T19:37:29.000000Z",
        "ready_at": null,
        "delivered_at": null,
        "collected_at": null,
        "settled_at": "2026-04-02T19:49:23.000000Z",
        "received_by": "01c5525b-1ea0-4a0f-9cd9-293a70e13574",
        "processed_by": null,
        "delivered_by": null,
        "settled_by": "01c5525b-1ea0-4a0f-9cd9-293a70e13574",
        "created_at": "2026-04-02T19:37:29.000000Z",
        "updated_at": "2026-04-02T19:49:23.000000Z"
    }
}

## Database Queries

* sqlite - select * from "sessions" where "id" = 'iNp6NBtua4yryQwFwpgUFwu8OdTR8kC2jAAFOGMv' limit 1 (8.87 ms)
* sqlite - select * from "users" where "id" = '01c5525b-1ea0-4a0f-9cd9-293a70e13574' limit 1 (0.59 ms)
* sqlite - select * from "laundry_orders" where "id" = '47de73a9-f5c1-48b1-8c86-ec01aed96a3c' limit 1 (0.59 ms)
* sqlite - select * from "roles" where "roles"."id" = 'b45852e1-5af0-413e-87be-65926dc42e0a' limit 1 (0.47 ms)
* sqlite - update "laundry_orders" set "status" = 'settled', "payment_method" = 'card', "settled_at" = '2026-04-02 19:49:23', "settled_by" = '01c5525b-1ea0-4a0f-9cd9-293a70e13574', "updated_at" = '2026-04-02 19:49:23' where "id" = '47de73a9-f5c1-48b1-8c86-ec01aed96a3c' (3.27 ms)
* sqlite - select count(*) as aggregate from "journal_entries" where strftime('%Y-%m-%d', "created_at") = cast('2026-04-02' as text) (0.19 ms)
* sqlite - insert into "journal_entries" ("entry_date", "description", "source", "source_id", "reference", "total_debit", "total_credit", "status", "created_by", "posted_by", "posted_at", "id", "entry_no", "updated_at", "created_at") values ('2026-04-02 00:00:00', 'Laundry revenue — LND-20260402-0002', 'laundry', '47de73a9-f5c1-48b1-8c86-ec01aed96a3c', 'LND-20260402-0002', 15000, 15000, 'posted', '01c5525b-1ea0-4a0f-9cd9-293a70e13574', '01c5525b-1ea0-4a0f-9cd9-293a70e13574', '2026-04-02 19:49:23', '019d4fbe-49ae-720a-be7d-d2f8dd779229', 'JE-20260402-0001', '2026-04-02 19:49:24', '2026-04-02 19:49:24') (0.67 ms)
* sqlite - select * from "accounts" where "code" = '1200' limit 1 (0.24 ms)
* sqlite - insert into "journal_lines" ("journal_entry_id", "account_id", "type", "amount", "notes", "id", "updated_at", "created_at") values ('019d4fbe-49ae-720a-be7d-d2f8dd779229', '019d3563-fef0-727b-92bb-c181592d97a2', 'debit', 15000, NULL, '019d4fbe-49b8-7239-8980-1475235a304b', '2026-04-02 19:49:24', '2026-04-02 19:49:24') (0.33 ms)
* sqlite - select * from "accounts" where "code" = '4300' limit 1 (0.15 ms)
* sqlite - insert into "journal_lines" ("journal_entry_id", "account_id", "type", "amount", "notes", "id", "updated_at", "created_at") values ('019d4fbe-49ae-720a-be7d-d2f8dd779229', '019d3563-ff8b-7329-b641-8f518d8eabb1', 'credit', 12711.86, NULL, '019d4fbe-49ba-710f-a0fd-fe98bb209c5b', '2026-04-02 19:49:24', '2026-04-02 19:49:24') (0.12 ms)
* sqlite - select * from "accounts" where "code" = '2200' limit 1 (0.15 ms)
* sqlite - insert into "journal_lines" ("journal_entry_id", "account_id", "type", "amount", "notes", "id", "updated_at", "created_at") values ('019d4fbe-49ae-720a-be7d-d2f8dd779229', '019d3563-ff2f-73d5-95d1-fd1e5104fc87', 'credit', 2288.14, NULL, '019d4fbe-49bc-7227-b3e1-af080f63199b', '2026-04-02 19:49:24', '2026-04-02 19:49:24') (0.17 ms)
* sqlite - select "value" from "system_settings" where "key" = 'tzs_exchange_rate' limit 1 (0.21 ms)
* sqlite - select count(*) as aggregate from "finance_payments" where strftime('%Y-%m-%d', "created_at") = cast('2026-04-02' as text) (0.25 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.5 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.39 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.35 ms)
