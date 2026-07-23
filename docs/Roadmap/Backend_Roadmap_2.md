
# Updated YouStayOn Roadmap

Below is the corrected version of your roadmap. I have adjusted the structure to reflect the work actually completed, including the fact that **Account Deletion and Email Verification are complete**, the **Subscription Domain is actively being implemented**, and scheduler execution is deliberately deferred to the Infrastructure phase.

---

# YouStayOn Backend Roadmap

## Phase 0 — Software Architecture

### Milestone 01.1 — Functional Requirements Specification

**Status: ✅ Complete**

Defined the product scope and MVP functionality.

The product direction is:

> **Utility Subscription & Data Expiry Intelligence Platform**

Core product promise:

> **Never Get Caught Offline.**

The platform is designed to manage and monitor utility-related subscriptions such as:

* Mobile data
* Airtime
* Cable TV
* Internet
* Electricity
* Netflix

The broader architecture is designed to support future domains without requiring a major redesign.

---

### Milestone 01.2 — Non-Functional Requirements Specification

**Status: ✅ Defined / Architecture phase**

Requirements include:

* Performance
* Security
* Scalability
* Reliability
* Maintainability
* Testability
* Observability
* Fault tolerance

The backend is being developed with production-grade standards rather than as a throwaway MVP implementation.

---

### Milestone 01.3 — System Architecture Specification

**Status: ✅ Defined**

The current architectural direction is:

```text
Flutter Mobile Application
          ↓
Laravel REST API
          ↓
Domain Layer
          ↓
Application Services / Actions
          ↓
Repositories
          ↓
Eloquent Models
          ↓
MySQL
```

The project follows a domain-oriented modular structure.

Current backend patterns include:

Controller
    ↓
Action / Application Service
    ↓
Domain Service
    ↓
Repository
    ↓
Model
```

Supporting architectural patterns include:

* DTOs
* Form Requests
* API Resources
* Contracts / Interfaces
* Enums
* Domain Exceptions
* API Response Envelopes
* Feature Tests
* Unit Tests

---

### Milestone 01.4 — Database Design Specification

**Status: ✅ Defined**

The database is being designed to support future domains such as:

* Subscriptions
* Radar Intelligence
* Notifications
* Wallet
* Savings
* Loans
* Insurance
* Investments
* Rewards
* Referrals
* AI Assistant

The goal is to avoid major structural redesign as the platform grows.

---

# Phase 1 — Backend Foundation

## Milestone 02.1 — Laravel Project Bootstrap

**Status: ✅ Complete**

Current foundation:

```text
Laravel 12
PHP 8.2+
MySQL
Laravel Sanctum
REST API
```

Established:

* Environment configuration
* API routing
* Database migrations
* Factories
* Seeders
* Testing infrastructure
* Git repository
* Domain-oriented application structure

---

## Milestone 02.2 — Core Architecture

**Status: ✅ Substantially implemented**

Current architecture:

HTTP Request
      ↓
Controller
      ↓
Form Request
      ↓
     DTO
      ↓
Action / Service
      ↓
Repository Contract
      ↓
Repository Implementation
      ↓
Eloquent Model
      ↓
API Resource
      ↓
Standard API Response
```

Established standards include:

* No hardcoding of business logic in controllers
* Dependency injection
* Repository interfaces
* DTO-based data transfer
* Domain-specific exceptions
* Enums for controlled states
* Reusable API resources
* Test-first verification
* Domain-focused directory structure

---

## Milestone 02.3 — Authentication

**Status: ✅ Complete and tested**

Implemented capabilities:

```text
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout
GET  /api/auth/me
POST /api/auth/forgot-password
POST /api/auth/reset-password
```

Authentication is protected by:

```text
Laravel Sanctum
```

The authentication system includes:

* Registration
* Login
* Logout
* Current authenticated user
* Password recovery
* Password reset
* Token management

---

# Phase 2 — User Domain

**Status: ✅ Complete**

---

## Milestone 03.1 — User Profile Management

**Status: ✅ Complete**

```text
GET   /api/profile
PATCH /api/profile
```

Implemented:

* Profile retrieval
* Profile update
* Validation
* Authentication protection
* Feature testing

---

## Milestone 03.2 — Password Management

**Status: ✅ Complete**

```text
PATCH /api/profile/change-password
```

Implemented:

* Current password verification
* Strong password validation
* Password confirmation
* Password hashing
* Revocation of all Sanctum tokens
* Feature testing

