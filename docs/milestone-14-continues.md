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
================================================================================================================================================================================================================================================================================================
Perfect. I checked your Flutter setup, and you are now in a clean and healthy state. The dependency conflict has been resolved, `flutter pub get` succeeds, and the package graph is consistent. That means we can continue the Flutter implementation exactly as planned, with feature-first Clean Architecture that mirrors the Laravel backend we just completed.

One correction before we proceed: Milestone 14.1.8 (Premium Design System) is the correct next step. The dependency layer is already configured successfully, so we should not revisit it.
================================================================================================================================================================================================================================================================================================
### Milestone 14.1.8: Premium design system

Foundation design tokens that every Flutter feature will use, aligned with the Laravel backend domains and the YouStayOn product identity.

### Why this comes first

Everything in the app will consume the same design tokens: colors, typography, spacing, radii, elevation, and motion. This prevents redesign later and keeps the Flutter frontend aligned with the backend architecture.

Our goal is a product that feels like Moniepoint + Apple Wallet + Notion + Stripe: clean, premium, fast, and trustworthy.

### Design principles

| Principle    | Implementation                        |
| ------------ | ------------------------------------- |
| Trust        | Deep blue foundation                  |
| Success      | Emerald accents                       |
| Urgency      | Amber and red signals                 |
| Intelligence | Purple radar highlights               |
| Speed        | Minimal surfaces and subtle shadows   |
| Clarity      | Large typography and generous spacing |

### Step 14.1.8.1: Create the design folder structure

Inside `mobile/lib/core`, create:

```
lib/
└── core/
    ├── theme/
    │   ├── app_colors.dart
    │   ├── app_text_styles.dart
    │   ├── app_spacing.dart
    │   ├── app_radius.dart
    │   ├── app_shadows.dart
    │   ├── app_theme.dart
    │   └── app_motion.dart
    └── constants/
        └── app_constants.dart
```

This structure mirrors the backend `Core` layer.

### Step 14.1.8.2: Color palette

Create `lib/core/theme/app_colors.dart`.

dart

```
import 'package:flutter/material.dart';

final class AppColors {
  AppColors._();

  // Brand
  static const Color primary = Color(0xFF0B63F6);
  static const Color primaryDark = Color(0xFF0848B5);
  static const Color primaryLight = Color(0xFFEAF2FF);

  // Background
  static const Color background = Color(0xFFF8FAFC);
  static const Color surface = Color(0xFFFFFFFF);
  static const Color surfaceSecondary = Color(0xFFF1F5F9);

  // Text
  static const Color textPrimary = Color(0xFF0F172A);
  static const Color textSecondary = Color(0xFF475569);
  static const Color textMuted = Color(0xFF94A3B8);

  // Status
  static const Color success = Color(0xFF16A34A);
  static const Color warning = Color(0xFFF59E0B);
  static const Color danger = Color(0xFFDC2626);
  static const Color info = Color(0xFF2563EB);

  // Radar Intelligence
  static const Color radar = Color(0xFF7C3AED);
  static const Color radarLight = Color(0xFFF3E8FF);

  // Borders
  static const Color border = Color(0xFFE2E8F0);
  static const Color divider = Color(0xFFE5E7EB);

  // Misc
  static const Color white = Colors.white;
  static const Color black = Colors.black;
}
```

These colors map directly to backend concepts:

* Primary → Wallet / Payments

* Radar → Intelligence Engine

* Success → Completed purchases

* Warning → Expiring subscriptions

* Danger → Failed payments / expired services

### Step 14.1.8.3: Typography scale

Create `lib/core/theme/app_text_styles.dart`.

dart

