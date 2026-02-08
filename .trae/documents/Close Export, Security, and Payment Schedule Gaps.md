## Current State Findings
- Export: Multiple CSV endpoints exist; no PDF or Excel libraries or code paths. See Admin/Staff reports and audit logs controllers.
- Security: No max_login_attempts or password_expiry policies. System settings infra exists but lacks security keys; login flow has no throttle/lockout.
- Payment Schedules: tuition_fees.payment_schedule JSON exists. Admin stores a minimal {installment_allowed, plan} flag, but FeeAssignment expects itemized schedule entries; no generator creates them, so installments aren’t operational.

## PDF/Excel Export Implementation
- Add dependencies: dompdf (barryvdh/laravel-dompdf) for PDF; maatwebsite/excel for .xlsx.
- Create an ExportService to centralize dataset → CSV/PDF/Excel mapping and shared query building.
- PDF: Build Blade-based templates per report type (students, payments, audit logs, SMS logs); paginate and stream large PDFs.
- Excel: Implement exports using FromCollection/FromView with chunking for large datasets; add memory-safe streaming.
- Routing: Extend existing export routes to accept a format param (csv|pdf|xlsx) and delegate to ExportService.
- Access & audit: Enforce role checks; log export events and parameters.
- Tests: Endpoint tests for PDF/XLSX generation and CSV parity; validate column order, totals, and file headers.

## Security Settings (Lockout & Expiry)
- System settings: Add keys max_login_attempts, lockout_minutes, password_expiry_days; expose in admin settings UI.
- Migrations: users table add failed_login_attempts (int), lockout_until (datetime), password_expires_at (datetime).
- Throttling & lockout: Use route throttle middleware for burst protection and persistent lockout after max attempts (set lockout_until); reset counters on successful login.
- Password expiry: Set password_expires_at on creation/reset; enforce at login (redirect to change-password) and notify before expiry.
- Admin UI: Editable values with validation and defaults; audit changes.
- Tests: Cover lockout threshold, unlock after window, expiry enforcement, notification timing.

## Payment Schedules (Installments)
- Generator: Convert plan (monthly|quarterly|semester) + base tuition + start context into itemized schedule entries [{label, amount, due_date}]. Even split with rounding to match total.
- Persist: Store generated entries in tuition_fees.payment_schedule; allow manual overrides when needed.
- Admin controller: When plan is set/updated, generate and save entries; optional first due date input.
- Assignment: Ensure FeeAssignment creates tuition_installment FeeRecord entries from itemized schedule and respects due dates.
- Service: Update FeeManagementService to compute totals/penalties with installment records and avoid treating tuition as a single lump sum.
- UI: Preview schedule in Tuition Fee edit page; validate counts, dates, and totals.
- Tests: Verify monthly/quarterly generation, totals consistency, due date spacing, ledger entries, and penalty accrual behavior.

## Migration & Backfill
- Apply new users columns; backfill password_expires_at for existing accounts using default policy.
- Generate schedules for active tuition fees using selected plan; mark entries audited.

## Rollout & Verification
- Deploy dependencies and migrations; run automated tests.
- Manually validate one report in PDF and Excel, confirm layout/columns.
- Simulate failed logins to confirm lockout; test password expiry redirect.
- Create a tuition fee with monthly plan and confirm installments appear in FeeAssignment and ledger.

Would you like me to proceed with this implementation plan?