---

## Milestone 03.3 — Avatar Management

**Status: ✅ Complete**

```text
POST   /api/profile/avatar
DELETE /api/profile/avatar
```

Implemented:

* Image validation
* Avatar storage
* Avatar replacement
* Previous-file deletion
* Database path management
* Avatar removal
* Storage cleanup
* Authentication protection
* Feature testing

---

## Milestone 03.4 — Account Deletion

**Status: ✅ Complete**

```text
DELETE /api/profile
```

Implemented:

```text
Authenticated User
       ↓
Revoke Sanctum Tokens
       ↓
Remove Avatar File
       ↓
Delete User Account
       ↓
Database Cascade
       ↓
Return Success
```

Test coverage includes:

* Authenticated user can delete account
* Guest cannot delete account
* User is removed from the database
* All Sanctum tokens are revoked
* Avatar file is removed
* Account without an avatar can be deleted

---

## Milestone 03.5 — Email Verification

**Status: ✅ Complete**

Email verification is part of the authentication and account-security foundation.

---

# Phase 3 — Utility Subscription Domain

**Status: 🚧 In Progress**

This is the current development phase.

The Subscription Domain is the foundation upon which the Radar Intelligence system will later operate.

---

# Milestone 04.1 — Subscription Categories

**Status: ✅ Complete**

Current categories support the utility ecosystem, including:

* Mobile Data
* Airtime
* Cable TV
* Internet
* Electricity
* Netflix

The category system is designed to be extensible rather than hardcoded into controllers.

---

# Milestone 04.2 — Subscription Creation

**Status: ✅ Complete**

```text
POST /api/subscriptions
```

Current subscription data includes:

```text
User
Category
Provider
Plan
Amount
Currency
Started At
Expires At
Renewal At
Status
Notes
```

The `Subscription` model currently supports:

```text
ACTIVE
EXPIRED
EXHAUSTED
CANCELLED
```

The status is represented through:

```text
SubscriptionStatus Enum
```

---

# Milestone 04.3 — Subscription Retrieval

**Status: ✅ Complete**

Implemented:

```text
GET /api/subscriptions
GET /api/subscriptions/{uuid}
```

Implemented capabilities:

* List authenticated user's subscriptions
* Retrieve a specific subscription
* Ownership protection
* Category relationship loading
* API resource serialization

---

# Milestone 04.4 — Subscription Cancellation

**Status: ✅ Complete**

```text
POST /api/subscriptions/{uuid}/cancel
```

Business rule:

> Cancellation is not the reversal of a completed recharge.

It means:

> Cancelling a subscription record that is still eligible for cancellation.

The domain model enforces cancellation eligibility through:

```text
Subscription::cancel()
```

The status transition is:

```text
ACTIVE
   ↓
CANCELLED
```

Invalid cancellation attempts are rejected through:

```text
SubscriptionCannotBeCancelledException
```

---

# Milestone 04.5 — Subscription Expiry Processing

**Status: ✅ Complete**

The system can identify subscriptions whose expiry time has passed.

The lifecycle transition is:

```text
ACTIVE
   ↓
expires_at <= now()
   ↓
EXPIRED
```

The expiry-processing capability has been implemented as a reusable processing mechanism rather than controller-specific logic.

---

## Expiry Command

```text
subscriptions:process-expiry
```

The command can process subscriptions that are due to expire.

The scheduler registration exists for periodic execution.

However:

> **The scheduler worker process will not be run as a permanent development-terminal process.**

The actual long-running scheduler execution:

```text
php artisan schedule:work
```

will be handled properly during:

# Phase 8 — Production Infrastructure

This is where we will configure:

* Scheduler workers
* Queue workers
* Process supervisors
* Deployment services
* Restart policies
* Monitoring
* Failure recovery

Therefore, the current development workflow intentionally does **not** require `schedule:work` to remain permanently running in the terminal.

---

# Milestone 04.6 — Subscription Renewal

**Status: ✅ Complete**

Endpoint:

```text
POST /api/subscriptions/{uuid}/renew
```

Implemented business rules:

```text
ACTIVE
   ↓
Can Renew
```

```text
EXPIRED
   ↓
Can Renew
```

```text
EXHAUSTED
   ↓
Can Renew
```

```text
CANCELLED
   ↓
Cannot Renew
```

Renewal behavior:

