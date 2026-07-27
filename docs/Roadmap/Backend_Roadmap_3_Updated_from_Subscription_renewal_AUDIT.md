Excellent. The usage capability is now complete and committed/pushed. The next capability should be **renewal history / auditability**, exactly as you proposed.

The current state is strong: subscription creation, lifecycle operations, usage recording, usage history, usage summaries, usage limits, exhaustion processing, expiry processing, and renewal are already covered by tests. The renewal service also already preserves the original subscription, so we should now make that preservation **auditable and queryable**, rather than redesigning renewal itself.

## 1. Next implementation: Renewal History / Auditability

### Goal

When a subscription is renewed:

```text
Original Subscription
        │
        │ renewal
        ▼
New Subscription
        │
        └── linked to original subscription
```

The original record must remain unchanged.

Example:

```text
Subscription A
├── Started: 01 July
├── Expires: 31 July
├── Status: EXPIRED
└── Renewed → Subscription B

Subscription B
├── Started: 01 August
├── Expires: 31 August
├── Status: ACTIVE
└── Renewed from: Subscription A
```

This gives us an auditable chain:

```text
A → B → C → D
```

### Recommended implementation

I recommend adding a self-referencing relationship to `subscriptions`:

```text
subscriptions
├── id
├── uuid
├── user_id
├── subscription_category_id
├── renewed_from_subscription_id nullable
├── ...
```

Then:

```php
$subscription->renewedFrom
$subscription->renewals
```

This preserves the original subscription while allowing the system to answer:

* What subscription was this renewed from?
* How many times has this subscription been renewed?
* What is the full renewal history?
* What was the original subscription in this chain?
* When did each renewal occur?

The renewal service should create a **new subscription record**, setting:

```text
renewed_from_subscription_id = original_subscription_id
```

The original subscription remains immutable.

We should implement this with:

1. Database migration
2. Model relationship
3. Repository support
4. Renewal service update
5. API resource representation
6. Renewal-history endpoint
7. Feature tests
8. Full regression test
9. Commit and push

This is the next capability I recommend implementing.

---

# Updated YouStayOn Roadmap

Based on the project direction and the architecture already established, I would organize the remaining work as follows.

## Phase 1 — Subscription Intelligence Core

### Completed

* ✅ Subscription categories
* ✅ Create subscription
* ✅ List subscriptions
* ✅ View subscription
* ✅ Cancel subscription
* ✅ Subscription expiry processing
* ✅ Renewal
* ✅ Subscription usage recording
* ✅ Usage history
* ✅ Usage summary
* ✅ Usage limit enforcement
* ✅ Exhaustion detection and processing
* ✅ Renewal preserves original subscription

### Next

* [ ] **Renewal history / auditability**
* [ ] Subscription timeline
* [ ] Subscription lifecycle event history
* [ ] Subscription state-transition audit log
* [ ] Subscription notification/reminder engine
* [ ] Expiry warning thresholds
* [ ] Usage threshold alerts
* [ ] Renewal reminders
* [ ] Subscription intelligence/radar calculations

The immediate next task is **renewal history**.

---

# Phase 2 — Radar Intelligence

This is the core differentiating feature of YouStayOn.

The system should transform raw subscription data into intelligence.

### Planned capabilities

* [ ] Days remaining calculation
* [ ] Expiry risk classification
* [ ] Usage velocity calculation
* [ ] Estimated exhaustion date
* [ ] Usage trend analysis
* [ ] Subscription health score
* [ ] Risk level:

  * Safe
  * Monitor
  * Warning
  * Critical
  
* [ ] Radar intelligence summary
* [ ] Dashboard intelligence cards
* [ ] Radar feed
* [ ] Radar history

Example:

```text
Data Subscription
├── 20 GB total
├── 15 GB used
├── 5 GB remaining
├── 10 days remaining
└── Average daily usage: 2 GB

Radar:
⚠ High exhaustion risk
Estimated exhaustion: 2.5 days
Subscription expiry: 10 days
```

This is likely one of the most important backend domains in the MVP.

---

# Phase 3 — Reminder and Notification Engine

Once the system can calculate intelligence, it can notify users.

### Core capabilities

* [ ] Notification domain
* [ ] Notification records
* [ ] In-app notifications
* [ ] Expiry reminders
* [ ] Usage exhaustion warnings
* [ ] Renewal reminders
* [ ] Notification preferences
* [ ] Notification read/unread state
* [ ] Notification history
* [ ] Queue-based delivery
* [ ] Scheduled reminder jobs
* [ ] Idempotent notification rules

The architecture should eventually support:

```text
Scheduler
    ↓
Reminder Engine
    ↓
Domain Event
    ↓
Notification Listener
    ↓
Queued Notification
```

---

