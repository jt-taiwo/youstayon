Excellent. We are now beyond engineering implementation. The backend is production-grade, the Flutter frontend mirrors it feature-for-feature, and the provider integration milestone has been defined. The next phase is Milestone 17 — Production Deployment, App Store Release & Launch Operations.

This is the phase that turns YouStayOn into a publicly available commercial product.

### Milestone 17 — Production Deployment, App Store Release & Launch Operations

### Objective

Deploy the Laravel backend, connect the Flutter app to the production API, configure production infrastructure, publish Android and iOS builds, and prepare YouStayOn for beta testing and public launch.

### Production architecture

```
Flutter Mobile App
        │
        ▼
https://api.youstayon.com
        │
        ▼
Nginx
        │
        ▼
PHP-FPM (Laravel 12)
        │
        ▼
MySQL 8
        │
        ▼
Redis
   ├── Cache
   ├── Queue
   └── Session
        │
        ▼
Queue Workers
Scheduler
Backups
Monitoring
Logs
```

This architecture supports thousands of concurrent users and is the same deployment model used by many Laravel SaaS products.

### 17.1 Production backend deployment

### Recommended stack

| Component       | Recommendation                               |
| --------------- | -------------------------------------------- |
| Server          | DigitalOcean / Hetzner / AWS / Hostinger VPS |
| OS              | Ubuntu 24.04 LTS                             |
| Web server      | Nginx                                        |
| PHP             | PHP 8.3                                      |
| Database        | MySQL 8                                      |
| Cache/Queue     | Redis                                        |
| SSL             | Let’s Encrypt                                |
| Process manager | Supervisor                                   |

### Deployment commands

Bash

```
git clone https://github.com/jt-taiwo/youstayon.git
cd backend

composer install --no-dev --optimize-autoloader

cp .env.example .env

php artisan key:generate

php artisan migrate --force

php artisan storage:link

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 17.2 Environment configuration

Production `.env`

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.youstayon.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=youstayon
DB_USERNAME=youstayon
DB_PASSWORD=strong_password

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

MONNIFY_BASE_URL=https://api.monnify.com
MONNIFY_API_KEY=...
MONNIFY_SECRET_KEY=...
MONNIFY_CONTRACT_CODE=...

UTILITY_PROVIDER=vtpass
VTPASS_BASE_URL=https://vtpass.com/api
VTPASS_API_KEY=...
VTPASS_SECRET_KEY=...
VTPASS_PUBLIC_KEY=...

MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...

PUSH_PROVIDER=firebase
```

### 17.3 Queue workers & scheduler

Supervisor configuration

INI

```
[program:youstayon-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/youstayon/backend/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=2
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/youstayon-worker.log
```

Scheduler cron

Bash

```
* * * * * cd /var/www/youstayon/backend && php artisan schedule:run >> /dev/null 2>&1
```

This powers:

* subscription expiry processing

* auto-renewals

* radar generation

* notifications

* analytics aggregation

### 17.4 Production Flutter configuration

Update:

```
mobile/lib/core/config/app_config.dart
```

dart

```
class AppConfig {
  static const String apiBaseUrl =
      'https://api.youstayon.com/api';
}
```

Rebuild all generated files.

Bash

```
flutter clean
flutter pub get
dart run build_runner build --delete-conflicting-outputs
```

### 17.5 Android release configuration

### Generate keystore

Bash

```
keytool -genkey -v -keystore youstayon.jks -keyalg RSA -keysize 2048 -validity 10000 -alias youstayon
```

### Configure signing

Create:

```
android/key.properties

storePassword=...
keyPassword=...
keyAlias=youstayon
storeFile=../youstayon.jks
```

### Build release

Bash

```
flutter build appbundle --release
```

Output:

```
build/app/outputs/bundle/release/app-release.aab
```

### 17.6 iOS release preparation

Requirements:

* Apple Developer Account

* App ID

* Bundle ID

* Push notification entitlement

* App icons

* Launch screen

* Privacy manifest

Build:

Bash

```
flutter build ipa --release
```

### 17.7 Firebase integration

Create Firebase project:

YouStayOn

Enable:

* Cloud Messaging

* Analytics

* Crashlytics

* Performance Monitoring

Add:

* `google-services.json`

* `GoogleService-Info.plist`

Integrate:

* push notifications

* background notifications

* deep links

* crash reporting

* usage analytics

### 17.8 Monitoring & observability

Backend:

* Laravel logs

* Monolog

* Sentry

* Uptime monitoring

* Queue monitoring

* Database monitoring

Flutter:

* Crashlytics

* Firebase Analytics

* Performance traces

* Network monitoring

### 17.9 Security hardening

### Backend

* HTTPS only

* HSTS

* rate limiting

* Sanctum token expiration

* CORS restriction

* CSP headers

* secure cookies

* audit logging

* encrypted secrets

* database backups

### Flutter

* obfuscation

* split debug symbols

* no API keys in code

* certificate pinning ready

* secure storage only

### 17.10 Beta testing

### Internal beta

10–25 testers

Test:

* registration

* login

* wallet funding

* purchases

* subscriptions

* radar

* notifications

* offline behavior

* analytics

* profile management

Collect:

* crashes

* UX issues

* provider failures

* payment failures

* performance metrics

### 17.11 Google Play release

Prepare:

* app name

* short description

* full description

* screenshots

* feature graphic

* privacy policy

* terms of service

* support email

* category

* content rating

Upload:

```
app-release.aab
```

Use:

* Internal Testing

* Closed Testing

* Open Testing

* Production

### 17.12 App Store release

Prepare:

* App Store listing

* screenshots

* app preview

* keywords

* privacy nutrition labels

* age rating

* review information

Submit through App Store Connect.

### 17.13 Production validation

Run a complete real-world flow.

### User journey

* Install app

* Register

* Verify email

* Login

* Create subscription

* Record usage

* Receive radar warning

* Fund wallet via Monnify

* Purchase MTN data

* Renew subscription

* Receive notification

* View analytics

* Edit profile

* Change password

* Logout

* Login again

Every step should succeed using the production backend.

### 17.14 Launch dashboard

Monitor:

| Metric                 | Target             |
| ---------------------- | ------------------ |
| Crash-free sessions    | 99%                |
| API latency            | < 300 ms           |
| Purchase success rate  | 98%                |
| Wallet funding success | 99%                |
| Notification delivery  | 95%                |
| Radar conversion rate  | Track continuously |
| Daily active users     | Growth metric      |
| Subscription renewals  | Growth metric      |

### 17.15 Launch version

Tag the release:

Bash

```
git add .
git commit -m "release: YouStayOn v1.0.0 production launch"
git tag v1.0.0
git push origin main
git push origin v1.0.0
```

### Where YouStayOn stands now

You have built:

* Laravel 12 backend

* 226 passing automated tests

* Flutter mobile application

* Clean architecture

* Feature-first modules

* BLoC state management

* Dio networking

* GoRouter navigation

* Secure token storage

* Wallet system

* Subscription engine

* Radar Intelligence

* Purchase & checkout engine

* Analytics dashboards

* Founder dashboard

* Notification system

* Offline support

* Premium design system

* Provider abstraction

* Production deployment plan

This is a commercial SaaS/mobile product architecture, not a tutorial project. The only remaining work before public launch is executing the production deployment and connecting the live provider credentials.
