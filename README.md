# Course Bale Bot

بات فروش دوره آموزشی برای پلتفرم بله، ساخته‌شده با Laravel.

## قابلیت‌ها

- منوی اصلی فارسی برای خرید دوره، ثبت‌نام وبینار، معرفی تیم، پشتیبانی فنی و ارتباط با مشاورین
- دریافت وب‌هوک از Bale Bot API
- ارسال پیام با API مستقیم بله
- ذخیره لیدها و رویدادهای وب‌هوک در دیتابیس
- داشبورد ساده مدیریتی در مسیر `/`
- پنل ادمین Filament در مسیر `/admin`
- Docker، Nginx، PHP config و GitHub Actions CI

## راه‌اندازی محلی

```bash
composer require filament/filament:"^3.2"
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan filament:upgrade
php artisan admin:create-user admin@example.com --name="Admin"
php artisan serve
```

توکن بات را در `.env` قرار بده:

```dotenv
BALE_BOT_TOKEN=your-token
APP_URL=https://your-domain.com
COURSE_CHECKOUT_URL=https://your-domain.com/course
WEBINAR_LANDING_URL=https://your-domain.com/webinar
SUPPORT_TECHNICAL_URL=https://web.bale.ai/...
SUPPORT_CONSULTANT_URL=https://web.bale.ai/...
```

## تنظیم وب‌هوک بله

بعد از بالا آمدن دامنه HTTPS:

```bash
php artisan bale:set-webhook https://your-domain.com/webhook/bale
```

API بله طبق مستندات رسمی روی این ساختار صدا زده می‌شود:

```text
https://tapi.bale.ai/bot<TOKEN>/METHOD_NAME
```

## اجرای Docker

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan filament:upgrade
```

برنامه روی `http://localhost:8080` در دسترس است.

## پنل ادمین Filament

بعد از ساخت کاربر ادمین، پنل از مسیر زیر باز می‌شود:

```text
http://localhost:8000/admin
```

در پنل، Resourceهای `Leads` و `Webhook events` به دیتابیس بات وصل هستند و کارت‌های آماری داشبورد از همان داده‌ها خوانده می‌شوند.

## دیپلوی

نمونه کانفیگ‌ها:

- Nginx production: `deploy/nginx/course-bale-bot.conf`
- PHP ini: `docker/php/php.ini`
- اسکریپت دیپلوی: `deploy/scripts/deploy.sh`
- CI/CD: `.github/workflows/ci.yml`

## تست

```bash
composer test
```

## نکته امنیتی

`.env` در Git ذخیره نمی‌شود. توکن بات، لینک‌های خصوصی پشتیبانی و secretها باید فقط در محیط اجرا یا secretهای CI/CD قرار بگیرند.