# Phase 4 — Transaction and Utility Operations

The prototype contains broader utility flows including:

* Airtime purchase
* Data purchase
* Cable TV renewal
* Internet subscription
* Electricity

These should eventually be implemented through a reusable transaction architecture.

### Planned

* [ ] Transaction domain
* [ ] Transaction states
* [ ] Transaction reference system
* [ ] Provider abstraction
* [ ] Airtime purchase
* [ ] Data purchase
* [ ] Cable TV renewal
* [ ] Electricity payment
* [ ] Internet subscription payment
* [ ] Provider webhook handling
* [ ] Transaction reconciliation
* [ ] Idempotency
* [ ] Failed transaction recovery

The key architecture should be:

```text
User Request
    ↓
Transaction Service
    ↓
Provider Interface
    ↓
Provider Adapter
    ↓
External Provider
    ↓
Webhook / Callback
    ↓
Transaction Reconciliation
```

No provider-specific logic should leak into the core domain.

---

# Phase 5 — Wallet

The prototype already includes a Wallet Hub.

The wallet should not be implemented as a simple balance column.

### Planned

* [ ] Wallet account
* [ ] Double-entry ledger
* [ ] Wallet transactions
* [ ] Credits
* [ ] Debits
* [ ] Holds
* [ ] Reversals
* [ ] Refunds
* [ ] Transaction reconciliation
* [ ] Wallet audit trail

The proper structure is:

```text
Wallet
    │
    ▼
Ledger
    │
    ├── Debit
    ├── Credit
    ├── Hold
    ├── Release
    └── Reversal
```

This is important because the original project direction is intended to support future financial capabilities.

---

# Phase 6 — Security Center

The prototype includes a Security Center.

### Planned

* ✅ Sanctum authentication
* ✅ Password change
* ✅ Account deletion
* ✅ Token revocation
* ✅ Email verification
* [ ] Active device/session management
* [ ] Login history
* [ ] Security events
* [ ] Suspicious activity detection
* [ ] Account security score
* [ ] Two-factor authentication
* [ ] OTP infrastructure hardening

---

# Phase 7 — User Profile and Preferences

Current profile capability is already partially implemented.

Remaining:

* ✅ Profile retrieval
* ✅ Profile update
* ✅ Avatar upload
* ✅ Avatar removal
* [ ] Notification preferences
* [ ] Subscription preferences
* [ ] Default units
* [ ] Timezone preference
* [ ] Language preference
* [ ] Privacy preferences

---

# Phase 8 — Flutter Application

The backend should remain the current priority until the core API is stable.

Then we implement Flutter using the uploaded prototype as the canonical design system. The prototype contains the major flows including authentication, dashboard, services, wallet, purchases, Radar Intelligence, security, and profile screens. 

### Flutter architecture

```text
lib/
├── core/
├── features/
│   ├── authentication/
│   ├── dashboard/
│   ├── subscriptions/
│   ├── radar/
│   ├── wallet/
│   ├── transactions/
│   ├── security/
│   └── profile/
└── app/
```

Using:

* Clean Architecture
* BLoC
* Repository pattern
* DTO/model mapping
* Secure storage
* API client
* Centralized routing
* Material 3 design system

The prototype should be treated as the official design system rather than merely visual inspiration. 

---

# Phase 9 — Production Infrastructure

Remaining:

* [ ] Environment configuration
* [ ] Queue workers
* [ ] Scheduler
* [ ] Cache strategy
* [ ] Logging
* [ ] Monitoring
* [ ] Error tracking
* [ ] Database backups
* [ ] CI pipeline
* [ ] Automated tests in CI
* [ ] Deployment
* [ ] Database migration strategy
* [ ] Rollback strategy
* [ ] Production health checks

---

# Phase 10 — Security and Compliance Hardening

Remaining:

* [ ] Rate limiting review
* [ ] OWASP review
* [ ] Input validation audit
* [ ] Authorization audit
* [ ] API security audit
* [ ] Sensitive data encryption review
* [ ] Secret management
* [ ] Audit logging
* [ ] Abuse prevention
* [ ] Fraud prevention
* [ ] Data retention rules

---

# Recommended Immediate Sequence

I recommend we continue in this exact order:

```text
1. Renewal History / Auditability       ← NEXT
2. Subscription Timeline
3. Notification / Reminder Engine
4. Radar Intelligence Engine
5. Radar API
6. Transaction Architecture
7. Airtime / Data / Cable / Electricity
8. Wallet Ledger
9. Flutter Application
10. CI/CD + Deployment
11. Security Hardening
12. Production Readiness
```

The **next implementation is Renewal History / Auditability**. We should begin with the database design and migration, then update the model, repository, service, resource, endpoint, and tests in the same test-first progression we have been using.
