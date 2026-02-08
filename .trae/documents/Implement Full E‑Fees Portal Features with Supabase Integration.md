## Scope & Assumptions
- Keep current UI/UX styling and page layouts already in the repository (Tailwind, Blade).
- Use existing role system (`roles`, `users.role_id`, `roleable_type/id`) and map to Supabase where needed.
- Supabase is the primary database; Laravel models continue to use Eloquent with the existing schema, plus additional tables where required.
- Real-time updates: Supabase Realtime or polling fallback; cache via Laravel cache.

## Architecture
- Data: Laravel Eloquent models mapped to Supabase tables; respect existing tables (`users`, `roles`, `students`, `fee_records`, `additional_charges`, `discounts`, `tuition_fees`, new `tuition_fee_*`).
- Services: SMS Gateway service (Semaphore-like) via a thin wrapper; Payment Gateway service for online payments (GCash/PayMaya/Card) via drivers.
- Jobs/Queues: Laravel queue workers for SMS sending, payment receipt generation, and report exports.
- Config: System-wide settings loaded from DB and cached; updated via Settings UI.

## Supabase Integration
- Configure DB connection to Supabase PostgreSQL; ensure table names match current migrations.
- Implement a Supabase Realtime subscriber: broadcast events to dashboard components for totals, reminders sent, payment updates.
- Use read replicas-friendly queries and caching with invalidation on write.

## Data Model Alignment
- Continue with existing `users` + `roles` + polymorphic `roleable` (Admin/Staff/Student) to avoid breaking code.
- Add/confirm tables required by features: `sms_templates`, `sms_logs`, `sms_schedules`, `system_settings`, `generated_reports`, `payment_receipts`, `payments` (if not present).
- For online payments, store provider metadata and receipt file URLs in Supabase storage.

## Admin: Dashboard Overview
- Backend: Aggregate totals (fees collected, pending payments, SMS reminders sent) with cached queries; expose JSON endpoints for charts.
- Realtime: Subscribe to payment and sms events to update counts live; fallback to polling.
- UI: Summary cards (collected, pending, reminders), charts (line/bar), recent activity log.
- Performance: Add indices and `where` filters; cache computed aggregates with short TTL and invalidate on writes.

## Admin: User Management
- CRUD for Staff and Students using existing controllers; add status toggles and password resets.
- RBAC: Middleware guards (`admin`, `staff`, `student`) using `users.role_id` → `roles.role_name`.
- UI: List, search, filters, edit forms, reset password, activate/deactivate; confirm modals.
- Sync: Ensure all writes persist to Supabase; reads filtered by status and role.

## Admin: Fees Management
- Tuition + charges + discount rules: use existing `TuitionFee` and new per-fee components (`tuition_fee_charges`, `tuition_fee_discounts`).
- Calculation service: fixed and percentage discounts; validate amounts and caps; integrate into fee assignment generation.
- Forms: Create/Edit fees with dynamic lists; validate inputs; preview totals.
- Data: Store settings in relational tables; ensure fee assignments use current parameters.

## Admin: Reports & Analytics
- Reporting queries: payments, balances, trends; filter by date range, grade, status.
- UI: Tabular + charts; export to PDF/Excel.
- Jobs: Async export generation saved to Supabase storage and recorded in `generated_reports`.

## Admin: SMS Control
- Templates: CRUD in `sms_templates`; placeholders like `{{name}}` `{{balance}}`.
- Logs: `sms_logs` shows history and delivery status; filter and search.
- Gateway: Integrate provider; queue jobs for sending; retry/backoff.
- Automation: Scheduled reminders in `sms_schedules`; triggers on pending balances and payment success via events.

## Admin: System Settings
- UI for school year, semester, payment schedules, penalties, auth/security.
- Persist in `system_settings`; cache and auto-load on boot; propagate to modules (fees, payments, reminders).

## Staff: Student Records Management
- Page: View balances, discounts, charges, payment status; edit with validation.
- Sync: Immediate write to Supabase; recompute totals; audit changes.
- Search/Filters: By class/section/status; pagination.

## Staff: Payment Processing
- Record payments with method, amount, reference; recompute balances; create `payment_receipts`.
- Notifications: Confirmations, optional email/SMS; queue receipt generation.

## Staff: SMS Reminders
- Manual or scheduled sends; filters by class/section/status.
- Use same gateway and logs; show delivery reports.

## Staff: Payment History Access
- Transactions list with date, amount, method, reference, receipt link; search and filters.
- Secure access via middleware; data from Supabase `payments`/`payment_receipts`.

## Staff: Reports Generation
- Class/section-based reports; filters; export PDF/Excel; schedule and store exports in `generated_reports`.

## Student: Dashboard
- Show current balance, paid fees, upcoming deadlines, reminders; realtime updates.
- Friendly UI: cards and timelines; due date alerts.

## Student: Online Payment Option
- Gateways: integrate drivers; handle callbacks; update balances; log payments; generate receipts.
- Security: CSRF, signed routes, webhook verification.

## Student: Payment History
- List all transactions with downloadable receipts; secure by student.
- RLS-like behavior: enforce authorization via middleware and query filters.

## Student: SMS Notifications
- Event-driven SMS for due dates, overdue, and payment success; log all notifications.
- Opt-in/out settings per student; respect preferences.

## Student: Profile Management
- Update contact info and mobile numbers; validate and persist; audit changes.

## Real-time & Caching Strategy
- Supabase Realtime channels for `payments`, `sms_logs`, `fee_assignments` to push updates to dashboards.
- Cache aggregates in Laravel; invalidate on write; polling fallback.

## Security & Access Control
- Middleware per role; controller authorization; input validation everywhere.
- Protect sensitive data; never log secrets; secure webhooks and storage URLs.

## Testing & Verification
- Feature tests for RBAC, CRUD, fees calculation, payments, SMS send.
- Job/queue tests for exports and notifications; integration tests for gateways.

## Data Migration & Seeding
- Confirm/create missing tables (`sms_*`, `system_settings`, `generated_reports`, `payments`, `payment_receipts`).
- Seed base roles, an admin account, default settings, sample templates.

## Rollout & Feature Flags
- Gradual enablement of online payments and automated SMS via flags in `system_settings`.

---
Please confirm this plan. Once approved, I will start wiring Supabase realtime, create any missing tables, implement the modules in the order above, and deliver verified, functional pages with the existing UI/UX.