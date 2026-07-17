# SolarLink Login Flow — Agent Fix Guide

> Senior-level code audit of the **entire authentication flow** across all four roles
> (Customer, Technician, Vendor, Admin). Each section describes a confirmed bug or
> anti-pattern, the root cause, and precise fix instructions.

---

## 🏗️ Files in Scope

| Layer | File | Purpose |
|-------|------|---------|
| Frontend | `resources/js/Pages/Auth/Login.vue` | Unified login page (role-aware) |
| Frontend | `resources/js/Pages/Auth/Register.vue` | Multi-step registration wizard |
| Frontend | `resources/js/Layouts/DashboardLayout.vue` | Dashboard shell with logout buttons |
| Backend  | `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | Login create/store/destroy |
| Backend  | `app/Http/Controllers/Auth/RegisteredUserController.php` | Registration store |
| Backend  | `app/Http/Requests/Auth/LoginRequest.php` | Login validation + rate limiting |
| Backend  | `app/Http/Middleware/RoleMiddleware.php` | Route-level role gate |
| Backend  | `app/Http/Middleware/RedirectIfAuthenticated.php` | Guest middleware (redirect if logged in) |
| Backend  | `app/Http/Middleware/Authenticate.php` | Auth middleware (redirect if NOT logged in) |
| Routes   | `routes/auth.php` | Auth route definitions |
| Routes   | `routes/web.php` | Dashboard route groups |

---

## 🚨 Bug #1 — Login Does NOT Validate the Submitted Role Against the User's Actual Role

**Severity:** 🔴 Critical (Security)

**What happens:**  
The login form sends `form.role` to the server, but `LoginRequest.php` and
`AuthenticatedSessionController::store()` never check whether the submitted role
matches the authenticated user's `role` column. A customer can visit
`/technician/login`, get pre-filled technician credentials, and log in. Worse, a
real attacker can POST `{ email, password, role: 'admin' }` to `/login` and the
server ignores the role entirely — it just authenticates.

**Root cause:**  
`LoginRequest::authenticate()` only calls `Auth::attempt(['email', 'password'])`.
The `role` field is not validated or cross-referenced.

**Fix:**  
In `AuthenticatedSessionController::store()`, after successful authentication,
compare the authenticated user's DB role with the submitted `role` field (or the
route path). If they don't match, log the user out immediately and return an error.

```php
// In store() — AFTER $request->authenticate()
$user = Auth::user();
$submittedRole = $request->input('role');

if ($submittedRole && $user->role !== $submittedRole) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    throw ValidationException::withMessages([
        'email' => 'These credentials do not match a ' . $submittedRole . ' account.',
    ]);
}
```

---

## 🚨 Bug #2 — "Forgot?" Password Link Is a Dead `<span>`, Not a Real Link

**Severity:** 🟡 Medium (UX)

**What happens:**  
On `Login.vue` line 276, the "Forgot?" text is a `<span>` with cursor-pointer
styling but no navigation. Clicking it does nothing.

**Root cause:**  
The developer styled it as a link but never added `<Link>` or `@click` logic.

**Fix:**  
Replace the `<span>` with an Inertia `<Link>` that navigates to the
`password.request` route:

```vue
<Link :href="route('password.request')"
      class="text-[10px] font-bold text-solar-primary cursor-pointer hover:underline uppercase tracking-wider">
    Forgot?
</Link>
```

---

## 🚨 Bug #3 — Admin Logout URL Is Hardcoded, Not Using Ziggy `route()`

**Severity:** 🟡 Medium (Consistency / Breakability)

**What happens:**  
In `DashboardLayout.vue` line 238, the admin logout uses a hardcoded string
`'/admin/logout'` instead of `route('admin.logout')`.

**Root cause:**  
The route `admin.logout` was registered *after* this code was written, and the
developer forgot to update it.

**Fix:**
```js
case 'admin':
    return route('admin.logout')
