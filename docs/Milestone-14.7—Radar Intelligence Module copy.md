Excellent. We are now implementing Milestone 14.7 — Radar Intelligence Module, which is the core product differentiator of YouStayOn. This is not just another dashboard screen; it is the feature that turns YouStayOn from a utility payment app into a subscription intelligence platform. Every part of this milestone is built directly on the Laravel Radar APIs you already implemented and tested.

Because the backend already includes:

* Radar Overview

* Radar Feed

* Subscription Prediction

* Renewal Suggestions

* Cheapest Equivalent Recommendation

* Conflict Detection

* Daily Radar Digest

* Radar Score

the Flutter implementation will mirror the backend 100%, with no frontend-only business logic.

### Milestone 14.7: Radar intelligence module

We are building the signature YouStayOn experience: the Radar feed, priority intelligence cards, expiry predictions, depletion forecasts, recommendation engine integration, and radar score visualization directly connected to the Laravel Radar APIs.

### What we are building

| Laravel endpoint                   | Flutter experience                 |
| ---------------------------------- | ---------------------------------- |
| GET /subscriptions/radar/overview  | Radar summary cards                |
| GET /subscriptions/radar           | Priority intelligence feed         |
| GET /subscriptions/{id}/prediction | Expiry and depletion forecast      |
| GET /dashboard/radar-score         | Animated radar score visualization |
| Renewal recommendation services    | Action cards                       |
| Cheapest equivalent recommendation | Cost-saving intelligence           |
| Conflict detection                 | Duplicate subscription warnings    |

This module becomes the home screen of the product after login.

### Feature structure

Create:

```
lib/features/radar/
├── data/
│   ├── datasources/
│   │   └── radar_remote_data_source.dart
│   ├── models/
│   │   ├── radar_overview_model.dart
│   │   ├── radar_feed_item_model.dart
│   │   ├── subscription_prediction_model.dart
│   │   └── radar_score_model.dart
│   └── repositories/
│       └── radar_repository_impl.dart
├── domain/
│   ├── repositories/
│   │   └── radar_repository.dart
│   └── usecases/
│       ├── get_radar_overview.dart
│       ├── get_radar_feed.dart
│       ├── get_subscription_prediction.dart
│       └── get_radar_score.dart
└── presentation/
    ├── bloc/
    │   ├── radar_bloc.dart
    │   ├── radar_event.dart
    │   └── radar_state.dart
    ├── pages/
    │   └── radar_page.dart
    └── widgets/
        ├── radar_score_card.dart
        ├── radar_summary_card.dart
        ├── intelligence_card.dart
        ├── prediction_card.dart
        └── recommendation_chip.dart
```

This mirrors the Laravel module boundaries.

### Step 14.7.1 — Radar overview model

Create `lib/features/radar/data/models/radar_overview_model.dart`.

dart

```
class RadarOverviewModel {
  const RadarOverviewModel({
    required this.critical,
    required this.warning,
    required this.healthy,
    required this.expired,
    required this.nextExpiringName,
    required this.nextExpiringDate,
  });

  final int critical;
  final int warning;
  final int healthy;
  final int expired;
  final String? nextExpiringName;
  final String? nextExpiringDate;

  factory RadarOverviewModel.fromJson(Map<String, dynamic> json) {
    return RadarOverviewModel(
      critical: json['critical'] as int,
      warning: json['warning'] as int,
      healthy: json['healthy'] as int,
      expired: json['expired'] as int,
      nextExpiringName: json['next_expiring_name'] as String?,
      nextExpiringDate: json['next_expiring_date'] as String?,
    );
  }
}
```

These fields should match the Laravel `GetRadarOverview` response exactly.

### Step 14.7.2 — Radar feed model

Create `lib/features/radar/data/models/radar_feed_item_model.dart`.

dart