```
import 'package:flutter/material.dart';
import 'app_colors.dart';

final class AppTextStyles {
  AppTextStyles._();

  static const String fontFamily = 'Inter';

  static const TextStyle displayLarge = TextStyle(
    fontSize: 32,
    fontWeight: FontWeight.w700,
    color: AppColors.textPrimary,
    height: 1.2,
  );

  static const TextStyle displayMedium = TextStyle(
    fontSize: 28,
    fontWeight: FontWeight.w700,
    color: AppColors.textPrimary,
    height: 1.25,
  );

  static const TextStyle headingLarge = TextStyle(
    fontSize: 24,
    fontWeight: FontWeight.w700,
    color: AppColors.textPrimary,
    height: 1.3,
  );

  static const TextStyle headingMedium = TextStyle(
    fontSize: 20,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
    height: 1.3,
  );

  static const TextStyle headingSmall = TextStyle(
    fontSize: 18,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
    height: 1.35,
  );

  static const TextStyle bodyLarge = TextStyle(
    fontSize: 16,
    fontWeight: FontWeight.w500,
    color: AppColors.textPrimary,
    height: 1.5,
  );

  static const TextStyle bodyMedium = TextStyle(
    fontSize: 14,
    fontWeight: FontWeight.w400,
    color: AppColors.textPrimary,
    height: 1.5,
  );

  static const TextStyle bodySmall = TextStyle(
    fontSize: 12,
    fontWeight: FontWeight.w400,
    color: AppColors.textSecondary,
    height: 1.4,
  );

  static const TextStyle label = TextStyle(
    fontSize: 12,
    fontWeight: FontWeight.w600,
    color: AppColors.textSecondary,
    letterSpacing: 0.3,
  );

  static const TextStyle button = TextStyle(
    fontSize: 16,
    fontWeight: FontWeight.w600,
    color: AppColors.white,
    height: 1.2,
  );
}
```

We will use Inter because it performs extremely well across Android and iOS and matches modern fintech products.

### Step 14.1.8.4: Spacing system

Create `lib/core/theme/app_spacing.dart`.

dart

```
final class AppSpacing {
  AppSpacing._();

  static const double xs = 4;
  static const double sm = 8;
  static const double md = 12;
  static const double lg = 16;
  static const double xl = 20;
  static const double xxl = 24;
  static const double xxxl = 32;
  static const double section = 40;
  static const double screen = 24;
}
```

Every screen, card, button, and list will use these values.

### Step 14.1.8.5: Radius system

Create `lib/core/theme/app_radius.dart`.

dart

```
import 'package:flutter/material.dart';

final class AppRadius {
  AppRadius._();

  static const Radius sm = Radius.circular(8);
  static const Radius md = Radius.circular(12);
  static const Radius lg = Radius.circular(16);
  static const Radius xl = Radius.circular(20);
  static const Radius pill = Radius.circular(999);

  static const BorderRadius radiusSm = BorderRadius.all(sm);
  static const BorderRadius radiusMd = BorderRadius.all(md);
  static const BorderRadius radiusLg = BorderRadius.all(lg);
  static const BorderRadius radiusXl = BorderRadius.all(xl);
  static const BorderRadius radiusPill = BorderRadius.all(pill);
}
```

### Step 14.1.8.6: Shadow tokens

Create `lib/core/theme/app_shadows.dart`.

dart

```
import 'package:flutter/material.dart';

final class AppShadows {
  AppShadows._();

  static const List<BoxShadow> sm = [
    BoxShadow(
      color: Color(0x0A000000),
      blurRadius: 8,
      offset: Offset(0, 2),
    ),
  ];

  static const List<BoxShadow> md = [
    BoxShadow(
      color: Color(0x14000000),
      blurRadius: 16,
      offset: Offset(0, 6),
    ),
  ];

  static const List<BoxShadow> lg = [
    BoxShadow(
      color: Color(0x1A000000),
      blurRadius: 24,
      offset: Offset(0, 10),
    ),
  ];
}
```

### Step 14.1.8.7: Motion language

Create `lib/core/theme/app_motion.dart`.

dart

```
import 'package:flutter/animation.dart';

final class AppMotion {
  AppMotion._();

  static const Duration fast = Duration(milliseconds: 150);
  static const Duration normal = Duration(milliseconds: 250);
  static const Duration slow = Duration(milliseconds: 400);

  static const Curve standardCurve = Curves.easeOutCubic;
  static const Curve emphasizedCurve = Curves.easeInOutCubic;
}
```

