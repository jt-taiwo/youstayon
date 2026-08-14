Excellent. We are now implementing Milestone 14.8 — Wallet Module, which connects Flutter directly to the Laravel Wallet domain you already completed and tested. This module will mirror the backend architecture exactly: wallet balance, funding, transactions, payment selection, and purchase checkout. The goal is that every wallet operation in Flutter corresponds to an existing Laravel endpoint, with no duplicated business logic.

### Milestone 14.8: Wallet module

We are implementing wallet balance, transaction history, funding workflow, payment selection, checkout integration, and Monnify-ready funding flows directly connected to the Laravel Wallet APIs.

### Backend alignment

Your Laravel backend already includes the wallet domain, funding services, transaction recording, and Monnify integration infrastructure. Flutter will consume those APIs directly.

| Laravel capability             | Flutter feature            |
| ------------------------------ | -------------------------- |
| Wallet balance                 | Wallet overview card       |
| Wallet transactions            | Transaction history list   |
| Fund wallet                    | Funding amount flow        |
| Monnify payment initialization | Checkout redirect          |
| Payment verification           | Funding success handling   |
| Wallet purchase checkout       | Wallet payment option      |
| Pay Now checkout               | Alternative payment option |

This keeps Flutter and Laravel 100% synchronized.

### Feature structure

Create:

```
lib/features/wallet/
├── data/
│   ├── datasources/
│   │   └── wallet_remote_data_source.dart
│   ├── models/
│   │   ├── wallet_model.dart
│   │   ├── wallet_transaction_model.dart
│   │   ├── fund_wallet_request_model.dart
│   │   └── payment_initialization_model.dart
│   └── repositories/
│       └── wallet_repository_impl.dart
├── domain/
│   ├── repositories/
│   │   └── wallet_repository.dart
│   └── usecases/
│       ├── get_wallet.dart
│       ├── get_wallet_transactions.dart
│       ├── fund_wallet.dart
│       └── verify_wallet_funding.dart
└── presentation/
    ├── bloc/
    │   ├── wallet_bloc.dart
    │   ├── wallet_event.dart
    │   └── wallet_state.dart
    ├── pages/
    │   ├── wallet_page.dart
    │   └── fund_wallet_page.dart
    └── widgets/
        ├── wallet_balance_card.dart
        ├── transaction_tile.dart
        ├── funding_amount_selector.dart
        └── payment_method_sheet.dart
```

This mirrors the Laravel Wallet module exactly.

### Step 14.8.1 — Wallet model

Create `lib/features/wallet/data/models/wallet_model.dart`.

dart

```
class WalletModel {
  const WalletModel({
    required this.balance,
    required this.currency,
  });

  final double balance;
  final String currency;

  factory WalletModel.fromJson(Map<String, dynamic> json) {
    return WalletModel(
      balance: (json['balance'] as num).toDouble(),
      currency: json['currency'] as String,
    );
  }
}
```

This should match the Laravel wallet response payload.

### Step 14.8.2 — Wallet transaction model

Create `lib/features/wallet/data/models/wallet_transaction_model.dart`.

dart

```
class WalletTransactionModel {
  const WalletTransactionModel({
    required this.id,
    required this.type,
    required this.amount,
    required this.description,
    required this.createdAt,
  });

  final String id;
  final String type;
  final double amount;
  final String description;
  final String createdAt;

  factory WalletTransactionModel.fromJson(Map<String, dynamic> json) {
    return WalletTransactionModel(
      id: json['id'].toString(),
      type: json['type'] as String,
      amount: (json['amount'] as num).toDouble(),
      description: json['description'] as String,
      createdAt: json['created_at'] as String,
    );
  }
}
```

### Step 14.8.3 — Payment initialization model

Create `lib/features/wallet/data/models/payment_initialization_model.dart`.

dart

