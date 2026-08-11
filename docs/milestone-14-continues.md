Excellent. We are now officially beginning Milestone 14.1 — Flutter Project Foundation, and we will build it the same way we built the Laravel backend: production-grade, step-by-step, no shortcuts, and with Flutter mirroring the Laravel architecture 100%.

This phase is not about building screens first. It is about building a foundation that will support every backend feature we already implemented without future refactoring.

### Milestone 14.1 — Flutter Project Foundation

### Backend-to-Flutter contract (source of truth)

The Flutter app must mirror the Laravel domains exactly.

| Laravel domain | Flutter feature |
| -------------- | --------------- |
| Authentication | authentication  |
| User           | user            |
| Wallet         | wallet          |
| Payment        | payment         |
| Purchase       | purchase        |
| Subscription   | subscription    |
| Notification   | notification    |
| Dashboard      | dashboard       |
| Analytics      | analytics       |
| Intelligence   | intelligence    |

Every DTO, enum, API resource, and workflow we built in Laravel will be represented in Flutter with the same names wherever practical.

### Step 14.1.1 — Create the Flutter project

From the YouStayOn root folder, create the Flutter app inside `mobile/`.

### Commands

Run these commands from C:\YouStayOn.

Bash

```
cd C:\YouStayOn

flutter create mobile
```

When it finishes, verify:

Bash

```
dir
```

You should now have:

```
YouStayOn/
├── backend/
├── mobile/
├── docs/
├── postman/
├── sql/
└── .github/
```

### Step 14.1.2 — Verify Flutter installation

Run:

Bash

```
cd mobile

flutter doctor
```

Send me the output if there are any issues.

### Step 14.1.3 — Replace the default Flutter structure

Inside `mobile/lib`, we will not use Flutter’s default sample structure.

Create this structure exactly.

```
lib/
├── app/
│   ├── di/
│   ├── router/
│   ├── theme/
│   └── core/
│       ├── constants/
│       ├── errors/
│       ├── network/
│       ├── storage/
│       ├── utils/
│       └── widgets/
│
├── features/
│   ├── authentication/
│   ├── user/
│   ├── wallet/
│   ├── payment/
│   ├── purchase/
│   ├── subscription/
│   ├── notification/
│   ├── dashboard/
│   ├── analytics/
│   └── intelligence/
│
├── shared/
│   ├── animations/
│   ├── extensions/
│   ├── models/
│   ├── services/
│   ├── theme/
│   └── widgets/
│
└── main.dart
```

This mirrors the Laravel backend domains exactly.

### Step 14.1.4 — Clean Architecture template for every feature

Each feature will use the same structure.

Example:

```
features/subscription/
├── presentation/
│   ├── pages/
│   ├── widgets/
│   └── bloc/
│
├── domain/
│   ├── entities/
│   ├── repositories/
│   └── usecases/
│
└── data/
    ├── models/
    ├── datasources/
    └── repositories/
```

This is the Flutter equivalent of our Laravel:

* Controllers

* Services

* Repositories

* DTOs

* Models

### Step 14.1.5 — Premium app direction

We are targeting a Moniepoint / Carbon / Revolut level experience.

Design principles:

* Deep navy background

* Emerald success accents

* Electric blue highlights

* Glassmorphism cards

* Rounded 24px surfaces

* Premium typography

* Fast transitions

* Skeleton loading

* Haptic feedback

* Optimistic UI updates

The first screen must immediately showcase Radar Intelligence.

### Step 14.1.6 — Git checkpoint

After creating the Flutter project and folder structure:

Bash

```
cd C:\YouStayOn

git status
```

You should see the new `mobile/` directory tracked.

Then commit:

Bash

```
git add .
git commit -m "feat(mobile): initialize Flutter project foundation"
git push origin main
```

### Your task now

Do these in order:

1. `flutter create mobile`

2. `cd mobile`

3. `flutter doctor`

4. Create the folder structure above

5. `git status`

Send me the flutter doctor output and confirm that the folder structure has been created.

Then we proceed to Step 14.1.7 — Dependency Configuration, where we will install Dio, BLoC, GoRouter, Freezed, Hive, Secure Storage, and dependency injection exactly aligned with the Laravel backend we have completed.
=====================
Perfect. This is a strong place to begin. Your Flutter installation is healthy, and the only issue reported is Visual Studio for Windows desktop builds, which is not a blocker for YouStayOn because we are targeting Android first (and later iOS). We can ignore that for now.

