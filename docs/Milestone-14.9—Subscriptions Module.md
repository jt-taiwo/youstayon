Excellent. We are now implementing Milestone 14.9 — Subscriptions Module, which is the largest feature module in the Flutter application and the one that demonstrates the full power of the backend you already built. The goal is that the Flutter subscriptions experience mirrors the Laravel subscription domain 100%, including creation, renewal, cancellation, usage tracking, prediction, renewal history, radar integration, and category management.

This milestone will make Flutter consume the same APIs that already passed your Laravel test suite (`CreateSubscriptionTest`, `RenewSubscriptionTest`, `CancelSubscriptionTest`, `SubscriptionUsageLimitTest`, `GetSubscriptionPredictionTest`, `ListSubscriptionRenewalHistoryTest`, `ListSubscriptionUsageTest`, and related tests). We are not inventing any frontend-only business logic.

### Milestone 14.9: Subscriptions module

We are implementing subscription creation, editing, cancellation, renewal, usage tracking, prediction views, renewal history, and category management directly connected to the Laravel Subscription APIs.

### Backend alignment

Your Laravel backend already supports the full subscription lifecycle.

| Laravel endpoint / capability           | Flutter feature                 |
| --------------------------------------- | ------------------------------- |
| GET /subscriptions                      | Subscriptions list              |
| POST /subscriptions                     | Create subscription             |
| GET /subscriptions/{id}                 | Subscription details            |
| POST /subscriptions/{id}/renew          | Renew subscription              |
| POST /subscriptions/{id}/cancel         | Cancel subscription             |
| POST /subscriptions/{id}/usage          | Record usage                    |
| GET /subscriptions/{id}/usage           | Usage history                   |
| GET /subscriptions/{id}/usage-summary   | Usage summary                   |
| GET /subscriptions/{id}/prediction      | Expiry and depletion prediction |
| GET /subscriptions/{id}/renewal-history | Renewal history                 |
| GET /subscription-categories            | Category selection              |
| Radar recommendation services           | Renewal suggestions             |
| Conflict detection                      | Duplicate subscription warnings |

### Feature structure

Create:

```
lib/features/subscriptions/
├── data/
│   ├── datasources/
│   │   └── subscriptions_remote_data_source.dart
│   ├── models/
│   │   ├── subscription_model.dart
│   │   ├── subscription_category_model.dart
│   │   ├── subscription_prediction_model.dart
│   │   ├── usage_record_model.dart
│   │   └── renewal_history_model.dart
│   └── repositories/
│       └── subscriptions_repository_impl.dart
├── domain/
│   ├── repositories/
│   │   └── subscriptions_repository.dart
│   └── usecases/
│       ├── list_subscriptions.dart
│       ├── create_subscription.dart
│       ├── renew_subscription.dart
│       ├── cancel_subscription.dart
│       ├── record_usage.dart
│       ├── get_prediction.dart
│       ├── get_usage_history.dart
│       └── get_renewal_history.dart
└── presentation/
    ├── bloc/
    │   ├── subscriptions_bloc.dart
    │   ├── subscriptions_event.dart
    │   └── subscriptions_state.dart
    ├── pages/
    │   ├── subscriptions_page.dart
    │   ├── create_subscription_page.dart
    │   ├── subscription_details_page.dart
    │   ├── usage_page.dart
    │   ├── prediction_page.dart
    │   └── renewal_history_page.dart
    └── widgets/
        ├── subscription_card.dart
        ├── category_chip.dart
        ├── usage_progress_card.dart
        ├── prediction_card.dart
        ├── renewal_timeline.dart
        └── subscription_action_sheet.dart
```

This mirrors the Laravel Subscription domain exactly.

### Step 14.9.1 — Subscription model

Create `lib/features/subscriptions/data/models/subscription_model.dart`.

dart