```text
Original Subscription
        │
        │ remains unchanged
        ▼

New Subscription Record
        │
        ├── New UUID
        ├── Same user
        ├── Same category
        ├── Same provider
        ├── Same plan
        └── New lifecycle period
```

This preserves the original subscription as historical record data.

### Tests

Unit/service-level tests:

```text
7 tests
14 assertions
```

Feature/API-level tests:

```text
7 tests
15 assertions
```

Covered scenarios:

* Active subscription can be renewed
* Expired subscription can be renewed
* Exhausted subscription can be renewed
* Cancelled subscription cannot be renewed
* Nonexistent subscription cannot be renewed
* User cannot renew another user's subscription
* Original subscription remains unchanged
* Guest cannot renew a subscription

---

# Milestone 04.7 — Subscription Usage / Exhaustion Processing

**Status: ⏳ NEXT**

This is the next logical capability.

This is the counterpart to expiry processing.

The system currently knows:

```text
Time-Based Exhaustion
```

through:

```text
expires_at
```

The next capability is:

```text
Usage-Based Exhaustion
```

Conceptually:

```text
Subscription
      ↓
Usage Tracking
      ↓
Usage >= Allowance
      ↓
EXHAUSTED
```

For example:

```text
Data Subscription
      ↓
10 GB Allowance
      ↓
10 GB Used
      ↓
EXHAUSTED
```

The exact implementation should be determined before coding because it affects the data model.

A production-grade implementation should avoid simply adding an arbitrary field without deciding whether YouStayOn needs:

* Usage amount
* Total allowance
* Unit of measurement
* Usage percentage
* Usage history
* Usage events
* Provider-reported usage
* Manual user updates
* Future API integration

Therefore, the next step should be a **usage/exhaustion design decision**, followed by implementation.
==========================================================================
Finally we have successfully completedThe Subscription domain as it  currently has verified coverage for:



Subscription creation

Category retrieval

Subscription retrieval and listing

Cancellation

Renewal

Expiry processing

Usage recording

Exhaustion processing

Authentication and authorization boundaries
---

# Milestone 04.8 — Subscription Update

**Status: ⏳ Planned**

Users should eventually be able to update subscription metadata such as:

```text
Provider
Plan
Amount
Renewal Date
Notes
```

The update capability must preserve lifecycle rules and prevent invalid state transitions.

---

# Milestone 04.9 — Subscription Lifecycle Completion

**Status: ⏳ Planned**

The completed lifecycle should eventually resemble:

```text
CREATE
  ↓
ACTIVE
  │
  ├──────────────→ CANCELLED
  │
  ├──────────────→ EXPIRED
  │
  ├──────────────→ EXHAUSTED
  │
  └──────────────→ RENEWED
                         ↓
                 NEW SUBSCRIPTION
```

---

# Phase 4 — Radar Intelligence

**Status: ⏳ Not Started**

This phase is the primary product differentiator.

The Radar should answer:

> **What do I need to worry about right now?**

---

## Milestone 05.1 — Radar Intelligence Engine

The Radar should aggregate signals from the Subscription Domain:

```text
Subscription
      ↓
Expiry State
      ↓
Usage State
      ↓
Lifecycle State
      ↓
Risk Evaluation
      ↓
Radar Intelligence
```

Example:

```text
Subscription expires tomorrow
        +
High usage
        +
Important utility
        ↓
HIGH RISK
```

---

## Milestone 05.2 — Radar Risk Classification

Initial conceptual classification:

```text
🔴 Critical
🟠 Urgent
🟡 Needs Attention
🟢 Normal
```

The exact classification should be implemented through reusable domain logic rather than hardcoded inside a dashboard controller.

---

## Milestone 05.3 — Radar Dashboard API

The API should eventually provide an intelligence-oriented response such as:

```text
GET /api/radar
```

Conceptually:

```text
YOUR UTILITY RADAR
──────────────────

🔴 Expired
🟠 Expires Soon
🟡 Needs Attention
🟢 Active
```

The Radar should aggregate subscriptions instead of forcing the Flutter application to independently calculate business risk.

---

# Phase 5 — Notification Infrastructure

**Status: ⏳ Not Started**

Reusable architecture:

```text
Domain Event
      ↓
Listener
      ↓
Notification
      ↓
Channel
```

Potential channels:

* In-app
* Email
* Push notifications
* WhatsApp integration

Example:

```text
SubscriptionExpiringSoon
          ↓
Reminder Listener
          ↓
Notification
          ↓
Delivery Channel
```

