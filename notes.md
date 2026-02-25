to start the server 127.0.0.1:8000

php artisan serve
to prompt right in terminal

pkill -f "php artisan serve"
 to stop all running instances of php artisan serve



paste link here for the member to access only 8000


admin@gmail.com 
admin



cd /d d:\Efees\Efees
git status
git pull


Good 👍 since it’s **Laravel**, after you push to GitHub you still need to run some commands on your Hostinger server (unless you configured automatic deployment scripts).

---

# ✅ After `git push` (Laravel on Hostinger SSH)

### 1️⃣ SSH into your server

```bash
ssh -p 65002 u174002700@your-server-ip
```

### 2️⃣ Go to your Laravel project folder

(Usually inside `domains` in Hostinger)
```bash
cd domains/yourdomain.com/public_html
```

⚠️ If your Laravel project is outside `public_html` (recommended), go to the project root instead.

---

### 3️⃣ Pull latest changes

```bash
git pull origin main
```

(or `master` if that's your branch)

---

### 4️⃣ Install / update dependencies

```bash
composer install --no-dev --optimize-autoloader
```

---

### 5️⃣ Run migrations (if database changes)

```bash
php artisan migrate --force
```

---

### 6️⃣ Clear and cache config (VERY IMPORTANT for 500 errors)

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
```

---

### 7️⃣ Fix permissions (common cause of 500 error)

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

If still error:

```bash
chown -R yourusername:yourusername storage bootstrap/cache
```

---

# 🔥 If you are getting 500 error

Check Laravel log:

```bash
tail -f storage/logs/laravel.log
```

That will show the real error.

---

# 🚀 BEST PRACTICE (Automatic Deploy Script)

You can create a deploy script like this:

```bash
nano deploy.sh
```

Paste:

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Save then run:

```bash
chmod +x deploy.sh
./deploy.sh
```

---

If you want, tell me:

* Is your Laravel inside `public_html`?
* Or is only the `public` folder inside `public_html`?

Because that setup is VERY important in Hostinger.
