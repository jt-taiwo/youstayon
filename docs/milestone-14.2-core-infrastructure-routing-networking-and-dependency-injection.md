Excellent. This is the right point to begin Milestone 14.2 — Flutter Core Infrastructure. Your Laravel backend is now in a production-grade state with 226 passing tests, Sanctum authentication, wallets, purchases, subscriptions, Radar Intelligence, notifications, analytics, and dashboard APIs. From this point forward, the Flutter app will be built as a client of that backend, not as a prototype.

We are going to mirror the backend architecture 100%. The Flutter app will use feature-first Clean Architecture, and every backend domain will have a corresponding Flutter feature module.

### Milestone 14.2: Flutter core infrastructure

We are building the foundation that every feature will use: environment configuration, networking, secure authentication, routing, dependency injection, and the application bootstrap flow.

### Backend → Flutter architecture mapping

| Laravel Domain      | Flutter Feature |
| ------------------- | --------------- |
| Authentication      | auth            |
| User                | profile         |
| Wallet              | wallet          |
| Purchase            | purchase        |
| Subscription        | subscriptions   |
| Notification        | notifications   |
| Analytics           | analytics       |
| Dashboard           | dashboard       |
| Radar Intelligence  | radar           |
| Core Infrastructure | core            |

This ensures that when we call `/api/auth/login`, `/api/dashboard/overview`, `/api/subscriptions`, or `/api/purchases`, the Flutter layer maps directly to the Laravel service layer we already built.

### Step 14.2.1 — Environment configuration

We will support multiple environments from day one: local, staging, and production.

Create:

```
lib/core/config/
├── app_environment.dart
└── app_config.dart
```

Create `lib/core/config/app_environment.dart`.

dart

```
enum AppEnvironment {
  development,
  staging,
  production,
}
```

Create `lib/core/config/app_config.dart`.

dart

```
import 'app_environment.dart';

final class AppConfig {
  AppConfig._();

  static AppEnvironment environment = AppEnvironment.development;

  static String get baseUrl {
    switch (environment) {
      case AppEnvironment.development:
        // Android emulator
        return 'http://10.0.2.2:8000/api';

      case AppEnvironment.staging:
        return 'https://staging.youstayon.com/api';

      case AppEnvironment.production:
        return 'https://api.youstayon.com/api';
    }
  }

  static bool get isDevelopment =>
      environment == AppEnvironment.development;

  static bool get isProduction =>
      environment == AppEnvironment.production;
}
```

For a physical Android device on the same Wi-Fi network, we will later change the development URL to your computer's local IP.

### Step 14.2.2 — API endpoints

Create:

```
lib/core/network/api_endpoints.dart
```

dart

```
final class ApiEndpoints {
  ApiEndpoints._();

  // Authentication
  static const login = '/auth/login';
  static const register = '/auth/register';
  static const logout = '/auth/logout';
  static const me = '/auth/me';
  static const forgotPassword = '/auth/forgot-password';

  // Dashboard
  static const dashboardOverview = '/dashboard/overview';
  static const dashboardSnapshot = '/dashboard/snapshot';
  static const radarScore = '/dashboard/radar-score';
  static const recentActivity = '/dashboard/recent-activity';

  // Wallet
  static const wallet = '/wallet';
  static const walletFund = '/wallet/fund';

  // Purchases
  static const purchases = '/purchases';

  // Subscriptions
  static const subscriptions = '/subscriptions';

  // Notifications
  static const notifications = '/notifications';

  // Analytics
  static const analyticsDashboard = '/analytics/dashboard';
}
```

These endpoints exactly mirror the Laravel routes we implemented.

### Step 14.2.3 — Secure token storage

Create:

```
lib/core/storage/
└── secure_storage_service.dart
```

dart

```
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

final class SecureStorageService {
  SecureStorageService(this._storage);

  final FlutterSecureStorage _storage;

  static const _tokenKey = 'auth_token';
  static const _userKey = 'user_json';

  Future<void> saveToken(String token) async {
    await _storage.write(key: _tokenKey, value: token);
  }

  Future<String?> getToken() {
    return _storage.read(key: _tokenKey);
  }

  Future<void> deleteToken() async {
    await _storage.delete(key: _tokenKey);
  }

  Future<void> saveUserJson(String json) async {
    await _storage.write(key: _userKey, value: json);
  }

  Future<String?> getUserJson() {
    return _storage.read(key: _userKey);
  }

  Future<void> clearAll() async {
    await _storage.deleteAll();
  }
}
```

This stores the Laravel Sanctum token securely on Android and iOS.

### Step 14.2.4 — Dio API client

Create:

```
lib/core/network/
├── dio_client.dart
└── auth_interceptor.dart
```

Create `lib/core/network/auth_interceptor.dart`.

dart

```
import 'package:dio/dio.dart';
import '../storage/secure_storage_service.dart';

final class AuthInterceptor extends Interceptor {
  AuthInterceptor(this._storage);

  final SecureStorageService _storage;

  @override
  Future<void> onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    final token = await _storage.getToken();

    if (token != null && token.isNotEmpty) {
      options.headers['Authorization'] = 'Bearer $token';
    }

    options.headers['Accept'] = 'application/json';

    handler.next(options);
  }
}
```

Create `lib/core/network/dio_client.dart`.

