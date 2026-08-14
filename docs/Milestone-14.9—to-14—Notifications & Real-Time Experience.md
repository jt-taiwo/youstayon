Perfect. This is the right place to continue. I’ve tracked the backend architecture we completed (226 passing tests, Laravel 12 + MySQL clean architecture, Sanctum auth, Wallet, Purchases, Subscriptions, Radar Intelligence, Notifications, Analytics, Founder Dashboard, and the provider abstraction layer) and the Flutter milestones we’ve already scaffolded through Milestone 14.13. We can now implement Milestone 14.14 — Notifications & Real-Time Experience in a way that remains 100% aligned with the Laravel backend, including the notification engine, unread counts, scheduler-driven reminders, and future push delivery.

### Milestone 14.14 — Notifications & Real-Time Experience

### Goal

Transform YouStayOn from a static utility tracker into a real-time intelligence platform. The Flutter app should stay synchronized with the Laravel backend and surface notifications instantly across the entire app.

### Backend APIs already available

These are already implemented and tested on the Laravel side.

| Feature                      | Endpoint                        |
| ---------------------------- | ------------------------------- |
| List notifications           | GET /notifications              |
| Unread count                 | GET /notifications/unread-count |
| Mark notification read       | POST /notifications/{id}/read   |
| Mark all read                | POST /notifications/read-all    |
| Generate radar notifications | Scheduler / command             |
| Notification preferences     | User settings module            |

The Flutter implementation will consume these directly without changing backend behavior.

### Architecture

We keep the same feature-first clean architecture used across Milestones 14.1–14.13.

```
lib/features/notifications/
├── data/
│   ├── datasources/
│   │   └── notification_remote_data_source.dart
│   ├── models/
│   │   └── notification_model.dart
│   └── repositories/
│       └── notification_repository_impl.dart
├── domain/
│   ├── entities/
│   │   └── app_notification.dart
│   ├── repositories/
│   │   └── notification_repository.dart
│   └── usecases/
│       ├── get_notifications.dart
│       ├── get_unread_count.dart
│       ├── mark_notification_read.dart
│       └── mark_all_notifications_read.dart
└── presentation/
    ├── bloc/
    │   ├── notification_bloc.dart
    │   ├── notification_event.dart
    │   └── notification_state.dart
    ├── pages/
    │   └── notification_center_page.dart
    └── widgets/
        ├── notification_tile.dart
        ├── unread_badge.dart
        └── radar_alert_card.dart
```

### Premium UX

### Notification Center

A dedicated screen accessible from the dashboard bell icon.

Sections:

* Unread

* Today

* Earlier

* Radar Intelligence

* Renewal Reminders

* Wallet

* Purchases

* System

Each notification card includes:

* priority color stripe

* icon

* title

* message

* relative timestamp

* deep-link target

* read indicator

* swipe actions

### Dashboard Badge

The dashboard bell icon shows a live unread badge.

Examples:

* 1

* 7

* 24

* 99+

This badge updates automatically after:

* polling

* mark-as-read

* mark-all-read

* app resume

* push notification

### Notification Entity

Mirror the Laravel payload exactly.

dart

```
class AppNotification {
  final String id;
  final String type;
  final String title;
  final String message;
  final String priority;
  final bool read;
  final DateTime createdAt;
  final Map<String, dynamic>? payload;
  final String? deepLink;

  const AppNotification({
    required this.id,
    required this.type,
    required this.title,
    required this.message,
    required this.priority,
    required this.read,
    required this.createdAt,
    this.payload,
    this.deepLink,
  });
}
```

This should map directly from the Laravel notification resource.

### BLoC State

dart

```
sealed class NotificationState {}

class NotificationLoading extends NotificationState {}

class NotificationLoaded extends NotificationState {
  final List<AppNotification> notifications;
  final int unreadCount;

  NotificationLoaded({
    required this.notifications,
    required this.unreadCount,
  });
}

class NotificationError extends NotificationState {
  final String message;
  NotificationError(this.message);
}
```

