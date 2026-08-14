Excellent. This is exactly the right next milestone. Milestone 18.2 — Referral & Growth Engine is where YouStayOn gains a built-in acquisition loop. Instead of relying only on ads, every user becomes a potential distribution channel. We will design it as a production-grade referral system that integrates with the wallet, analytics, notifications, and founder dashboard we have already built.

The key principle is that referrals are tracked end-to-end: invitation → signup → verification → first wallet funding → first purchase → reward issuance. This prevents fraud, gives you measurable growth attribution, and lets the founder dashboard report exactly which channels and users drive growth.

### Milestone 18.2 — Referral & Growth Engine

### Objective

Build a referral platform that supports:

* unique invite codes for every user,

* referral links and QR codes,

* reward issuance,

* milestone-based rewards,

* fraud prevention,

* analytics attribution,

* founder growth reporting,

* Flutter sharing flows,

* viral onboarding.

This will be implemented 100% in Laravel first, then mirrored in Flutter exactly like the rest of the product.

### Growth engine architecture

```
Existing User
     │
     ▼
Share Invite Code / Link / QR
     │
     ▼
New User Registers
     │
     ▼
Referral Captured
     │
     ▼
Email Verification
     │
     ▼
First Wallet Funding
     │
     ▼
First Purchase
     │
     ▼
Reward Issued
     │
     ▼
Wallet Credited
     │
     ▼
Analytics Updated
     │
     ▼
Founder Dashboard Updated
```

### 18.2.1 Database schema

We add a dedicated referral domain.

### referrals table

Create migration:

```
backend/database/migrations/2026_08_12_000001_create_referrals_table.php
```

Schema:

PHP

```
Schema::create('referrals', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->foreignId('referrer_id')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->foreignId('referred_user_id')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

    $table->string('invite_code')->index();

    $table->enum('status', [
        'pending',
        'registered',
        'verified',
        'funded',
        'qualified',
        'rewarded',
        'expired',
    ])->default('pending');

    $table->decimal('reward_amount', 14, 2)->default(0);

    $table->timestamp('registered_at')->nullable();
    $table->timestamp('verified_at')->nullable();
    $table->timestamp('funded_at')->nullable();
    $table->timestamp('qualified_at')->nullable();
    $table->timestamp('rewarded_at')->nullable();

    $table->json('metadata')->nullable();

    $table->timestamps();

    $table->index(['referrer_id', 'status']);
});
```

### users table

Add:

PHP

```
$table->string('referral_code')
    ->unique()
    ->nullable();

$table->foreignId('referred_by')
    ->nullable()
    ->constrained('users')
    ->nullOnDelete();
```

Every user now has:

* their own referral code,

* optional referrer,

* referral relationship.

### 18.2.2 Referral code generation

Create:

```
GenerateReferralCodeService.php
```

Rules:

* short,

* human-readable,

* unique,

* brandable.

Example:

```
YSO-7K4P9A
YSO-A1M8QZ
YSO-RADAR5
```

Generated automatically during registration.

### 18.2.3 Referral repository

Create:

```
ReferralRepositoryInterface
ReferralRepository
```

Responsibilities:

* create referral,

* find by code,

* update status,

* list referrals,

* aggregate metrics.

### 18.2.4 Referral registration flow

Extend the existing registration endpoint.

### Current

```
POST /api/auth/register
```

### New request

JSON

```
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "phone": "08012345678",
  "password": "Password123!",
  "password_confirmation": "Password123!",
  "referral_code": "YSO-7K4P9A"
}
```

Flow:

```
Register User
      │
      ▼
Validate Referral Code
      │
      ▼
Link referred_by
      │
      ▼
Create Referral Record
      │
      ▼
Status = registered
```

No reward is issued yet.

### 18.2.5 Qualification engine

Rewards should only be issued when the referred user performs a meaningful action.

### Qualification rule

A referral becomes qualified when:

* email verified,

