# YouStayOn — Milestone 14 Flutter Frontend Foundation (Master Continuation Prompt)

We are continuing the YouStayOn project from the completed Laravel backend. Treat this as a strict continuation of the same production-grade architecture.

## Project identity

**YouStayOn — Utility Subscription & Data Expiry Intelligence Platform**

Tagline: **Never Get Caught Offline**

The backend is complete through **Milestone 13** and all tests are green.

Current backend status: **226 tests passed, 563 assertions**.

Do not redesign backend architecture. Flutter must mirror it exactly.

## Backend stack (already implemented)

* Laravel 12

* MySQL

* Sanctum authentication

* Controller → Service → Repository architecture

* DTOs

* API Resources

* Form Requests

* Enums

* Domain-driven module organization

Backend domains already exist:

* Authentication

* User

* Wallet

* Payment

* Purchase

* Subscription

* Notification

* Dashboard

* Analytics

* Intelligence

## Flutter objective

Build a **premium production-grade Flutter application** that mirrors every backend domain and workflow.

The Flutter architecture must be feature-first and scalable enough for future modules such as:

* Savings

* Loans

* Insurance

* Investments

* Rewards

* Referrals

* AI Assistant

## Required Flutter architecture

Use:

* Flutter latest stable

* flutter_bloc

* dio

* freezed

* json_serializable

* get_it

* injectable

* go_router

* flutter_secure_storage

* hive

* connectivity_plus

* cached_network_image

* shimmer

* intl

* equatable

Project structure:

lib/
app/
router/
theme/
di/
core/
features/
authentication/
user/
wallet/
payment/
purchase/
subscription/
notification/
dashboard/
analytics/
intelligence/
shared/
widgets/
animations/
network/
storage/
utils/
main.dart

## Layer architecture

Each feature must contain:

presentation/
pages/
widgets/
bloc/
domain/
entities/
repositories/
usecases/
data/
models/
datasources/
repositories/

Mirror Laravel DTOs and API Resources exactly.

## Navigation architecture

Use **GoRouter**.

Bottom navigation:

* Home

* Subscriptions

* Wallet

* Intelligence

* Profile

## UX requirements

The app must look comparable to:

* Moniepoint

* Carbon

* Revolut

* Apple Wallet

Design language:

* Deep navy / midnight background

* Emerald success accents

* Electric blue highlights

* Glassmorphism cards

* Rounded 20–24px surfaces

* Premium typography

* 60fps animations

* Skeleton loading

* Optimistic updates

* Haptic feedback

* Instant navigation

## Home screen (first experience)

Hero card:

* Radar Score

* Expiring subscriptions

* Predicted monthly spend

* Potential savings

* Renew button

Dashboard sections:

* Recent activity

* Usage trends

* Spending analytics

* Upcoming renewals

* Intelligent recommendation

## Subscription screens

Must support:

* List subscriptions

* Create

* View details

* Usage history

* Usage summary

* Record usage

* Prediction

* Cheapest equivalent recommendation

* Renewal history

* Renew

* Cancel

## Wallet screens

Must support:

* Balance

* Fund wallet

* Wallet transactions

* Purchase with wallet

## Payment screens

Must support:

* Pay Now initialization

* Checkout URL handling

* Verification flow

## Purchase screens

Must support:

* Airtime

* Data

* Electricity

* Cable

* Internet

The provider layer is currently abstracted through the fake provider architecture.

Flutter must not depend on a specific telecom provider.

## Notification screens

Must support:

* List notifications

* Mark read

* Mark all read

* Unread count

* Preferences

* Quiet hours

## Dashboard / Analytics

Consume existing backend endpoints:

* /dashboard

* /dashboard/overview

* /dashboard/snapshot

* /dashboard/radar-score

* /dashboard/category-breakdown

* /dashboard/spending

* /dashboard/usage-trends

Analytics:

* Commerce overview

* Founder dashboard

* Dashboard snapshot

* Provider performance

* Renewal & Radar analytics

## Intelligence

Consume:

GET /api/intelligence

Display:

* Radar score

* Predictive budget

* Conflicts

* Recommendation

* Dashboard-ready summary

## Networking