```

---

## 🚨 Bug #4 — Demo Credentials Pre-filled in Production Login Form

**Severity:** 🔴 Critical (Security)

**What happens:**  
The login form initializes with `email: activeConfig.value.email` (e.g.,
`customer@solarlink.io`) and `password: 'password'` hard-coded in the `useForm`
call on line 107-112 of `Login.vue`. This is extremely dangerous in production.

**Root cause:**  
The demo auto-fill feature was designed for development convenience but the form
*defaults* to pre-filled credentials, not just the "Click to Pre-fill" button.

**Fix:**  
Initialize the form with empty values. Let the `triggerAutofill()` button handle
demo pre-filling only when clicked:

```ts
const form = useForm({
    email: '',
    password: '',
    role: activeRole.value,
    remember: false,
})
```

Optionally, guard the auto-fill button behind an environment check:
```ts
const isDev = import.meta.env.DEV
```
And only show the autofill widget `v-if="isDev"`.

---

## 🚨 Bug #5 — `ensureDemoUsersExist()` Runs on Every Login Page Load

**Severity:** 🟡 Medium (Performance / Security)

**What happens:**  
Every time ANY user visits the login page or submits the login form,
`AuthenticatedSessionController` calls `ensureDemoUsersExist()`, which:
1. Runs 5 `firstOrCreate` queries against the users table
2. Runs 3 `->exists()` queries against profile tables
3. Checks if `Conversation::count() === 0` and seeds chat data

This adds ~8 DB queries to every login page load.

**Root cause:**  
Demo seeding was put inside the controller instead of a proper database seeder.

**Fix:**  
1. Move the demo data logic to `DatabaseSeeder.php` or a dedicated `DemoSeeder`.
2. Run it via `php artisan db:seed --class=DemoSeeder` once during setup.
3. Remove the `ensureDemoUsersExist()` call from both `create()` and `store()`.

If you want a "self-seeding" dev experience, guard it:
```php
if (app()->environment('local') && User::count() === 0) {
    $this->ensureDemoUsersExist();
}
```

---

## 🚨 Bug #6 — `Authenticate` Middleware Always Redirects to `/login` (Generic)

**Severity:** 🟢 Low (UX)

**What happens:**  
When a session expires while a technician is on `/technician/calendar`, they get
redirected to `route('login')` which redirects to `/user/login` — the customer
portal. They lose context of which portal they were using.

**Root cause:**  
`Authenticate.php` line 15 always returns `route('login')`, and the `login` named
route (in `auth.php` line 20-22) redirects to `route('user.login')`.

**Fix:**  
Make `Authenticate::redirectTo()` detect the URL prefix and redirect to the
appropriate role-specific login:

```php
protected function redirectTo(Request $request): ?string
{
    if ($request->expectsJson()) {
        return null;
    }

    $path = $request->path();
    if (str_starts_with($path, 'technician')) return route('technician.login');
    if (str_starts_with($path, 'vendor'))     return route('vendor.login');
    if (str_starts_with($path, 'admin'))      return route('admin.login');
    return route('user.login');
}
```

---

## 🚨 Bug #7 — Register Form Sends `role: 'admin'` if Manipulated

**Severity:** 🔴 Critical (Security)

**What happens:**  
The registration form includes `role` as a client-submitted field. The backend
validates it with `'in:customer,technician,vendor'` — which correctly excludes
`admin`. **This is fine.** However, this should be explicitly documented and the
frontend should NOT expose admin as a registerable role (which it doesn't currently).

**Root cause:**  
Not a current bug per se, but the architecture relies entirely on backend
validation to prevent admin self-registration. There is no server-side safeguard
if someone accidentally adds `'admin'` to the validation rule in the future.

**Fix:**  
Add an explicit comment in `RegisteredUserController::store()`:
```php
// SECURITY: 'admin' is intentionally excluded from allowed registration roles.
// Admin accounts must be created via artisan command or database seeder only.
'role' => 'required|string|in:customer,technician,vendor', // Never add 'admin' here
```

---

## 🚨 Bug #8 — `Register.vue` Has a v-slot Typo on `<Sun>` Component

**Severity:** 🟢 Low (Bug)

**What happens:**  
`Register.vue` line 141:
```html
<Sun v-slot="isDark" v-if="isDark" class="h-5 w-5" />
```
`v-slot="isDark"` is invalid on a leaf component (`Sun` has no slots). It should
just be `v-if="isDark"`.

**Root cause:**  
Copy-paste typo.

**Fix:**
```html
<Sun v-if="isDark" class="h-5 w-5" />
```

---

## 🚨 Bug #9 — Login `Register Account` Link Has Broken Logic for Admin

**Severity:** 🟢 Low (UX)

**What happens:**  
`Login.vue` line 328:
```vue
:href="activeRole === 'customer' ? route('user.register')
     : activeRole === 'technician' ? route('technician.register')
     : activeRole === 'vendor' ? route('vendor.register')
     : route('user.register')"
