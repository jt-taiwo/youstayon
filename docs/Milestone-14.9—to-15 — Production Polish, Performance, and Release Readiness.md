Excellent. We now move to Milestone 14.15 — Production Polish, Performance, and Release Readiness.

This milestone is what separates a functional Flutter app from a product that feels App Store / Play Store quality. The backend is already production-grade (226 tests passing), and the Flutter app now mirrors every Laravel domain: Authentication, Dashboard, Radar, Wallet, Subscriptions, Purchases, Analytics, Settings, and Notifications. Milestone 14.15 focuses on performance, consistency, responsiveness, accessibility, and deployment readiness.

### Milestone 14.15 — Production Polish & Release Readiness

### Objective

Make YouStayOn feel premium, fast, stable, and launch-ready across Android and iOS while preserving 100% compatibility with the Laravel backend.

### What we will build

| Area                     | Outcome                              |
| ------------------------ | ------------------------------------ |
| Navigation polish        | Instant transitions, preserved state |
| Performance optimization | 60fps scrolling and animations       |
| Offline resilience       | Graceful cache-first behavior        |
| Error handling           | Unified failure experience           |
| Accessibility            | Screen-reader and contrast support   |
| Responsive layout        | Phones, tablets, foldables           |
| Theme refinement         | Premium light and dark mode          |
| App lifecycle handling   | Resume, refresh, token validation    |
| Release configuration    | Production-ready builds              |
| QA checklist             | End-to-end validation before launch  |

### 14.15.1 Navigation Performance

We already use GoRouter. Now optimize it.

### Shell Navigation

Keep module state alive while switching tabs.

dart

```
ShellRoute(
  builder: (context, state, child) {
    return AppShell(child: child);
  },
  routes: [
    // dashboard
    // radar
    // wallet
    // subscriptions
    // settings
  ],
)
```

This prevents rebuilding entire screens every tab change.

### Transition Standard

Use a unified transition builder.

dart

```
CustomTransitionPage(
  child: child,
  transitionsBuilder: (context, animation, secondary, child) {
    return FadeTransition(
      opacity: animation,
      child: child,
    );
  },
)
```

Target: 150–220 ms transitions.

### 14.15.2 Performance Optimization

### Image Caching

Use `cached_network_image` everywhere.

* avatars

* provider logos

* subscription icons

* analytics illustrations

### List Performance

Replace large `Column`s with:

dart

```
ListView.builder(
  itemCount: notifications.length,
  itemBuilder: (context, index) {
    return NotificationTile(...);
  },
)
```

### Rebuild Minimization

Use `BlocSelector`.

dart

```
BlocSelector<WalletBloc, WalletState, double>(
  selector: (state) => state.balance,
  builder: (_, balance) => WalletBalanceCard(balance),
)
```

Only balance widgets rebuild.

### Keep-Alive

For dashboard, radar, wallet, subscriptions.

dart

```
class DashboardPage extends StatefulWidget
    with AutomaticKeepAliveClientMixin {

  @override
  bool get wantKeepAlive => true;
}
```

### 14.15.3 Offline Resilience

### Cache Strategy

Hive boxes:

```
dashboard_box
radar_box
wallet_box
subscription_box
notification_box
settings_box
```

### Flow

```
Open screen
     |
     +--> Show cached data immediately
     |
     +--> Refresh from API
     |
     +--> Update cache
     |
     +--> Update UI
```

### Connectivity Recovery

When connectivity returns:

* sync wallet

* refresh dashboard

* refresh notifications

* refresh radar feed

* validate Sanctum token

### 14.15.4 Global Error Handling

Create:

```
core/errors/
├── app_failure.dart
├── error_mapper.dart
├── network_failure.dart
├── auth_failure.dart
└── server_failure.dart
```

### Dio Interceptor

Map backend responses.

| Status      | Flutter Failure       |
| ----------- | --------------------- |
| 401         | SessionExpiredFailure |
| 403         | PermissionFailure     |
| 404         | NotFoundFailure       |
| 422         | ValidationFailure     |
| 500         | ServerFailure         |
| No internet | NetworkFailure        |

Display consistent UI:

* retry button

* offline message

* session expired dialog

* maintenance screen

### 14.15.5 Accessibility

### Text Scaling

Support up to 200% text size.

Avoid fixed heights.

### Semantic Labels

dart

```
Semantics(
  label: 'Radar score',
  value: '$score out of 100',
  child: RadarScoreGauge(score),
)
```

### Contrast

Verify WCAG AA.

