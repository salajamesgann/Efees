## Root Cause
- `EnsureRole` is registered as a global middleware in `bootstrap/app.php:14`, but its `handle(Request, Closure, string $role)` method requires a role parameter. Global middleware is invoked with only `(request, next)`, causing the “Too few arguments” error.

## Required Changes
- Remove global append and register it as a route middleware alias.
- Keep routes using `ensureRole:admin|staff|student` to pass the parameter.

## File Updates
- `bootstrap/app.php`
  - Replace:
    ```php
    $middleware->append(\App\Http\Middleware\EnsureRole::class);
    ```
  - With:
    ```php
    $middleware->alias('ensureRole', \App\Http\Middleware\EnsureRole::class);
    ```
- `app/Http/Middleware/EnsureRole.php` (no change required)
  - Signature is currently `handle(Request $request, Closure $next, string $role)` at `EnsureRole.php:11` and will work when invoked via route alias.
- `routes/web.php`
  - Routes already use `ensureRole:...` (e.g., `routes/web.php:25, 27–29, 36–47, 67, 93, 106, 116, 122`). No change if the alias is defined.

## Optional Defensive Guard (if you want crash-proof behavior)
- Make the third parameter optional to avoid accidental global registration:
  ```php
  public function handle(Request $request, Closure $next, ?string $role = null)
  {
      if ($role === null) { return $next($request); }
      $user = Auth::user();
      if (!$user || optional($user->role)->role_name !== $role) { abort(403); }
      return $next($request);
  }
  ```
- Not strictly needed once aliasing is correct, but prevents future pipeline errors.

## Cache Clear
- After the change, clear framework caches to ensure middleware alias is picked up:
  - `php artisan optimize:clear`

## Verification Steps
- Login with any role and hit an unprotected page to confirm no error occurs.
- Access `admin` pages:
  - `GET /admin_dashboard` (`routes/web.php:28`) should load for admin and return 403 for non-admin.
- Access `staff` and `student` pages (e.g., `routes/web.php:36–47`, `:25–27`) with their respective accounts; confirm proper access/denials.

## Rollback
- If needed, revert alias back and re-append—but this will reintroduce the error. The correct approach is to keep it as a route alias.

Please confirm and I will apply the changes, clear caches, and re-test end-to-end.