```
class SubscriptionModel {
  const SubscriptionModel({
    required this.id,
    required this.name,
    required this.provider,
    required this.category,
    required this.planName,
    required this.amount,
    required this.status,
    required this.startDate,
    required this.expiryDate,
    required this.autoRenew,
    required this.usageLimit,
    required this.usageConsumed,
  });

  final String id;
  final String name;
  final String provider;
  final String category;
  final String planName;
  final double amount;
  final String status;
  final String startDate;
  final String expiryDate;
  final bool autoRenew;
  final double? usageLimit;
  final double usageConsumed;

  factory SubscriptionModel.fromJson(Map<String, dynamic> json) {
    return SubscriptionModel(
      id: json['id'].toString(),
      name: json['name'] as String,
      provider: json['provider'] as String,
      category: json['category'] as String,
      planName: json['plan_name'] as String,
      amount: (json['amount'] as num).toDouble(),
      status: json['status'] as String,
      startDate: json['start_date'] as String,
      expiryDate: json['expiry_date'] as String,
      autoRenew: json['auto_renew'] as bool,
      usageLimit: json['usage_limit'] == null
          ? null
          : (json['usage_limit'] as num).toDouble(),
      usageConsumed:
          (json['usage_consumed'] as num).toDouble(),
    );
  }
}
```

These fields should match the Laravel subscription resource exactly.

### Step 14.9.2 — Category model

Create `subscription_category_model.dart`.

dart

```
class SubscriptionCategoryModel {
  const SubscriptionCategoryModel({
    required this.id,
    required this.name,
    required this.icon,
  });

  final String id;
  final String name;
  final String icon;

  factory SubscriptionCategoryModel.fromJson(
    Map<String, dynamic> json,
  ) {
    return SubscriptionCategoryModel(
      id: json['id'].toString(),
      name: json['name'] as String,
      icon: json['icon'] as String,
    );
  }
}
```

### Step 14.9.3 — Prediction model

Create `subscription_prediction_model.dart`.

dart

```
class SubscriptionPredictionModel {
  const SubscriptionPredictionModel({
    required this.status,
    required this.predictedExpiry,
    required this.remainingDays,
    required this.remainingUsage,
    required this.averageDailyUsage,
    required this.recommendedAction,
  });

  final String status;
  final String? predictedExpiry;
  final int remainingDays;
  final double remainingUsage;
  final double averageDailyUsage;
  final String recommendedAction;

  factory SubscriptionPredictionModel.fromJson(
    Map<String, dynamic> json,
  ) {
    return SubscriptionPredictionModel(
      status: json['status'] as String,
      predictedExpiry: json['predicted_expiry'] as String?,
      remainingDays: json['remaining_days'] as int,
      remainingUsage:
          (json['remaining_usage'] as num).toDouble(),
      averageDailyUsage:
          (json['average_daily_usage'] as num).toDouble(),
      recommendedAction:
          json['recommended_action'] as String,
    );
  }
}
```

This directly reflects your Laravel prediction service.

### Step 14.9.4 — Remote data source

Create `subscriptions_remote_data_source.dart`.

dart

