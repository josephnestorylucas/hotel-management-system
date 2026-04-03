You are a Laravel backend + Blade engineer.

========================================
OBJECTIVE
========================================
Add receipt printing for all payment-related models and the financial system.

Receipts must be consistent, printable, and available anywhere a payment is completed or recorded.

========================================
STEP 1: IDENTIFY ALL PAYMENT MODELS
========================================

Search for:
- payment
- settle
- checkout
- transaction
- invoice
- receipt

List every model/flow that finalizes payments, such as:
- Booking / Checkout charges
- Laundry orders
- Restaurant orders
- Bar orders
- Store sales
- Conference / events
- Walk-in transactions

Confirm which model is the source of truth for each payment.

========================================
STEP 2: STANDARD RECEIPT DATA CONTRACT
========================================

Create a shared contract or trait (e.g., ReceiptPrintable) that returns:
- receipt_no
- issued_at
- module
- customer_name
- customer_phone
- items summary
- subtotal
- discount
- tax
- total
- amount_paid
- balance (if any)
- payment_method
- transaction_reference
- cashier

Every payment model must implement this data format.

========================================
STEP 3: RECEIPT NUMBERING
========================================

Implement a single receipt numbering service:
- Unique and sequential
- Prefix like HMS-YYYY-XXXX
- Idempotent: reprint must reuse existing number

Store receipt_no in the transaction record or a new receipts table.

========================================
STEP 4: RECEIPT RENDERING
========================================

Create a single printable Blade view:
- resources/views/receipts/print.blade.php
- Print CSS for A4 and thermal
- Clear layout: header, customer, items, totals, payment details

========================================
STEP 5: ROUTES + CONTROLLERS
========================================

Add routes to print receipts:
- /receipts/{module}/{id}/print or
- /receipts/{transaction}/print

Controller must:
- authorize cashier/manager
- load model + transaction
- render receipt view

========================================
STEP 6: UI INTEGRATION
========================================

Add a "Print receipt" button in:
- in  the  all  orders   where  are  not  paid the  reciept  cna  say it  has  not  been  paid 
- Checkout payment success
- Laundry settle success
- Restaurant settle success
- Bar settle success
- Store sale completion
- Transaction details screen

========================================
STEP 7: FINANCIAL SYSTEM INTEGRATION
========================================

Ensure accounting records store and display:
- receipt_no
- transaction_reference

Add print link in financial reports or transaction views.

========================================
STEP 8: EDGE CASES
========================================

Handle:
- walk-ins
- partial payments (show balance)
- refunds (print refund receipt with reference)
- missing customer data (show "N/A")

========================================
STEP 9: VALIDATION
========================================

Verify for each module:
1. Payment completes
2. Receipt generated and printed
3. Reprint uses same receipt_no
4. Totals match transaction

========================================
OUTPUT REQUIRED
========================================

1. List of models updated
2. Receipt view path
3. Routes/controllers added
4. Confirmation all modules print consistently
5. Confirmation receipt numbers are stored in accounting records