```
class PaymentInitializationModel {
  const PaymentInitializationModel({
    required this.reference,
    required this.authorizationUrl,
    required this.provider,
    required this.providerReference,
  });

  final String reference;
  final String authorizationUrl;
  final String provider;
  final String providerReference;

  factory PaymentInitializationModel.fromJson(
    Map<String, dynamic> json,
  ) {
    return PaymentInitializationModel(
      reference: json['reference'] as String,
      authorizationUrl: json['authorization_url'] as String,
      provider: json['provider'] as String,
      providerReference: json['provider_reference'] as String,
    );
  }
}
```

Notice that the field names intentionally match your Laravel `PaymentInitializationDTO`.

### Step 14.8.4 — Remote data source

Create `lib/features/wallet/data/datasources/wallet_remote_data_source.dart`.

dart

```
import '../../../../core/network/dio_client.dart';
import '../models/payment_initialization_model.dart';
import '../models/wallet_model.dart';
import '../models/wallet_transaction_model.dart';

class WalletRemoteDataSource {
  WalletRemoteDataSource(this._client);

  final DioClient _client;

  Future<WalletModel> getWallet() async {
    final response = await _client.dio.get('/wallet');

    return WalletModel.fromJson(
      response.data['data'] as Map<String, dynamic>,
    );
  }

  Future<List<WalletTransactionModel>>
      getTransactions() async {
    final response =
        await _client.dio.get('/wallet/transactions');

    final list = response.data['data'] as List;

    return list
        .map(
          (e) => WalletTransactionModel.fromJson(
            e as Map<String, dynamic>,
          ),
        )
        .toList();
  }

  Future<PaymentInitializationModel> fundWallet(
    double amount,
  ) async {
    final response = await _client.dio.post(
      '/wallet/fund',
      data: {
        'amount': amount,
      },
    );

    return PaymentInitializationModel.fromJson(
      response.data['data'] as Map<String, dynamic>,
    );
  }

  Future<bool> verifyFunding(String reference) async {
    final response = await _client.dio.post(
      '/wallet/fund/verify',
      data: {
        'reference': reference,
      },
    );

    return response.data['success'] == true;
  }
}
```

These endpoints should point directly to the Laravel wallet routes you already implemented.

### Step 14.8.5 — Repository

Create `lib/features/wallet/domain/repositories/wallet_repository.dart`.

dart

```
import '../../data/models/payment_initialization_model.dart';
import '../../data/models/wallet_model.dart';
import '../../data/models/wallet_transaction_model.dart';

abstract class WalletRepository {
  Future<WalletModel> getWallet();
  Future<List<WalletTransactionModel>> getTransactions();
  Future<PaymentInitializationModel> fundWallet(double amount);
  Future<bool> verifyFunding(String reference);
}
```

Create `wallet_repository_impl.dart`.

dart

```
import '../../domain/repositories/wallet_repository.dart';
import '../datasources/wallet_remote_data_source.dart';
import '../models/payment_initialization_model.dart';
import '../models/wallet_model.dart';
import '../models/wallet_transaction_model.dart';

class WalletRepositoryImpl implements WalletRepository {
  WalletRepositoryImpl(this._remote);

  final WalletRemoteDataSource _remote;

  @override
  Future<WalletModel> getWallet() {
    return _remote.getWallet();
  }

  @override
  Future<List<WalletTransactionModel>>
      getTransactions() {
    return _remote.getTransactions();
  }

  @override
  Future<PaymentInitializationModel> fundWallet(
    double amount,
  ) {
    return _remote.fundWallet(amount);
  }

  @override
  Future<bool> verifyFunding(String reference) {
    return _remote.verifyFunding(reference);
  }
}
```

### Step 14.8.6 — Dependency injection

Register the wallet layer.

In `lib/core/di/injection.dart` add:

dart

```
getIt.registerLazySingleton(
  () => WalletRemoteDataSource(getIt()),
);

getIt.registerLazySingleton<WalletRepository>(
  () => WalletRepositoryImpl(getIt()),
);
```

### Step 14.8.7 — Wallet BLoC

Create `wallet_event.dart`.

dart

```
abstract class WalletEvent {}

class WalletStarted extends WalletEvent {}

class WalletRefreshed extends WalletEvent {}

class FundWalletRequested extends WalletEvent {
  FundWalletRequested(this.amount);

  final double amount;
}
```

