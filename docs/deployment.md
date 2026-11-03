# Production Deployment Guide

This guide details configuring and deploying **AI Sales Assistant** in a high-availability production architecture.

---

## Architecture Overview

```
                      [Cloudflare / DNS & CDN]
                                 │
                        [Nginx Reverse Proxy]
                                 │
                   ┌─────────────┴─────────────┐
                   ▼                           ▼
          [PHP 8.3/8.4 FPM]           [Laravel Reverb (WS)]
                   │
    ┌──────────────┼──────────────┐
    ▼              ▼              ▼
[MySQL 8]     [Redis Cluster]  [Object Storage / S3]
                   │
          [Laravel Horizon]
          (Queue Workers)
```

---

## 1. Web Server (Nginx)

Example Nginx virtual host configuration:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name app.yourdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name app.yourdomain.com;
    root /var/www/ai-sales-assistant/public;

    ssl_certificate /etc/letsencrypt/live/app.yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/app.yourdomain.com/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 2. Process Management (Supervisor)

Create `/etc/supervisor/conf.d/ai-sales-worker.conf`:

```ini
[program:ai-sales-horizon]
process_name=%(program_name)s
command=php /var/www/ai-sales-assistant/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/ai-sales-assistant/storage/logs/horizon.log
stopwaitsecs=3600
```

---

## 3. Recurring Scheduled Tasks (Cron)

Add to the web server's crontab:
```bash
* * * * * cd /var/www/ai-sales-assistant && php artisan schedule:run >> /dev/null 2>&1
```

---

## 4. Optimization Commands on Deploy

Include in your CI/CD deployment pipeline:
```bash
php artisan down
git pull origin main
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan horizon:terminate
php artisan up
```
