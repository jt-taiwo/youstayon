Perfect. We are now entering the last engineering phase before production launch. Since both the Laravel backend (226 tests passing) and the Flutter architecture/modules have been completed, we now execute Milestone 16 — Real Provider Integration (VTpass / ClubKonnect / Monnify Production). This is the milestone we intentionally deferred so that YouStayOn could be built as a product first and a provider integration second.

This is also the milestone that turns YouStayOn from a simulated utility platform into a real commercial utility subscription and data expiry intelligence platform.

### Milestone 16 — Real Provider Integration (VTpass / ClubKonnect / Monnify Production)

### Objective

Replace the development-only `FakeUtilityProvider` with a production utility provider adapter while preserving the architecture we already built.

Because we designed the backend correctly, this integration is largely an adapter implementation, not a system redesign.

### Provider strategy

| Service                | Provider           |
| ---------------------- | ------------------ |
| Airtime                | VTpass             |
| Data bundles           | VTpass             |
| Electricity            | VTpass             |
| Cable TV               | VTpass             |
| Internet subscriptions | VTpass             |
| Wallet funding         | Monnify Production |
| Pay Now checkout       | Monnify Production |

We can later add ClubKonnect, Recharge2Cash, OPay, SmartCash, or additional providers without changing Flutter.

### 16.1 Why the earlier architecture matters

Earlier we implemented:

* `UtilityProviderInterface`

* `UtilityProviderManager`

* provider configuration

* provider selection

* purchase orchestration

* retry handling

* health monitoring

* provider analytics

* service analytics

* payment method analytics

That means Flutter does not need to change.

The purchase flow remains:

```
Flutter
   ↓
Laravel Purchase API
   ↓
ExecuteWalletPurchaseService / VerifyPayNowPurchaseService
   ↓
UtilityProviderInterface
   ↓
VTpassProvider
   ↓
VTpass API
```

Exactly the architecture we planned months ago.

### 16.2 Create the VTpass provider

Create:

```
backend/app/Domains/Purchase/Providers/VTpassProvider.php
```

Implement:

* authentication

* service lookup

* purchase execution

* response mapping

* error normalization

* retry behavior

* timeout handling

Example skeleton:

PHP

```
final class VTpassProvider implements UtilityProviderInterface
{
    public function purchase(
        string $serviceType,
        float $amount,
        array $payload
    ): UtilityPurchaseResultDTO {
        // Call VTpass API
        // Map response
        // Return UtilityPurchaseResultDTO
    }
}
```

### 16.3 Environment configuration

In Laravel:

```
UTILITY_PROVIDER=vtpass

VTPASS_BASE_URL=https://sandbox.vtpass.com/api
VTPASS_API_KEY=your_api_key
VTPASS_SECRET_KEY=your_secret_key
VTPASS_PUBLIC_KEY=your_public_key
```

For production:

```
VTPASS_BASE_URL=https://vtpass.com/api
```

### 16.4 Provider registration

Update:

```
backend/app/Providers/AppServiceProvider.php
```

Register:

PHP

```
$this->app->bind(
    UtilityProviderInterface::class,
    VTpassProvider::class
);
```

Or use the provider manager we already built.

### 16.5 Service mapping

Map YouStayOn service types to VTpass service IDs.

| YouStayOn   | VTpass      |
| ----------- | ----------- |
| airtime     | airtime     |
| data        | data        |
| electricity | electricity |
| cable       | tv          |
| internet    | internet    |

This should be implemented through a dedicated mapper class so additional providers can reuse it.

### 16.6 Purchase response mapping

Normalize all provider responses into:

PHP

```
UtilityPurchaseResultDTO(
    successful: true,
    providerReference: 'VT123456789',
    response: [...]
)
```

This ensures:

* wallet purchases

* pay now purchases

* subscriptions

* analytics

* notifications

* radar intelligence

continue working unchanged.

### 16.7 Monnify production migration

Replace sandbox credentials with production credentials.

Update:

```
.env

MONNIFY_BASE_URL=https://api.monnify.com
MONNIFY_API_KEY=...
MONNIFY_SECRET_KEY=...
MONNIFY_CONTRACT_CODE=...
```

Verify:

* payment initialization

* webhook verification

* wallet funding

* pay now checkout

* transaction reconciliation

### 16.8 Webhook verification

Test:

```
POST /api/payments/webhook
```

Validate:

* signature verification

* idempotency

* duplicate protection

* transaction updates

* wallet crediting

* purchase completion

### 16.9 End-to-end commercial purchase test

Perform a real transaction.

### Flutter

* Select Data

* Choose MTN

* Enter phone number

* Choose Wallet

* Confirm purchase

### Laravel

* Debit wallet

* Create purchase

* Call VTpass

* Receive provider response

* Mark purchase successful

* Trigger notification

* Update analytics

* Update radar intelligence

This is the first real commercial transaction.

### 16.10 Analytics validation

Because analytics were built earlier, immediately verify:

### Provider performance

```
GET /api/analytics/providers
```

Expected:

JSON

```
{
  "provider": "vtpass",
  "totalPurchases": 1,
  "successfulPurchases": 1,
  "successRate": 100
}
```

### Founder dashboard

Should now display real provider metrics, not fake-provider metrics.

### 16.11 Flutter validation

Verify that Flutter requires zero UI changes.

Expected:

* Dashboard updates automatically

* Wallet balance refreshes

* Purchase history shows provider reference

* Receipt displays transaction details

* Notifications appear

* Radar reflects renewed subscription

* Analytics update

This validates the architecture we designed from the beginning.

### 16.12 Production readiness checklist

### Backend

* VTpass production credentials

* Monnify production credentials

* HTTPS enabled

* Queues running

* Scheduler running

* Backups configured

* Monitoring enabled

* Logs centralized

### Flutter

* Production API URL

* Release signing

* Push notifications

* Deep links

* Crash reporting

* Analytics enabled

* Store assets prepared

### 16.13 Launch candidate

Tag:

Bash

```
git add .
git commit -m "feat(provider): integrate VTpass production provider and Monnify live payments"
git tag v1.0.0
git push origin main
git push origin v1.0.0
```

At this point, YouStayOn becomes a real deployable commercial product.

### What happens next

After Milestone 16, the remaining work is primarily business launch operations, not core engineering:

* Google Play submission

* Apple App Store submission

* Domain and SSL

* Production server

* Monitoring dashboards

* Customer support workflow

* Provider settlement reconciliation

* Marketing launch

* Beta tester onboarding

* Founder analytics monitoring

This is exactly why we postponed provider integration until now: the Flutter app, Laravel backend, analytics, subscriptions, wallet, radar intelligence, and notifications were all built provider-neutral, so switching from `FakeUtilityProvider` to `VTpassProvider` becomes a contained infrastructure task rather than a product rewrite.