```
If the active role is `admin`, the "Register Account" link goes to the customer
registration page. Admins should not have a registration link at all.

**Root cause:**  
The ternary fallback defaults to `user.register` instead of being hidden.

**Fix:**  
Hide the "Register Account" section entirely when the active role is `admin`:
```vue
<div v-if="activeRole !== 'admin'" class="text-center mt-6">
    <!-- existing register link -->
</div>
```

---

## 🚨 Bug #10 — Login Form Does Not Show Server-side `status` Messages

**Severity:** 🟡 Medium (UX)

**What happens:**  
The controller passes `'status' => session('status')` to the login page (e.g.,
after a successful password reset). But `Login.vue` never renders `props.status`
anywhere in the template.

**Root cause:**  
The prop is defined but the corresponding success banner/alert is missing from the
template.

**Fix:**  
Add a status banner above the form:
```vue
<div v-if="status" class="mb-4 p-3 rounded-xl bg-solar-success/10 border border-solar-success/20 text-sm font-semibold text-solar-success text-center">
    {{ status }}
</div>
```

---

## 🚨 Bug #11 — `SL` Text Logo Placeholders Not Yet Replaced With Image Logo

**Severity:** 🟢 Low (Branding)

**What happens:**  
The `Login.vue` (line 170-172), `Register.vue` (line 162-164), `Footer.vue`, and
`DashboardLayout.vue` (lines 267, 339) still use the old `<span>SL</span>` text
placeholder instead of the actual logo image at `/images/logo.png`.

**Root cause:**  
The logo was only recently generated and only `PublicNavbar.vue` was updated.

**Fix:**  
In each file, replace:
```html
<div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-solar-primary to-solar-primary-accent flex items-center justify-center ...">
    <span class="text-white font-extrabold text-sm">SL</span>
</div>
```
With:
```html
<img src="/images/logo.png" alt="SolarLink Logo" class="h-9 w-9 rounded-xl object-contain" />
```

---

## 🛠️ Fix Priority Order

| Priority | Bug # | Description | Risk |
|----------|-------|-------------|------|
| 1 | #1 | Role not validated on login | 🔴 Security |
| 2 | #4 | Demo creds pre-filled by default | 🔴 Security |
| 3 | #5 | Demo seeder runs on every request | 🟡 Perf/Security |
| 4 | #7 | Document admin registration guard | 🔴 Security awareness |
| 5 | #2 | Forgot password link dead | 🟡 UX |
| 6 | #10 | Status message not displayed | 🟡 UX |
| 7 | #6 | Auth redirect ignores role context | 🟢 UX |
| 8 | #3 | Admin logout hardcoded | 🟡 Consistency |
| 9 | #9 | Admin sees Register link | 🟢 UX |
| 10 | #8 | v-slot typo on Sun icon | 🟢 Bug |
| 11 | #11 | SL text logo placeholders | 🟢 Branding |

---

## ⚙️ Agent Instructions

1. **Fix one bug at a time** in priority order above.
2. **Verify each fix** before moving to the next (rebuild assets if frontend changes, test the flow manually).
3. **Do NOT break existing styles or layouts.** Maintain the premium aesthetic.
4. **Do NOT remove** the demo auto-fill button entirely — just make the form start empty and guard the widget behind `import.meta.env.DEV`.
5. **Run `npm run build`** after any Vue file changes to ensure clean compilation.
6. **Test all 4 login portals** (customer, technician, vendor, admin) after each fix.