One important observation before we proceed: your repository currently contains three frontend directories:

* `frontend-mobile/`

* `frontend-web/`

* `mobile/` (the new Flutter project we just created)

Earlier, we intentionally preserved `frontend-mobile` because it contained an old Flutter prototype. Since we are now building the production Flutter app from scratch, `mobile/` becomes the canonical Flutter application going forward. We will leave `frontend-mobile` untouched for now as historical reference, but all new work will happen in `C:\YouStayOn\mobile`.

### Milestone 14.1.7 — Dependency Configuration

This step establishes the same architectural discipline we used in Laravel: networking, dependency injection, immutable models, state management, routing, local storage, secure authentication, and code generation.

### 1. Replace `mobile/pubspec.yaml`

Open `mobile/pubspec.yaml` and replace its contents with the following. This gives us the exact dependency set we will use throughout the project.

Edit

name: youstayon_mobile
description: YouStayOn Utility Subscription & Data Expiry Intelligence Platform
publish_to: "none"

version: 1.0.0+1

environment:
sdk: ^3.5.0

dependencies:
flutter:
sdk: flutter

cupertino_icons: ^1.0.8

# State management

flutter_bloc: ^9.1.1
equatable: ^2.0.7

# Networking

dio: ^5.9.0
pretty_dio_logger: ^1.4.0

# Routing

go_router: ^16.2.0

# Dependency injection

get_it: ^8.2.0
injectable: ^2.5.1

# Immutable models

freezed_annotation: ^3.1.0
json_annotation: ^4.9.0

# Secure storage

flutter_secure_storage: ^9.2.4

# Local cache

hive: ^2.2.3
hive_flutter: ^1.1.0

# Connectivity

connectivity_plus: ^6.1.5

# UI

cached_network_image: ^3.4.1
shimmer: ^3.0.0
intl: ^0.20.2

dev_dependencies:
flutter_test:
sdk: flutter

flutter_lints: ^5.0.0

build_runner: ^2.6.0
freezed: ^3.2.0
json_serializable: ^6.11.0
injectable_generator: ^2.8.1
hive_generator: ^2.0.1

flutter:
uses-material-design: true

assets:
- assets/images/
- assets/icons/
- assets/animations/

### 2. Install the dependencies

Run:

Bash

```
cd C:\YouStayOn\mobile

flutter pub get
```

You should see all packages resolve successfully.

### 3. Create the asset directories

Inside `mobile/`, create:

```
assets/
├── images/
├── icons/
└── animations/
```

Flutter will fail to load declared asset paths if the directories do not exist, so create them now.

### 4. Verify package resolution

Run:

Bash

```
flutter pub deps
```

This confirms that:

* flutter_bloc

* dio

* go_router

* get_it

* injectable

* freezed

* hive

* flutter_secure_storage

are all available.

### Why each dependency maps to our Laravel backend

| Flutter package        | Laravel equivalent                        |
| ---------------------- | ----------------------------------------- |
| Dio                    | HTTP client for API Resources             |
| flutter_bloc           | Service orchestration / application state |
| GetIt + Injectable     | Laravel service container                 |
| Freezed                | DTOs and immutable value objects          |
| json_serializable      | API Resource serialization                |
| Hive                   | Cached local projections                  |
| flutter_secure_storage | Sanctum token persistence                 |
| GoRouter               | Route definitions                         |
| Connectivity Plus      | Offline synchronization support           |
| Cached Network Image   | User avatars / provider logos             |
| Shimmer                | Skeleton loading for dashboard            |

This is intentionally designed so that Flutter becomes the client-side mirror of our Laravel architecture.

### Git checkpoint

Once `flutter pub get` succeeds and the asset folders exist:

Bash

```
cd C:\YouStayOn

git status
```

You should see changes inside `mobile/`.

Then commit:

Bash

```
git add .
git commit -m "feat(mobile): configure Flutter foundation dependencies and assets"
git push origin main
```

After you run `flutter pub get`, send me the output (or simply confirm it completed successfully), and we will move immediately to Milestone 14.1.8 — Design System (Colors, Typography, Spacing, Radius, Elevation), where we will build the premium visual language for YouStayOn before writing any screens.
===============









