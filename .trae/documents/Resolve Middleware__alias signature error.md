## Root Cause
- In your Laravel version, `Middleware::alias()` expects an array of alias mappings, not two separate arguments. Current code in `bootstrap/app.php:14` calls `$middleware->alias('ensureRole', \App\Http\Middleware\EnsureRole::class);`, causing the fatal TypeError.

## Required Change
- Update `bootstrap/app.php` to pass an associative array to `alias()`:
  ```php
  $middleware->alias([
      'ensureRole' => \App\Http\Middleware\EnsureRole::class,
  ]);
  ```

## Follow-ups
- Clear caches: `php artisan optimize:clear`.
- Verify routes and runtime: run `php artisan route:list` and hit protected pages to confirm no TypeError and correct 403 behavior for non-matching roles.

## Notes
- Your `EnsureRole` signature already supports optional role and is safe.
- No changes needed in `routes/web.php` since they already use `ensureRole:admin|staff|student`.

I will apply the change, clear caches, and verify end-to-end.