Navigation, cards, modals, and Radar animations will all use these timings.

### Step 14.1.8.8: Application theme

Create `lib/core/theme/app_theme.dart`.

dart

```
import 'package:flutter/material.dart';
import 'app_colors.dart';
import 'app_radius.dart';
import 'app_text_styles.dart';

final class AppTheme {
  AppTheme._();

  static ThemeData light() {
    return ThemeData(
      useMaterial3: true,
      scaffoldBackgroundColor: AppColors.background,
      colorScheme: ColorScheme.fromSeed(
        seedColor: AppColors.primary,
        brightness: Brightness.light,
        primary: AppColors.primary,
        secondary: AppColors.radar,
        surface: AppColors.surface,
      ),
      appBarTheme: const AppBarTheme(
        backgroundColor: AppColors.background,
        elevation: 0,
        centerTitle: false,
        foregroundColor: AppColors.textPrimary,
      ),
      textTheme: const TextTheme(
        displayLarge: AppTextStyles.displayLarge,
        displayMedium: AppTextStyles.displayMedium,
        headlineLarge: AppTextStyles.headingLarge,
        headlineMedium: AppTextStyles.headingMedium,
        headlineSmall: AppTextStyles.headingSmall,
        bodyLarge: AppTextStyles.bodyLarge,
        bodyMedium: AppTextStyles.bodyMedium,
        bodySmall: AppTextStyles.bodySmall,
        labelLarge: AppTextStyles.button,
        labelMedium: AppTextStyles.label,
      ),
      cardTheme: CardThemeData(
        color: AppColors.surface,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: AppRadius.radiusLg,
          side: const BorderSide(color: AppColors.border),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: AppColors.surface,
        border: OutlineInputBorder(
          borderRadius: AppRadius.radiusLg,
          borderSide: const BorderSide(color: AppColors.border),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: AppRadius.radiusLg,
          borderSide: const BorderSide(color: AppColors.border),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: AppRadius.radiusLg,
          borderSide: const BorderSide(
            color: AppColors.primary,
            width: 1.5,
          ),
        ),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.primary,
          foregroundColor: AppColors.white,
          shape: RoundedRectangleBorder(
            borderRadius: AppRadius.radiusLg,
          ),
          padding: const EdgeInsets.symmetric(
            horizontal: 20,
            vertical: 16,
          ),
        ),
      ),
    );
  }
}
```

### Step 14.1.8.9: Wire the theme into main.dart

Replace the generated `lib/main.dart` with:

dart

```
import 'package:flutter/material.dart';
import 'core/theme/app_theme.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const YouStayOnApp());
}

class YouStayOnApp extends StatelessWidget {
  const YouStayOnApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'YouStayOn',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.light(),
      home: const Scaffold(
        body: Center(
          child: Text('YouStayOn'),
        ),
      ),
    );
  }
}
```

### Step 14.1.8.10: Create asset folders

From `mobile/`:

Bash

```
mkdir assets
mkdir assets\images
mkdir assets\icons
mkdir assets\animations
```

### Test the design system

Run:

Bash

```
flutter run
```

You should see a clean white screen with YouStayOn rendered using the premium typography and theme configuration.

### Git commit

Commit from the repository root (`C:\YouStayOn`), not from `mobile`:

Bash

```
cd C:\YouStayOn
git add .
git commit -m "feat(mobile): add premium design system and application theme"
git push origin main
```

This keeps the Flutter work tracked alongside the Laravel backend, which is exactly the repository strategy we want.

After this commit, we proceed to Milestone 14.2 — Flutter Core Infrastructure, where we will build environment configuration, Dio networking, secure token storage, authenticated API client, GoRouter navigation, dependency injection, and the splash/auth bootstrap flow that connects directly to the Laravel Sanctum backend we already completed.

============================ 































