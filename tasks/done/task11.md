we  are  implementing  this   

# Snippe Documentation



Snippe is a payment processing API that enables you to accept payments via mobile money, card, and QR code, and send disbursements to mobile money and bank accounts.

Base URL [#base-url]

```
https://api.snippe.sh
```

Authentication [#authentication]

All API requests require authentication using an API key in the `Authorization` header:

```http
Authorization: Bearer your_api_key_here
```

***

Quick Start [#quick-start]

<Cards>
  <Card title="Authentication" href="/docs/2026-01-25/authentication">
    Learn how to authenticate with the API using API keys
  </Card>

  <Card title="Payment Sessions" href="/docs/2026-01-25/sessions">
    Create hosted checkout pages for your customers
  </Card>

  <Card title="Payments" href="/docs/2026-01-25/payments">
    Collect money via mobile money, cards, or QR codes
  </Card>

  <Card title="Disbursements" href="/docs/2026-01-25/disbursements">
    Send money to mobile money and bank accounts
  </Card>

  <Card title="Webhooks" href="/docs/2026-01-25/webhooks">
    Receive real-time payment and payout notifications
  </Card>
</Cards>

***

Payment Types [#payment-types]

<Tabs items={['Mobile Money', 'Card', 'Dynamic QR']}>
  <Tab value="Mobile Money">
    **Type:** `mobile`

    Customer receives a USSD push notification to authorize the payment on their phone.

    **Supported networks:** Airtel Money, M-Pesa, Mixx by Yas, Halotel
  </Tab>

  <Tab value="Card">
    **Type:** `card`

    Returns a `payment_url` to redirect the customer to a secure checkout page.

    **Supported cards:** Visa, Mastercard, local debit cards
  </Tab>

  <Tab value="Dynamic QR">
    **Type:** `dynamic-qr`

    Returns a `payment_qr_code` that customers scan with their mobile money app.

    **Use case:** In-store payments, POS systems
  </Tab>
</Tabs>

***

Integration Flow [#integration-flow]

<Steps>
  <Step>
    Get API Keys Create an account and generate API keys from the [#get-api-keys-create-an-account-and-generate-api-keys-from-the]

    [Dashboard](https://snippe.sh/dashboard).
  </Step>

  <Step>
    \### Create Payment Call 

    `POST /v1/payments`

     with payment details.
  </Step>

  <Step>
    Handle Response Redirect customer (card) or wait for USSD confirmation [#handle-response-redirect-customer-card-or-wait-for-ussd-confirmation]

    (mobile).
  </Step>

  <Step>
    Receive Webhook Get notified when payment completes via webhook. [#receive-webhook-get-notified-when-payment-completes-via-webhook]
  </Step>
</Steps>

***

Idempotency [#idempotency]

<Callout>
  Always include an `Idempotency-Key` header to prevent duplicate transactions
  when retrying requests.
</Callout>

```http
POST /v1/payments
Idempotency-Key: unique-key-123
```

If the same key is used twice, the original response is returned.

***

Rate Limits [#rate-limits]

API requests are rate-limited to **60 requests per minute**.

| Header                  | Description                  |
| ----------------------- | ---------------------------- |
| `X-Ratelimit-Limit`     | Maximum requests per minute  |
| `X-Ratelimit-Remaining` | Remaining requests in window |
| `X-Ratelimit-Reset`     | Seconds until limit resets   |

<Callout type="warn">
  If you exceed the rate limit, you'll receive a `429 Too Many Requests`
  response. Implement exponential backoff in your retry logic.
</Callout>

***

Response Format [#response-format]

<Tabs items={['Success', 'Error']}>
  <Tab value="Success">
    ```json
    {
      "status": "success",
      "code": 200,
      "data": {
        // Response data
      }
    }
    ```
  </Tab>

  <Tab value="Error">
    ```json
    {
      "status": "error",
      "code": 400,
      "error_code": "validation_error",
      "message": "Description of the error"
    }
    ```
  </Tab>
</Tabs>



# Authentication



All API requests require authentication using an API key in the `Authorization` header.

```http
Authorization: Bearer snp_your_api_key_here
```

***

Getting Your API Key [#getting-your-api-key]

<Steps>
  <Step>
    Log in to Dashboard [#log-in-to-dashboard]

    Go to your [Snippe Dashboard](https://snippe.sh) and sign in to your account.
  </Step>

  <Step>
    Navigate to API Keys [#navigate-to-api-keys]

    Go to **Settings** → **API Keys** in the sidebar.
  </Step>

  <Step>
    Create New Key [#create-new-key]

    Click **Create API Key** and select the scopes you need.
  </Step>

  <Step>
    Copy and Store [#copy-and-store]

    Copy your API key immediately and store it securely.
  </Step>
</Steps>

<Callout type="warn">
  Your API key is only shown once. Store it securely immediately. If you lose
  it, you'll need to create a new one.
</Callout>

***

Example Request [#example-request]

```bash
curl -X GET https://api.snippe.sh/v1/payments \
  -H "Authorization: Bearer snp_your_api_key_here"
```

***

API Key Scopes [#api-key-scopes]

| Scope                 | Description               |
| --------------------- | ------------------------- |
| `collection:read`     | View payments and balance |
| `collection:create`   | Create payment intents    |
| `disbursement:read`   | View payouts              |
| `disbursement:create` | Create payouts            |

<Callout>
  Select only the scopes your application needs. This follows the principle of
  least privilege.
</Callout>

***

Error Responses [#error-responses]

<Tabs items={['Invalid API Key', 'Insufficient Scope']}>
  <Tab value="Invalid API Key">
    **HTTP 401 Unauthorized**

    ```json
    {
      "status": "error",
      "code": 401,
      "error_code": "unauthorized",
      "message": "invalid or missing API key"
    }
    ```

    **Common causes:** API key is missing from the request, API key is malformed or expired, or using test key in production.
  </Tab>

  <Tab value="Insufficient Scope">
    **HTTP 403 Forbidden**

    ```json
    {
      "status": "error",
      "code": 403,
      "error_code": "insufficient_scope",
      "message": "API key does not have required scope: disbursement:create"
    }
    ```

    **Fix:** Create a new API key with the required scope.
  </Tab>
</Tabs>

***

Best Practices [#best-practices]

<Callout type="warn">
  Never expose your API key in client-side code, public repositories, or logs.
</Callout>

Store API keys in environment variables. Use different keys for development and production. Rotate keys periodically. Revoke compromised keys immediately.


# Payments



The Payments API allows you to collect money from customers through various channels.

Payment Types [#payment-types]

<Cards>
  <Card title="Mobile Money" href="/docs/2026-01-25/payments/mobile-money">
    Airtel Money, M-Pesa, Mixx by Yas, Halotel. Customer receives USSD push to
    authorize.
  </Card>

  <Card title="Card Payments" href="/docs/2026-01-25/payments/card">
    Visa, Mastercard, local debit cards. Customer redirected to secure checkout.
  </Card>

  <Card title="Dynamic QR" href="/docs/2026-01-25/payments/dynamic-qr">
    QR code customers scan with their mobile money app to pay.
  </Card>
</Cards>

***

Payment Flow [#payment-flow]

<Steps>
  <Step>
    Create Payment Intent POST /v1/payments with payment type and amount. [#create-payment-intent-post-v1payments-with-payment-type-and-amount]
  </Step>

  <Step>
    Customer Completes Payment - Mobile: USSD push to customer's phone - [#customer-completes-payment---mobile-ussd-push-to-customers-phone--]

    **Card:** Redirect to `payment_url` - **QR:** Customer scans
    `payment_qr_code`
  </Step>

  <Step>
    Receive Webhook payment.completed or payment.failed sent to your [#receive-webhook-paymentcompleted-or-paymentfailed-sent-to-your]

    webhook URL.
  </Step>

  <Step>
    Verify Status (Optional) GET /v1/payments/{reference} to confirm [#verify-status-optional-get-v1paymentsreference-to-confirm]

    payment status.
  </Step>
</Steps>

***

API Endpoints [#api-endpoints]

| Endpoint                        | Method | Description           |
| ------------------------------- | ------ | --------------------- |
| `/v1/payments`                  | POST   | Create payment intent |
| `/v1/payments`                  | GET    | List all payments     |
| `/v1/payments/{reference}`      | GET    | Get payment status    |
| `/v1/payments/{reference}/push` | POST   | Trigger USSD push     |
| `/v1/payments/balance`          | GET    | Get account balance   |
| `/v1/payments/search`           | GET    | Search payments       |

***

Payment Status [#payment-status]

| Status      | Description                               |
| ----------- | ----------------------------------------- |
| `pending`   | Payment created, awaiting customer action |
| `completed` | Payment successful, funds received        |
| `failed`    | Payment failed (declined, timeout, etc.)  |
| `voided`    | Payment cancelled before completion       |
| `expired`   | Payment expired (4 hour timeout)          |

<Callout>
  Payments expire after 4 hours if not completed. Create a new payment if the
  customer wants to retry.
</Callout>

***

List Payments [#list-payments]

Retrieve all payments for your account with pagination.

```http
GET /v1/payments?limit=20&offset=0
Authorization: Bearer <api_key>
```

Query Parameters [#query-parameters]

| Parameter | Type    | Default | Description                |
| --------- | ------- | ------- | -------------------------- |
| `limit`   | integer | 20      | Results per page (max 100) |
| `offset`  | integer | 0       | Pagination offset          |

Response [#response]

```json
{
  "status": "success",
  "code": 200,
  "data": {
    "items": [
      {
        "amount": {
          "currency": "TZS",
          "value": 500
        },
        "api_version": "2026-01-25",
        "channel": {
          "provider": "selcomtanqr",
          "type": "other"
        },
        "completed_at": "2026-01-25T00:50:44.105159Z",
        "created_at": "2026-01-25T00:47:50.243311Z",
        "customer": {
          "email": "customer@email.com",
          "first_name": "Customer",
          "last_name": "Name",
          "phone": "+255781000000"
        },
        "external_reference": "S20388368013",
        "id": "672ad0e0-f95b-46b8-a91a-09490351055b",
        "metadata": {
          "order_id": "ORD-12345"
        },
        "object": "payment",
        "payment_type": "dynamic-qr",
        "reference": "6a490816-799b-4fc9-b9b6-2ec67c54e17e",
        "settlement": {
          "fees": { "currency": "TZS", "value": 9 },
          "gross": { "currency": "TZS", "value": 500 },
          "net": { "currency": "TZS", "value": 491 }
        },
        "status": "completed"
      }
    ],
    "total": 81,
    "limit": 20,
    "offset": 0
  }
}
```

***

Get Payment Status [#get-payment-status]

Retrieve the current status of a payment by its reference.

```http
GET /v1/payments/{reference}
Authorization: Bearer <api_key>
```

Response [#response-1]

```json
{
  "status": "success",
  "code": 200,
  "data": {
    "amount": {
      "currency": "TZS",
      "value": 500
    },
    "api_version": "2026-01-25",
    "channel": {
      "provider": "selcomtanqr",
      "type": "other"
    },
    "completed_at": "2026-01-25T00:50:44.105159Z",
    "created_at": "2026-01-25T00:47:50.243311Z",
    "expires_at": "2026-01-25T04:47:50.159178Z",
    "object": "payment",
    "payment_type": "dynamic-qr",
    "reference": "6a490816-799b-4fc9-b9b6-2ec67c54e17e",
    "status": "completed"
  }
}
```

***

Get Account Balance [#get-account-balance]

Retrieve your current account balance.

```http
GET /v1/payments/balance
Authorization: Bearer <api_key>
```

Response [#response-2]

```json
{
  "status": "success",
  "code": 200,
  "data": {
    "api_version": "2026-01-25",
    "available": {
      "currency": "TZS",
      "value": 6943
    },
    "balance": {
      "currency": "TZS",
      "value": 6943
    },
    "object": "balance"
  }
}
```

***

Search Payments [#search-payments]

Search for payments by reference.

```http
GET /v1/payments/search?reference={payment_reference}
Authorization: Bearer <api_key>
```

Query Parameters [#query-parameters-1]

| Parameter   | Type   | Description                 |
| ----------- | ------ | --------------------------- |
| `reference` | string | Payment reference to search |

***

Idempotency [#idempotency]

<Callout type="warn">
  Always use the `Idempotency-Key` header to prevent duplicate payments when
  retrying failed requests.
</Callout>

```http
POST /v1/payments
Idempotency-Key: order-12345-attempt-1
```

* Keys are valid for 24 hours
* Same key + same request body = returns cached response
* Same key + different body = returns error


# Mobile Money



Collect payments from customers using mobile money. The customer receives a USSD push notification to authorize the payment.

Supported Networks [#supported-networks]

| Network      | Country |
| ------------ | ------- |
| Airtel Money | TZ      |
| M-Pesa       | TZ      |
| Mixx by Yas  | TZ      |
| Halotel      | TZ      |

***

Create Payment [#create-payment]

```http
POST /v1/payments
Authorization: Bearer <api_key>
Content-Type: application/json
Idempotency-Key: <unique_key>
```

Request [#request]

```json
{
  "payment_type": "mobile",
  "details": {
    "amount": 500,
    "currency": "TZS"
  },
  "phone_number": "255781000000",
  "customer": {
    "firstname": "FirstName",
    "lastname": "LastName",
    "email": "customer@email.com"
  },
  "webhook_url": "https://yoursite.com/webhooks/snippe",
  "metadata": {
    "order_id": "ORD-12345"
  }
}
```

Response [#response]

```json
{
  "status": "success",
  "code": 201,
  "data": {
    "amount": {
      "currency": "TZS",
      "value": 500
    },
    "api_version": "2026-01-25",
    "expires_at": "2026-01-25T05:04:54.063993853Z",
    "object": "payment",
    "payment_type": "mobile",
    "reference": "9015c155-9e29-4e8e-8fe6-d5d81553c8e6",
    "status": "pending"
  }
}
```

***

Request Parameters [#request-parameters]

Required Fields [#required-fields]

| Field                | Type    | Description                          |
| -------------------- | ------- | ------------------------------------ |
| `payment_type`       | string  | Must be `mobile`                     |
| `details.amount`     | integer | Amount in smallest currency unit     |
| `details.currency`   | string  | Currency code (`TZS`)                |
| `phone_number`       | string  | Customer phone number (255XXXXXXXXX) |
| `customer.firstname` | string  | Customer first name                  |
| `customer.lastname`  | string  | Customer last name                   |
| `customer.email`     | string  | Customer email                       |

Optional Fields [#optional-fields]

| Field         | Type   | Description                   |
| ------------- | ------ | ----------------------------- |
| `webhook_url` | string | URL for webhook notifications |
| `metadata`    | object | Custom key-value data         |

***

How It Works [#how-it-works]

1. Create a payment intent with the customer's phone number
2. Customer receives a USSD push on their phone
3. Customer enters their PIN to authorize
4. Snippe sends a webhook notification with the result
5. Payment expires after 4 hours if not completed

***

Error Responses [#error-responses]

Validation Error (400) [#validation-error-400]

```json
{
  "status": "error",
  "code": 400,
  "error_code": "validation_error",
  "message": "amount is required"
}
```

Unauthorized (401) [#unauthorized-401]

```json
{
  "status": "error",
  "code": 401,
  "error_code": "unauthorized",
  "message": "invalid or missing API key"
}
```


# Dynamic QR



Generate QR codes that customers scan with their mobile money app to complete payment.

***

Create Payment [#create-payment]

```http
POST /v1/payments
Authorization: Bearer <api_key>
Content-Type: application/json
Idempotency-Key: <unique_key>
```

Request [#request]

```json
{
  "payment_type": "dynamic-qr",
  "details": {
    "amount": 500,
    "currency": "TZS",
    "redirect_url": "https://your_domain.com/payment_done",
    "cancel_url": "https://your_domain.com/payment_failed"
  },
  "phone_number": "255781000000",
  "customer": {
    "firstname": "FirstName",
    "lastname": "LastName",
    "email": "customer@email.com"
  },
  "webhook_url": "https://yoursite.com/webhooks/snippe",
  "metadata": {
    "order_id": "ORD-12345"
  }
}
```

Response [#response]

```json
{
  "status": "success",
  "code": 201,
  "data": {
    "amount": {
      "currency": "TZS",
      "value": 500
    },
    "api_version": "2026-01-25",
    "expires_at": "2026-01-25T04:47:50.159178853Z",
    "object": "payment",
    "payment_qr_code": "000201010212041552545429990002026390014tz.go.bot.tips01050399802086389040052045999530383454035005802TZ5909NEUROTECH6003DSM610512345622401086389040003086389040081500012tz.co.selcom0130 https://selcom.link/addbbff1 630401F1",
    "payment_token": "63890400",
    "payment_type": "dynamic-qr",
    "payment_url": "https://tz.selcom.online/paymentgw/checkout/...",
    "reference": "6a490816-799b-4fc9-b9b6-2ec67c54e17e",
    "status": "pending"
  }
}
```

***

Request Parameters [#request-parameters]

Required Fields [#required-fields]

| Field              | Type    | Description                      |
| ------------------ | ------- | -------------------------------- |
| `payment_type`     | string  | Must be `dynamic-qr`             |
| `details.amount`   | integer | Amount in smallest currency unit |
| `details.currency` | string  | Currency code (`TZS`)            |

Optional Fields [#optional-fields]

| Field                  | Type   | Description                   |
| ---------------------- | ------ | ----------------------------- |
| `phone_number`         | string | Customer phone number         |
| `customer.firstname`   | string | Customer first name           |
| `customer.lastname`    | string | Customer last name            |
| `customer.email`       | string | Customer email                |
| `details.redirect_url` | string | URL to redirect after success |
| `details.cancel_url`   | string | URL to redirect on cancel     |
| `webhook_url`          | string | URL for webhook notifications |
| `metadata`             | object | Custom key-value data         |

***

Response Fields [#response-fields]

| Field             | Description                                     |
| ----------------- | ----------------------------------------------- |
| `payment_qr_code` | QR code data string to display to customer      |
| `payment_url`     | Hosted payment page URL (alternative to QR)     |
| `payment_token`   | Payment token for reference                     |
| `reference`       | Unique payment reference                        |
| `expires_at`      | Payment expiration time (4 hours from creation) |

***

How It Works [#how-it-works]

1. Create a payment intent
2. Display the QR code from `payment_qr_code` to the customer (render as QR image)
3. Alternatively, redirect customer to `payment_url`
4. Customer scans the QR code with their mobile money app
5. Customer confirms payment in their app
6. Snippe sends a webhook notification with the result


# Trigger Push Payment



Trigger or retry a USSD push notification for a pending payment. Use this when the customer missed the initial push or it timed out.

***

Trigger Push [#trigger-push]

```http
POST /v1/payments/{reference}/push
Authorization: Bearer <api_key>
Content-Type: application/json
```

Path Parameter [#path-parameter]

| Parameter   | Type   | Description                                  |
| ----------- | ------ | -------------------------------------------- |
| `reference` | string | Payment reference or payment token (from QR) |

Request Body (Optional) [#request-body-optional]

```json
{
  "phone": "+255712345678"
}
```

| Field   | Type   | Required | Description                                                     |
| ------- | ------ | -------- | --------------------------------------------------------------- |
| `phone` | string | No       | Phone number to send push to (defaults to original payer phone) |

Response [#response]

```json
{
  "status": 200,
  "message": "USSD push sent successfully",
  "data": {
    "reference": "pi_a1b2c3d4e5f6",
    "external_reference": "SEL123456789",
    "status": "pending",
    "phone": "+255712345678"
  }
}
```

***

Error Responses [#error-responses]

| Status | Code              | Description                            |
| ------ | ----------------- | -------------------------------------- |
| 400    | `invalid_request` | Payment is not in pending status       |
| 403    | `forbidden`       | Payment doesn't belong to your account |
| 404    | `not_found`       | Payment not found                      |

***

Examples [#examples]

Retry push by reference [#retry-push-by-reference]

```bash
curl -X POST https://api.snippe.sh/v1/payments/pi_a1b2c3d4e5f6/push \
  -H "Authorization: Bearer sk_live_xxx"
```

Trigger push for QR payment using payment token [#trigger-push-for-qr-payment-using-payment-token]

```bash
curl -X POST https://api.snippe.sh/v1/payments/63877176/push \
  -H "Authorization: Bearer sk_live_xxx"
```

Send push to different phone number [#send-push-to-different-phone-number]

```bash
curl -X POST https://api.snippe.sh/v1/payments/pi_a1b2c3d4e5f6/push \
  -H "Authorization: Bearer sk_live_xxx" \
  -H "Content-Type: application/json" \
  -d '{"phone": "+255787654321"}'
```


# Webhooks



Snippe sends webhooks to your specified URL when payment or payout status changes. Use webhooks to update your system in real-time.

<Callout>
  Always provide a `webhook_url` when creating payments or payouts to receive
  status notifications.
</Callout>

***

Webhook Headers [#webhook-headers]

All webhooks include the following headers:

| Header                | Description                            |
| --------------------- | -------------------------------------- |
| `Content-Type`        | `application/json`                     |
| `User-Agent`          | `Snipe-Webhook/1.0`                    |
| `X-Webhook-Event`     | Event type (e.g., `payment.completed`) |
| `X-Webhook-Timestamp` | Unix timestamp when webhook was sent   |
| `X-Webhook-Signature` | HMAC-SHA256 signature for verification |

***

Event Types [#event-types]

<Tabs items={["Payment Events", "Payout Events"]}>
  <Tab value="Payment Events">
    \| Event | Description | | ----- | ----------- | | `payment.completed` |
    Payment successfully completed | | `payment.failed` | Payment failed or was
    declined |
  </Tab>

  <Tab value="Payout Events">
    \| Event | Description | | ----- | ----------- | | `payout.completed` |
    Payout successfully sent | | `payout.failed` | Payout failed |
  </Tab>
</Tabs>

***

Payment Events [#payment-events]

payment.completed [#paymentcompleted]

Sent when a payment is successfully completed.

<Callout>
  **Trigger:** Customer completes payment via mobile money, card, or QR code.

  **Action:** Mark the order as paid, deliver the product/service.
</Callout>

```json
{
  "id": "evt_427edf89c5c8c02a2301254e",
  "type": "payment.completed",
  "api_version": "2026-01-25",
  "created_at": "2026-01-25T01:05:17.834276191Z",
  "data": {
    "reference": "9015c155-9e29-4e8e-8fe6-d5d81553c8e6",
    "external_reference": "S20388385575",
    "status": "completed",
    "amount": {
      "value": 500,
      "currency": "TZS"
    },
    "settlement": {
      "gross": { "value": 500, "currency": "TZS" },
      "fees": { "value": 9, "currency": "TZS" },
      "net": { "value": 491, "currency": "TZS" }
    },
    "channel": {
      "type": "mobile_money",
      "provider": "airtel"
    },
    "customer": {
      "phone": "+255781000000",
      "name": "Customer Name",
      "email": "customer@email.com"
    },
    "metadata": {
      "order_id": "ORD-12345",
      "product": "Premium Plan"
    },
    "completed_at": "2026-01-25T01:05:16.8303Z"
  }
}
```

***

payment.failed [#paymentfailed]

Sent when a payment fails.

<Callout type="warn">
  **Trigger:** Payment was declined, insufficient funds, or other failure.

  **Action:** Notify the customer, offer retry option.
</Callout>

```json
{
  "id": "evt_a1b2c3d4e5f6g7h8i9j0k1l2",
  "type": "payment.failed",
  "api_version": "2026-01-25",
  "created_at": "2026-01-25T01:05:17.834276191Z",
  "data": {
    "reference": "9015c155-9e29-4e8e-8fe6-d5d81553c8e6",
    "external_reference": "S20388385575",
    "status": "failed",
    "amount": {
      "value": 500,
      "currency": "TZS"
    },
    "settlement": {
      "gross": { "value": 500, "currency": "TZS" },
      "fees": { "value": 9, "currency": "TZS" },
      "net": { "value": 491, "currency": "TZS" }
    },
    "channel": {
      "type": "mobile_money",
      "provider": "airtel"
    },
    "customer": {
      "phone": "+255781000000",
      "name": "Customer Name",
      "email": "customer@email.com"
    },
    "metadata": {
      "order_id": "ORD-12345"
    },
    "failure_reason": "Something went wrong",
    "completed_at": "2026-01-25T01:05:16.8303Z"
  }
}
```

***

Payout Events [#payout-events]

payout.completed [#payoutcompleted]

Sent when a payout is successfully completed.

<Callout>
  **Trigger:** Funds have been successfully sent to the recipient.

  **Action:** Update your records, notify sender of successful transfer.
</Callout>

```json
{
  "id": "evt_a1b2c3d4e5f6g7h8i9j0k1l2",
  "type": "payout.completed",
  "api_version": "2026-01-25",
  "created_at": "2026-01-25T10:30:00Z",
  "data": {
    "reference": "PAY-ABC123XYZ",
    "status": "completed",
    "amount": {
      "value": 50000,
      "currency": "TZS"
    },
    "settlement": {
      "gross": { "value": 50000, "currency": "TZS" },
      "fees": { "value": 500, "currency": "TZS" },
      "net": { "value": 50500, "currency": "TZS" }
    },
    "channel": {
      "type": "mobile_money",
      "provider": "MPESA"
    },
    "customer": {
      "phone": "255712345678",
      "name": "Customer Name"
    },
    "metadata": {
      "order_id": "12345"
    },
    "completed_at": "2026-01-25T10:30:00Z"
  }
}
```

***

payout.failed [#payoutfailed]

Sent when a payout fails.

<Callout type="error">
  **Trigger:** Payout could not be completed (invalid recipient, network error, etc.).

  **Action:** Funds are returned to balance, notify sender of failure.
</Callout>

```json
{
  "id": "evt_a1b2c3d4e5f6g7h8i9j0k1l2",
  "type": "payout.failed",
  "api_version": "2026-01-25",
  "created_at": "2026-01-25T10:30:00Z",
  "data": {
    "reference": "PAY-ABC123XYZ",
    "status": "failed",
    "amount": {
      "value": 50000,
      "currency": "TZS"
    },
    "settlement": {
      "gross": { "value": 50000, "currency": "TZS" },
      "fees": { "value": 500, "currency": "TZS" },
      "net": { "value": 50500, "currency": "TZS" }
    },
    "channel": {
      "type": "mobile_money",
      "provider": "MPESA"
    },
    "customer": {
      "phone": "255712345678",
      "name": "Customer Name"
    },
    "metadata": {
      "order_id": "12345"
    },
    "failure_reason": "Recipient phone number is invalid"
  }
}
```

***

Webhook Signature Verification [#webhook-signature-verification]

<Callout type="warn">
  Always verify webhook signatures in production to ensure requests are from
  Snippe.
</Callout>

Validate the `X-Webhook-Signature` header using HMAC-SHA256:

<Tabs items={['Node.js', 'Python', 'Go']}>
  <Tab value="Node.js">
    ```javascript
    const crypto = require('crypto');

    function verifyWebhookSignature(payload, signature, secret) {
    const expectedSignature = crypto
    .createHmac('sha256', secret)
    .update(payload)
    .digest('hex');

    return crypto.timingSafeEqual(
    Buffer.from(signature),
    Buffer.from(expectedSignature)
    );
    }

    ```
  </Tab>

  <Tab value="Python">
    ```python
    import hmac
    import hashlib

    def verify_webhook_signature(payload, signature, secret):
        expected = hmac.new(
            secret.encode(),
            payload.encode(),
            hashlib.sha256
        ).hexdigest()
        return hmac.compare_digest(signature, expected)
    ```
  </Tab>

  <Tab value="Go">
    ```go
    import (
        "crypto/hmac"
        "crypto/sha256"
        "encoding/hex"
    )

    func verifyWebhookSignature(payload, signature, secret string) bool {
    mac := hmac.New(sha256.New, []byte(secret))
    mac.Write([]byte(payload))
    expected := hex.EncodeToString(mac.Sum(nil))
    return hmac.Equal([]byte(signature), []byte(expected))
    }

    ```
  </Tab>
</Tabs>

***

Best Practices [#best-practices]

<Steps>
  <Step>
    Respond Quickly [#respond-quickly]

    Return a `2xx` status code within 30 seconds. Process webhooks asynchronously.
  </Step>

  <Step>
    Handle Duplicates [#handle-duplicates]

    Use the event `id` to deduplicate. You may receive the same event multiple times.
  </Step>

  <Step>
    Verify Signatures [#verify-signatures]

    Always validate webhook signatures in production environments.
  </Step>

  <Step>
    Implement Retries [#implement-retries]

    Snippe retries failed webhooks with exponential backoff. Ensure your endpoint is idempotent.
  </Step>
</Steps>

<Callout type="error">
  If your endpoint consistently fails, webhooks may be disabled. Monitor your webhook endpoint health.
</Callout>

```
```
