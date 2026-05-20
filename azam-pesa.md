1. Setup checklist

Before your first disbursement call, make sure you have:
Requirement	Where it comes from
Sandbox account with confirmed email	Sandbox Developer Portal — same account you use for Checkout
Application Name + Client ID + Secret Key	Register App in the portal (one app can be enabled for both Checkout and Disbursement)
Disbursement service enabled on your application	Request via your AzamPay account manager — KYC on file required for production
Callback URL configured	Set during app registration; can be updated in My Application
Funded merchant sub-account	AzamPay credits your merchant sub-account on onboarding; reconcile with your account manager

    One application, two APIs. The same appName + clientId + clientSecret mints a Bearer token that authenticates both /api/v1/checkout and /api/v1/azampay/rw/*. Whether disbursement actually runs depends on whether your application is provisioned for it on the platform.

2. Base URLs
Service	Sandbox URL
Authenticator (token generation)	https://authenticator-test-rw.azampay.co.tz
Disbursement API	https://api-disbursement-test-rw.azampay.co.tz
Admin Portal (transactions, vendor config)	https://admin-test-rw.azampay.co.tz

Production URLs are issued by your account manager once KYC is approved.
3. Authentication

Every /rw/* call requires a Bearer token. The token-generation flow is identical to Checkout:

Code
curl -X POST https://authenticator-test-rw.azampay.co.tz/AppRegistration/GenerateToken \
  -H 'Content-Type: application/json' \
  -d '{
    "appName": "<your-application-name>",
    "clientId": "<your-client-id>",
    "clientSecret": "<your-secret-key>"
  }'

Response:

Code
{
  "data": {
    "accessToken": "eyJhbGciOiJkaXIi...",
    "expire": "2026-04-27T22:00:00Z",
    "validFrom": "2026-04-27T16:00:00Z"
  },
  "success": true,
  "statusCode": 200,
  "message": "Token generated successfully"
}

Include accessToken in Authorization: Bearer <token> on every subsequent call. Tokens last ~6 hours; refresh by calling GenerateToken again before expiry.
Common auth failures
HTTP	Body	Cause
401	{"status":"Error","message":"Please Provide Valid Authorization"}	Missing / malformed / expired bearer
423 (from authenticator)	{"message":"Provided detail is not valid for this app or secret key has been expired",...}	Wrong clientSecret, or the secret has been rotated/regenerated. Get the latest from the portal's My Application page.
4. The four disbursement endpoints

All four endpoints live under /api/v1/azampay/rw/. Bodies use camelCase, no checksum field.
4.1 POST /api/v1/azampay/rw/NameLookup

Resolve a destination account holder's name and the resolved provider. Use this before disburse to confirm you're paying the right person and to populate destination.fullName on the disburse call.

Code
curl -X POST https://api-disbursement-test-rw.azampay.co.tz/api/v1/azampay/rw/NameLookup \
  -H "Authorization: Bearer <your-access-token>" \
  -H 'Content-Type: application/json' \
  -d '{
    "accountNumber": "250781916866"
  }'

Request fields
Field	Required	Constraints
accountNumber	✓	1–30 chars, regex ^[a-zA-Z0-9\-_.+]+$, no whitespace. MSISDN (with or without 25-prefix) for MTN/Airtel; bank account number for banks.

Response (200 — found):

Code
{
  "success": true,
  "accountNumber": "250781916866",
  "accountName": "Patrick NTWALI",
  "provider": "MTN",
  "message": "Success",
  "statusCode": 200
}

The provider field is the AzamPay short code for the institution that owns the account. Use this exact value in destination.bankName on /rw/disburse. Possible values:
provider	BIC	Institution
MTN	MTNRWXXX	MTN Mobile Money Rwanda
AIRTEL	AIRTELRW	Airtel Money Rwanda
BK	BKIGRWRW	Banque de Kigali
EQUITY	EQBLRWRW	Equity Bank Rwanda
GTBANK	GTBIRWRK	GT Bank Rwanda
I&M	IMRWRWRW	I&M Bank (BCR)
BOA	AFRWRWRW	Bank of Africa Rwanda
BPR	BPRWRWRW	Banque Populaire du Rwanda
ECOBANK	ECOCRWRW	Ecobank Rwanda
ACCESS	BKORRWRW	Access Bank Rwanda
COGEBANQUE	CGBKRWRW	Cogebanque
BRD	BRDRRWRW	Banque Rwandaise de Développement
AB BANK	ABBRRWRW	AB Bank Rwanda
UNGUKA	UNGURWRW	Unguka Bank

Response (404 — not found):

Code
{
  "success": false,
  "accountNumber": "999999999999",
  "accountName": null,
  "provider": null,
  "message": "Account not found",
  "statusCode": 404
}

    Sandbox note. In sandbox the simulator returns deterministic fake names ("Magdalena Berge", "Micah Boyle", …) generated from the account number, with message: "Lookup successful (Sandbox)". Production returns the real account holder's name from the partner.

4.2 GET /api/v1/azampay/rw/CheckBalance

Read the current RWF balance of your merchant collection sub-account. No request body — the merchant identity is resolved from the JWT vendor claim.

Code
curl -X GET https://api-disbursement-test-rw.azampay.co.tz/api/v1/azampay/rw/CheckBalance \
  -H "Authorization: Bearer <your-access-token>"

Response (200):

Code
{
  "data": "100000.00",
  "message": "Balance: 100000.00 RWF",
  "success": true,
  "statusCode": 200
}

data is a string with two-decimal precision. Always RWF.
4.3 POST /api/v1/azampay/rw/disburse

Initiate a disbursement. The API accepts and enqueues the request synchronously (status: "Pending"), then settles it asynchronously through AzamPay's payment rail. Final status is delivered via callback and is also retrievable via /rw/TransactionStatus.

Code
curl -X POST https://api-disbursement-test-rw.azampay.co.tz/api/v1/azampay/rw/disburse \
  -H "Authorization: Bearer <your-access-token>" \
  -H 'Content-Type: application/json' \
  -d '{
    "destination": {
      "bankName": "MTN",
      "accountNumber": "250781916866",
      "fullName": "Patrick NTWALI"
    },
    "transferDetails": {
      "type": "disbursement",
      "amount": 5000,
      "dateInEpoch": 1777307540
    },
    "externalReferenceId": "payout-2026-04-27-0001",
    "remarks": "April salary — engineering"
  }'

Request fields
Field	Required	Constraints
destination.bankName	✓	1–100 chars, no whitespace. Use the short code from the provider table (e.g. MTN, BK, EQUITY).
destination.accountNumber	✓	1–40 chars, regex ^[a-zA-Z0-9\-_.+]+$, no whitespace. MSISDN for MNOs, account number for banks.
destination.fullName	✓	1–100 chars, no whitespace. Best obtained from /rw/NameLookup.
transferDetails.amount	✓	Number, between 1 and 10,000,000 (RWF).
transferDetails.dateInEpoch	✓	Unix epoch seconds (10-digit integer).
transferDetails.type	optional	Free-form, ≤ 50 chars. Defaults to "Disbursement".
externalReferenceId	✓	≤ 30 chars, regex ^[a-zA-Z0-9\-_.]+$. Must be unique per merchant — duplicates are rejected with 400 Detected duplicate transaction.
remarks	optional	≤ 500 chars. Free-form note that surfaces in your reconciliation reports and the callback message.
additionalProperties	optional	Free-form JSON object, dictionary-size-limited.

Response (200 — accepted for processing):

Code
{
  "success": true,
  "pgReferenceId": "019dcfc8dbe170ed92cef555a4cf6f14",
  "message": "Your transaction is in process",
  "statusCode": 200,
  "status": "Pending"
}

pgReferenceId is AzamPay's tracking reference. Persist it in your order/payout record — you'll need it for status polling and to correlate the callback.

    No Source block. Unlike the legacy /disburse, you do not send your own merchant account/credentials in the body. The merchant identity is resolved server-side from the Bearer token's vendor claim. This means the same Bearer token used by Checkout will only initiate disbursements from the merchant account associated with that application — you cannot disburse from someone else's funds by passing their account number.

4.4 GET /api/v1/azampay/rw/TransactionStatus

Query the final state of a previously-submitted disbursement. Either pgReferenceId or externalReferenceId is sufficient — pass exactly one.

Code
curl -X GET "https://api-disbursement-test-rw.azampay.co.tz/api/v1/azampay/rw/TransactionStatus?pgReferenceId=019dcfc8dbe170ed92cef555a4cf6f14" \
  -H "Authorization: Bearer <your-access-token>"

Response (200):

Code
{
  "transId": "4b8ef697e0d3e02b4ef89359670feb59",
  "statusCode": 200,
  "success": true,
  "message": null,
  "fspReferenceId": "M00022584",
  "initiatorReferenceId": "payout-2026-04-27-0001",
  "pgReferenceId": "019dcfc8dbe170ed92cef555a4cf6f14",
  "amount": "500",
  "status": "success",
  "additionalProperties": {},
  "operator": "GTB",
  "signature": null
}

Field	Meaning
status	success | failure | pending
fspReferenceId	The settlement reference for the underlying credit transfer. Use this when raising support tickets. In sandbox it has the prefix SBX-.
initiatorReferenceId	Echoes the externalReferenceId you sent on disburse.
pgReferenceId	AzamPay's tracking reference (matches the one from disburse).
amount	The transferred amount only — fees are not included here.
message	Populated on failure with a human-readable reason (e.g. "Insufficient balance on collection. Current balance 388.0"). null on success.
operator	Provider code. Always GTB for Rwanda.
signature	Reserved for future use.

Polling cadence: /rw/TransactionStatus is safe to call every few seconds while a transaction is pending. There is no rate-limit on polling, but real settlement typically completes within 5–30 seconds for MNO destinations and within 1–3 minutes for cross-bank transfers.
5. Asynchronous callback

When a disbursement reaches a final state, AzamPay POSTs to the Callback URL configured for your application. The body uses the standard V0 wire shape used by Checkout, with disbursement-specific fields populated:

Code
{
  "message": "Disbursement of RWF 500 to 250781916866 completed successfully. Ref: M00022584",
  "transactionstatus": "success",
  "operator": "17",
  "reference": "019dcfc8dbe170ed92cef555a4cf6f14",
  "externalreference": "019dcfc8dbe170ed92cef555a4cf6f14",
  "utilityref": "payout-2026-04-27-0001",
  "amount": "500",
  "transid": "019dcfc8dbe170ed92cef555a4cf6f14",
  "msisdn": null,
  "mnoreference": "M00022584",
  "submerchantAcc": null,
  "additionalProperties": {},
  "signature": "yiLdNw/TOV5rOa3UCVIDr…"
}

Field	Meaning
transactionstatus	success | failure | pending
reference / externalreference / transid	AzamPay pgReferenceId (same value, three field names for backwards compatibility)
utilityref	Your externalReferenceId
mnoreference	Settlement reference for the underlying credit transfer
operator	Numeric provider code (17 for Rwanda disbursements)
signature	RSA-SHA256 signature over vendorRef + pgRef + transactionStatus + operator using AzamPay's RSA private key. Verify with the public key from /PublicKey.

What your callback handler must do:

    Match utilityref (your externalReferenceId) to a known pending disbursement in your system.
    Update your record based on transactionstatus.
    (Recommended) Verify signature.
    Return HTTP 200 to acknowledge. AzamPay retries non-200 responses with exponential backoff.

Several legacy fields (user, password, clientId, submerchantAcc) are emitted as null and are preserved only for backwards-compatible parsers — do not consume them.
6. Fees and balance math

Every disbursement carries a single combined AzamPay disbursement fee. Your merchant account is debited for amount + fee up front; the destination receives exactly the requested amount. The fee applicable to your account is configured by AzamPay during onboarding and can be reviewed with your account manager.
Worked example

A 5,000 RWF disbursement on a merchant with a combined fee of 180 RWF:

Code
Transfer amount:                   5,000 RWF
AzamPay disbursement fee:           +180 RWF
─────────────────────────────────────────────
Total debit from your account:    5,180 RWF
What the recipient receives:      5,000 RWF

You can verify the math after any successful transaction by comparing balances:

    Pre-disburse /rw/CheckBalance → e.g. "100000.00"
    Post-disburse /rw/CheckBalance → e.g. "94820.00"
    Delta = 5,180 ✓ (transfer + fee)

7. Validation surface — every error you can hit

Every 400 returned by /rw/* carries a stable envelope and a Reference Id (TraceId) for support correlation:

Code
{
  "success": false,
  "pgReferenceId": null,
  "message": "Invalid request data: <Path.Field>: <reason>. Reference Id: <traceId>",
  "statusCode": 400,
  "status": "Failure"
}

(envelope keys vary slightly per endpoint — /rw/NameLookup adds accountNumber/accountName/provider; /rw/CheckBalance uses data instead of pgReferenceId)
/rw/NameLookup
Trigger	Field-tagged message
empty body	Request body is required.
{} or missing accountNumber	AccountNumber: The AccountNumber field is required.; AccountNumber: The AccountNumber field cannot be empty or contain only whitespace.
accountNumber > 30 chars	AccountNumber: The field AccountNumber must be a string with a maximum length of 30.
accountNumber regex violation (e.g. spaces, !, ?)	AccountNumber: AccountNumber can only contain alphanumeric characters and -_.+
/rw/disburse
Trigger	Field-tagged message
empty body	Request body is required.
{}	Destination: required; TransferDetails: required; ExternalReferenceId: required
destination.bankName missing / blank	Destination.BankName: The BankName field is required.; Destination.BankName: The BankName field cannot be empty or contain only whitespace.
destination.bankName > 100 chars	Destination.BankName: The field BankName must be a string with a maximum length of 100.
destination.accountNumber missing	Destination.AccountNumber: The AccountNumber field is required.; Destination.AccountNumber: The AccountNumber field cannot be empty or contain only whitespace.
destination.accountNumber > 40 chars	Destination.AccountNumber: The field AccountNumber must be a string with a maximum length of 40.
destination.accountNumber regex violation	Destination.AccountNumber: AccountNumber can only contain alphanumeric characters and -_.+
destination.fullName missing	Destination.FullName: The FullName field is required.; …whitespace
transferDetails missing	TransferDetails: The TransferDetails field is required.
transferDetails.amount missing	TransferDetails.Amount: Amount is required.
amount ≤ 0 or > 10,000,000	TransferDetails.Amount: Amount must be between 1 and 10,000,000 RWF.
dateInEpoch missing	TransferDetails.DateInEpoch: DateInEpoch is required.
dateInEpoch outside 10-digit epoch range	TransferDetails.DateInEpoch: DateInEpoch must be a valid Unix epoch (seconds).
externalReferenceId missing	ExternalReferenceId: The ExternalReferenceId field is required.
externalReferenceId > 30 chars	ExternalReferenceId: The field ExternalReferenceId must be a string with a maximum length of 30.
externalReferenceId regex violation	ExternalReferenceId: ExternalReferenceId can only contain alphanumeric characters, hyphens, underscores, and dots.
remarks > 500 chars	Remarks: The field Remarks must be a string with a maximum length of 500.
Duplicate externalReferenceId	Detected duplicate transaction: Duplicate ExternalReferenceId (idempotency check, 400)
Wrong Content-Type (not JSON)	Unsupported Media Type (415, RFC-7807 ProblemDetails)
Body containing XSS / SQL-injection patterns	Potential XSS Detected / Potential SQL Injection Detected (400, RFC-7807 ProblemDetails — caught by the security middleware before validation runs)
/rw/TransactionStatus
Trigger	Message
Neither pgReferenceId nor externalReferenceId	Either pgReferenceId or externalReferenceId must be provided.
pgReferenceId > 50 chars	pgReferenceId exceeds maximum length of 50 characters.
Query containing XSS patterns	Potential XSS Detected (400, RFC-7807)
/rw/CheckBalance

No request body to validate. The only client-visible failures are auth (401) or downstream balance lookup failures (500 — operational issue, contact support with the Reference Id).
8. Sandbox testing

The sandbox base URL https://api-disbursement-test-rw.azampay.co.tz runs against AzamPay's disbursement sandbox — a faithful in-process replica that exercises the same jobs, callbacks, and balance accounting as production but does not move real money.

What is faithful:

    Settlement and fee accounting against your merchant collection account.
    Fee calculation, including per-merchant rates configured by AzamPay.
    Callback delivery to your registered URL with the standardized V0 shape and RSA signature.
    Auto-provisioned starter balance: when you first authenticate as a new sandbox merchant, the sandbox credits 100,000 RWF to your sub-account so you can test immediately.
    Idempotency on externalReferenceId.
    All validation rules.

What diverges from production:

    /rw/NameLookup returns deterministic fake names (e.g. Magdalena Berge) keyed by the queried account number, with a "Lookup successful (Sandbox)" message. Production returns the real account holder's name.
    Settlement completes synchronously (status: "success" immediately). Production MTN destinations may dwell in pending for a few seconds before settling.
    External destinations (real MTN, BK, Equity numbers that don't exist in the sandbox account ledger) "leave the system" — the merchant is debited, fees are retained, but the destination credit goes nowhere. This is correct sandbox behavior; you'll see the balance delta on your side but no real recipient.
    fspReferenceId is prefixed SBX-… instead of the production reference format.

Quick sandbox happy-path

Code
BASE="https://api-disbursement-test-rw.azampay.co.tz"
TOKEN="<bearer from /AppRegistration/GenerateToken>"

# 1. Pre-balance
curl -s "$BASE/api/v1/azampay/rw/CheckBalance" -H "Authorization: Bearer $TOKEN"

# 2. Lookup the destination
curl -s -X POST "$BASE/api/v1/azampay/rw/NameLookup" \
  -H "Authorization: Bearer $TOKEN" \
  -H 'Content-Type: application/json' \
  -d '{"accountNumber":"250781916866"}'

# 3. Disburse 500 RWF
EPOCH=$(date +%s); EXT=$(openssl rand -hex 12)
RESP=$(curl -s -X POST "$BASE/api/v1/azampay/rw/disburse" \
  -H "Authorization: Bearer $TOKEN" \
  -H 'Content-Type: application/json' \
  -d "{\"destination\":{\"bankName\":\"MTN\",\"accountNumber\":\"250781916866\",\"fullName\":\"Patrick NTWALI\"},\"transferDetails\":{\"type\":\"disbursement\",\"amount\":500,\"dateInEpoch\":$EPOCH},\"externalReferenceId\":\"$EXT\",\"remarks\":\"sandbox happy path\"}")
PG=$(echo "$RESP" | python3 -c "import sys,json;print(json.load(sys.stdin)['pgReferenceId'])")
echo "pgRef: $PG"

# 4. Wait for settlement, then check status
sleep 10
curl -s "$BASE/api/v1/azampay/rw/TransactionStatus?pgReferenceId=$PG" \
  -H "Authorization: Bearer $TOKEN"

# 5. Post-balance — should be down by (500 + 80 + 100) = 680 RWF on default fees
curl -s "$BASE/api/v1/azampay/rw/CheckBalance" -H "Authorization: Bearer $TOKEN"

9. Common scenarios and how to handle them
Scenario	What happens	What you do
Customer enters wrong MSISDN, lookup fails	/rw/NameLookup returns 404	Show error in your UI, ask for a corrected number. Don't proceed to disburse.
Merchant balance insufficient	disburse returns 200 Pending; TransactionStatus later shows failure with message indicating the funding shortfall.	Reconcile and top up your sub-account. The merchant was not debited.
MTN/Airtel destination wallet is locked or full	Settlement fails after acceptance. TransactionStatus.status: "failure", fees still retained by AzamPay.	Manual reversal via support — AzamPay will credit back the transfer amount on request; fees remain per AzamPay's agreement with you.
Network issue during disburse — got no response	The transaction may or may not have been enqueued	Re-call /rw/disburse with the same externalReferenceId. If it was enqueued, you get 400 Detected duplicate transaction. If not, you get a normal 200 Pending. Either way, you end up with at most one disbursement.
Callback never arrives	Your callback URL is unreachable or returning non-200	Poll /rw/TransactionStatus directly. AzamPay also retries callbacks with exponential backoff. Verify your callback URL in the portal's My Application page.
You changed your callback URL after some pending transactions were initiated	New callback URL applies to new transactions only. In-flight transactions continue to deliver to the URL configured at disburse time.	Either keep the old endpoint reachable until in-flight transactions settle, or rely on /rw/TransactionStatus to reconcile.
10. Glossary
Term	Meaning
pgReferenceId	AzamPay's unique identifier for this disbursement. Persist it. Returned on disburse and echoed in TransactionStatus, callback transid/reference/externalreference.
externalReferenceId	Your unique identifier for this disbursement. Used for idempotency. Surfaces as initiatorReferenceId in TransactionStatus and utilityref in callback. Max 30 chars, alphanumeric + -_..
fspReferenceId	The settlement reference for the underlying credit transfer. Use this when raising support tickets. Sandbox prefix: SBX-….
BIC	The 8-character SWIFT/BIC code for the destination institution. Internally resolved from destination.bankName; not exposed on /rw/* responses.
Provider short code	AzamPay's compact identifier for an institution (MTN, BK, etc.). Use this in destination.bankName; it's also returned in /rw/NameLookup.provider.
Quick Reference — Rwanda Disbursement Sandbox URLs
Service	URL
Authenticator	https://authenticator-test-rw.azampay.co.tz
Disbursement API	https://api-disbursement-test-rw.azampay.co.tz
Admin Portal	https://admin-test-rw.azampay.co.tz
Next Steps

    Disbursement API Reference — see the Rwanda API reference in the sidebar for the OpenAPI definition with request/response schemas and an inline playground.
    Checkout — to accept payments, see Getting Started (Rwanda).
    Production — submit your KYC documents through the portal's KYC Submission section. AzamPay will review and provision production disbursement credentials.
    Support — email support@azampay.com or contact your AzamPay account manager. Always include the Reference Id from the failing response when reporting an issue.