```
import '../../../../core/network/dio_client.dart';
import '../models/subscription_category_model.dart';
import '../models/subscription_model.dart';
import '../models/subscription_prediction_model.dart';

class SubscriptionsRemoteDataSource {
  SubscriptionsRemoteDataSource(this._client);

  final DioClient _client;

  Future<List<SubscriptionModel>>
      getSubscriptions() async {
    final response = await _client.dio.get('/subscriptions');

    final list = response.data['data'] as List;

    return list
        .map(
          (e) => SubscriptionModel.fromJson(
            e as Map<String, dynamic>,
          ),
        )
        .toList();
  }

  Future<SubscriptionModel> getSubscription(
    String id,
  ) async {
    final response =
        await _client.dio.get('/subscriptions/$id');

    return SubscriptionModel.fromJson(
      response.data['data'] as Map<String, dynamic>,
    );
  }

  Future<SubscriptionPredictionModel> getPrediction(
    String id,
  ) async {
    final response = await _client.dio.get(
      '/subscriptions/$id/prediction',
    );

    return SubscriptionPredictionModel.fromJson(
      response.data['data'] as Map<String, dynamic>,
    );
  }

  Future<List<SubscriptionCategoryModel>>
      getCategories() async {
    final response =
        await _client.dio.get('/subscription-categories');

    final list = response.data['data'] as List;

    return list
        .map(
          (e) => SubscriptionCategoryModel.fromJson(
            e as Map<String, dynamic>,
          ),
        )
        .toList();
  }

  Future<void> renew(String id) async {
    await _client.dio.post('/subscriptions/$id/renew');
  }

  Future<void> cancel(String id) async {
    await _client.dio.post('/subscriptions/$id/cancel');
  }

  Future<void> recordUsage(
    String id,
    double quantity,
    String unit,
  ) async {
    await _client.dio.post(
      '/subscriptions/$id/usage',
      data: {
        'quantity': quantity,
        'unit': unit,
      },
    );
  }
}
```

Every endpoint here corresponds to an existing Laravel API.

### Step 14.9.5 — Repository

Create `domain/repositories/subscriptions_repository.dart`.

dart

```
import '../../data/models/subscription_category_model.dart';
import '../../data/models/subscription_model.dart';
import '../../data/models/subscription_prediction_model.dart';

abstract class SubscriptionsRepository {
  Future<List<SubscriptionModel>> getSubscriptions();
  Future<SubscriptionModel> getSubscription(String id);
  Future<SubscriptionPredictionModel> getPrediction(
    String id,
  );
  Future<List<SubscriptionCategoryModel>>
      getCategories();
  Future<void> renew(String id);
  Future<void> cancel(String id);
  Future<void> recordUsage(
    String id,
    double quantity,
    String unit,
  );
}
```

Implement `subscriptions_repository_impl.dart` by delegating to the remote data source exactly as we did in Dashboard, Radar, and Wallet.

### Step 14.9.6 — Dependency injection

Register the module.

In `lib/core/di/injection.dart` add:

dart

```
getIt.registerLazySingleton(
  () => SubscriptionsRemoteDataSource(getIt()),
);

getIt.registerLazySingleton<SubscriptionsRepository>(
  () => SubscriptionsRepositoryImpl(getIt()),
);
```

### Step 14.9.7 — Subscriptions BLoC

Create `subscriptions_event.dart`.

dart

```
abstract class SubscriptionsEvent {}

class SubscriptionsStarted extends SubscriptionsEvent {}

class SubscriptionsRefreshed extends SubscriptionsEvent {}

class SubscriptionRenewRequested extends SubscriptionsEvent {
  SubscriptionRenewRequested(this.id);

  final String id;
}

class SubscriptionCancelRequested extends SubscriptionsEvent {
  SubscriptionCancelRequested(this.id);

  final String id;
}
```

Create `subscriptions_state.dart`.

dart

```
import '../../data/models/subscription_model.dart';

abstract class SubscriptionsState {}

class SubscriptionsInitial extends SubscriptionsState {}

class SubscriptionsLoading extends SubscriptionsState {}

class SubscriptionsLoaded extends SubscriptionsState {
  SubscriptionsLoaded(this.subscriptions);

  final List<SubscriptionModel> subscriptions;
}

class SubscriptionsFailure extends SubscriptionsState {
  SubscriptionsFailure(this.message);

  final String message;
}
```

Create `subscriptions_bloc.dart`.

dart