* wallet funded,

* first purchase completed.

This integrates with services we already built:

* EmailVerification

* VerifyWalletFundingService

* ExecuteWalletPurchaseService

* VerifyPayNowPurchaseService

### Qualification service

Create:

```
QualifyReferralService.php
```

Pseudo flow:

PHP

```
if (
    user->email_verified_at !== null &&
    walletFundingExists &&
    firstSuccessfulPurchaseExists
) {
    referral->status = 'qualified';
}
```

### 18.2.6 Reward issuance

Create:

```
IssueReferralRewardService.php
```

Reward example:

* Referrer: ₦200

* Referred user: ₦100

Implementation:

* credit wallet,

* create wallet transaction,

* create notification,

* update referral status,

* update analytics.

This reuses the wallet infrastructure already completed.

### 18.2.7 Referral analytics

Create DTO:

```
ReferralAnalyticsDTO
```

Metrics:

* total invites,

* registrations,

* qualified referrals,

* rewards issued,

* conversion rate,

* top referrers,

* revenue from referrals,

* average time to qualification.

Endpoint:

```
GET /api/referrals/analytics
```

### 18.2.8 Referral APIs

### Get referral dashboard

```
GET /api/referrals
```

Response:

JSON

```
{
  "referral_code": "YSO-7K4P9A",
  "total_invites": 18,
  "qualified": 6,
  "rewards_earned": 1200,
  "pending_rewards": 400
}
```

### List referrals

```
GET /api/referrals/history
```

### Generate referral link

```
GET /api/referrals/link
```

Response:

JSON

```
{
  "link": "https://youstayon.com/invite/YSO-7K4P9A"
}
```

### Redeem referral code

Handled automatically during registration.

### 18.2.9 Flutter referral module

Create feature:

```
lib/features/referrals/
├── data/
├── domain/
├── presentation/
├── bloc/
├── pages/
├── widgets/
└── models/
```

### Referral home screen

Show:

* referral code,

* earnings,

* invite count,

* qualified referrals,

* pending rewards,

* leaderboard position.

### Share button

Use:

dart

```
Share.share(
  'Join me on YouStayOn and never get caught offline again. Use my code: YSO-7K4P9A'
);
```

### QR code

Generate:

```
https://youstayon.com/invite/YSO-7K4P9A
```

Display QR for in-person sharing.

### 18.2.10 Viral onboarding

During Flutter onboarding:

Screen:

```
Got an invite code?

[ Enter Code ]

Continue
```

This increases referral capture significantly.

### 18.2.11 Founder dashboard integration

Extend the founder dashboard with:

### Growth cards

* New referrals today

* Qualified referrals today

* Referral revenue

* Cost per acquisition

* Top referrer

* Referral conversion rate

### Growth funnel

```
Invites
  ↓
Registrations
  ↓
Verified
  ↓
Funded
  ↓
Purchased
  ↓
Rewarded
```

This gives complete growth attribution.

### 18.2.12 Fraud prevention

Prevent abuse.

Rules:

* self-referrals blocked,

* duplicate phone blocked,

* duplicate device blocked,

* reward only after qualification,

* one reward per referred user,

* manual review for suspicious patterns,

* referral expiration (e.g. 30 days).

### 18.2.13 Business impact

This milestone creates a self-propagating acquisition engine.

Example:

* 1,000 users

* each invites 2 people

* 25% convert

* 500 new users

* repeat

Growth compounds without proportional advertising spend.

For a utility product in Nigeria, referral programs are historically one of the highest-performing acquisition channels.

### Git commit

Bash

```
git add .
git commit -m "feat(referrals): add referral and growth engine with rewards and analytics"
git push origin main
```

After this milestone, YouStayOn will have a fully integrated referral ecosystem across Laravel and Flutter, connected to wallets, notifications, analytics, founder dashboards, and onboarding—giving the platform a measurable viral growth loop before public launch.