---

# Phase 6 — Background Processing

**Status: ⏳ Not Started**

This phase will formalize:

## Milestone 07.1 — Scheduler

```text
Scheduled Execution
        ↓
Scan Subscriptions
        ↓
Evaluate Expiry / Usage
        ↓
Generate Domain Events
```

Important architectural decision:

> Scheduler registration may be implemented earlier when a capability requires it, but permanent scheduler execution and operational process management belong to the Infrastructure/Deployment phase.

---

## Milestone 07.2 — Queues and Jobs

```text
API / Scheduler
      ↓
Dispatch Job
      ↓
Queue
      ↓
Worker
      ↓
Process
```

Long-running or asynchronous work will not block API requests.

---

# Phase 7 — Flutter Mobile Application

**Status: ⏳ Not Started**

The Flutter application will mirror the backend domain structure:

```text
features/
├── authentication/
├── profile/
├── subscriptions/
├── radar/
├── notifications/
└── settings/
```

Architecture:

```text
Presentation
      ↓
BLoC
      ↓
Use Case
      ↓
Repository
      ↓
API
```

The uploaded prototype is treated as the canonical design reference. Its design system should be extracted into reusable Flutter design tokens rather than copied as scattered hardcoded UI values. 

---

# Phase 8 — Production Infrastructure

**Status: ⏳ Not Started**

This phase will include:

* CI/CD
* Automated testing pipelines
* Environment promotion
* Production deployment
* Nginx
* PHP-FPM
* Queue workers
* Scheduler workers
* Process supervision
* Database backups
* Monitoring
* Logging
* Error tracking
* Security hardening
* Disaster recovery
* Rollback procedures

This is where the following operational processes will be properly configured:

```text
php artisan schedule:work
```

and:

```text
php artisan queue:work
```

They should not be treated as permanently running manual terminal processes during the current development phase.

---

# Current Project Position

```text
PHASE 0 — Architecture
████████████████████  Complete

PHASE 1 — Backend Foundation
████████████████████  Complete

PHASE 2 — User Domain
████████████████████  Complete

PHASE 3 — Utility Subscription Domain
████████████████░░░░  In Progress

    ├── Subscription Categories       ✅
    ├── Create Subscription            ✅
    ├── List Subscriptions             ✅
    ├── Get Subscription               ✅
    ├── Cancel Subscription            ✅
    ├── Expiry Processing              ✅
    ├── Expiry Command                 ✅
    ├── Scheduler Registration         ✅
    ├── Permanent schedule:work         ⏸ Deferred to infrastructure
    ├── Renew Subscription              ✅
    ├── Usage / Exhaustion Processing   ⏳ Next
    ├── Update Subscription             ⏳ Planned
    └── Lifecycle Completion            ⏳ Planned

PHASE 4 — Radar Intelligence
░░░░░░░░░░░░░░░░░░░░  Not Started

PHASE 5 — Notifications
░░░░░░░░░░░░░░░░░░░░  Not Started

PHASE 6 — Background Processing
░░░░░░░░░░░░░░░░░░░░  Not Started

PHASE 7 — Flutter Application
░░░░░░░░░░░░░░░░░░░░  Not Started

PHASE 8 — Production Infrastructure
░░░░░░░░░░░░░░░░░░░░  Not Started
```

## The Correct Immediate Sequence

```text
1. Commit Renew Subscription
        ↓
2. Design Usage / Exhaustion Model
        ↓
3. Implement Usage / Exhaustion Processing
        ↓
4. Test Subscription Domain
        ↓
5. Commit Subscription Domain milestone
        ↓
6. Begin Radar Intelligence Engine
        ↓
7. Build Radar API
        ↓
8. Build Notification Infrastructure
        ↓
9. Formalize Background Processing
        ↓
10. Build Flutter Application
        ↓
11. Implement Production Infrastructure
```

### My recommendation for your immediate next action

**Commit the completed Renew Subscription capability now.**

Then we should **not immediately code Usage/Exhaustion Processing** until we first make the data-model decision for usage tracking. Your current `Subscription` model and migration currently represent lifecycle dates and status, but do not yet represent a usage allowance or consumed amount. 

The next development step should therefore be:

> **Milestone 04.7 — Design the Subscription Usage / Exhaustion Model**

After that design is agreed, we implement the complete capability with migrations, model changes, domain logic, repository support, API surface if required, and tests.
