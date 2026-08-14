Excellent. Based on our roadmap, the next milestone is Milestone 14.11 — Notifications & Intelligence Module. This is the right place to build the feature that differentiates YouStayOn from a generic VTU app: proactive subscription intelligence.

### Milestone 14.11 — Notifications & Intelligence Module

Build the complete notification center, unread counters, read actions, radar-triggered intelligence cards, and intelligent recommendation experience directly connected to the Laravel notification and intelligence APIs you already completed and tested.

This milestone connects Flutter to all of the backend endpoints that already passed your Laravel test suite:

| Backend endpoint                | Flutter feature            |
| ------------------------------- | -------------------------- |
| GET /notifications              | Notification Center        |
| GET /notifications/unread-count | Badge counter              |
| PATCH /notifications/{id}/read  | Mark notification read     |
| PATCH /notifications/read-all   | Mark all read              |
| GET /intelligence/overview      | Intelligence dashboard     |
| Radar recommendation services   | Smart recommendation cards |

The goal is that the Flutter app becomes proactive, not reactive: users should be warned before data expires, before subscriptions renew, and before usage depletion occurs.

### What we will build

### Notification Center

* Real-time unread badge

* Premium notification list

* Priority coloring (critical, warning, info)

* Mark as read

* Mark all as read

* Pull-to-refresh

* Offline caching

* Infinite scroll ready

### Intelligence Dashboard

* Radar intelligence overview

* Recommendation engine cards

* Expiry alerts

* Depletion forecasts

* Cost-saving recommendations

* Cheapest equivalent plan suggestions

* Subscription conflict alerts

* Auto-renew recommendations

### Premium UX

* Glassmorphism notification cards

* Animated priority indicators

* Swipe gestures

* Haptic feedback hooks

* Empty-state illustrations

* Dark mode support

* Accessibility support

### Flutter architecture (mirrors Laravel)

```
lib/features/notifications/
├── data/
│   ├── datasources/
│   ├── models/
│   ├── repositories/
│   └── services/
├── domain/
│   ├── entities/
│   ├── repositories/
│   └── usecases/
├── presentation/
│   ├── bloc/
│   ├── pages/
│   └── widgets/
└── notifications_module.dart

lib/features/intelligence/
├── data/
├── domain/
├── presentation/
└── intelligence_module.dart
```

### Step 14.11.1 — Notification domain entities

Create:

```
lib/features/notifications/domain/entities/notification_entity.dart
```

dart

```
class NotificationEntity {
  final String id;
  final String title;
  final String body;
  final String type;
  final String priority;
  final bool read;
  final DateTime createdAt;

  const NotificationEntity({
    required this.id,
    required this.title,
    required this.body,
    required this.type,
    required this.priority,
    required this.read,
    required this.createdAt,
  });
}
```

Create:

```
lib/features/intelligence/domain/entities/intelligence_overview_entity.dart
```

dart

```
class IntelligenceOverviewEntity {
  final int critical;
  final int warning;
  final int healthy;
  final int expired;
  final int exhausted;

  const IntelligenceOverviewEntity({
    required this.critical,
    required this.warning,
    required this.healthy,
    required this.expired,
    required this.exhausted,
  });
}
```

### Step 14.11.2 — Repository interfaces

Create:

```
lib/features/notifications/domain/repositories/notification_repository.dart
```

dart

```
import '../entities/notification_entity.dart';

abstract class NotificationRepository {
  Future<List<NotificationEntity>> getNotifications();
  Future<int> getUnreadCount();
  Future<void> markAsRead(String id);
  Future<void> markAllAsRead();
}
```

Create:

```
lib/features/intelligence/domain/repositories/intelligence_repository.dart
```

dart

```
import '../entities/intelligence_overview_entity.dart';

abstract class IntelligenceRepository {
  Future<IntelligenceOverviewEntity> getOverview();
}
```

### Step 14.11.3 — API datasource

Create:

```
lib/features/notifications/data/datasources/notification_remote_data_source.dart
```

dart

```
import '../../../../core/network/api_client.dart';

class NotificationRemoteDataSource {
  final ApiClient api;

  NotificationRemoteDataSource(this.api);

  Future<dynamic> getNotifications() async {
    final response = await api.get('/notifications');
    return response.data['data'];
  }

  Future<dynamic> getUnreadCount() async {
    final response = await api.get('/notifications/unread-count');
    return response.data['data'];
  }

  Future<void> markAsRead(String id) async {
    await api.patch('/notifications/$id/read');
  }

  Future<void> markAllAsRead() async {
    await api.patch('/notifications/read-all');
  }
}
```

Create:

```
lib/features/intelligence/data/datasources/intelligence_remote_data_source.dart
```

dart

```
import '../../../../core/network/api_client.dart';

class IntelligenceRemoteDataSource {
  final ApiClient api;

  IntelligenceRemoteDataSource(this.api);

  Future<dynamic> getOverview() async {
    final response = await api.get('/intelligence/overview');
    return response.data['data'];
  }
}
```

### Step 14.11.4 — Repository implementations