```
class RadarFeedItemModel {
  const RadarFeedItemModel({
    required this.id,
    required this.subscriptionName,
    required this.priority,
    required this.status,
    required this.message,
    required this.action,
    required this.expiresAt,
  });

  final String id;
  final String subscriptionName;
  final String priority;
  final String status;
  final String message;
  final String action;
  final String expiresAt;

  factory RadarFeedItemModel.fromJson(Map<String, dynamic> json) {
    return RadarFeedItemModel(
      id: json['id'] as String,
      subscriptionName: json['subscription_name'] as String,
      priority: json['priority'] as String,
      status: json['status'] as String,
      message: json['message'] as String,
      action: json['action'] as String,
      expiresAt: json['expires_at'] as String,
    );
  }
}
```

This represents one intelligence item from `/subscriptions/radar`.

### Step 14.7.3 — Radar remote data source

Create `lib/features/radar/data/datasources/radar_remote_data_source.dart`.

dart

```
import '../../../../core/network/dio_client.dart';
import '../models/radar_feed_item_model.dart';
import '../models/radar_overview_model.dart';

class RadarRemoteDataSource {
  RadarRemoteDataSource(this._client);

  final DioClient _client;

  Future<RadarOverviewModel> getOverview() async {
    final response =
        await _client.dio.get('/subscriptions/radar/overview');

    return RadarOverviewModel.fromJson(
      response.data['data'] as Map<String, dynamic>,
    );
  }

  Future<List<RadarFeedItemModel>> getFeed() async {
    final response =
        await _client.dio.get('/subscriptions/radar');

    final list = response.data['data'] as List;

    return list
        .map(
          (e) => RadarFeedItemModel.fromJson(
            e as Map<String, dynamic>,
          ),
        )
        .toList();
  }

  Future<Map<String, dynamic>> getRadarScore() async {
    final response =
        await _client.dio.get('/dashboard/radar-score');

    return response.data['data'] as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getPrediction(
    String id,
  ) async {
    final response = await _client.dio.get(
      '/subscriptions/$id/prediction',
    );

    return response.data['data'] as Map<String, dynamic>;
  }
}
```

Notice that we are consuming the exact Laravel routes already tested.

### Step 14.7.4 — Repository

Create `lib/features/radar/domain/repositories/radar_repository.dart`.

dart

```
import '../../data/models/radar_feed_item_model.dart';
import '../../data/models/radar_overview_model.dart';

abstract class RadarRepository {
  Future<RadarOverviewModel> getOverview();
  Future<List<RadarFeedItemModel>> getFeed();
  Future<Map<String, dynamic>> getRadarScore();
  Future<Map<String, dynamic>> getPrediction(String id);
}
```

Create `radar_repository_impl.dart`.

dart

```
import '../../domain/repositories/radar_repository.dart';
import '../datasources/radar_remote_data_source.dart';
import '../models/radar_feed_item_model.dart';
import '../models/radar_overview_model.dart';

class RadarRepositoryImpl implements RadarRepository {
  RadarRepositoryImpl(this._remote);

  final RadarRemoteDataSource _remote;

  @override
  Future<RadarOverviewModel> getOverview() {
    return _remote.getOverview();
  }

  @override
  Future<List<RadarFeedItemModel>> getFeed() {
    return _remote.getFeed();
  }

  @override
  Future<Map<String, dynamic>> getRadarScore() {
    return _remote.getRadarScore();
  }

  @override
  Future<Map<String, dynamic>> getPrediction(String id) {
    return _remote.getPrediction(id);
  }
}
```

### Step 14.7.5 — Dependency injection

Register in `injection.dart`.

dart

```
getIt.registerLazySingleton(
  () => RadarRemoteDataSource(getIt()),
);

getIt.registerLazySingleton<RadarRepository>(
  () => RadarRepositoryImpl(getIt()),
);
```

### Step 14.7.6 — Radar BLoC

Create `radar_event.dart`.

dart

```
abstract class RadarEvent {}

class RadarStarted extends RadarEvent {}

class RadarRefreshed extends RadarEvent {}
```

