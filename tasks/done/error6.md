# ErrorException - Internal Server Error

Undefined array key "source_module"

PHP 8.2.12
Laravel 12.56.0
127.0.0.1:8000

## Stack Trace

0 - app\Models\FinancialTransaction.php:40
1 - app\Http\Controllers\Laundry\LaundryOrderController.php:338
2 - vendor\laravel\framework\src\Illuminate\Database\Concerns\ManagesTransactions.php:35
3 - vendor\laravel\framework\src\Illuminate\Database\DatabaseManager.php:491
4 - vendor\laravel\framework\src\Illuminate\Support\Facades\Facade.php:363
5 - app\Http\Controllers\Laundry\LaundryOrderController.php:271
6 - vendor\laravel\framework\src\Illuminate\Routing\ControllerDispatcher.php:46
7 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:265
8 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:211
9 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:822
10 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
11 - app\Http\Middleware\RoleMiddleware.php:34
12 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
13 - app\Http\Middleware\SetLocale.php:31
14 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
15 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:50
16 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
17 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
18 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
19 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken.php:87
20 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
21 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
22 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
23 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
24 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
25 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
26 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
27 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
28 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
29 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
30 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
31 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
32 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
33 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
34 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
35 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
36 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
37 - app\Http\Middleware\SecurityHeaders.php:18
38 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
39 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
40 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
41 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
42 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
43 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
44 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
45 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
46 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
47 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
48 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
49 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
50 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
51 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
52 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
53 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
54 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
55 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:26
56 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
57 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
58 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
59 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
60 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
61 - public\index.php:20
62 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23

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
* **cookie**: XSRF-TOKEN=eyJpdiI6IllzOHhTVDRIYkhtSHNEcmFrcjlpeVE9PSIsInZhbHVlIjoibDMxSHBYQ3pCL014OXh0a0o5ckw5RHVTQjJsdFo1T0xLNC9adUxOQlpNcDlKemFBWkJacmhmSnNRanFmQ1pvVStqb3pITmxwUTJyQWc2dDREOXYwTGxYbGhNZm1IMzY5ekpnR3FnRDJocGhlVndNdnZWR05KaS80bzc2d2o0SG4iLCJtYWMiOiJhMGQ5YTYwMGU2ZmFjN2M4NDYxNzNkYTJjYTdiZDFjOWE1MzNhNDExMWFkYzBmZDdjNzBhODgxZjlkZjVmMTFkIiwidGFnIjoiIn0%3D; laravel-session=eyJpdiI6Im5KZ3RzQjdodk4rVWVrZ1M4WlNnV3c9PSIsInZhbHVlIjoiWDlWNFRyR1JzZDM4TmZmWTM3UW41eWVnQVpsTWRJeXhFMFFxRGhuYU9nM0VvVFl0aThwaEtuZDVhYkNFazA1N3daKzJqK2Y2b0V1a216SHBKSDZHbHpxSGk1ajM0NkEvdEY1KzEwSXJ3dlByd2E4aXhPa1VIM1o4YjVDbnZGZmMiLCJtYWMiOiJkZmNjYWFhMGZjYjQyZTg2MjJkZmY3NmEzNDAwYmRhNDgzZGE4MDhhNDU5ZThhYTVkYmM3ODI0MTM3MGJhMTExIiwidGFnIjoiIn0%3D
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
        "settled_at": "2026-04-02T19:53:17.000000Z",
        "received_by": "01c5525b-1ea0-4a0f-9cd9-293a70e13574",
        "processed_by": null,
        "delivered_by": null,
        "settled_by": "01c5525b-1ea0-4a0f-9cd9-293a70e13574",
        "created_at": "2026-04-02T19:37:29.000000Z",
        "updated_at": "2026-04-02T19:53:17.000000Z"
    }
}

## Database Queries

