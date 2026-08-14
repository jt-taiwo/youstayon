Excellent. This is the milestone where the Flutter app becomes a real business operating system, not just a consumer utility app. Everything we are about to build maps directly to the Laravel analytics services and DTOs we completed during Milestone 13, including:

* `GET /analytics/commerce-overview`

* `GET /analytics/providers`

* `GET /analytics/services`

* `GET /analytics/payment-methods`

* `GET /analytics/founder`

* `GET /analytics/dashboard`

* Revenue trends

* Radar analytics

* Founder KPIs

* Dashboard snapshot

Because your backend test suite is now 226 tests passed, we can treat those APIs as production contracts and mirror them 1:1 in Flutter.

### Milestone 14.12 — Analytics & Founder Dashboard Module

Build a premium analytics experience that consumes the Laravel analytics APIs directly and presents provider performance, service performance, payment conversion, commerce KPIs, founder metrics, and dashboard snapshots with fast loading, offline caching, and executive-grade visualizations.

### What we will build

### Executive dashboard

* Total revenue

* Purchase volume

* Success rate

* Active subscriptions

* Radar engagement

* Wallet usage

* Pay Now usage

* Monthly recurring revenue estimate

### Provider analytics

* VTpass / Fake provider performance

* Success vs failure

* Revenue contribution

* Purchase volume

* Conversion rate

### Service analytics

* Airtime

* Data

* Electricity

* Cable TV

* Internet

* Revenue by service

* Purchase frequency

* Success rate

### Payment analytics

* Wallet adoption

* Pay Now adoption

* Funding conversion

* Checkout completion

* Payment distribution

### Revenue trends

* Daily

* Weekly

* Monthly

* Animated charts

* Growth indicators

### Founder dashboard

* Commerce overview

* Payment conversion

* Provider performance

* Service performance

* Radar intelligence metrics

* Snapshot API

* Export-ready architecture

### Flutter architecture (mirrors Laravel)

```
lib/features/analytics/
├── data/
│   ├── datasources/
│   ├── models/
│   └── repositories/
├── domain/
│   ├── entities/
│   ├── repositories/
│   └── usecases/
├── presentation/
│   ├── bloc/
│   ├── pages/
│   ├── widgets/
│   └── charts/
└── analytics_module.dart
```

### Step 14.12.1 — Domain entities

Create:

```
lib/features/analytics/domain/entities/provider_performance_entity.dart
```

dart

```
class ProviderPerformanceEntity {
  final String provider;
  final int totalPurchases;
  final int successfulPurchases;
  final int failedPurchases;
  final double successRate;
  final double purchaseVolume;

  const ProviderPerformanceEntity({
    required this.provider,
    required this.totalPurchases,
    required this.successfulPurchases,
    required this.failedPurchases,
    required this.successRate,
    required this.purchaseVolume,
  });
}
```

Create:

```
lib/features/analytics/domain/entities/service_performance_entity.dart
```

dart

```
class ServicePerformanceEntity {
  final String serviceType;
  final int totalPurchases;
  final int successfulPurchases;
  final int failedPurchases;
  final double successRate;
  final double purchaseVolume;

  const ServicePerformanceEntity({
    required this.serviceType,
    required this.totalPurchases,
    required this.successfulPurchases,
    required this.failedPurchases,
    required this.successRate,
    required this.purchaseVolume,
  });
}
```

Create:

```
lib/features/analytics/domain/entities/payment_method_conversion_entity.dart
```

dart

```
class PaymentMethodConversionEntity {
  final int walletPurchases;
  final int payNowPurchases;
  final double walletPercentage;
  final double payNowPercentage;

  const PaymentMethodConversionEntity({
    required this.walletPurchases,
    required this.payNowPurchases,
    required this.walletPercentage,
    required this.payNowPercentage,
  });
}
```

Create:

```
lib/features/analytics/domain/entities/commerce_overview_entity.dart
```

dart

```
class CommerceOverviewEntity {
  final double totalRevenue;
  final int totalPurchases;
  final int successfulPurchases;
  final int failedPurchases;
  final double successRate;

  const CommerceOverviewEntity({
    required this.totalRevenue,
    required this.totalPurchases,
    required this.successfulPurchases,
    required this.failedPurchases,
    required this.successRate,
  });
}
```

Create:

```
lib/features/analytics/domain/entities/founder_dashboard_entity.dart
```

dart

```
class FounderDashboardEntity {
  final CommerceOverviewEntity overview;
  final PaymentMethodConversionEntity paymentMethods;
  final List<ServicePerformanceEntity> services;
  final List<ProviderPerformanceEntity> providers;

  const FounderDashboardEntity({
    required this.overview,
    required this.paymentMethods,
    required this.services,
    required this.providers,
  });
}
```

This matches the final Laravel DTO architecture we stabilized.

### Step 14.12.2 — Repository interface

Create:

```
lib/features/analytics/domain/repositories/analytics_repository.dart
```

dart

```
import '../entities/commerce_overview_entity.dart';
import '../entities/founder_dashboard_entity.dart';
import '../entities/payment_method_conversion_entity.dart';
import '../entities/provider_performance_entity.dart';
import '../entities/service_performance_entity.dart';

abstract class AnalyticsRepository {
  Future<CommerceOverviewEntity> getCommerceOverview();
  Future<List<ProviderPerformanceEntity>> getProviderPerformance();
  Future<List<ServicePerformanceEntity>> getServicePerformance();
  Future<PaymentMethodConversionEntity> getPaymentMethodConversion();
  Future<FounderDashboardEntity> getFounderDashboard();
  Future<FounderDashboardEntity> getDashboardSnapshot();
}
```