dart

```
import 'package:dio/dio.dart';
import 'package:pretty_dio_logger/pretty_dio_logger.dart';

import '../config/app_config.dart';
import '../storage/secure_storage_service.dart';
import 'auth_interceptor.dart';

final class DioClient {
  DioClient(this._storage) {
    dio = Dio(
      BaseOptions(
        baseUrl: AppConfig.baseUrl,
        connectTimeout: const Duration(seconds: 15),
        receiveTimeout: const Duration(seconds: 15),
        sendTimeout: const Duration(seconds: 15),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      ),
    );

    dio.interceptors.add(AuthInterceptor(_storage));

    if (AppConfig.isDevelopment) {
      dio.interceptors.add(
        PrettyDioLogger(
          requestHeader: true,
          requestBody: true,
          responseHeader: false,
          responseBody: true,
          error: true,
        ),
      );
    }
  }

  final SecureStorageService _storage;
  late final Dio dio;
}
```

This client is now capable of calling every Laravel endpoint with automatic token injection.

### Step 14.2.5 — Dependency injection

Create:

```
lib/core/di/
├── injection.dart
└── injection.config.dart   (generated)
```

Create `lib/core/di/injection.dart`.

dart

```
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get_it/get_it.dart';

import '../network/dio_client.dart';
import '../storage/secure_storage_service.dart';

final getIt = GetIt.instance;

Future<void> configureDependencies() async {
  getIt.registerLazySingleton(
    () => const FlutterSecureStorage(),
  );

  getIt.registerLazySingleton(
    () => SecureStorageService(getIt()),
  );

  getIt.registerLazySingleton(
    () => DioClient(getIt()),
  );
}
```

We are starting manually. We can migrate to `injectable` code generation later once the module graph grows.

### Step 14.2.6 — GoRouter navigation

Create:

```
lib/core/router/
├── app_router.dart
└── route_names.dart
```

Create `lib/core/router/route_names.dart`.

dart

```
final class RouteNames {
  RouteNames._();

  static const splash = '/';
  static const login = '/login';
  static const register = '/register';
  static const home = '/home';
}
```

Create `lib/core/router/app_router.dart`.

dart

```
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../../features/auth/presentation/pages/login_page.dart';
import '../../features/dashboard/presentation/pages/home_page.dart';
import '../../features/splash/presentation/pages/splash_page.dart';
import 'route_names.dart';

final class AppRouter {
  AppRouter._();

  static final GoRouter router = GoRouter(
    initialLocation: RouteNames.splash,
    routes: [
      GoRoute(
        path: RouteNames.splash,
        builder: (context, state) => const SplashPage(),
      ),
      GoRoute(
        path: RouteNames.login,
        builder: (context, state) => const LoginPage(),
      ),
      GoRoute(
        path: RouteNames.home,
        builder: (context, state) => const HomePage(),
      ),
    ],
  );
}
```

### Step 14.2.7 — Create feature shells

Create the initial feature structure:

```
lib/features/
├── splash/
│   └── presentation/pages/splash_page.dart
├── auth/
│   └── presentation/pages/login_page.dart
└── dashboard/
    └── presentation/pages/home_page.dart
```

Create `splash_page.dart`.

dart

```
import 'package:flutter/material.dart';

class SplashPage extends StatelessWidget {
  const SplashPage({super.key});

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      body: Center(
        child: CircularProgressIndicator(),
      ),
    );
  }
}
```

Create `login_page.dart`.

dart

```
import 'package:flutter/material.dart';

class LoginPage extends StatelessWidget {
  const LoginPage({super.key});

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      body: Center(
        child: Text('Login'),
      ),
    );
  }
}
```

Create `home_page.dart`.

dart

```
import 'package:flutter/material.dart';

class HomePage extends StatelessWidget {
  const HomePage({super.key});

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      body: Center(
        child: Text('Home'),
      ),
    );
  }
}
```

### Step 14.2.8 — Bootstrap the application

Replace `lib/main.dart` with:

dart

```
import 'package:flutter/material.dart';

import 'core/di/injection.dart';
import 'core/router/app_router.dart';
import 'core/theme/app_theme.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();

  await configureDependencies();

  runApp(const YouStayOnApp());
}

class YouStayOnApp extends StatelessWidget {
  const YouStayOnApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(
      title: 'YouStayOn',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.light(),
      routerConfig: AppRouter.router,
    );
  }
}
```

### Step 14.2.9 — Verify infrastructure

Run:

Bash

```
flutter analyze
flutter run
```

Expected behavior:

* App launches successfully

* Theme loads correctly

* Splash page displays

* GoRouter is active

* Dependency injection initializes

* Dio client is constructed

* Secure storage service is available

### Git commit

From C:\YouStayOn:

Bash

```
git add .
git commit -m "feat(mobile): add core infrastructure, routing, networking, and dependency injection"
git push origin main
```

At this point, Flutter is no longer a blank project. It is a production-ready client architecture connected to the Laravel backend foundation.

The next milestone is Milestone 14.3 — Authentication Module, where we will implement the complete Laravel Sanctum flow: login, register, logout, current user, token persistence, authenticated routing, form validation, BLoC state management, error handling, loading states, and automatic session restoration exactly matching the backend APIs we already built.