Create a reusable API client.

Requirements:

* Base URL configuration

* Environment switching

* Auth interceptor

* Token refresh handling

* Error normalization

* Retry strategy

* Logging in debug only

## Storage

Use:

* flutter_secure_storage for tokens

* Hive for cached dashboard and subscriptions

Offline behavior:

Cache:

* Dashboard

* Radar

* Notifications

* Wallet balance

* Subscriptions

Then synchronize automatically when connectivity returns.

## Performance targets

* App launch < 2 seconds

* Navigation < 150 ms

* Cached dashboard < 100 ms

* 60 fps scrolling

* Lazy-loaded lists

* Image caching

* Preloaded bottom tabs

## Development rules

* No hardcoding

* No shortcuts

* Reusable components only

* Strong typing everywhere

* Freezed models

* Repository abstraction

* Dependency injection

* Testable architecture

* Production-grade code only

## Implementation sequence

Begin with **Milestone 14.1 — Flutter Project Foundation**

Tasks:

1. Create Flutter project

2. Configure dependencies

3. Create folder architecture

4. Configure themes

5. Configure typography

6. Configure color system

7. Configure design tokens

8. Configure GoRouter

9. Configure GetIt / Injectable

10. Configure Dio API client

11. Configure secure storage

12. Configure environment configuration

13. Create app shell

14. Create splash screen

15. Create authentication flow scaffold

Proceed strictly step-by-step.

Wait for confirmation after each major milestone.

Do not skip implementation details.

Assume the Laravel backend remains the source of truth for every entity, endpoint, DTO, and workflow.

We are now starting **Milestone 14.1 — Flutter Project Foundation**.

Start the new chat with that prompt, and we’ll build the Flutter frontend in the same production-grade, step-by-step manner we used for the backend, with the architecture mirroring the Laravel implementation exactly.
### I actually recommend that we continue from here, and I think that is the safer choice for this project.

Your concern is valid: we have spent weeks building a Laravel backend with 226 passing tests, 563 assertions, domain-driven architecture, provider abstraction, analytics, intelligence, notifications, auto-renewal, and a large number of interconnected DTOs and endpoints. The biggest risk when moving to Flutter is not forgetting a screen — it is drifting away from the backend contracts (DTOs, API resources, enums, validation rules, pagination formats, authentication flow, and edge-case behavior).

Here is what I propose.

### My recommendation: continue in this same thread

We will treat this conversation as the master implementation thread for YouStayOn. I will not assume anything that is not already established here, and every Flutter module will be built directly against the Laravel backend we just finished.

This gives us several advantages:

* 100% contract fidelity: I can continuously reference the exact backend architecture we implemented, including the fixes we made during testing.

* No context drift: We already resolved dozens of inconsistencies (DTO names, payment fields, repository methods, analytics aggregation, provider abstraction, renewal workflows, etc.). Keeping this thread avoids reintroducing them.

* Incremental verification: After each Flutter feature, we can compare it against the corresponding Laravel feature before moving on.

* Shared terminology: The Flutter app will use the same domain names, enums, statuses, DTOs, and workflows as the backend.

### The rule we will follow

For every Flutter feature, we will mirror the backend 1:1.

| Laravel backend      | Flutter frontend                 |
| -------------------- | -------------------------------- |
| Domain               | Feature module                   |
| Controller           | Remote data source               |
| Service              | Use case                         |
| Repository           | Repository                       |
| DTO / Resource       | Freezed model                    |
| Enum                 | Dart enum                        |
| Validation rules     | Form validation                  |
| Sanctum token flow   | Secure storage + Dio interceptor |
| Dashboard payload    | Dashboard state model            |
| Intelligence payload | Intelligence feature model       |

### Before we write Flutter code

I want to create a Backend-to-Flutter Mapping Ledger inside this thread. This will become the contract document we follow throughout Milestone 14.

It will include:

* Every authenticated endpoint

* Every public endpoint

* Every request payload

* Every response payload

* Every enum

* Every status value

* Every pagination format

* Every dashboard object

* Every analytics object

* Every intelligence object

* Every notification type

* Every subscription lifecycle state

* Wallet and purchase workflows

