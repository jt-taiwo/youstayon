Excellent. The next milestone is the one that gives YouStayOn a defensible competitive advantage beyond being a utility purchase app.

### Milestone 18.3 — Rewards & Loyalty System

This milestone transforms YouStayOn from a transactional utility platform into a retention engine. The goal is to reward users for behaviors that increase lifetime value: renewing subscriptions, funding wallets, making purchases, using Radar Intelligence, referring friends, and maintaining healthy utility habits.

Unlike simple cashback systems, we will build a points-based loyalty platform that integrates with wallets, subscriptions, purchases, notifications, analytics, referrals, and the founder dashboard we have already completed.

### Objective

Create a production-grade rewards ecosystem that supports:

* loyalty points,

* cashback campaigns,

* streak rewards,

* renewal rewards,

* referral rewards,

* milestone achievements,

* redemption into wallet credit,

* analytics,

* founder reporting,

* future partner rewards.

### Loyalty architecture

```
User Action
     │
     ▼
Event Engine
     │
     ▼
Reward Rules
     │
     ▼
Points Awarded
     │
     ▼
Wallet / Loyalty Ledger
     │
     ▼
Notification
     │
     ▼
Analytics
     │
     ▼
Founder Dashboard
```

### 18.3.1 Database schema

Create a dedicated loyalty domain.

### loyalty_accounts table

PHP

```
Schema::create('loyalty_accounts', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->foreignId('user_id')
        ->unique()
        ->constrained()
        ->cascadeOnDelete();

    $table->bigInteger('points')->default(0);

    $table->enum('tier', [
        'bronze',
        'silver',
        'gold',
        'platinum',
    ])->default('bronze');

    $table->timestamp('last_activity_at')->nullable();

    $table->timestamps();
});
```

### loyalty_transactions table

PHP

```
Schema::create('loyalty_transactions', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->foreignId('loyalty_account_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->enum('type', [
        'earn',
        'redeem',
        'expire',
        'bonus',
        'adjustment',
    ]);

    $table->string('reason');

    $table->bigInteger('points');

    $table->json('metadata')->nullable();

    $table->timestamps();
});
```

This gives us a complete loyalty ledger.

### 18.3.2 Reward rules engine

Create:

```
RewardRuleInterface
RewardEngine
EvaluateRewardService
```

Rules should be configuration-driven.

Example:

| Action                | Points |
| --------------------- | ------ |
| Registration          | 100    |
| Email verification    | 50     |
| First wallet funding  | 200    |
| Every ₦1,000 purchase | 10     |
| Subscription renewal  | 30     |
| Referral qualified    | 200    |
| 7-day streak          | 100    |
| 30-day streak         | 500    |

### 18.3.3 Event integration

Connect rewards to existing services.

### Registration

```
RegisterService
    ↓
RewardEngine::awardRegistration()
```

### Wallet funding

```
VerifyWalletFundingService
    ↓
RewardEngine::awardWalletFunding()
```

### Purchase

```
ExecuteWalletPurchaseService
VerifyPayNowPurchaseService
    ↓
RewardEngine::awardPurchasePoints()
```

### Subscription renewal

```
RenewSubscriptionService
AutoRenewSubscriptionService
    ↓
RewardEngine::awardRenewalPoints()
```

### Referral qualification

```
IssueReferralRewardService
    ↓
RewardEngine::awardReferralPoints()
```

Because we built an event-driven architecture earlier, this integrates cleanly.

### 18.3.4 Loyalty tiers

Automatically upgrade users.

| Tier     | Requirement  |
| -------- | ------------ |
| Bronze   | 0–999 points |
| Silver   | 1,000–4,999  |
| Gold     | 5,000–19,999 |
| Platinum | 20,000+      |

Benefits can include:

* bonus cashback,

* priority support,

* higher referral rewards,

* exclusive promotions,

* early feature access.

### 18.3.5 Redemption engine

Create:

```
RedeemLoyaltyPointsService
```

Example conversion:

```
100 points = ₦10 wallet credit
```

Flow:

```
Redeem Request
     │
     ▼
Validate Balance
     │
     ▼
Deduct Points
     │
     ▼
Credit Wallet
     │
     ▼
Create Wallet Transaction
     │
     ▼
Create Loyalty Transaction
     │
     ▼
Send Notification
```

This reuses the wallet infrastructure already completed.

### 18.3.6 Loyalty APIs

### Get account

```
GET /api/loyalty
```

Response:

JSON

```
{
  "points": 2350,
  "tier": "silver",
  "next_tier": "gold",
  "points_to_next_tier": 2650
}
```

### Transaction history

```
GET /api/loyalty/history
```

### Redeem points

```
POST /api/loyalty/redeem
```

Request:

JSON

```
{
  "points": 500
}
```

### Reward catalog

```
GET /api/loyalty/catalog
```

### 18.3.7 Flutter loyalty module

Create feature:

```
lib/features/loyalty/
├── data/
├── domain/
├── presentation/
├── bloc/
├── pages/
├── widgets/
└── models/
```

### Loyalty dashboard

Display:

* points balance,

* tier badge,

* progress to next tier,

* recent rewards,

* redemption options,

* streak status.

### Tier progress

Animated circular indicator.

Example:

```
Silver Tier
2,350 / 5,000 points

███████████░░░░░░
```

### Reward history

Timeline:

```
+200 First Wallet Funding
+30 Subscription Renewal
+10 Purchase Reward
-500 Redeemed to Wallet
```

### Redeem screen

Options:

* ₦50 wallet credit,

* ₦100 wallet credit,

* ₦500 wallet credit,

* ₦1,000 wallet credit.

### 18.3.8 Streak system

Reward consistency.

Track:

* consecutive active days,

* consecutive purchases,

* consecutive renewals,

* consecutive Radar interactions.

Example:

```
Current Streak: 12 days

Next Reward:
15 days → +150 points
```

This significantly improves retention.

### 18.3.9 Achievement system

Examples:

* First Purchase

* First Renewal

* Wallet Champion

* Referral Master

* Radar Guardian

* Data Optimizer

* Power User

* Platinum Founder

Achievements award bonus points and create notifications.

### 18.3.10 Founder analytics integration

Extend founder dashboard with:

### Loyalty metrics

* Total points issued

* Total points redeemed

* Outstanding liability

* Active loyalty users

* Average points per user

* Tier distribution

* Redemption rate

* Retention uplift

### Tier distribution chart

```
Bronze    ███████████ 65%
Silver    █████       23%
Gold      ██          9%
Platinum  █           3%
```

This helps optimize reward economics.

### 18.3.11 Notification integration

Trigger notifications for:

* points earned,

* tier upgrade,

* streak milestone,

* redemption successful,

* achievement unlocked,

* promotional bonus.

Examples:

```
You earned 30 points for renewing your MTN Data subscription.

Congratulations! You are now a Gold member.

500 points have been converted into ₦50 wallet credit.
```

### 18.3.12 Business impact

This system increases:

* purchase frequency,

* subscription renewals,

* wallet funding,

* referral activity,

* user retention,

* customer lifetime value.

It also creates a measurable loyalty asset that competitors cannot easily replicate.

### Git commit

Bash

```
git add .
git commit -m "feat(loyalty): add rewards and loyalty system with tiers and redemption"
git push origin main
```

After this milestone, YouStayOn will have a fully integrated loyalty ecosystem across Laravel and Flutter, connected to wallets, purchases, subscriptions, referrals, notifications, analytics, and the founder dashboard—giving the platform a powerful retention engine before large-scale user acquisition.
