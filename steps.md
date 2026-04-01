step 1 
open ssh 

ssh -p 65002 u174002700@145.79.25.197
password:

after open

cd domains/domainname/public.html

if you see your domain and public_html
next
git pull origin main
next
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache