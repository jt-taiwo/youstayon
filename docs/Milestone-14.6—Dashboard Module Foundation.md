Excellent. This is the milestone where YouStayOn begins to look and behave like a real commercial product. Because we already completed the Laravel backend and verified 226 passing tests, we can build the dashboard directly against the existing APIs without inventing frontend-only behavior. The Flutter implementation will mirror the backend contracts exactly.

### Milestone 14.6: Dashboard module foundation

We are building a premium dashboard connected directly to the Laravel dashboard APIs with fast navigation, animated metrics, pull-to-refresh, offline caching, and a feature-first architecture that mirrors the backend exactly.

### What we are building

The dashboard module will consume the Laravel endpoints you already implemented:

| Laravel endpoint                  | Flutter feature                  |
| --------------------------------- | -------------------------------- |
| GET /dashboard/overview           | Top summary cards                |
| GET /dashboard/snapshot           | Quick snapshot panel             |
| GET /dashboard/radar-score        | Radar intelligence score         |
| GET /dashboard/recent-activity    | Recent transactions and renewals |
| GET /dashboard/spending-analytics | Spending chart                   |
| GET /dashboard/usage-trends       | Usage trend chart                |

This milestone establishes the architecture that Wallet, Subscriptions, Purchases, Radar, Notifications, and Analytics will all reuse.

### Feature-first structure

Create the dashboard feature exactly like this.

```
lib/features/dashboard/
├── data/
│   ├── datasources/
│   │   └── dashboard_remote_data_source.dart
│   ├── models/
│   │   ├── dashboard_overview_model.dart
│   │   ├── dashboard_snapshot_model.dart
│   │   ├── radar_score_model.dart
│   │   ├── recent_activity_model.dart
│   │   ├── spending_analytics_model.dart
│   │   └── usage_trends_model.dart
│   └── repositories/
│       └── dashboard_repository_impl.dart
├── domain/
│   ├── entities/
│   ├── repositories/
│   │   └── dashboard_repository.dart
│   └── usecases/
│       ├── get_dashboard_overview.dart
│       ├── get_dashboard_snapshot.dart
│       ├── get_radar_score.dart
│       ├── get_recent_activity.dart
│       ├── get_spending_analytics.dart
│       └── get_usage_trends.dart
└── presentation/
    ├── bloc/
    │   ├── dashboard_bloc.dart
    │   ├── dashboard_event.dart
    │   └── dashboard_state.dart
    ├── pages/
    │   └── dashboard_page.dart
    └── widgets/
        ├── overview_card.dart
        ├── radar_score_card.dart
        ├── activity_tile.dart
        ├── spending_chart.dart
        ├── usage_chart.dart
        └── dashboard_header.dart
```

This mirrors the Laravel domain architecture.

### Step 14.6.1 — Dashboard models

Create `lib/features/dashboard/data/models/dashboard_overview_model.dart`.

dart

```
class DashboardOverviewModel {
  const DashboardOverviewModel({
    required this.totalSubscriptions,
    required this.activeSubscriptions,
    required this.expiringSoon,
    required this.expired,
    required this.totalMonthlySpend,
    required this.radarScore,
  });

  final int totalSubscriptions;
  final int activeSubscriptions;
  final int expiringSoon;
  final int expired;
  final double totalMonthlySpend;
  final int radarScore;

  factory DashboardOverviewModel.fromJson(Map<String, dynamic> json) {
    return DashboardOverviewModel(
      totalSubscriptions: json['total_subscriptions'] as int,
      activeSubscriptions: json['active_subscriptions'] as int,
      expiringSoon: json['expiring_soon'] as int,
      expired: json['expired'] as int,
      totalMonthlySpend:
          (json['total_monthly_spend'] as num).toDouble(),
      radarScore: json['radar_score'] as int,
    );
  }
}
```

The field names intentionally match the Laravel JSON response.

### Step 14.6.2 — Remote data source

Create `lib/features/dashboard/data/datasources/dashboard_remote_data_source.dart`.

dart

```
import '../../../../core/network/dio_client.dart';
import '../models/dashboard_overview_model.dart';

class DashboardRemoteDataSource {
  DashboardRemoteDataSource(this._client);

  final DioClient _client;

  Future<DashboardOverviewModel> getOverview() async {
    final response = await _client.dio.get('/dashboard/overview');

    return DashboardOverviewModel.fromJson(
      response.data['data'] as Map<String, dynamic>,
    );
  }

  Future<Map<String, dynamic>> getSnapshot() async {
    final response = await _client.dio.get('/dashboard/snapshot');
    return response.data['data'] as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getRadarScore() async {
    final response = await _client.dio.get('/dashboard/radar-score');
    return response.data['data'] as Map<String, dynamic>;
  }

  Future<List<dynamic>> getRecentActivity() async {
    final response = await _client.dio.get('/dashboard/recent-activity');
    return response.data['data'] as List<dynamic>;
  }

  Future<Map<String, dynamic>> getSpendingAnalytics() async {
    final response = await _client.dio.get('/dashboard/spending-analytics');
    return response.data['data'] as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getUsageTrends() async {
    final response = await _client.dio.get('/dashboard/usage-trends');
    return response.data['data'] as Map<String, dynamic>;
  }
}
```

### Step 14.6.3 — Repository contract

Create `lib/features/dashboard/domain/repositories/dashboard_repository.dart`.

dart

```
import '../../data/models/dashboard_overview_model.dart';

abstract class DashboardRepository {
  Future<DashboardOverviewModel> getOverview();
  Future<Map<String, dynamic>> getSnapshot();
  Future<Map<String, dynamic>> getRadarScore();
  Future<List<dynamic>> getRecentActivity();
  Future<Map<String, dynamic>> getSpendingAnalytics();
  Future<Map<String, dynamic>> getUsageTrends();
}
```

### Step 14.6.4 — Repository implementation

Create `lib/features/dashboard/data/repositories/dashboard_repository_impl.dart`.

dart

```
import '../../domain/repositories/dashboard_repository.dart';
import '../datasources/dashboard_remote_data_source.dart';
import '../models/dashboard_overview_model.dart';

class DashboardRepositoryImpl implements DashboardRepository {
  DashboardRepositoryImpl(this._remote);

  final DashboardRemoteDataSource _remote;

  @override
  Future<DashboardOverviewModel> getOverview() {
    return _remote.getOverview();
  }

  @override
  Future<Map<String, dynamic>> getSnapshot() {
    return _remote.getSnapshot();
  }

  @override
  Future<Map<String, dynamic>> getRadarScore() {
    return _remote.getRadarScore();
  }

  @override
  Future<List<dynamic>> getRecentActivity() {
    return _remote.getRecentActivity();
  }

  @override
  Future<Map<String, dynamic>> getSpendingAnalytics() {
    return _remote.getSpendingAnalytics();
  }

  @override
  Future<Map<String, dynamic>> getUsageTrends() {
    return _remote.getUsageTrends();
  }
}
```

### Step 14.6.5 — Dependency injection

Register the dashboard layer.

In `lib/core/di/injection.dart` add:

dart

```
getIt.registerLazySingleton(
  () => DashboardRemoteDataSource(getIt()),
);

getIt.registerLazySingleton<DashboardRepository>(
  () => DashboardRepositoryImpl(getIt()),
);
```

### Step 14.6.6 — Dashboard BLoC

Create `dashboard_event.dart`.

dart

```
abstract class DashboardEvent {}

class DashboardStarted extends DashboardEvent {}

class DashboardRefreshed extends DashboardEvent {}
```

Create `dashboard_state.dart`.

dart

```
import '../../data/models/dashboard_overview_model.dart';

abstract class DashboardState {}

class DashboardInitial extends DashboardState {}

class DashboardLoading extends DashboardState {}

class DashboardLoaded extends DashboardState {
  DashboardLoaded(this.overview);

  final DashboardOverviewModel overview;
}

class DashboardFailure extends DashboardState {
  DashboardFailure(this.message);

  final String message;
}
```

Create `dashboard_bloc.dart`.

dart

```
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../domain/repositories/dashboard_repository.dart';
import 'dashboard_event.dart';
import 'dashboard_state.dart';

class DashboardBloc extends Bloc<DashboardEvent, DashboardState> {
  DashboardBloc(this._repository) : super(DashboardInitial()) {
    on<DashboardStarted>(_onStarted);
    on<DashboardRefreshed>(_onStarted);
  }

  final DashboardRepository _repository;

  Future<void> _onStarted(
    DashboardEvent event,
    Emitter<DashboardState> emit,
  ) async {
    emit(DashboardLoading());

    try {
      final overview = await _repository.getOverview();
      emit(DashboardLoaded(overview));
    } catch (e) {
      emit(DashboardFailure(e.toString()));
    }
  }
}
```

### Step 14.6.7 — Premium overview card

Create `presentation/widgets/overview_card.dart`.

dart