* dark backgrounds

* success/warning colors

* chart colors

* badges

### Touch Targets

Minimum 48x48 dp.

### 14.15.6 Responsive Layout

Create:

```
core/responsive/
├── responsive_layout.dart
├── breakpoint.dart
└── adaptive_spacing.dart
```

Breakpoints:

| Width    | Layout       |
| -------- | ------------ |
| <600     | Phone        |
| 600–840  | Small tablet |
| 840–1200 | Tablet       |
| 1200     | Desktop/web  |

Dashboard should automatically become 2-column / 3-column on tablets.

### 14.15.7 Theme Refinement

### Light Theme

* White surfaces

* Soft gray backgrounds

* Blue accent

* Green success

* Orange warning

* Red critical

### Dark Theme

* Near-black background

* Elevated cards

* Vibrant accent colors

* Reduced eye strain

Use Material 3 dynamic color tokens while preserving YouStayOn branding.

### 14.15.8 Motion System

Standard animations:

| Element           | Duration |
| ----------------- | -------- |
| Page transition   | 180 ms   |
| Card fade         | 160 ms   |
| Button press      | 80 ms    |
| Pull-to-refresh   | Native   |
| Metric counter    | 400 ms   |
| Radar score gauge | 600 ms   |
| Unread badge      | 120 ms   |

Use `AnimatedSwitcher`, `AnimatedOpacity`, and `TweenAnimationBuilder`.

### 14.15.9 App Lifecycle

Implement observer.

dart

```
class AppLifecycleHandler extends WidgetsBindingObserver {

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      // validate token
      // refresh notifications
      // refresh dashboard snapshot
    }
  }
}
```

This keeps data fresh after returning to the app.

### 14.15.10 Release Configuration

### Android

Update:

```
android/app/build.gradle
```

Configure:

* applicationId

* versionCode

* versionName

* minSdk

* ProGuard

* R8 shrinking

* release signing

### iOS

Configure:

* Bundle ID

* Team

* Provisioning

* Push capability

* Background fetch

* Notification capability

### 14.15.11 Environment Management

Use flavors.

```
dev
staging
production
```

Example:

dart

```
class Environment {

  static const baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://10.0.2.2:8000/api',
  );
}
```

Production build:

Bash

```
flutter build apk \
  --dart-define=API_BASE_URL=https://api.youstayon.com/api
```

### 14.15.12 Crash Reporting

Integrate:

* Firebase Crashlytics

* FlutterError.onError

* BlocObserver logging

* Dio request logging (disabled in production)

### 14.15.13 Security Hardening

* Secure token storage only

* No tokens in logs

* Certificate pinning ready

* Obfuscation enabled

* Screenshot protection for sensitive screens (optional)

* Root/jailbreak detection (optional future enhancement)

### 14.15.14 Launch Checklist

### Authentication

* Register

* Login

* Logout

* Session restore

* Token expiry

* Password change

### Dashboard

* Overview

* Snapshot

* Radar score

* Recent activity

* Spending analytics

* Usage trends

### Radar

* Feed

* Predictions

* Recommendations

* Deep links

### Wallet

* Balance

* Transactions

* Funding

* Checkout integration

### Subscriptions

* Create

* Edit

* Renew

* Cancel

* Usage

* History

* Prediction

### Purchases

* Airtime

* Data

* Electricity

* Cable

* Wallet payment

* Pay Now

* Receipts

### Analytics

* Commerce overview

* Founder dashboard

* Provider performance

* Service performance

* Payment conversion

### Notifications

* List

* Unread count

* Mark read

* Mark all

* Push

* Local

* Deep linking

### Settings

* Profile

* Avatar

* Password

* Preferences

* Quiet hours

* Account deletion

### Performance Targets

| Metric                 | Target  |
| ---------------------- | ------- |
| Cold launch            | <2.5 s  |
| Warm launch            | <1 s    |
| Dashboard render       | <400 ms |
| Navigation             | <200 ms |
| API response UI update | <100 ms |
| Scroll FPS             | 60 fps  |
| Memory footprint       | <180 MB |
| APK size               | <40 MB  |

### Git Commit

After implementing this milestone:

Bash

```
git add .
git commit -m "feat(mobile): complete production polish and release readiness"
git push origin main
```

At that point, the Flutter frontend will be fully aligned with the Laravel backend, feature-for-feature and endpoint-for-endpoint, with premium UX, offline resilience, notification infrastructure, analytics dashboards, and production deployment readiness suitable for a real YouStayOn launch.
