Excellent. This is exactly the right place to continue. We have now completed a production-grade Laravel backend with 226 passing tests, and we have already designed the Flutter architecture through Milestone 14.9. The next milestone is where the entire backend and frontend begin to operate as one unified product.

The goal of Milestone 14.10 — Purchases & Checkout Module is to mirror the Laravel commerce engine 100%, including the provider abstraction layer, wallet payments, Pay Now (Monnify-ready) flow, purchase history, receipts, and future real-provider integration.

### Milestone 14.10 — Purchases & checkout module

Status: Ready to implement

### What we are implementing

This milestone maps directly to the Laravel backend modules we already completed and tested.

| Laravel backend        | Flutter implementation            |
| ---------------------- | --------------------------------- |
| Service catalog        | Purchase catalog screens          |
| Provider abstraction   | Provider selection UI             |
| Wallet purchase engine | Wallet checkout flow              |
| Pay Now initialization | Monnify payment flow              |
| Purchase verification  | Payment continuation screen       |
| Purchase history       | Purchase history module           |
| Purchase receipt       | Receipt screen                    |
| Analytics integration  | Purchase metrics                  |
| Radar integration      | Subscription purchase attribution |

This module will become the commercial engine of YouStayOn.

### Flutter architecture for purchases

We will add a new feature module:

```
lib/features/purchases/
├── data/
│   ├── models/
│   ├── repositories/
│   └── datasources/
├── domain/
│   ├── entities/
│   ├── repositories/
│   └── usecases/
├── presentation/
│   ├── bloc/
│   ├── pages/
│   └── widgets/
└── purchases.dart
```

This follows the same feature-first clean architecture used across authentication, dashboard, radar, wallet, and subscriptions.

### Domain entities

We mirror the Laravel DTOs and resources exactly.

### Purchase entity

dart

```
class Purchase {
  final String uuid;
  final String reference;
  final String serviceType;
  final String provider;
  final double amount;
  final String paymentMethod;
  final String status;
  final DateTime? completedAt;
  final Map<String, dynamic> payload;
  final Map<String, dynamic>? responsePayload;

  const Purchase({
    required this.uuid,
    required this.reference,
    required this.serviceType,
    required this.provider,
    required this.amount,
    required this.paymentMethod,
    required this.status,
    this.completedAt,
    required this.payload,
    this.responsePayload,
  });
}
```

### Purchase receipt entity

dart

```
class PurchaseReceipt {
  final Purchase purchase;
  final String providerReference;
  final DateTime timestamp;
  final String formattedAmount;

  const PurchaseReceipt({
    required this.purchase,
    required this.providerReference,
    required this.timestamp,
    required this.formattedAmount,
  });
}
```

### Backend API mapping

These are the Laravel endpoints we already built.

| Flutter use case | Laravel endpoint                  |
| ---------------- | --------------------------------- |
| List services    | GET /api/services                 |
| Create purchase  | POST /api/purchases               |
| Continue payment | POST /api/purchases/verify        |
| Purchase history | GET /api/purchases                |
| Purchase details | GET /api/purchases/{uuid}         |
| Receipt          | GET /api/purchases/{uuid}/receipt |

Because we intentionally built provider abstraction, Flutter never talks to VTpass or Monnify directly.

Flutter only talks to Laravel.

### Purchase flow (wallet)

This mirrors the backend test suite that already passed.

### Wallet purchase flow

Flutter → Laravel → provider manager → wallet → purchase → radar

1. Select service

Airtime / Data / Electricity / Cable

2. Select provider

MTN / Airtel / Glo / 9mobile

3. Enter payload

Phone number, meter number, smartcard, etc.

4. Choose Wallet

Use wallet balance as payment method

5. POST /api/purchases

Authenticated Laravel purchase request

6. Wallet debited

Backend executes wallet purchase service

7. Provider manager executes purchase

Currently FakeUtilityProvider; later VTpass

8. Radar updated

Subscription attribution and analytics updated

9. Receipt returned

Flutter shows success screen and receipt

### Pay Now flow (Monnify-ready)

This also mirrors the Laravel implementation.

### Pay Now flow

Flutter → Laravel → payment initialization → Monnify-ready checkout

1. Choose Pay Now

Instead of wallet payment

2. POST /api/purchases