The bloc loads both:

* notification list

* unread count

in parallel.

### Polling Strategy

Until websocket broadcasting is added, use lightweight polling.

### Poll Interval

* App active: 30 seconds

* Dashboard visible: 15 seconds

* Background: paused

* Resume from background: immediate refresh

This keeps backend load minimal while feeling real-time.

Implementation:

dart

```
Timer.periodic(
  const Duration(seconds: 30),
  (_) => context.read<NotificationBloc>().add(
        RefreshNotifications(),
      ),
);
```

### Local Notifications

Use:

* `flutter_local_notifications`

Behavior:

When the app receives a new notification from polling:

* display a local notification

* increment unread badge

* animate the dashboard bell

* insert notification at top of feed

Example:

* “Your MTN data expires in 3 hours”

* “Wallet funding completed”

* “Auto-renew purchase successful”

### Push Notifications

Add Firebase Messaging.

Dependencies:

YAML

```
firebase_core:
firebase_messaging:
flutter_local_notifications:
```

Flow:

```
Laravel Notification Engine
          |
          v
Firebase Cloud Messaging
          |
          v
Flutter App
          |
          +--> Foreground local notification
          +--> Background notification
          +--> Terminated launch
          +--> Deep-link routing
```

### Deep Linking

Each notification should navigate directly into the correct module.

| Notification Type    | Destination                   |
| -------------------- | ----------------------------- |
| subscription_expired | /subscriptions/:id            |
| renewal_due          | /subscriptions/:id/prediction |
| purchase_completed   | /purchases/:id                |
| wallet_funded        | /wallet                       |
| radar_recommendation | /radar                        |
| security_alert       | /settings/security            |

GoRouter should parse notification payloads and route accordingly.

### Background Handling

Register Firebase background handler.

dart

```
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(
  RemoteMessage message,
) async {
  await Firebase.initializeApp();

  // persist notification
  // update badge
}
```

This ensures notifications arrive even when the app is closed.

### Notification Preferences Sync

Connect directly to the backend settings APIs.

Supported toggles:

* Radar alerts

* Renewal reminders

* Purchase confirmations

* Wallet activity

* Promotional notifications

* Email notifications

* Push notifications

* Quiet hours

Quiet hours should be respected both:

* on Laravel scheduler

* on Flutter local notification display

### Badge Synchronization

Unread count should stay consistent across:

* dashboard bell

* notification center

* app icon badge

* background notifications

* multiple devices

Sequence:

```
Backend unread count
       |
       v
Flutter sync
       |
       +--> Dashboard badge
       +--> Notification page
       +--> App icon badge
       +--> Local notification counter
```

### Premium Animations

Use subtle motion.

* Bell shake on new notification

* Badge scale animation

* Card fade-in

* Swipe-to-read

* Pull-to-refresh

* Staggered notification appearance

Keep transitions under 250 ms for perceived speed.

### Offline Behavior

Hive cache:

* last notification list

* unread count

* last sync timestamp

When offline:

* display cached notifications

* show “Offline” banner

* queue read actions

* sync automatically when connectivity returns

### Performance Targets

* Notification center open: <150 ms

* Badge update: instant

* Poll request: <50 KB

* Cached load: <30 ms

* Push-to-screen navigation: <500 ms

### Integration Checklist

* Notification entity

* Remote datasource

* Repository

* Use cases

* NotificationBloc

* Notification center page

* Dashboard unread badge

* Polling service

* Local notifications

* Firebase messaging

* Background handler

* Deep linking

* Preference synchronization

* Quiet hours support

* Offline cache

* App icon badge

* Animated UI

* GoRouter integration

After this milestone, YouStayOn becomes a living intelligence product: the backend scheduler generates Radar recommendations and renewal reminders, Laravel stores and prioritizes them, Flutter receives them in real time, displays premium alerts, deep-links users directly into the relevant subscription or purchase flow, and keeps unread counts synchronized across the entire application.