Create `radar_state.dart`.

dart

```
import '../../data/models/radar_feed_item_model.dart';
import '../../data/models/radar_overview_model.dart';

abstract class RadarState {}

class RadarInitial extends RadarState {}

class RadarLoading extends RadarState {}

class RadarLoaded extends RadarState {
  RadarLoaded({
    required this.overview,
    required this.feed,
    required this.score,
  });

  final RadarOverviewModel overview;
  final List<RadarFeedItemModel> feed;
  final int score;
}

class RadarFailure extends RadarState {
  RadarFailure(this.message);

  final String message;
}
```

Create `radar_bloc.dart`.

dart

```
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../domain/repositories/radar_repository.dart';
import 'radar_event.dart';
import 'radar_state.dart';

class RadarBloc extends Bloc<RadarEvent, RadarState> {
  RadarBloc(this._repository) : super(RadarInitial()) {
    on<RadarStarted>(_load);
    on<RadarRefreshed>(_load);
  }

  final RadarRepository _repository;

  Future<void> _load(
    RadarEvent event,
    Emitter<RadarState> emit,
  ) async {
    emit(RadarLoading());

    try {
      final overview = await _repository.getOverview();
      final feed = await _repository.getFeed();
      final scoreData = await _repository.getRadarScore();

      emit(
        RadarLoaded(
          overview: overview,
          feed: feed,
          score: scoreData['score'] as int,
        ),
      );
    } catch (e) {
      emit(RadarFailure(e.toString()));
    }
  }
}
```

### Step 14.7.7 — Premium radar score card

Create `presentation/widgets/radar_score_card.dart`.

dart

```
import 'package:flutter/material.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';

class RadarScoreCard extends StatelessWidget {
  const RadarScoreCard({
    super.key,
    required this.score,
  });

  final int score;

  @override
  Widget build(BuildContext context) {
    final progress = score / 100;

    return Container(
      padding: const EdgeInsets.all(AppSpacing.lg),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadius.xl),
      ),
      child: Row(
        children: [
          SizedBox(
            width: 72,
            height: 72,
            child: Stack(
              alignment: Alignment.center,
              children: [
                CircularProgressIndicator(
                  value: progress,
                  strokeWidth: 8,
                  backgroundColor: AppColors.border,
                  color: AppColors.primary,
                ),
                Text(
                  '$score',
                  style: const TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textPrimary,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: AppSpacing.lg),
          const Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Radar Intelligence Score',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w600,
                    color: AppColors.textPrimary,
                  ),
                ),
                SizedBox(height: AppSpacing.xs),
                Text(
                  'Higher scores mean your subscriptions are healthy and well managed.',
                  style: TextStyle(
                    color: AppColors.textSecondary,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
```

This becomes the hero component of the product.

### Step 14.7.8 — Priority intelligence card

Create `presentation/widgets/intelligence_card.dart`.

dart

```
import 'package:flutter/material.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../data/models/radar_feed_item_model.dart';

class IntelligenceCard extends StatelessWidget {
  const IntelligenceCard({
    super.key,
    required this.item,
  });

  final RadarFeedItemModel item;

  @override
  Widget build(BuildContext context) {
    final color = switch (item.priority) {
      'critical' => AppColors.error,
      'warning' => AppColors.warning,
      _ => AppColors.success,
    };

    return Container(
      margin: const EdgeInsets.only(bottom: AppSpacing.md),
      padding: const EdgeInsets.all(AppSpacing.lg),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadius.xl),
        border: Border.all(
          color: color.withValues(alpha: 0.25),
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(Icons.radar, color: color),
              const SizedBox(width: AppSpacing.sm),
              Expanded(
                child: Text(
                  item.subscriptionName,
                  style: const TextStyle(
                    fontWeight: FontWeight.w600,
                    color: AppColors.textPrimary,
                  ),
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: AppSpacing.sm,
                  vertical: AppSpacing.xs,
                ),
                decoration: BoxDecoration(
                  color: color.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(AppRadius.lg),
                ),
                child: Text(
                  item.priority.toUpperCase(),
                  style: TextStyle(
                    color: color,
                    fontSize: 11,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.md),
          Text(
            item.message,
            style: const TextStyle(
              color: AppColors.textSecondary,
            ),
          ),
          const SizedBox(height: AppSpacing.md),
          Row(
            children: [
              const Icon(
                Icons.schedule,
                size: 16,
                color: AppColors.textSecondary,
              ),
              const SizedBox(width: AppSpacing.xs),
              Text(
                item.expiresAt,
                style: const TextStyle(
                  color: AppColors.textSecondary,
                ),
              ),
              const Spacer(),
              TextButton(
                onPressed: () {},
                child: Text(item.action),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
```