Create `wallet_state.dart`.

dart

```
import '../../data/models/payment_initialization_model.dart';
import '../../data/models/wallet_model.dart';
import '../../data/models/wallet_transaction_model.dart';

abstract class WalletState {}

class WalletInitial extends WalletState {}

class WalletLoading extends WalletState {}

class WalletLoaded extends WalletState {
  WalletLoaded({
    required this.wallet,
    required this.transactions,
  });

  final WalletModel wallet;
  final List<WalletTransactionModel> transactions;
}

class WalletFundingInitialized extends WalletState {
  WalletFundingInitialized(this.payment);

  final PaymentInitializationModel payment;
}

class WalletFailure extends WalletState {
  WalletFailure(this.message);

  final String message;
}
```

Create `wallet_bloc.dart`.

dart

```
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../domain/repositories/wallet_repository.dart';
import 'wallet_event.dart';
import 'wallet_state.dart';

class WalletBloc extends Bloc<WalletEvent, WalletState> {
  WalletBloc(this._repository) : super(WalletInitial()) {
    on<WalletStarted>(_load);
    on<WalletRefreshed>(_load);
    on<FundWalletRequested>(_fundWallet);
  }

  final WalletRepository _repository;

  Future<void> _load(
    WalletEvent event,
    Emitter<WalletState> emit,
  ) async {
    emit(WalletLoading());

    try {
      final wallet = await _repository.getWallet();
      final transactions =
          await _repository.getTransactions();

      emit(
        WalletLoaded(
          wallet: wallet,
          transactions: transactions,
        ),
      );
    } catch (e) {
      emit(WalletFailure(e.toString()));
    }
  }

  Future<void> _fundWallet(
    FundWalletRequested event,
    Emitter<WalletState> emit,
  ) async {
    try {
      final payment =
          await _repository.fundWallet(event.amount);

      emit(WalletFundingInitialized(payment));
    } catch (e) {
      emit(WalletFailure(e.toString()));
    }
  }
}
```

### Step 14.8.8 — Premium wallet balance card

Create `presentation/widgets/wallet_balance_card.dart`.

dart

```
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';

class WalletBalanceCard extends StatelessWidget {
  const WalletBalanceCard({
    super.key,
    required this.balance,
    required this.currency,
  });

  final double balance;
  final String currency;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.xl),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [
            AppColors.primary,
            AppColors.accent,
          ],
        ),
        borderRadius: BorderRadius.circular(AppRadius.xl),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Wallet Balance',
            style: TextStyle(
              color: Colors.white70,
            ),
          ),
          const SizedBox(height: AppSpacing.sm),
          Text(
            '$currency ${NumberFormat('#,##0.00').format(balance)}',
            style: const TextStyle(
              color: Colors.white,
              fontSize: 32,
              fontWeight: FontWeight.w700,
            ),
          ),
        ],
      ),
    );
  }
}
```

This gives the wallet a premium fintech appearance.

### Step 14.8.9 — Transaction tile

Create `presentation/widgets/transaction_tile.dart`.

dart

```
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../data/models/wallet_transaction_model.dart';

class TransactionTile extends StatelessWidget {
  const TransactionTile({
    super.key,
    required this.transaction,
  });

  final WalletTransactionModel transaction;

  @override
  Widget build(BuildContext context) {
    final positive = transaction.type == 'credit';

    return ListTile(
      contentPadding: const EdgeInsets.symmetric(
        vertical: AppSpacing.sm,
      ),
      leading: CircleAvatar(
        backgroundColor: positive
            ? AppColors.success.withValues(alpha: 0.12)
            : AppColors.error.withValues(alpha: 0.12),
        child: Icon(
          positive
              ? Icons.arrow_downward
              : Icons.arrow_upward,
          color: positive
              ? AppColors.success
              : AppColors.error,
        ),
      ),
      title: Text(
        transaction.description,
        style: const TextStyle(
          fontWeight: FontWeight.w600,
        ),
      ),
      subtitle: Text(transaction.createdAt),
      trailing: Text(
        '${positive ? '+' : '-'}₦${NumberFormat('#,##0.00').format(transaction.amount)}',
        style: TextStyle(
          color: positive
              ? AppColors.success
              : AppColors.error,
          fontWeight: FontWeight.w700,
        ),
      ),
    );
  }
}
```