* sqlite - select * from "sessions" where "id" = 'iNp6NBtua4yryQwFwpgUFwu8OdTR8kC2jAAFOGMv' limit 1 (12.63 ms)
* sqlite - select * from "users" where "id" = '01c5525b-1ea0-4a0f-9cd9-293a70e13574' limit 1 (0.58 ms)
* sqlite - select * from "laundry_orders" where "id" = '47de73a9-f5c1-48b1-8c86-ec01aed96a3c' limit 1 (0.68 ms)
* sqlite - select * from "roles" where "roles"."id" = 'b45852e1-5af0-413e-87be-65926dc42e0a' limit 1 (1.43 ms)
* sqlite - update "laundry_orders" set "status" = 'settled', "payment_method" = 'card', "settled_at" = '2026-04-02 19:53:17', "settled_by" = '01c5525b-1ea0-4a0f-9cd9-293a70e13574', "updated_at" = '2026-04-02 19:53:17' where "id" = '47de73a9-f5c1-48b1-8c86-ec01aed96a3c' (3.96 ms)
* sqlite - select count(*) as aggregate from "journal_entries" where strftime('%Y-%m-%d', "created_at") = cast('2026-04-02' as text) (0.28 ms)
* sqlite - insert into "journal_entries" ("entry_date", "description", "source", "source_id", "reference", "total_debit", "total_credit", "status", "created_by", "posted_by", "posted_at", "id", "entry_no", "updated_at", "created_at") values ('2026-04-02 00:00:00', 'Laundry revenue — LND-20260402-0002', 'laundry', '47de73a9-f5c1-48b1-8c86-ec01aed96a3c', 'LND-20260402-0002', 15000, 15000, 'posted', '01c5525b-1ea0-4a0f-9cd9-293a70e13574', '01c5525b-1ea0-4a0f-9cd9-293a70e13574', '2026-04-02 19:53:17', '019d4fc1-d8a3-72bb-bd86-5c7972027881', 'JE-20260402-0001', '2026-04-02 19:53:17', '2026-04-02 19:53:17') (0.62 ms)
* sqlite - select * from "accounts" where "code" = '1200' limit 1 (0.27 ms)
* sqlite - insert into "journal_lines" ("journal_entry_id", "account_id", "type", "amount", "notes", "id", "updated_at", "created_at") values ('019d4fc1-d8a3-72bb-bd86-5c7972027881', '019d3563-fef0-727b-92bb-c181592d97a2', 'debit', 15000, NULL, '019d4fc1-d8ab-710d-bace-fb88bd246fa6', '2026-04-02 19:53:17', '2026-04-02 19:53:17') (0.25 ms)
* sqlite - select * from "accounts" where "code" = '4300' limit 1 (0.13 ms)
* sqlite - insert into "journal_lines" ("journal_entry_id", "account_id", "type", "amount", "notes", "id", "updated_at", "created_at") values ('019d4fc1-d8a3-72bb-bd86-5c7972027881', '019d3563-ff8b-7329-b641-8f518d8eabb1', 'credit', 12711.86, NULL, '019d4fc1-d8ac-7142-b4bb-154a8f3434e2', '2026-04-02 19:53:17', '2026-04-02 19:53:17') (0.16 ms)
* sqlite - select * from "accounts" where "code" = '2200' limit 1 (0.14 ms)
* sqlite - insert into "journal_lines" ("journal_entry_id", "account_id", "type", "amount", "notes", "id", "updated_at", "created_at") values ('019d4fc1-d8a3-72bb-bd86-5c7972027881', '019d3563-ff2f-73d5-95d1-fd1e5104fc87', 'credit', 2288.14, NULL, '019d4fc1-d8ae-71f6-995c-b11b9b2b749c', '2026-04-02 19:53:17', '2026-04-02 19:53:17') (0.13 ms)
* sqlite - select "value" from "system_settings" where "key" = 'tzs_exchange_rate' limit 1 (0.23 ms)
* sqlite - select count(*) as aggregate from "finance_payments" where strftime('%Y-%m-%d', "created_at") = cast('2026-04-02' as text) (0.28 ms)
* sqlite - insert into "finance_payments" ("payment_type", "checkout_id", "order_id", "method", "currency", "amount", "amount_usd", "exchange_rate", "status", "notes", "created_by", "id", "payment_number", "updated_at", "created_at") values ('walkin', NULL, NULL, 'card', 'TZS', '15000.00', 6, 2500, 'completed', 'Laundry Order LND-20260402-0002', '01c5525b-1ea0-4a0f-9cd9-293a70e13574', '034cd834-34ce-433d-8375-ffc3772057ed', 'PAY-20260402-0001', '2026-04-02 19:53:17', '2026-04-02 19:53:17') (3.34 ms)
* sqlite - select count(*) as aggregate from "financial_transactions" where strftime('%Y-%m-%d', "created_at") = cast('2026-04-02' as text) (0.55 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.66 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-system_currency', 'laravel-cache-illuminate:cache:flexible:created:system_currency') and "expiration" <= 1775159597 (11.31 ms)
* sqlite - select "value" from "system_settings" where "key" = 'default_currency' limit 1 (0.47 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1775159657, 'laravel-cache-system_currency', 's:3:"USD";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (8.36 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-system_currency') (0.45 ms)
* sqlite - select * from "cache" where "key" in ('laravel-cache-tzs_exchange_rate') (0.42 ms)
* sqlite - delete from "cache" where "key" in ('laravel-cache-tzs_exchange_rate', 'laravel-cache-illuminate:cache:flexible:created:tzs_exchange_rate') and "expiration" <= 1775159597 (8.13 ms)
* sqlite - select "value" from "system_settings" where "key" = 'tzs_exchange_rate' limit 1 (0.48 ms)
* sqlite - insert into "cache" ("expiration", "key", "value") values (1775159657, 'laravel-cache-tzs_exchange_rate', 's:4:"2500";') on conflict ("key") do update set "expiration" = "excluded"."expiration", "key" = "excluded"."key", "value" = "excluded"."value" (7.59 ms)