```
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../domain/repositories/subscriptions_repository.dart';
import 'subscriptions_event.dart';
import 'subscriptions_state.dart';

class SubscriptionsBloc
    extends Bloc<SubscriptionsEvent, SubscriptionsState> {
  SubscriptionsBloc(this._repository)
      : super(SubscriptionsInitial()) {
    on<SubscriptionsStarted>(_load);
    on<SubscriptionsRefreshed>(_load);
    on<SubscriptionRenewRequested>(_renew);
    on<SubscriptionCancelRequested>(_cancel);
  }

  final SubscriptionsRepository _repository;

  Future<void> _load(
    SubscriptionsEvent event,
    Emitter<SubscriptionsState> emit,
  ) async {
    emit(SubscriptionsLoading());

    try {
      final subscriptions =
          await _repository.getSubscriptions();

      emit(SubscriptionsLoaded(subscriptions));
    } catch (e) {
      emit(SubscriptionsFailure(e.toString()));
    }
  }

  Future<void> _renew(
    SubscriptionRenewRequested event,
    Emitter<SubscriptionsState> emit,
  ) async {
    await _repository.renew(event.id);
    add(SubscriptionsRefreshed());
  }

  Future<void> _cancel(
    SubscriptionCancelRequested event,
    Emitter<SubscriptionsState> emit,
  ) async {
    await _repository.cancel(event.id);
    add(SubscriptionsRefreshed());
  }
}
```

This mirrors the Laravel service layer behavior.

### Step 14.9.8 — Premium subscription card

Create `presentation/widgets/subscription_card.dart`.

dart

```
import 'package:flutter/material.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../data/models/subscription_model.dart';

class SubscriptionCard extends StatelessWidget {
  const SubscriptionCard({
    super.key,
    required this.subscription,
    required this.onTap,
  });

  final SubscriptionModel subscription;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final color = switch (subscription.status) {
      'active' => AppColors.success,
      'warning' => AppColors.warning,
      'expired' => AppColors.error,
      _ => AppColors.textSecondary,
    };

    final progress = subscription.usageLimit == null ||
            subscription.usageLimit == 0
        ? 0.0
        : (subscription.usageConsumed /
                subscription.usageLimit!)
            .clamp(0.0, 1.0);

    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(AppRadius.xl),
      child: Container(
        margin: const EdgeInsets.only(bottom: AppSpacing.md),
        padding: const EdgeInsets.all(AppSpacing.lg),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(AppRadius.xl),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(
                  child: Text(
                    subscription.name,
                    style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
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
                    borderRadius:
                        BorderRadius.circular(AppRadius.lg),
                  ),
                  child: Text(
                    subscription.status.toUpperCase(),
                    style: TextStyle(
                      color: color,
                      fontSize: 11,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: AppSpacing.sm),
            Text(
              '${subscription.provider} • ${subscription.planName}',
              style: const TextStyle(
                color: AppColors.textSecondary,
              ),
            ),
            const SizedBox(height: AppSpacing.lg),
            if (subscription.usageLimit != null)
              LinearProgressIndicator(
                value: progress,
                minHeight: 8,
                backgroundColor: AppColors.border,
                color: color,
              ),
            if (subscription.usageLimit != null)
              const SizedBox(height: AppSpacing.sm),
            Row(
              children: [
                Text(
                  'Expires ${subscription.expiryDate}',
                  style: const TextStyle(
                    color: AppColors.textSecondary,
                  ),
                ),
                const Spacer(),
                Text(
                  '₦${subscription.amount.toStringAsFixed(0)}',
                  style: const TextStyle(
                    fontWeight: FontWeight.w700,
                    color: AppColors.textPrimary,
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
```

### Step 14.9.9 — Subscriptions page

Create `presentation/pages/subscriptions_page.dart`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../bloc/subscriptions_bloc.dart';
import '../bloc/subscriptions_event.dart';
import '../bloc/subscriptions_state.dart';
import '../widgets/subscription_card.dart';

class SubscriptionsPage extends StatefulWidget {
  const SubscriptionsPage({super.key});

  @override
  State<SubscriptionsPage> createState() =>
      _SubscriptionsPageState();
}