* Provider abstraction behavior

* Error envelope format

That ledger will ensure that the Flutter implementation cannot silently diverge from the Laravel backend.

### The Flutter architecture we will use

We will still use the premium feature-first architecture we planned, but now it will be generated from the backend rather than from assumptions.

For example:

```
features/
  authentication/
  user/
  wallet/
  payment/
  purchase/
  subscription/
  notification/
  dashboard/
  analytics/
  intelligence/
```

Each feature will be implemented only after we verify the corresponding Laravel module.

### The UX goal

We will build a premium consumer fintech-quality app:

* Moniepoint-level responsiveness

* Carbon/Revolut-style cards

* Glassmorphism surfaces

* Deep navy theme

* Emerald and electric blue accents

* 60fps navigation

* Skeleton loading

* Optimistic updates

* Offline caching

* Haptic feedback

* Instant tab switching

* Preloaded dashboard

And we will not move to the next feature until the current one matches the backend behavior exactly.

So my recommendation is: stay in this thread and proceed to Flutter from here. I think that gives us the highest probability of achieving a 100% faithful Flutter implementation of the Laravel backend we have just completed.

I’m ready to begin Milestone 14.1 — Flutter Project Foundation, starting with the backend-to-Flutter contract ledger and the Flutter architecture scaffold that mirrors every Laravel domain we have implemented.
Agreed. And before we touch Flutter, I want to answer your Git question carefully because it is important.

### You did it the right way

Using the terminal from the YouStayOn root folder (`C:\YouStayOn`) and running:

Bash

```
git add .
git commit -m "..."
git push origin main
```

was actually the correct workflow.

Your repository root is YouStayOn, not YouStayOn/backend. Earlier in the project we intentionally created a monorepo structure:

```
YouStayOn/
├── backend/      (Laravel API)
├── mobile/       (Flutter app)
├── docs/
├── postman/
├── sql/
└── .github/
```

That means Git is tracking the entire project, not just Laravel.

So every time you committed from `C:\YouStayOn`, Git recorded changes in:

* `backend/`

* `docs/`

* `postman/`

* `sql/`

* `.github/`

and any other tracked project files.

That is exactly what we wanted.

### What would have been wrong?

If you had initialized a second Git repository inside `backend/`, that would have caused problems (nested repositories, separate histories, difficult deployment, etc.).

But based on everything we’ve done, we have been using one repository rooted at `YouStayOn`, so nothing is broken on GitHub.

### What we will do for Flutter

We will continue using the same repository.

The Flutter application will live inside:

```
YouStayOn/mobile/
```

So our workflow becomes:

Bash

```
cd C:\YouStayOn

git add .
git commit -m "feat(mobile): add Flutter project foundation"
git push origin main
```

This is actually better than having separate repositories because:

* backend and frontend evolve together,

* API contract changes are committed with the Flutter updates that consume them,

* deployment tags represent complete product versions,

* teammates can clone one repository and get the full stack.

### I recommend one small improvement

Before every commit, run:

Bash

```
git status
```

This lets us verify that we are committing exactly what we expect.

For example:

Bash

```
git status
git add .
git status
git commit -m "feat(mobile): initialize Flutter architecture"
git push origin main
```

That is the workflow I recommend going forward.

### We are now starting Milestone 14

Because we are staying in this thread, I am going to build Flutter directly from the Laravel backend we just completed.

I will treat the Laravel backend as the source of truth, and I will maintain a backend-to-Flutter contract ledger as we go.

### Milestone 14.1 — Flutter Project Foundation

Our objective is to create a production-grade Flutter architecture that mirrors the Laravel domains 100%.

We will begin with:

1. Flutter project initialization

2. Dependency configuration

3. Feature-first folder architecture

4. Design system (colors, typography, spacing, radius, elevation)

5. App theme

6. Routing (GoRouter)

7. Dependency injection (GetIt / Injectable)

8. Dio networking layer

9. Secure token storage

10. Environment configuration

11. App shell

12. Premium splash screen

13. Authentication scaffold

Every file we create will map to an existing Laravel domain.

We proceed from here, and we do not allow Flutter architecture to diverge from the backend architecture at any point.

###
















































v