Laravel initializes payment transaction

3. Receive checkout URL

Monnify-ready authorization URL from backend

4. Open checkout

In-app browser / WebView

5. Return to app

After payment completion

6. Verify purchase

POST /api/purchases/verify

7. Purchase completed

Provider fulfillment and analytics updates

8. Receipt screen

Show receipt and transaction reference

Notice that Flutter remains Monnify-ready, while the real gateway remains isolated inside Laravel.

### Screens to build

### 1. Service Catalog

Premium card grid.

```
Airtime
Data
Electricity
Cable TV
Internet
```

Animated cards using our premium design system.

### 2. Provider Selection

For example:

```
MTN
Airtel
Glo
9mobile
```

Later this list can be fetched dynamically from Laravel.

### 3. Purchase Form

Fields change depending on service type.

Examples:

Airtime

* Phone number

* Amount

Electricity

* Meter number

* Distribution company

* Amount

Cable

* Smartcard number

* Bouquet

* Amount

This mirrors the backend payload structure exactly.

### 4. Payment Method Bottom Sheet

Premium animated bottom sheet.

```
Pay with Wallet
Balance: ₦12,450

Pay Now
Bank Transfer / Card (Monnify)
```

### 5. Purchase Processing Screen

Animated loader.

Examples:

* Processing payment...

* Verifying transaction...

* Delivering airtime...

* Generating receipt...

This creates a premium fintech experience.

### 6. Success Receipt Screen

Receipt card.

```
Airtime Purchase

Amount
₦2,000

Provider
MTN

Reference
PAY-ABC123XYZ

Provider Ref
VT-987654

Status
Successful
```

Buttons:

* Share receipt

* Download receipt

* Done

### BLoC architecture

We separate flows cleanly.

```
PurchaseCatalogBloc
ProviderSelectionBloc
PurchaseFormBloc
CheckoutBloc
PaymentContinuationBloc
PurchaseHistoryBloc
ReceiptBloc
```

This matches our existing feature architecture.

### Repository layer

dart

```
abstract class PurchaseRepository {
  Future<List<ServiceItem>> getServices();

  Future<PurchaseCheckoutResponse> createPurchase(
    PurchaseRequest request,
  );

  Future<Purchase> verifyPurchase(String reference);

  Future<List<Purchase>> getPurchaseHistory();

  Future<PurchaseReceipt> getReceipt(String uuid);
}
```

Implementation uses the authenticated Dio client we already built in Milestone 14.2.

### Premium UX details

To make YouStayOn feel like a real fintech product:

### Micro-interactions

* Card hover animations

* Payment method selection animation

* Success checkmark animation

* Receipt slide-up animation

* Pull-to-refresh elasticity

### Performance

* Skeleton loaders

* Optimistic UI updates

* Local caching of service catalog

* Pagination for purchase history

* Background refresh

### Accessibility

* Screen reader labels

* High contrast support

* Dynamic text scaling

* Large tap targets

* Haptic feedback on payment actions

### Folder structure

```
lib/features/purchases/presentation/pages/
├── purchase_catalog_page.dart
├── provider_selection_page.dart
├── purchase_form_page.dart
├── checkout_page.dart
├── payment_processing_page.dart
├── payment_webview_page.dart
├── purchase_success_page.dart
├── purchase_history_page.dart
└── purchase_receipt_page.dart
```

### Integration with existing modules

This module connects directly with everything we already built.

| Module         | Integration            |
| -------------- | ---------------------- |
| Authentication | Sanctum token          |
| Dashboard      | Recent activity        |
| Radar          | Renewal attribution    |
| Wallet         | Wallet payment         |
| Subscriptions  | Subscription purchases |
| Analytics      | Purchase metrics       |
| Notifications  | Purchase confirmations |

This is why our earlier backend architecture decisions matter now.

### Commit checkpoint

After implementing the module foundation:

Bash

```
git add .
git commit -m "feat(mobile): add purchases and checkout module foundation"
git push origin main
```

This will preserve the milestone exactly like we did for the Laravel backend.

At the end of Milestone 14.10, YouStayOn will support end-to-end purchases from Flutter through Laravel, including wallet checkout, Pay Now initialization, purchase verification, receipts, history, radar attribution, and future VTpass integration without any frontend redesign.