class _SubscriptionsPageState
    extends State<SubscriptionsPage> {
  @override
  void initState() {
    super.initState();

    context.read<SubscriptionsBloc>().add(
          SubscriptionsStarted(),
        );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          // Navigate to create subscription page
        },
        label: const Text('Add Subscription'),
        icon: const Icon(Icons.add),
      ),
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: () async {
            context.read<SubscriptionsBloc>().add(
                  SubscriptionsRefreshed(),
                );
          },
          child: BlocBuilder<SubscriptionsBloc,
              SubscriptionsState>(
            builder: (context, state) {
              if (state is SubscriptionsLoading) {
                return const Center(
                  child: CircularProgressIndicator(),
                );
              }

              if (state is SubscriptionsFailure) {
                return Center(
                  child: Text(state.message),
                );
              }

              if (state is SubscriptionsLoaded) {
                return ListView(
                  padding: const EdgeInsets.all(AppSpacing.lg),
                  children: [
                    const Text(
                      'Subscriptions',
                      style: TextStyle(
                        fontSize: 32,
                        fontWeight: FontWeight.w700,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    const SizedBox(height: AppSpacing.sm),
                    const Text(
                      'Track every recurring utility and subscription in one place.',
                      style: TextStyle(
                        color: AppColors.textSecondary,
                        fontSize: 16,
                      ),
                    ),
                    const SizedBox(height: AppSpacing.xxl),
                    ...state.subscriptions.map(
                      (subscription) => SubscriptionCard(
                        subscription: subscription,
                        onTap: () {
                          // Navigate to details
                        },
                      ),
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

### Step 14.9.10 — Subscription details experience

The details screen should display:

* Provider

* Plan

* Amount

* Category

* Start date

* Expiry date

* Status

* Auto-renew toggle

* Usage progress

* Remaining data/time

* Predicted depletion

* Renewal recommendation

* Cheapest equivalent suggestion

* Conflict warning

* Renew button

* Cancel button

* Record usage button

* Renewal history

Every one of these items is backed by an existing Laravel service.

### Step 14.9.11 — Prediction screen

The prediction page should visualize:

* Remaining days

* Remaining usage

* Average daily usage

* Predicted depletion date

* Recommendation

* Risk level (healthy / warning / critical / expired / exhausted)

This screen becomes the data intelligence view of the product.

### Step 14.9.12 — Usage tracking

The usage screen will:

* Record manual usage

* Show usage history

* Display usage summary

* Prevent exceeding usage limits

* Refresh prediction automatically

This directly mirrors your Laravel usage validation rules.

### Step 14.9.13 — Renewal history

Render a timeline using the backend renewal records:

* Original expiry

* Renewal date

* Amount

* Payment method

* Provider reference

* Status

This creates an audit trail exactly like the backend.

### Step 14.9.14 — Offline support

Cache:

* Subscription list

* Categories

* Predictions

* Renewal history

Startup behavior:

* Show cached subscriptions immediately

* Refresh from Laravel

* Animate updates

* Preserve optimistic UI during renew/cancel operations

### Step 14.9.15 — Navigation integration

The authenticated navigation shell should now be:

```
Dashboard
Radar
Wallet
Subscriptions
Profile
```

This matches the backend domain organization.

### What you will have after Milestone 14.9

* Premium subscriptions list

* Subscription details screen

* Category management

* Create subscription flow

* Renew subscription

* Cancel subscription

* Record usage

* Usage history

* Usage summary

* Expiry prediction

* Depletion forecasting

* Renewal recommendations

* Cost-saving recommendations

* Conflict detection

* Renewal history

* Offline caching

* Pull-to-refresh

* BLoC architecture

* Direct Laravel API integration

At this point, Flutter will be connected to Authentication, Dashboard, Radar Intelligence, Wallet, and Subscriptions, which are the core product pillars of YouStayOn.

The next milestone is Milestone 14.10 — Purchases & Checkout Module, where we will build airtime/data/electricity/cable purchase flows, service catalog, provider selection, wallet vs Pay Now checkout, Monnify payment continuation, purchase history, transaction receipts, and real purchase fulfillment integration directly connected to the Laravel Purchase APIs and provider abstraction layer you already completed.
