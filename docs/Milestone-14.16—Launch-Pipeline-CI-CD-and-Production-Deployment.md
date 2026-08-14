Excellent. With Milestone 14.15 (Production Polish & Release Readiness) completed conceptually, we now move to Milestone 14.16 — Launch Pipeline, CI/CD, and Production Deployment.

This milestone turns YouStayOn into a deployable product. The goal is that every push to `main` can automatically build, test, and prepare release artifacts, while the Flutter app connects to the Laravel backend in staging and production exactly as the architecture was designed.

### Milestone 14.16 — Launch Pipeline, CI/CD & Production Deployment

### Objective

Create a professional deployment pipeline for both Laravel (backend) and Flutter (mobile) so that YouStayOn can be released to internal testers first, then to the Google Play Store and Apple App Store with minimal manual work.

### What we will build

| Area                   | Outcome                                         |
| ---------------------- | ----------------------------------------------- |
| GitHub Actions         | Automated Flutter build and test pipeline       |
| Backend deployment     | Laravel production deployment workflow          |
| Environment management | Development, staging, and production separation |
| Signing                | Android keystore and iOS signing readiness      |
| Release artifacts      | APK, AAB, and iOS archive generation            |
| API switching          | Automatic backend URL selection                 |
| Monitoring             | Crash reporting and performance tracking        |
| Launch checklist       | Production-ready verification                   |

### 14.16.1 Environment Architecture

Keep the same environment strategy we designed for Laravel.

### Flutter environments

```
lib/core/config/
├── env.dart
├── env_dev.dart
├── env_staging.dart
└── env_production.dart
```

### Example

dart

```
abstract class Env {
  String get apiBaseUrl;
  bool get enableLogging;
}

class ProductionEnv implements Env {
  @override
  String get apiBaseUrl => 'https://api.youstayon.com/api';

  @override
  bool get enableLogging => false;
}
```

### Build command

Bash

```
flutter build appbundle \
  --dart-define=ENV=production
```

This mirrors Laravel's `.env.production`.

### 14.16.2 GitHub Actions for Flutter

Create:

```
.github/workflows/flutter.yml
```

### Pipeline

YAML

```
name: Flutter CI

on:
  push:
    branches: [main]
  pull_request:

jobs:
  flutter:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v4

      - uses: subosito/flutter-action@v2
        with:
          flutter-version: '3.44.0'

      - run: cd mobile && flutter pub get
      - run: cd mobile && flutter analyze
      - run: cd mobile && flutter test
      - run: cd mobile && flutter build apk --debug
```

Every commit now validates the Flutter codebase automatically.

### 14.16.3 Backend Deployment Workflow

Create:

```
.github/workflows/backend.yml
```

### Deployment pipeline

YAML

```
name: Laravel CI

on:
  push:
    branches: [main]

jobs:
  laravel:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v4

      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'

      - run: cd backend && composer install
      - run: cd backend && php artisan test
```

Because your backend already passes 226 tests, this becomes a powerful production safety net.

### 14.16.4 Android Release Signing

Generate a keystore.

Bash

```
keytool -genkeypair \
  -v \
  -keystore youstayon-release.jks \
  -keyalg RSA \
  -keysize 2048 \
  -validity 10000 \
  -alias youstayon
```

Store:

```
android/app/keystore.properties
```

Never commit the keystore to Git.

Use GitHub Secrets instead.

### 14.16.5 iOS Signing Preparation

Configure:

* Bundle Identifier

* Team ID

* Provisioning profile

* Push Notification capability

* Background Modes

* Associated Domains (for deep links)

### 14.16.6 API Switching

Use compile-time environment variables.

dart

```
const env = String.fromEnvironment('ENV');

String get apiBaseUrl {
  switch (env) {
    case 'production':
      return 'https://api.youstayon.com/api';
    case 'staging':
      return 'https://staging-api.youstayon.com/api';
    default:
      return 'http://10.0.2.2:8000/api';
  }
}
```

This ensures Flutter never points to localhost in production.

### 14.16.7 Release Builds

### Debug

Bash

```
flutter run
```

### Internal testing APK

Bash

```
flutter build apk --release
```

### Play Store AAB

Bash

```
flutter build appbundle --release
```

### iOS archive

Bash

```
flutter build ipa --release
```

### 14.16.8 Monitoring Integration

Add:

* Firebase Crashlytics

* Firebase Analytics

* Performance Monitoring

Track:

* login failures

* checkout failures

* wallet funding errors

* subscription renewals

* radar interactions

* app startup time

* network latency

### 14.16.9 Production Security Checklist

### Flutter

* Obfuscation enabled

* Split debug symbols

* No API keys in source

* HTTPS only

* Certificate pinning ready

* Secure storage only

### Laravel

* APP_DEBUG=false

* HTTPS enforced

* Sanctum secure cookies

* Rate limiting enabled

* Queue workers running

* Scheduler running

* Database backups configured

### 14.16.10 End-to-End Validation

Run through the complete user journey.

### Authentication

* Register

* Verify email

* Login

* Restore session

* Logout

### Dashboard

* Overview

* Snapshot

* Radar score

* Analytics

### Wallet

* Fund wallet

* Verify payment

* View transaction

### Subscription

* Create

* Track usage

* Predict depletion

* Renew

* Cancel

### Purchases

* Airtime

* Data

* Electricity

* Cable

* Wallet checkout

* Pay Now checkout

### Notifications

* Radar alert

* Renewal reminder

* Push notification

* Deep link

### Settings

* Edit profile

* Upload avatar

* Change password

* Quiet hours

* Delete account

Everything should match the Laravel backend behavior exactly.

### 14.16.11 Release Candidate

Create:

```
v1.0.0-rc1
```

Tag:

Bash

```
git tag v1.0.0-rc1
git push origin v1.0.0-rc1
```

This becomes the build distributed to internal testers.

### 14.16.12 Launch Checklist

### Backend

* 226 tests passing

* Production database migrated

* Queue workers active

* Scheduler active

* HTTPS configured

* Monitoring enabled

### Flutter

* Release build successful

* APK tested

* AAB generated

* Push notifications working

* Deep links working

* Offline cache verified

* Dark mode verified

* Tablet layout verified

* Performance targets met

### Business

* Privacy policy

* Terms of service

* Support email

* App icons

* Screenshots

* Feature graphic

* Play Store listing

* App Store listing

### Git Commit

Bash

```
git add .
git commit -m "feat(mobile): add production deployment and CI/CD pipeline"
git push origin main
```

At this point, YouStayOn is production-ready: a Laravel backend with a fully mirrored Flutter frontend, premium UX, analytics, notifications, wallet, subscriptions, purchases, and a complete deployment pipeline suitable for real users and store submission.