Create:

```
lib/features/notifications/data/repositories/notification_repository_impl.dart
```

Map the Laravel JSON directly into `NotificationEntity`.

Create:

```
lib/features/intelligence/data/repositories/intelligence_repository_impl.dart
```

Map:

JSON

```
{
  "critical": 2,
  "warning": 4,
  "healthy": 6,
  "expired": 1,
  "exhausted": 0
}
```

into `IntelligenceOverviewEntity`.

### Step 14.11.5 — Notification BLoC

Create:

```
lib/features/notifications/presentation/bloc/notification_bloc.dart
```

Events:

* `LoadNotifications`

* `RefreshNotifications`

* `MarkNotificationRead`

* `MarkAllNotificationsRead`

State:

dart

```
class NotificationState {
  final bool loading;
  final List<NotificationEntity> notifications;
  final int unreadCount;
  final String? error;

  const NotificationState({
    this.loading = false,
    this.notifications = const [],
    this.unreadCount = 0,
    this.error,
  });

  NotificationState copyWith({
    bool? loading,
    List<NotificationEntity>? notifications,
    int? unreadCount,
    String? error,
  }) {
    return NotificationState(
      loading: loading ?? this.loading,
      notifications: notifications ?? this.notifications,
      unreadCount: unreadCount ?? this.unreadCount,
      error: error,
    );
  }
}
```

This mirrors the backend notification services exactly.

### Step 14.11.6 — Intelligence BLoC

Create:

```
lib/features/intelligence/presentation/bloc/intelligence_bloc.dart
```

State:

dart

```
class IntelligenceState {
  final bool loading;
  final IntelligenceOverviewEntity? overview;
  final String? error;

  const IntelligenceState({
    this.loading = false,
    this.overview,
    this.error,
  });
}
```

### Step 14.11.7 — Premium Notification Center UI

Create:

```
lib/features/notifications/presentation/pages/notifications_page.dart
```

Layout:

### Notifications

Today

Critical

Data expires in 2 hours

Your MTN 5GB subscription will expire soon.

Warning

Electricity token running low

Estimated depletion tomorrow.

Mark all as read

Each card should support:

* Swipe to mark read

* Tap to open related subscription

* Animated unread indicator

* Relative time formatting using `intl`

### Step 14.11.8 — Intelligence Dashboard UI

Create:

```
lib/features/intelligence/presentation/pages/intelligence_page.dart
```

Layout:

### Radar intelligence

### 2

Critical

### 4

Warning

### Recommended actions

Renew MTN 5GB Plan

Expires in 2 hours

Switch to cheaper equivalent plan

Save ₦500 monthly

This connects directly to the Laravel recommendation engine you already implemented.

### Step 14.11.9 — Dashboard integration

Update the bottom navigation:

| Tab           | Route          |
| ------------- | -------------- |
| Dashboard     | /dashboard     |
| Radar         | /radar         |
| Wallet        | /wallet        |
| Subscriptions | /subscriptions |
| Notifications | /notifications |

Add the unread badge:

dart

```
Badge(
  label: Text(unreadCount.toString()),
  child: const Icon(Icons.notifications_outlined),
)
```

This should refresh automatically whenever the notification BLoC updates.

### Step 14.11.10 — Dependency injection

Register:

* `NotificationRemoteDataSource`

* `NotificationRepositoryImpl`

* `NotificationRepository`

* `NotificationBloc`

* `IntelligenceRemoteDataSource`

* `IntelligenceRepositoryImpl`

* `IntelligenceRepository`

* `IntelligenceBloc`

Then run:

Bash

```
flutter pub run build_runner build --delete-conflicting-outputs
```

### Step 14.11.11 — Navigation wiring

Add routes:

dart

```
GoRoute(
  path: '/notifications',
  builder: (context, state) => const NotificationsPage(),
),

GoRoute(
  path: '/intelligence',
  builder: (context, state) => const IntelligencePage(),
),
```

### Step 14.11.12 — Backend parity checklist

This milestone mirrors the Laravel backend 100%:

| Laravel feature                 | Flutter implementation |
| ------------------------------- | ---------------------- |
| Notification listing            | Notification Center    |
| Unread count                    | Badge counter          |
| Mark read                       | Swipe/tap actions      |
| Mark all read                   | Bulk action            |
| Intelligence overview           | Radar dashboard        |
| Recommendation engine           | Recommendation cards   |
| Expiry prediction               | Priority alerts        |
| Depletion forecast              | Usage warnings         |
| Cheapest plan suggestion        | Cost-saving cards      |
| Subscription conflict detection | Conflict alerts        |
| Auto-renew suggestions          | Renewal prompts        |

### Git commit

After implementing the module:

Bash

```
git add .
git commit -m "feat(mobile): implement notifications and intelligence module"
git push origin main
```

Once this is complete, we proceed to Milestone 14.12 — Analytics & Founder Dashboard Module, where Flutter will consume the provider performance, service performance, payment conversion, commerce overview, founder dashboard, and dashboard snapshot APIs you already completed on the Laravel backend.