### Step 14.12.3 — Remote datasource

Create:

```
lib/features/analytics/data/datasources/analytics_remote_data_source.dart
```

dart

```
import '../../../../core/network/api_client.dart';

class AnalyticsRemoteDataSource {
  final ApiClient api;

  AnalyticsRemoteDataSource(this.api);

  Future<dynamic> commerceOverview() async =>
      (await api.get('/analytics/commerce-overview')).data['data'];

  Future<dynamic> providers() async =>
      (await api.get('/analytics/providers')).data['data'];

  Future<dynamic> services() async =>
      (await api.get('/analytics/services')).data['data'];

  Future<dynamic> paymentMethods() async =>
      (await api.get('/analytics/payment-methods')).data['data'];

  Future<dynamic> founder() async =>
      (await api.get('/analytics/founder')).data['data'];

  Future<dynamic> dashboard() async =>
      (await api.get('/analytics/dashboard')).data['data'];
}
```

Notice that the endpoint names are identical to Laravel.

### Step 14.12.4 — Repository implementation

Create:

```
lib/features/analytics/data/repositories/analytics_repository_impl.dart
```

Map each JSON payload directly into the corresponding entity.

Example:

dart

```
@override
Future<CommerceOverviewEntity> getCommerceOverview() async {
  final json = await remote.commerceOverview();

  return CommerceOverviewEntity(
    totalRevenue: (json['total_revenue'] as num).toDouble(),
    totalPurchases: json['total_purchases'],
    successfulPurchases: json['successful_purchases'],
    failedPurchases: json['failed_purchases'],
    successRate: (json['success_rate'] as num).toDouble(),
  );
}
```

Do the same for providers, services, payment methods, founder dashboard, and dashboard snapshot.

### Step 14.12.5 — Analytics BLoC

Create:

```
lib/features/analytics/presentation/bloc/analytics_bloc.dart
```

Events:

* `LoadAnalytics`

* `RefreshAnalytics`

State:

dart

```
class AnalyticsState {
  final bool loading;
  final FounderDashboardEntity? dashboard;
  final String? error;

  const AnalyticsState({
    this.loading = false,
    this.dashboard,
    this.error,
  });
}
```

This becomes the single analytics source of truth.

### Step 14.12.6 — Premium Founder Dashboard UI

Create:

```
lib/features/analytics/presentation/pages/founder_dashboard_page.dart
```

Layout:

### Founder dashboard

Revenue

### ₦245,000

+12%

Success rate

### 98.3%

+1.2%

### Payment methods

Wallet

68%

Pay Now

32%

### Top providers

Fake / VTpass

98.3%

120 purchases • ₦245,000 volume

### Step 14.12.7 — Analytics cards

Create reusable widgets:

```
lib/features/analytics/presentation/widgets/metric_card.dart
lib/features/analytics/presentation/widgets/provider_card.dart
lib/features/analytics/presentation/widgets/service_card.dart
lib/features/analytics/presentation/widgets/conversion_card.dart
```

These widgets should use:

* Glassmorphism

* Soft shadows

* Animated number transitions

* Skeleton loading

* Responsive layouts

* Dark mode

### Step 14.12.8 — Charts

Add a chart package:

YAML

```
dependencies:
  fl_chart: ^0.69.0
```

Run:

Bash

```
flutter pub get
```

Create:

```
lib/features/analytics/presentation/charts/revenue_chart.dart
```

Display:

* Daily revenue

* Weekly revenue

* Monthly revenue

* Animated line chart

* Touch tooltips

* Gradient fill

* Premium styling

This connects directly to the revenue trend service we built in Laravel.

### Step 14.12.9 — Dashboard snapshot integration

The dashboard snapshot should load:

* Commerce overview

* Radar score

* Recent activity

* Payment conversion

* Provider performance

* Service performance

in a single network request using:

```
GET /analytics/dashboard
```

This is exactly why we built the Laravel dashboard snapshot service.

### Step 14.12.10 — Navigation

Add route:

dart

```
GoRoute(
  path: '/analytics',
  builder: (context, state) => const FounderDashboardPage(),
),
```

Add a navigation entry from the dashboard:

* Dashboard

* Radar

* Wallet

* Subscriptions

* Purchases

* Analytics

* Notifications

### Step 14.12.11 — Offline caching

Cache:

* Commerce overview

* Founder dashboard

* Provider performance

* Service performance

using Hive.

Strategy:

* Load cache instantly

* Fetch fresh data in background

* Update UI when network completes

This gives the ultra-fast startup experience we want.

### Step 14.12.12 — Backend parity checklist

This Flutter implementation mirrors the Laravel backend 100%.

| Laravel analytics module | Flutter implementation   |
| ------------------------ | ------------------------ |
| Commerce overview        | Overview cards           |
| Provider performance     | Provider analytics       |
| Service performance      | Service analytics        |
| Payment conversion       | Wallet vs Pay Now charts |
| Founder dashboard        | Executive dashboard      |
| Dashboard snapshot       | Single-request dashboard |
| Revenue trends           | Line charts              |
| Radar analytics          | Intelligence metrics     |
| Success rates            | KPI indicators           |
| Purchase volume          | Business metrics         |

### Git commit

After implementation:

Bash

```
git add .
git commit -m "feat(mobile): implement analytics and founder dashboard module"
git push origin main
```

Once this milestone is complete, we move to Milestone 14.13 — Settings, Profile & Account Management, where we will implement profile editing, avatar upload, password change, notification preferences, quiet hours, security settings, account deletion, and complete user preference synchronization directly against the Laravel APIs that already passed your backend test suite.