This is where the backend recommendation engine becomes visible to the user.

### Step 14.7.9 — Radar page

Create `presentation/pages/radar_page.dart`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../bloc/radar_bloc.dart';
import '../bloc/radar_event.dart';
import '../bloc/radar_state.dart';
import '../widgets/intelligence_card.dart';
import '../widgets/radar_score_card.dart';

class RadarPage extends StatefulWidget {
  const RadarPage({super.key});

  @override
  State<RadarPage> createState() => _RadarPageState();
}

class _RadarPageState extends State<RadarPage> {
  @override
  void initState() {
    super.initState();

    context.read<RadarBloc>().add(
          RadarStarted(),
        );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: () async {
            context.read<RadarBloc>().add(
                  RadarRefreshed(),
                );
          },
          child: BlocBuilder<RadarBloc, RadarState>(
            builder: (context, state) {
              if (state is RadarLoading) {
                return const Center(
                  child: CircularProgressIndicator(),
                );
              }

              if (state is RadarFailure) {
                return Center(
                  child: Text(state.message),
                );
              }

              if (state is RadarLoaded) {
                return ListView(
                  padding: const EdgeInsets.all(AppSpacing.lg),
                  children: [
                    const Text(
                      'Radar Intelligence',
                      style: TextStyle(
                        fontSize: 32,
                        fontWeight: FontWeight.w700,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    const SizedBox(height: AppSpacing.sm),
                    const Text(
                      'AI-powered subscription insights and renewal predictions.',
                      style: TextStyle(
                        color: AppColors.textSecondary,
                        fontSize: 16,
                      ),
                    ),
                    const SizedBox(height: AppSpacing.xxl),
                    RadarScoreCard(score: state.score),
                    const SizedBox(height: AppSpacing.xxl),
                    const Text(
                      'Priority feed',
                      style: TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.w600,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    const SizedBox(height: AppSpacing.md),
                    ...state.feed.map(
                      (item) => IntelligenceCard(item: item),
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

### Step 14.7.10 — Offline intelligence

Exactly like the dashboard:

* Cache radar overview in Hive

* Cache radar feed

* Display cached intelligence instantly

* Refresh from Laravel in the background

* Animate updates

This makes Radar feel instantaneous, even with poor connectivity.

### Step 14.7.11 — Navigation integration

Add a Radar tab to the authenticated navigation shell.

The premium structure should be:

```
Dashboard
Radar
Wallet
Subscriptions
Profile
```

Radar should be one tap away from anywhere in the app.

### What you will have after Milestone 14.7

The user opens YouStayOn and immediately sees:

* Radar Score

* Critical expiries

* Warning subscriptions

* Depletion forecasts

* Renewal recommendations

* Cost-saving suggestions

* Priority intelligence feed

* Pull-to-refresh updates

* Offline persistence

* Animated premium cards

This is the feature that makes YouStayOn unique.

The next milestone is Milestone 14.8 — Wallet Module, where we will implement wallet balance, transaction history, funding workflow, payment selection, checkout integration, and Monnify-ready funding flows directly connected to the Laravel Wallet APIs you already built.
