## Authentication & Account Creation

* Create Parent accounts only via Admin: extend AdminParentController to create ParentContact + linked User (role parent) and attach to one/more Students via parent\_student pivot.

* Disable public registration: confirm no /register route and block any self-signup endpoints.

* Temporary credentials: generate random password, set must\_change\_password=true on User; send via SmsService using Parent primary contact number.

* First-login password change: add middleware MustChangePassword to redirect parent users to a forced password update route until flag is cleared.

## Parent-Role Access Control

* Use ensureRole:parent on all Parent routes; hide Admin/Staff navigation for parent users.

* Add policy checks on controllers and queries to scope all data by linked student\_ids from parent\_student.

## Parent Dashboard

* Build ParentDashboardController\@index to aggregate: total outstanding, total paid (via Payment model), upcoming and overdue items from FeeRecord.

* Reuse user\_dashboard.blade.php layout or create parent\_dashboard.blade.php with student cards showing Paid/Partial/Overdue.

## Multi-Child Selector

* Provide a selector populated from parent\_student relations; on change, filter fee records, payments, receipts for the selected student only.

* Enforce data isolation by scoping queries to the parent’s linked student\_ids.

## Detailed Fee Breakdown (Read-Only)

* ParentFeesController\@show: present tuition, misc/additional charges, discounts, penalties, totals, paid, remaining using FeeAssignment + FeeRecord.

* Read-only view with due dates and overdue highlights; no edit actions rendered.

## Online Payment Processing

* ParentPaymentController: authenticated parent-only payments; methods enabled by Admin (config/settings).

* Validate full/partial payments; prevent overpayment/duplicates by checking current balance before accepting amount.

* On success: create Payment, PaymentReceipt, and distribute payment across unpaid FeeRecords (oldest-first), mirroring StaffPaymentController logic.

## Payment Confirmation & Receipts

* Generate immutable receipt (PaymentReceipt) after successful payment; include receipt number, date, method, amount, student.

* Parent can view/download/print receipts; store URLs safely; audit access.

## Payment History & Statement of Account

* ParentPaymentHistoryController: list transactions per student with filters by date range, school year, method.

* Generate downloadable Statement of Account; prefer print-friendly HTML and CSV to avoid new PDF libs; optionally add PDF later if allowed.

## SMS & In‑App Notifications

* Use SmsService + SmsGatewayService to send: new fees, upcoming due, overdue, payment success, receipt available.

* Add ParentNotification preferences (SMS/in-app/both) stored on parent; synchronize contact changes with SMS sending.

* Log all events to SmsLog and notifications table.

## In‑App Notification Inbox

* ParentNotificationsController: list notifications, mark read/unread, keep SMS status in sync where applicable.

## Parent Profile Management

* ParentProfileController: edit name, email, mobile; validate inputs; forbid edits that affect student/financial records.

* On contact update, reflect changes in SMS preferences and future sends.

## Security, Permissions & Audit Logging

* Strictly forbid parent edits to fees/discounts/penalties/academics; expose read-only endpoints.

* Log parent actions: logins, payments, receipt access, profile updates via AuditService with timestamps and user\_id.

## Logout & Session Protection

* Implement secure logout; add session expiration for inactivity (config/session + middleware) on parent routes.

* Protect all parent routes with auth + ensureRole:parent + MustChangePassword when applicable.

## Data Model & Migrations

* User model: add must\_change\_password boolean.

* Parent contact/preferences: ensure ParentContact has mobile/email and a preference flag; if preferred\_contact\_method is being dropped, rely on explicit SMS preference fields.

* No new external libraries; reuse existing tables: Payment, FeeRecord, FeeAssignment, SmsLog, PaymentReceipt.

## Routing & Views

* Add Route::prefix('parent')->middleware(\['auth','ensureRole:parent']) for dashboard, payments, history, receipts, profile, notifications.

* Reuse and adapt existing views where possible; avoid duplication by partials.

## Verification & Tests

* Feature tests: parent login flow (must\_change\_password redirect), dashboard data accuracy, payment validation (overpay prevention), receipts generation, notification preferences enforcement.

* Security tests: data isolation (parents cannot access other students), role-guarded routes, no public registration.

* Manual QA: run scheduled SMS/report commands to ensure no parent-side breakage.

## Rollout Considerations

* Seed: create a demo Parent linked to multiple Students for UAT.

* Backfill: script to create Parent User rows for existing ParentContact entries; set must\_change\_password=true and SMS notify.

* Monitor: audit logs and error logs for early issues; add admin toggle in settings for enabling parent online payments.