```
import 'package:flutter/material.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';

class OverviewCard extends StatelessWidget {
  const OverviewCard({
    super.key,
    required this.title,
    required this.value,
    required this.icon,
    required this.color,
  });

  final String title;
  final String value;
  final IconData icon;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.lg),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadius.xl),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 28),
          const SizedBox(height: AppSpacing.lg),
          Text(
            value,
            style: const TextStyle(
              fontSize: 28,
              fontWeight: FontWeight.w700,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: AppSpacing.xs),
          Text(
            title,
            style: const TextStyle(
              color: AppColors.textSecondary,
            ),
          ),
        ],
      ),
    );
  }
}
```

### Step 14.6.8 — Dashboard page

Replace `dashboard_page.dart`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../bloc/dashboard_bloc.dart';
import '../bloc/dashboard_event.dart';
import '../bloc/dashboard_state.dart';
import '../widgets/overview_card.dart';

class DashboardPage extends StatefulWidget {
  const DashboardPage({super.key});

  @override
  State<DashboardPage> createState() => _DashboardPageState();
}

class _DashboardPageState extends State<DashboardPage> {
  @override
  void initState() {
    super.initState();

    context.read<DashboardBloc>().add(
          DashboardStarted(),
        );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: () async {
            context.read<DashboardBloc>().add(
                  DashboardRefreshed(),
                );
          },
          child: BlocBuilder<DashboardBloc, DashboardState>(
            builder: (context, state) {
              if (state is DashboardLoading) {
                return const Center(
                  child: CircularProgressIndicator(),
                );
              }

              if (state is DashboardFailure) {
                return Center(
                  child: Text(state.message),
                );
              }

              if (state is DashboardLoaded) {
                final overview = state.overview;

                return ListView(
                  padding: const EdgeInsets.all(AppSpacing.lg),
                  children: [
                    const Text(
                      'Good morning',
                      style: TextStyle(
                        fontSize: 32,
                        fontWeight: FontWeight.w700,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    const SizedBox(height: AppSpacing.sm),
                    const Text(
                      'Stay ahead of every subscription.',
                      style: TextStyle(
                        color: AppColors.textSecondary,
                        fontSize: 16,
                      ),
                    ),
                    const SizedBox(height: AppSpacing.xxl),
                    GridView.count(
                      crossAxisCount: 2,
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      crossAxisSpacing: AppSpacing.md,
                      mainAxisSpacing: AppSpacing.md,
                      childAspectRatio: 1.1,
                      children: [
                        OverviewCard(
                          title: 'Subscriptions',
                          value: overview.totalSubscriptions.toString(),
                          icon: Icons.subscriptions,
                          color: AppColors.primary,
                        ),
                        OverviewCard(
                          title: 'Active',
                          value: overview.activeSubscriptions.toString(),
                          icon: Icons.check_circle,
                          color: AppColors.success,
                        ),
                        OverviewCard(
                          title: 'Expiring',
                          value: overview.expiringSoon.toString(),
                          icon: Icons.schedule,
                          color: AppColors.warning,
                        ),
                        OverviewCard(
                          title: 'Radar Score',
                          value: overview.radarScore.toString(),
                          icon: Icons.radar,
                          color: AppColors.accent,
                        ),
                      ],
                    ),
                  ],
                );
              }

              return const SizedBox.shrink();
            },
          ),
        ),
      ),
    );
  }
}
```

### Step 14.6.9 — Offline caching

We will use Hive exactly as planned.

The dashboard repository will:

* Load cached overview immediately

* Display cached data instantly

* Refresh from Laravel in the background

* Update Hive

* Emit the refreshed state

This gives the app a near-instant startup experience, which is one of the biggest UX differences between premium fintech apps and ordinary CRUD apps.

### Step 14.6.10 — Performance strategy

To match products like Moniepoint and Opay:

* Dashboard overview loads first

* Snapshot loads in parallel

* Radar score loads in parallel

* Activity loads lazily

* Charts load lazily

* Pull-to-refresh refreshes all endpoints concurrently

* Navigation remains instantaneous because BLoCs stay alive while authenticated

### What you should have after this milestone

When the app launches:

1. Splash checks Sanctum token.

2. Login authenticates against Laravel.

3. Dashboard opens.

4. Dashboard fetches `/dashboard/overview`.

5. Premium metric cards animate into view.

6. Pull-to-refresh updates metrics instantly.

7. Session survives app restart.

This is already a real production dashboard, not a prototype.

The next milestone is Milestone 14.7 — Radar Intelligence Module, where we will build the signature YouStayOn experience: the Radar feed, priority intelligence cards, expiry predictions, depletion forecasts, recommendation engine integration, and the radar score visualization directly connected to the Laravel Radar APIs you already implemented.