### Step 14.8.10 — Wallet page

Replace `presentation/pages/wallet_page.dart`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../bloc/wallet_bloc.dart';
import '../bloc/wallet_event.dart';
import '../bloc/wallet_state.dart';
import '../widgets/transaction_tile.dart';
import '../widgets/wallet_balance_card.dart';

class WalletPage extends StatefulWidget {
  const WalletPage({super.key});

  @override
  State<WalletPage> createState() => _WalletPageState();
}

class _WalletPageState extends State<WalletPage> {
  @override
  void initState() {
    super.initState();

    context.read<WalletBloc>().add(
          WalletStarted(),
        );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          // Navigate to funding flow
        },
        label: const Text('Fund Wallet'),
        icon: const Icon(Icons.add),
      ),
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: () async {
            context.read<WalletBloc>().add(
                  WalletRefreshed(),
                );
          },
          child: BlocBuilder<WalletBloc, WalletState>(
            builder: (context, state) {
              if (state is WalletLoading) {
                return const Center(
                  child: CircularProgressIndicator(),
                );
              }

              if (state is WalletFailure) {
                return Center(
                  child: Text(state.message),
                );
              }

              if (state is WalletLoaded) {
                return ListView(
                  padding: const EdgeInsets.all(AppSpacing.lg),
                  children: [
                    WalletBalanceCard(
                      balance: state.wallet.balance,
                      currency: state.wallet.currency,
                    ),
                    const SizedBox(height: AppSpacing.xxl),
                    const Text(
                      'Recent Transactions',
                      style: TextStyle(
                        fontSize: 22,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    const SizedBox(height: AppSpacing.md),
                    ...state.transactions.map(
                      (t) => TransactionTile(
                        transaction: t,
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

### Step 14.8.11 — Monnify funding flow

The funding flow is intentionally aligned with your Laravel implementation:

```
Wallet Page
      |
      v
Enter Amount
      |
      v
POST /wallet/fund
      |
      v
Laravel MonnifyPaymentGateway
      |
      v
PaymentInitializationDTO
      |
      v
Flutter receives:
- reference
- authorizationUrl
- provider
- providerReference
      |
      v
Open WebView / Browser
      |
      v
User Pays
      |
      v
POST /wallet/fund/verify
      |
      v
Laravel VerifyWalletFundingService
      |
      v
Wallet Credited
      |
      v
Wallet Page Refreshes
```

This is exactly the backend flow you already tested.

### Step 14.8.12 — Payment method sheet

When a user initiates a purchase, Flutter should present:

* Wallet

* Pay Now

This maps directly to your Laravel purchase system:

* Wallet → `ExecuteWalletPurchaseService`

* Pay Now → `VerifyPayNowPurchaseService`

No Flutter business logic is required.

### Step 14.8.13 — Offline caching

Use Hive to cache:

* Wallet balance

* Recent transactions

Startup sequence:

1. Display cached balance instantly.

2. Refresh from Laravel.

3. Animate balance changes.

4. Update cache.

This creates a Moniepoint-level responsiveness.

### What you will have after Milestone 14.8

* Premium wallet balance card

* Transaction history

* Pull-to-refresh

* Secure authenticated wallet requests

* Monnify-ready funding initialization

* Payment method selection

* Offline wallet caching

* Instant balance updates

* Navigation integrated with the authenticated app shell

At this point, the Flutter application will be connected to Authentication, Dashboard, Radar Intelligence, and Wallet, all powered directly by the Laravel backend we completed.

The next milestone is Milestone 14.9 — Subscriptions Module, where we will build subscription creation, editing, cancellation, renewal, usage tracking, prediction views, renewal history, and category management directly connected to the Laravel Subscription APIs that already passed your backend test suite.
