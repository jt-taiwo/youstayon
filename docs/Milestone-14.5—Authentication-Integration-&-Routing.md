Excellent. We are now implementing Milestone 14.5 — Authentication Integration & Routing, which is the milestone that turns the Flutter app into a real client for the Laravel Sanctum backend. From this point onward, the mobile app will authenticate directly against your production-ready Laravel API, persist the Sanctum token securely, restore sessions automatically, and protect navigation exactly like a commercial fintech application.

This milestone is intentionally aligned 100% with the Laravel backend we completed. We will not create any Flutter-only authentication logic; every authentication decision will come from the backend.

### Milestone 14.5: Authentication integration & routing

We are connecting the premium authentication UI to the BLoC layer, Sanctum token storage, automatic session restoration, protected routes, and navigation guards so Flutter mirrors the Laravel backend exactly.

### What we are implementing

| Layer                           | Status        |
| ------------------------------- | ------------- |
| Sanctum token persistence       | Implement now |
| Authenticated Dio client        | Implement now |
| Automatic session restoration   | Implement now |
| Splash bootstrap                | Implement now |
| GoRouter route guards           | Implement now |
| Login → Dashboard navigation    | Implement now |
| Register → Dashboard navigation | Implement now |
| Logout → Login navigation       | Implement now |
| Expired token handling          | Implement now |

This is exactly how production Flutter apps integrate with Laravel Sanctum.

### Step 14.5.1 — Secure storage service

Create `lib/core/storage/secure_storage_service.dart`.

dart

```
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SecureStorageService {
  static const _tokenKey = 'auth_token';
  static const _userKey = 'user_json';

  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  Future<void> saveToken(String token) async {
    await _storage.write(key: _tokenKey, value: token);
  }

  Future<String?> getToken() async {
    return _storage.read(key: _tokenKey);
  }

  Future<void> saveUserJson(String json) async {
    await _storage.write(key: _userKey, value: json);
  }

  Future<String?> getUserJson() async {
    return _storage.read(key: _userKey);
  }

  Future<void> clearAll() async {
    await _storage.delete(key: _tokenKey);
    await _storage.delete(key: _userKey);
  }
}
```

This stores the Laravel Sanctum personal access token securely on the device.

### Step 14.5.2 — Auth interceptor

Create `lib/core/network/auth_interceptor.dart`.

dart

```
import 'package:dio/dio.dart';

import '../storage/secure_storage_service.dart';

class AuthInterceptor extends Interceptor {
  AuthInterceptor(this._storage);

  final SecureStorageService _storage;

  @override
  Future<void> onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    final token = await _storage.getToken();

    if (token != null) {
      options.headers['Authorization'] = 'Bearer $token';
      options.headers['Accept'] = 'application/json';
    }

    handler.next(options);
  }

  @override
  Future<void> onError(
    DioException err,
    ErrorInterceptorHandler handler,
  ) async {
    if (err.response?.statusCode == 401) {
      await _storage.clearAll();
    }

    handler.next(err);
  }
}
```

This ensures every authenticated request automatically includes:

http

```
Authorization: Bearer {sanctum_token}
```

which exactly matches your Laravel backend.

### Step 14.5.3 — Update Dio client

Update `lib/core/network/dio_client.dart`.

dart

```
import 'package:dio/dio.dart';
import 'package:pretty_dio_logger/pretty_dio_logger.dart';

import '../storage/secure_storage_service.dart';
import 'auth_interceptor.dart';

class DioClient {
  DioClient(this._storage)
      : dio = Dio(
          BaseOptions(
            baseUrl: 'http://10.0.2.2:8000/api',
            connectTimeout: const Duration(seconds: 30),
            receiveTimeout: const Duration(seconds: 30),
            sendTimeout: const Duration(seconds: 30),
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          ),
        ) {
    dio.interceptors.add(AuthInterceptor(_storage));

    dio.interceptors.add(
      PrettyDioLogger(
        requestBody: true,
        responseBody: true,
      ),
    );
  }

  final SecureStorageService _storage;
  final Dio dio;
}
```

For Android Emulator, `10.0.2.2` correctly points to your local Laravel server.

### Step 14.5.4 — Dependency injection

Update `lib/core/di/injection.dart`.

Register storage first.

dart

```
getIt.registerLazySingleton(
  () => SecureStorageService(),
);

getIt.registerLazySingleton(
  () => DioClient(getIt()),
);
```

The order matters because `DioClient` depends on `SecureStorageService`.

### Step 14.5.5 — Router refresh from AuthBloc

GoRouter must react automatically when authentication changes.

Create `lib/core/router/router_refresh_notifier.dart`.

dart

```
import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class RouterRefreshNotifier extends ChangeNotifier {
  RouterRefreshNotifier(Stream<dynamic> stream) {
    _subscription = stream.asBroadcastStream().listen(
          (_) => notifyListeners(),
        );
  }

  late final StreamSubscription<dynamic> _subscription;

  @override
  void dispose() {
    _subscription.cancel();
    super.dispose();
  }
}
```

### Step 14.5.6 — Update app router

Update `lib/core/router/app_router.dart`.

dart

```
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';

import '../../features/auth/presentation/bloc/auth_bloc.dart';
import '../../features/auth/presentation/bloc/auth_state.dart';
import '../../features/auth/presentation/pages/login_page.dart';
import '../../features/auth/presentation/pages/register_page.dart';
import '../../features/auth/presentation/pages/splash_page.dart';
import '../../features/dashboard/presentation/pages/dashboard_page.dart';
import '../di/injection.dart';
import 'router_refresh_notifier.dart';

class AppRouter {
  static final GoRouter router = GoRouter(
    initialLocation: '/splash',
    refreshListenable: RouterRefreshNotifier(
      getIt<AuthBloc>().stream,
    ),
    redirect: (context, state) {
      final authState = getIt<AuthBloc>().state;

      final loggingIn = state.matchedLocation == '/login';
      final registering = state.matchedLocation == '/register';
      final splash = state.matchedLocation == '/splash';

      if (authState is AuthLoading) {
        return splash ? null : '/splash';
      }

      if (authState is AuthAuthenticated) {
        if (loggingIn || registering || splash) {
          return '/dashboard';
        }
        return null;
      }

      if (authState is AuthUnauthenticated) {
        if (loggingIn || registering) {
          return null;
        }
        return '/login';
      }

      return null;
    },
    routes: [
      GoRoute(
        path: '/splash',
        builder: (_, __) => const SplashPage(),
      ),
      GoRoute(
        path: '/login',
        builder: (_, __) => const LoginPage(),
      ),
      GoRoute(
        path: '/register',
        builder: (_, __) => const RegisterPage(),
      ),
      GoRoute(
        path: '/dashboard',
        builder: (_, __) => const DashboardPage(),
      ),
    ],
  );
}
```

This gives us automatic navigation protection.

### Step 14.5.7 — Splash bootstrap

Update `SplashPage`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../../core/theme/app_colors.dart';
import '../bloc/auth_bloc.dart';
import '../bloc/auth_event.dart';

class SplashPage extends StatefulWidget {
  const SplashPage({super.key});

  @override
  State<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends State<SplashPage> {
  @override
  void initState() {
    super.initState();

    Future.microtask(() {
      context.read<AuthBloc>().add(AppStarted());
    });
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      backgroundColor: AppColors.primary,
      body: Center(
        child: Text(
          'YouStayOn',
          style: TextStyle(
            color: Colors.white,
            fontSize: 28,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
    );
  }
}
```

The splash screen immediately asks Laravel whether the stored Sanctum token is still valid.

### Step 14.5.8 — Connect LoginPage

Inside `LoginPage`, wrap with `BlocListener`.

dart

```
BlocListener<AuthBloc, AuthState>(
  listener: (context, state) {
    if (state is AuthFailure) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(state.message)),
      );
    }
  },
  child: BlocBuilder<AuthBloc, AuthState>(
    builder: (context, state) {
      // existing UI
    },
  ),
)
```

The `Sign in` button already dispatches:

dart

```
context.read<AuthBloc>().add(
  LoginRequested(
    email: _email.text.trim(),
    password: _password.text,
  ),
);
```

After successful login, GoRouter automatically redirects to `/dashboard`.

### Step 14.5.9 — Connect RegisterPage

Do exactly the same:

* Validate fields

* Dispatch `RegisterRequested`

* Listen for `AuthFailure`

* Allow GoRouter to redirect automatically

### Step 14.5.10 — Dashboard placeholder

Create `lib/features/dashboard/presentation/pages/dashboard_page.dart`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../auth/presentation/bloc/auth_bloc.dart';
import '../../../auth/presentation/bloc/auth_event.dart';

class DashboardPage extends StatelessWidget {
  const DashboardPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('YouStayOn'),
        actions: [
          IconButton(
            onPressed: () {
              context.read<AuthBloc>().add(
                    LogoutRequested(),
                  );
            },
            icon: const Icon(Icons.logout),
          ),
        ],
      ),
      body: const Center(
        child: Text(
          'Dashboard Connected',
        ),
      ),
    );
  }
}
```

Logout immediately:

* calls `POST /api/auth/logout`

* clears the Sanctum token

* emits `AuthUnauthenticated`

* redirects to `/login`

### Authentication flow

```
App Launch
     |
     v
Splash
     |
     v
Secure Storage
     |
     +-----------------------+
     |                       |
Token Exists           No Token
     |                       |
     v                       v
GET /api/auth/me        Login Page
     |
     +-----------------------+
     |                       |
Valid Token            Invalid Token
     |                       |
     v                       v
Dashboard              Login Page
     |
     v
Logout
     |
     v
POST /api/auth/logout
     |
     v
Token Cleared
     |
     v
Login Page
```

This is exactly the production flow used by Flutter apps connected to Laravel Sanctum.

### Step 14.5.11 — Backend configuration

Ensure Laravel is running.

Bash

```
cd C:\YouStayOn\backend
php artisan serve
```

If using Android Emulator:

```
Base URL:
http://10.0.2.2:8000/api
```

If using a physical Android device on the same Wi-Fi:

```
http://YOUR_PC_LOCAL_IP:8000/api
```

### Test checklist

Run the Flutter app and verify:

* App opens Splash

* No token → Login page

* Register creates a real Laravel user

* Login returns a real Sanctum token

* Dashboard opens

* App restart restores session automatically

* Logout returns to Login

* Invalid token forces re-authentication

When all of these pass, Flutter will be fully authenticated against the Laravel backend, and every subsequent module (Wallet, Subscriptions, Purchases, Radar Intelligence, Notifications, Dashboard, Analytics) will reuse the same authenticated architecture.

The next milestone is Milestone 14.6 — Excellent. We are now implementing Milestone 14.5 — Authentication Integration & Routing, which is the milestone that turns the Flutter app into a real client for the Laravel Sanctum backend. From this point onward, the mobile app will authenticate directly against your production-ready Laravel API, persist the Sanctum token securely, restore sessions automatically, and protect navigation exactly like a commercial fintech application.

This milestone is intentionally aligned 100% with the Laravel backend we completed. We will not create any Flutter-only authentication logic; every authentication decision will come from the backend.

### Milestone 14.5: Authentication integration & routing

We are connecting the premium authentication UI to the BLoC layer, Sanctum token storage, automatic session restoration, protected routes, and navigation guards so Flutter mirrors the Laravel backend exactly.

### What we are implementing

| Layer                           | Status        |
| ------------------------------- | ------------- |
| Sanctum token persistence       | Implement now |
| Authenticated Dio client        | Implement now |
| Automatic session restoration   | Implement now |
| Splash bootstrap                | Implement now |
| GoRouter route guards           | Implement now |
| Login → Dashboard navigation    | Implement now |
| Register → Dashboard navigation | Implement now |
| Logout → Login navigation       | Implement now |
| Expired token handling          | Implement now |

This is exactly how production Flutter apps integrate with Laravel Sanctum.

### Step 14.5.1 — Secure storage service

Create `lib/core/storage/secure_storage_service.dart`.

dart

```
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SecureStorageService {
  static const _tokenKey = 'auth_token';
  static const _userKey = 'user_json';

  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  Future<void> saveToken(String token) async {
    await _storage.write(key: _tokenKey, value: token);
  }

  Future<String?> getToken() async {
    return _storage.read(key: _tokenKey);
  }

  Future<void> saveUserJson(String json) async {
    await _storage.write(key: _userKey, value: json);
  }

  Future<String?> getUserJson() async {
    return _storage.read(key: _userKey);
  }

  Future<void> clearAll() async {
    await _storage.delete(key: _tokenKey);
    await _storage.delete(key: _userKey);
  }
}
```

This stores the Laravel Sanctum personal access token securely on the device.

### Step 14.5.2 — Auth interceptor

Create `lib/core/network/auth_interceptor.dart`.

dart

```
import 'package:dio/dio.dart';

import '../storage/secure_storage_service.dart';

class AuthInterceptor extends Interceptor {
  AuthInterceptor(this._storage);

  final SecureStorageService _storage;

  @override
  Future<void> onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    final token = await _storage.getToken();

    if (token != null) {
      options.headers['Authorization'] = 'Bearer $token';
      options.headers['Accept'] = 'application/json';
    }

    handler.next(options);
  }

  @override
  Future<void> onError(
    DioException err,
    ErrorInterceptorHandler handler,
  ) async {
    if (err.response?.statusCode == 401) {
      await _storage.clearAll();
    }

    handler.next(err);
  }
}
```

This ensures every authenticated request automatically includes:

http

```
Authorization: Bearer {sanctum_token}
```

which exactly matches your Laravel backend.

### Step 14.5.3 — Update Dio client

Update `lib/core/network/dio_client.dart`.

dart

```
import 'package:dio/dio.dart';
import 'package:pretty_dio_logger/pretty_dio_logger.dart';

import '../storage/secure_storage_service.dart';
import 'auth_interceptor.dart';

class DioClient {
  DioClient(this._storage)
      : dio = Dio(
          BaseOptions(
            baseUrl: 'http://10.0.2.2:8000/api',
            connectTimeout: const Duration(seconds: 30),
            receiveTimeout: const Duration(seconds: 30),
            sendTimeout: const Duration(seconds: 30),
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          ),
        ) {
    dio.interceptors.add(AuthInterceptor(_storage));

    dio.interceptors.add(
      PrettyDioLogger(
        requestBody: true,
        responseBody: true,
      ),
    );
  }

  final SecureStorageService _storage;
  final Dio dio;
}
```

For Android Emulator, `10.0.2.2` correctly points to your local Laravel server.

### Step 14.5.4 — Dependency injection

Update `lib/core/di/injection.dart`.

Register storage first.

dart

```
getIt.registerLazySingleton(
  () => SecureStorageService(),
);

getIt.registerLazySingleton(
  () => DioClient(getIt()),
);
```

The order matters because `DioClient` depends on `SecureStorageService`.

### Step 14.5.5 — Router refresh from AuthBloc

GoRouter must react automatically when authentication changes.

Create `lib/core/router/router_refresh_notifier.dart`.

dart

```
import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class RouterRefreshNotifier extends ChangeNotifier {
  RouterRefreshNotifier(Stream<dynamic> stream) {
    _subscription = stream.asBroadcastStream().listen(
          (_) => notifyListeners(),
        );
  }

  late final StreamSubscription<dynamic> _subscription;

  @override
  void dispose() {
    _subscription.cancel();
    super.dispose();
  }
}
```

### Step 14.5.6 — Update app router

Update `lib/core/router/app_router.dart`.

dart

```
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';

import '../../features/auth/presentation/bloc/auth_bloc.dart';
import '../../features/auth/presentation/bloc/auth_state.dart';
import '../../features/auth/presentation/pages/login_page.dart';
import '../../features/auth/presentation/pages/register_page.dart';
import '../../features/auth/presentation/pages/splash_page.dart';
import '../../features/dashboard/presentation/pages/dashboard_page.dart';
import '../di/injection.dart';
import 'router_refresh_notifier.dart';

class AppRouter {
  static final GoRouter router = GoRouter(
    initialLocation: '/splash',
    refreshListenable: RouterRefreshNotifier(
      getIt<AuthBloc>().stream,
    ),
    redirect: (context, state) {
      final authState = getIt<AuthBloc>().state;

      final loggingIn = state.matchedLocation == '/login';
      final registering = state.matchedLocation == '/register';
      final splash = state.matchedLocation == '/splash';

      if (authState is AuthLoading) {
        return splash ? null : '/splash';
      }

      if (authState is AuthAuthenticated) {
        if (loggingIn || registering || splash) {
          return '/dashboard';
        }
        return null;
      }

      if (authState is AuthUnauthenticated) {
        if (loggingIn || registering) {
          return null;
        }
        return '/login';
      }

      return null;
    },
    routes: [
      GoRoute(
        path: '/splash',
        builder: (_, __) => const SplashPage(),
      ),
      GoRoute(
        path: '/login',
        builder: (_, __) => const LoginPage(),
      ),
      GoRoute(
        path: '/register',
        builder: (_, __) => const RegisterPage(),
      ),
      GoRoute(
        path: '/dashboard',
        builder: (_, __) => const DashboardPage(),
      ),
    ],
  );
}
```

This gives us automatic navigation protection.

### Step 14.5.7 — Splash bootstrap

Update `SplashPage`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../../core/theme/app_colors.dart';
import '../bloc/auth_bloc.dart';
import '../bloc/auth_event.dart';

class SplashPage extends StatefulWidget {
  const SplashPage({super.key});

  @override
  State<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends State<SplashPage> {
  @override
  void initState() {
    super.initState();

    Future.microtask(() {
      context.read<AuthBloc>().add(AppStarted());
    });
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      backgroundColor: AppColors.primary,
      body: Center(
        child: Text(
          'YouStayOn',
          style: TextStyle(
            color: Colors.white,
            fontSize: 28,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
    );
  }
}
```

The splash screen immediately asks Laravel whether the stored Sanctum token is still valid.

### Step 14.5.8 — Connect LoginPage

Inside `LoginPage`, wrap with `BlocListener`.

dart

```
BlocListener<AuthBloc, AuthState>(
  listener: (context, state) {
    if (state is AuthFailure) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(state.message)),
      );
    }
  },
  child: BlocBuilder<AuthBloc, AuthState>(
    builder: (context, state) {
      // existing UI
    },
  ),
)
```

The `Sign in` button already dispatches:

dart

```
context.read<AuthBloc>().add(
  LoginRequested(
    email: _email.text.trim(),
    password: _password.text,
  ),
);
```

After successful login, GoRouter automatically redirects to `/dashboard`.

### Step 14.5.9 — Connect RegisterPage

Do exactly the same:

* Validate fields

* Dispatch `RegisterRequested`

* Listen for `AuthFailure`

* Allow GoRouter to redirect automatically

### Step 14.5.10 — Dashboard placeholder

Create `lib/features/dashboard/presentation/pages/dashboard_page.dart`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../auth/presentation/bloc/auth_bloc.dart';
import '../../../auth/presentation/bloc/auth_event.dart';

class DashboardPage extends StatelessWidget {
  const DashboardPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('YouStayOn'),
        actions: [
          IconButton(
            onPressed: () {
              context.read<AuthBloc>().add(
                    LogoutRequested(),
                  );
            },
            icon: const Icon(Icons.logout),
          ),
        ],
      ),
      body: const Center(
        child: Text(
          'Dashboard Connected',
        ),
      ),
    );
  }
}
```

Logout immediately:

* calls `POST /api/auth/logout`

* clears the Sanctum token

* emits `AuthUnauthenticated`

* redirects to `/login`

### Authentication flow

```
App Launch
     |
     v
Splash
     |
     v
Secure Storage
     |
     +-----------------------+
     |                       |
Token Exists           No Token
     |                       |
     v                       v
GET /api/auth/me        Login Page
     |
     +-----------------------+
     |                       |
Valid Token            Invalid Token
     |                       |
     v                       v
Dashboard              Login Page
     |
     v
Logout
     |
     v
POST /api/auth/logout
     |
     v
Token Cleared
     |
     v
Login Page
```

This is exactly the production flow used by Flutter apps connected to Laravel Sanctum.

### Step 14.5.11 — Backend configuration

Ensure Laravel is running.

Bash

```
cd C:\YouStayOn\backend
php artisan serve
```

If using Android Emulator:

```
Base URL:
http://10.0.2.2:8000/api
```

If using a physical Android device on the same Wi-Fi:

```
http://YOUR_PC_LOCAL_IP:8000/api
```

### Test checklist

Run the Flutter app and verify:

* App opens Splash

* No token → Login page

* Register creates a real Laravel user

* Login returns a real Sanctum token

* Dashboard opens

* App restart restores session automatically

* Logout returns to Login

* Invalid token forces re-authentication

When all of these pass, Flutter will be fully authenticated against the Laravel backend, and every subsequent module (Wallet, Subscriptions, Purchases, Radar Intelligence, Notifications, Dashboard, Analytics) will reuse the same authenticated architecture.

The next milestone is Milestone 14.6 — Dashboard Module Foundation, where we will build the premium dashboard home screen connected directly to the Laravel dashboard APIs (`/dashboard/overview`, `/dashboard/snapshot`, `/dashboard/radar-score`, `/dashboard/recent-activity`, `/dashboard/spending-analytics`, and `/dashboard/usage-trends`) with a high-performance card layout, animated metrics, pull-to-refresh, offline caching, and ultra-fast navigation that mirrors the backend exactly.
Excellent. We are now implementing Milestone 14.5 — Authentication Integration & Routing, which is the milestone that turns the Flutter app into a real client for the Laravel Sanctum backend. From this point onward, the mobile app will authenticate directly against your production-ready Laravel API, persist the Sanctum token securely, restore sessions automatically, and protect navigation exactly like a commercial fintech application.

This milestone is intentionally aligned 100% with the Laravel backend we completed. We will not create any Flutter-only authentication logic; every authentication decision will come from the backend.

### Milestone 14.5: Authentication integration & routing

We are connecting the premium authentication UI to the BLoC layer, Sanctum token storage, automatic session restoration, protected routes, and navigation guards so Flutter mirrors the Laravel backend exactly.

### What we are implementing

| Layer                           | Status        |
| ------------------------------- | ------------- |
| Sanctum token persistence       | Implement now |
| Authenticated Dio client        | Implement now |
| Automatic session restoration   | Implement now |
| Splash bootstrap                | Implement now |
| GoRouter route guards           | Implement now |
| Login → Dashboard navigation    | Implement now |
| Register → Dashboard navigation | Implement now |
| Logout → Login navigation       | Implement now |
| Expired token handling          | Implement now |

This is exactly how production Flutter apps integrate with Laravel Sanctum.

### Step 14.5.1 — Secure storage service

Create `lib/core/storage/secure_storage_service.dart`.

dart

```
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SecureStorageService {
  static const _tokenKey = 'auth_token';
  static const _userKey = 'user_json';

  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  Future<void> saveToken(String token) async {
    await _storage.write(key: _tokenKey, value: token);
  }

  Future<String?> getToken() async {
    return _storage.read(key: _tokenKey);
  }

  Future<void> saveUserJson(String json) async {
    await _storage.write(key: _userKey, value: json);
  }

  Future<String?> getUserJson() async {
    return _storage.read(key: _userKey);
  }

  Future<void> clearAll() async {
    await _storage.delete(key: _tokenKey);
    await _storage.delete(key: _userKey);
  }
}
```

This stores the Laravel Sanctum personal access token securely on the device.

### Step 14.5.2 — Auth interceptor

Create `lib/core/network/auth_interceptor.dart`.

dart

```
import 'package:dio/dio.dart';

import '../storage/secure_storage_service.dart';

class AuthInterceptor extends Interceptor {
  AuthInterceptor(this._storage);

  final SecureStorageService _storage;

  @override
  Future<void> onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    final token = await _storage.getToken();

    if (token != null) {
      options.headers['Authorization'] = 'Bearer $token';
      options.headers['Accept'] = 'application/json';
    }

    handler.next(options);
  }

  @override
  Future<void> onError(
    DioException err,
    ErrorInterceptorHandler handler,
  ) async {
    if (err.response?.statusCode == 401) {
      await _storage.clearAll();
    }

    handler.next(err);
  }
}
```

This ensures every authenticated request automatically includes:

http

```
Authorization: Bearer {sanctum_token}
```

which exactly matches your Laravel backend.

### Step 14.5.3 — Update Dio client

Update `lib/core/network/dio_client.dart`.

dart

```
import 'package:dio/dio.dart';
import 'package:pretty_dio_logger/pretty_dio_logger.dart';

import '../storage/secure_storage_service.dart';
import 'auth_interceptor.dart';

class DioClient {
  DioClient(this._storage)
      : dio = Dio(
          BaseOptions(
            baseUrl: 'http://10.0.2.2:8000/api',
            connectTimeout: const Duration(seconds: 30),
            receiveTimeout: const Duration(seconds: 30),
            sendTimeout: const Duration(seconds: 30),
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          ),
        ) {
    dio.interceptors.add(AuthInterceptor(_storage));

    dio.interceptors.add(
      PrettyDioLogger(
        requestBody: true,
        responseBody: true,
      ),
    );
  }

  final SecureStorageService _storage;
  final Dio dio;
}
```

For Android Emulator, `10.0.2.2` correctly points to your local Laravel server.

### Step 14.5.4 — Dependency injection

Update `lib/core/di/injection.dart`.

Register storage first.

dart

```
getIt.registerLazySingleton(
  () => SecureStorageService(),
);

getIt.registerLazySingleton(
  () => DioClient(getIt()),
);
```

The order matters because `DioClient` depends on `SecureStorageService`.

### Step 14.5.5 — Router refresh from AuthBloc

GoRouter must react automatically when authentication changes.

Create `lib/core/router/router_refresh_notifier.dart`.

dart

```
import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class RouterRefreshNotifier extends ChangeNotifier {
  RouterRefreshNotifier(Stream<dynamic> stream) {
    _subscription = stream.asBroadcastStream().listen(
          (_) => notifyListeners(),
        );
  }

  late final StreamSubscription<dynamic> _subscription;

  @override
  void dispose() {
    _subscription.cancel();
    super.dispose();
  }
}
```

### Step 14.5.6 — Update app router

Update `lib/core/router/app_router.dart`.

dart

```
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';

import '../../features/auth/presentation/bloc/auth_bloc.dart';
import '../../features/auth/presentation/bloc/auth_state.dart';
import '../../features/auth/presentation/pages/login_page.dart';
import '../../features/auth/presentation/pages/register_page.dart';
import '../../features/auth/presentation/pages/splash_page.dart';
import '../../features/dashboard/presentation/pages/dashboard_page.dart';
import '../di/injection.dart';
import 'router_refresh_notifier.dart';

class AppRouter {
  static final GoRouter router = GoRouter(
    initialLocation: '/splash',
    refreshListenable: RouterRefreshNotifier(
      getIt<AuthBloc>().stream,
    ),
    redirect: (context, state) {
      final authState = getIt<AuthBloc>().state;

      final loggingIn = state.matchedLocation == '/login';
      final registering = state.matchedLocation == '/register';
      final splash = state.matchedLocation == '/splash';

      if (authState is AuthLoading) {
        return splash ? null : '/splash';
      }

      if (authState is AuthAuthenticated) {
        if (loggingIn || registering || splash) {
          return '/dashboard';
        }
        return null;
      }

      if (authState is AuthUnauthenticated) {
        if (loggingIn || registering) {
          return null;
        }
        return '/login';
      }

      return null;
    },
    routes: [
      GoRoute(
        path: '/splash',
        builder: (_, __) => const SplashPage(),
      ),
      GoRoute(
        path: '/login',
        builder: (_, __) => const LoginPage(),
      ),
      GoRoute(
        path: '/register',
        builder: (_, __) => const RegisterPage(),
      ),
      GoRoute(
        path: '/dashboard',
        builder: (_, __) => const DashboardPage(),
      ),
    ],
  );
}
```

This gives us automatic navigation protection.

### Step 14.5.7 — Splash bootstrap

Update `SplashPage`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../../core/theme/app_colors.dart';
import '../bloc/auth_bloc.dart';
import '../bloc/auth_event.dart';

class SplashPage extends StatefulWidget {
  const SplashPage({super.key});

  @override
  State<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends State<SplashPage> {
  @override
  void initState() {
    super.initState();

    Future.microtask(() {
      context.read<AuthBloc>().add(AppStarted());
    });
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      backgroundColor: AppColors.primary,
      body: Center(
        child: Text(
          'YouStayOn',
          style: TextStyle(
            color: Colors.white,
            fontSize: 28,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
    );
  }
}
```

The splash screen immediately asks Laravel whether the stored Sanctum token is still valid.

### Step 14.5.8 — Connect LoginPage

Inside `LoginPage`, wrap with `BlocListener`.

dart

```
BlocListener<AuthBloc, AuthState>(
  listener: (context, state) {
    if (state is AuthFailure) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(state.message)),
      );
    }
  },
  child: BlocBuilder<AuthBloc, AuthState>(
    builder: (context, state) {
      // existing UI
    },
  ),
)
```

The `Sign in` button already dispatches:

dart

```
context.read<AuthBloc>().add(
  LoginRequested(
    email: _email.text.trim(),
    password: _password.text,
  ),
);
```

After successful login, GoRouter automatically redirects to `/dashboard`.

### Step 14.5.9 — Connect RegisterPage

Do exactly the same:

* Validate fields

* Dispatch `RegisterRequested`

* Listen for `AuthFailure`

* Allow GoRouter to redirect automatically

### Step 14.5.10 — Dashboard placeholder

Create `lib/features/dashboard/presentation/pages/dashboard_page.dart`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../auth/presentation/bloc/auth_bloc.dart';
import '../../../auth/presentation/bloc/auth_event.dart';

class DashboardPage extends StatelessWidget {
  const DashboardPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('YouStayOn'),
        actions: [
          IconButton(
            onPressed: () {
              context.read<AuthBloc>().add(
                    LogoutRequested(),
                  );
            },
            icon: const Icon(Icons.logout),
          ),
        ],
      ),
      body: const Center(
        child: Text(
          'Dashboard Connected',
        ),
      ),
    );
  }
}
```

Logout immediately:

* calls `POST /api/auth/logout`

* clears the Sanctum token

* emits `AuthUnauthenticated`

* redirects to `/login`

### Authentication flow

```
App Launch
     |
     v
Splash
     |
     v
Secure Storage
     |
     +-----------------------+
     |                       |
Token Exists           No Token
     |                       |
     v                       v
GET /api/auth/me        Login Page
     |
     +-----------------------+
     |                       |
Valid Token            Invalid Token
     |                       |
     v                       v
Dashboard              Login Page
     |
     v
Logout
     |
     v
POST /api/auth/logout
     |
     v
Token Cleared
     |
     v
Login Page
```

This is exactly the production flow used by Flutter apps connected to Laravel Sanctum.

### Step 14.5.11 — Backend configuration

Ensure Laravel is running.

Bash

```
cd C:\YouStayOn\backend
php artisan serve
```

If using Android Emulator:

```
Base URL:
http://10.0.2.2:8000/api
```

If using a physical Android device on the same Wi-Fi:

```
http://YOUR_PC_LOCAL_IP:8000/api
```

### Test checklist

Run the Flutter app and verify:

* App opens Splash

* No token → Login page

* Register creates a real Laravel user

* Login returns a real Sanctum token

* Dashboard opens

* App restart restores session automatically

* Logout returns to Login

* Invalid token forces re-authentication

When all of these pass, Flutter will be fully authenticated against the Laravel backend, and every subsequent module (Wallet, Subscriptions, Purchases, Radar Intelligence, Notifications, Dashboard, Analytics) will reuse the same authenticated architecture.

The next milestone is Milestone 14.6 — Dashboard Module Foundation, where we will build the premium dashboard home screen connected directly to the Laravel dashboard APIs (`/dashboard/overview`, `/dashboard/snapshot`, `/dashboard/radar-score`, `/dashboard/recent-activity`, `/dashboard/spending-analytics`, and `/dashboard/usage-trends`) with a high-performance card layout, animated metrics, pull-to-refresh, offline caching, and ultra-fast navigation that mirrors the backend exactly.
Excellent. We are now implementing Milestone 14.5 — Authentication Integration & Routing, which is the milestone that turns the Flutter app into a real client for the Laravel Sanctum backend. From this point onward, the mobile app will authenticate directly against your production-ready Laravel API, persist the Sanctum token securely, restore sessions automatically, and protect navigation exactly like a commercial fintech application.

This milestone is intentionally aligned 100% with the Laravel backend we completed. We will not create any Flutter-only authentication logic; every authentication decision will come from the backend.

### Milestone 14.5: Authentication integration & routing

We are connecting the premium authentication UI to the BLoC layer, Sanctum token storage, automatic session restoration, protected routes, and navigation guards so Flutter mirrors the Laravel backend exactly.

### What we are implementing

| Layer                           | Status        |
| ------------------------------- | ------------- |
| Sanctum token persistence       | Implement now |
| Authenticated Dio client        | Implement now |
| Automatic session restoration   | Implement now |
| Splash bootstrap                | Implement now |
| GoRouter route guards           | Implement now |
| Login → Dashboard navigation    | Implement now |
| Register → Dashboard navigation | Implement now |
| Logout → Login navigation       | Implement now |
| Expired token handling          | Implement now |

This is exactly how production Flutter apps integrate with Laravel Sanctum.

### Step 14.5.1 — Secure storage service

Create `lib/core/storage/secure_storage_service.dart`.

dart

```
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SecureStorageService {
  static const _tokenKey = 'auth_token';
  static const _userKey = 'user_json';

  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  Future<void> saveToken(String token) async {
    await _storage.write(key: _tokenKey, value: token);
  }

  Future<String?> getToken() async {
    return _storage.read(key: _tokenKey);
  }

  Future<void> saveUserJson(String json) async {
    await _storage.write(key: _userKey, value: json);
  }

  Future<String?> getUserJson() async {
    return _storage.read(key: _userKey);
  }

  Future<void> clearAll() async {
    await _storage.delete(key: _tokenKey);
    await _storage.delete(key: _userKey);
  }
}
```

This stores the Laravel Sanctum personal access token securely on the device.

### Step 14.5.2 — Auth interceptor

Create `lib/core/network/auth_interceptor.dart`.

dart

```
import 'package:dio/dio.dart';

import '../storage/secure_storage_service.dart';

class AuthInterceptor extends Interceptor {
  AuthInterceptor(this._storage);

  final SecureStorageService _storage;

  @override
  Future<void> onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    final token = await _storage.getToken();

    if (token != null) {
      options.headers['Authorization'] = 'Bearer $token';
      options.headers['Accept'] = 'application/json';
    }

    handler.next(options);
  }

  @override
  Future<void> onError(
    DioException err,
    ErrorInterceptorHandler handler,
  ) async {
    if (err.response?.statusCode == 401) {
      await _storage.clearAll();
    }

    handler.next(err);
  }
}
```

This ensures every authenticated request automatically includes:

http

```
Authorization: Bearer {sanctum_token}
```

which exactly matches your Laravel backend.

### Step 14.5.3 — Update Dio client

Update `lib/core/network/dio_client.dart`.

dart

```
import 'package:dio/dio.dart';
import 'package:pretty_dio_logger/pretty_dio_logger.dart';

import '../storage/secure_storage_service.dart';
import 'auth_interceptor.dart';

class DioClient {
  DioClient(this._storage)
      : dio = Dio(
          BaseOptions(
            baseUrl: 'http://10.0.2.2:8000/api',
            connectTimeout: const Duration(seconds: 30),
            receiveTimeout: const Duration(seconds: 30),
            sendTimeout: const Duration(seconds: 30),
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          ),
        ) {
    dio.interceptors.add(AuthInterceptor(_storage));

    dio.interceptors.add(
      PrettyDioLogger(
        requestBody: true,
        responseBody: true,
      ),
    );
  }

  final SecureStorageService _storage;
  final Dio dio;
}
```

For Android Emulator, `10.0.2.2` correctly points to your local Laravel server.

### Step 14.5.4 — Dependency injection

Update `lib/core/di/injection.dart`.

Register storage first.

dart

```
getIt.registerLazySingleton(
  () => SecureStorageService(),
);

getIt.registerLazySingleton(
  () => DioClient(getIt()),
);
```

The order matters because `DioClient` depends on `SecureStorageService`.

### Step 14.5.5 — Router refresh from AuthBloc

GoRouter must react automatically when authentication changes.

Create `lib/core/router/router_refresh_notifier.dart`.

dart

```
import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class RouterRefreshNotifier extends ChangeNotifier {
  RouterRefreshNotifier(Stream<dynamic> stream) {
    _subscription = stream.asBroadcastStream().listen(
          (_) => notifyListeners(),
        );
  }

  late final StreamSubscription<dynamic> _subscription;

  @override
  void dispose() {
    _subscription.cancel();
    super.dispose();
  }
}
```

### Step 14.5.6 — Update app router

Update `lib/core/router/app_router.dart`.

dart

```
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';

import '../../features/auth/presentation/bloc/auth_bloc.dart';
import '../../features/auth/presentation/bloc/auth_state.dart';
import '../../features/auth/presentation/pages/login_page.dart';
import '../../features/auth/presentation/pages/register_page.dart';
import '../../features/auth/presentation/pages/splash_page.dart';
import '../../features/dashboard/presentation/pages/dashboard_page.dart';
import '../di/injection.dart';
import 'router_refresh_notifier.dart';

class AppRouter {
  static final GoRouter router = GoRouter(
    initialLocation: '/splash',
    refreshListenable: RouterRefreshNotifier(
      getIt<AuthBloc>().stream,
    ),
    redirect: (context, state) {
      final authState = getIt<AuthBloc>().state;

      final loggingIn = state.matchedLocation == '/login';
      final registering = state.matchedLocation == '/register';
      final splash = state.matchedLocation == '/splash';

      if (authState is AuthLoading) {
        return splash ? null : '/splash';
      }

      if (authState is AuthAuthenticated) {
        if (loggingIn || registering || splash) {
          return '/dashboard';
        }
        return null;
      }

      if (authState is AuthUnauthenticated) {
        if (loggingIn || registering) {
          return null;
        }
        return '/login';
      }

      return null;
    },
    routes: [
      GoRoute(
        path: '/splash',
        builder: (_, __) => const SplashPage(),
      ),
      GoRoute(
        path: '/login',
        builder: (_, __) => const LoginPage(),
      ),
      GoRoute(
        path: '/register',
        builder: (_, __) => const RegisterPage(),
      ),
      GoRoute(
        path: '/dashboard',
        builder: (_, __) => const DashboardPage(),
      ),
    ],
  );
}
```

This gives us automatic navigation protection.

### Step 14.5.7 — Splash bootstrap

Update `SplashPage`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../../core/theme/app_colors.dart';
import '../bloc/auth_bloc.dart';
import '../bloc/auth_event.dart';

class SplashPage extends StatefulWidget {
  const SplashPage({super.key});

  @override
  State<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends State<SplashPage> {
  @override
  void initState() {
    super.initState();

    Future.microtask(() {
      context.read<AuthBloc>().add(AppStarted());
    });
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      backgroundColor: AppColors.primary,
      body: Center(
        child: Text(
          'YouStayOn',
          style: TextStyle(
            color: Colors.white,
            fontSize: 28,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
    );
  }
}
```

The splash screen immediately asks Laravel whether the stored Sanctum token is still valid.

### Step 14.5.8 — Connect LoginPage

Inside `LoginPage`, wrap with `BlocListener`.

dart

```
BlocListener<AuthBloc, AuthState>(
  listener: (context, state) {
    if (state is AuthFailure) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(state.message)),
      );
    }
  },
  child: BlocBuilder<AuthBloc, AuthState>(
    builder: (context, state) {
      // existing UI
    },
  ),
)
```

The `Sign in` button already dispatches:

dart

```
context.read<AuthBloc>().add(
  LoginRequested(
    email: _email.text.trim(),
    password: _password.text,
  ),
);
```

After successful login, GoRouter automatically redirects to `/dashboard`.

### Step 14.5.9 — Connect RegisterPage

Do exactly the same:

* Validate fields

* Dispatch `RegisterRequested`

* Listen for `AuthFailure`

* Allow GoRouter to redirect automatically

### Step 14.5.10 — Dashboard placeholder

Create `lib/features/dashboard/presentation/pages/dashboard_page.dart`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../auth/presentation/bloc/auth_bloc.dart';
import '../../../auth/presentation/bloc/auth_event.dart';

class DashboardPage extends StatelessWidget {
  const DashboardPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('YouStayOn'),
        actions: [
          IconButton(
            onPressed: () {
              context.read<AuthBloc>().add(
                    LogoutRequested(),
                  );
            },
            icon: const Icon(Icons.logout),
          ),
        ],
      ),
      body: const Center(
        child: Text(
          'Dashboard Connected',
        ),
      ),
    );
  }
}
```

Logout immediately:

* calls `POST /api/auth/logout`

* clears the Sanctum token

* emits `AuthUnauthenticated`

* redirects to `/login`

### Authentication flow

```
App Launch
     |
     v
Splash
     |
     v
Secure Storage
     |
     +-----------------------+
     |                       |
Token Exists           No Token
     |                       |
     v                       v
GET /api/auth/me        Login Page
     |
     +-----------------------+
     |                       |
Valid Token            Invalid Token
     |                       |
     v                       v
Dashboard              Login Page
     |
     v
Logout
     |
     v
POST /api/auth/logout
     |
     v
Token Cleared
     |
     v
Login Page
```

This is exactly the production flow used by Flutter apps connected to Laravel Sanctum.

### Step 14.5.11 — Backend configuration

Ensure Laravel is running.

Bash

```
cd C:\YouStayOn\backend
php artisan serve
```

If using Android Emulator:

```
Base URL:
http://10.0.2.2:8000/api
```

If using a physical Android device on the same Wi-Fi:

```
http://YOUR_PC_LOCAL_IP:8000/api
```

### Test checklist

Run the Flutter app and verify:

* App opens Splash

* No token → Login page

* Register creates a real Laravel user

* Login returns a real Sanctum token

* Dashboard opens

* App restart restores session automatically

* Logout returns to Login

* Invalid token forces re-authentication

When all of these pass, Flutter will be fully authenticated against the Laravel backend, and every subsequent module (Wallet, Subscriptions, Purchases, Radar Intelligence, Notifications, Dashboard, Analytics) will reuse the same authenticated architecture.

The next milestone is Milestone 14.6 — Dashboard Module Foundation, where we will build the premium dashboard home screen connected directly to the Laravel dashboard APIs (`/dashboard/overview`, `/dashboard/snapshot`, `/dashboard/radar-score`, `/dashboard/recent-activity`, `/dashboard/spending-analytics`, and `/dashboard/usage-trends`) with a high-performance card layout, animated metrics, pull-to-refresh, offline caching, and ultra-fast navigation that mirrors the backend exactly.
Excellent. We are now implementing Milestone 14.5 — Authentication Integration & Routing, which is the milestone that turns the Flutter app into a real client for the Laravel Sanctum backend. From this point onward, the mobile app will authenticate directly against your production-ready Laravel API, persist the Sanctum token securely, restore sessions automatically, and protect navigation exactly like a commercial fintech application.

This milestone is intentionally aligned 100% with the Laravel backend we completed. We will not create any Flutter-only authentication logic; every authentication decision will come from the backend.

### Milestone 14.5: Authentication integration & routing

We are connecting the premium authentication UI to the BLoC layer, Sanctum token storage, automatic session restoration, protected routes, and navigation guards so Flutter mirrors the Laravel backend exactly.

### What we are implementing

| Layer                           | Status        |
| ------------------------------- | ------------- |
| Sanctum token persistence       | Implement now |
| Authenticated Dio client        | Implement now |
| Automatic session restoration   | Implement now |
| Splash bootstrap                | Implement now |
| GoRouter route guards           | Implement now |
| Login → Dashboard navigation    | Implement now |
| Register → Dashboard navigation | Implement now |
| Logout → Login navigation       | Implement now |
| Expired token handling          | Implement now |

This is exactly how production Flutter apps integrate with Laravel Sanctum.

### Step 14.5.1 — Secure storage service

Create `lib/core/storage/secure_storage_service.dart`.

dart

```
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SecureStorageService {
  static const _tokenKey = 'auth_token';
  static const _userKey = 'user_json';

  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  Future<void> saveToken(String token) async {
    await _storage.write(key: _tokenKey, value: token);
  }

  Future<String?> getToken() async {
    return _storage.read(key: _tokenKey);
  }

  Future<void> saveUserJson(String json) async {
    await _storage.write(key: _userKey, value: json);
  }

  Future<String?> getUserJson() async {
    return _storage.read(key: _userKey);
  }

  Future<void> clearAll() async {
    await _storage.delete(key: _tokenKey);
    await _storage.delete(key: _userKey);
  }
}
```

This stores the Laravel Sanctum personal access token securely on the device.

### Step 14.5.2 — Auth interceptor

Create `lib/core/network/auth_interceptor.dart`.

dart

```
import 'package:dio/dio.dart';

import '../storage/secure_storage_service.dart';

class AuthInterceptor extends Interceptor {
  AuthInterceptor(this._storage);

  final SecureStorageService _storage;

  @override
  Future<void> onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    final token = await _storage.getToken();

    if (token != null) {
      options.headers['Authorization'] = 'Bearer $token';
      options.headers['Accept'] = 'application/json';
    }

    handler.next(options);
  }

  @override
  Future<void> onError(
    DioException err,
    ErrorInterceptorHandler handler,
  ) async {
    if (err.response?.statusCode == 401) {
      await _storage.clearAll();
    }

    handler.next(err);
  }
}
```

This ensures every authenticated request automatically includes:

http

```
Authorization: Bearer {sanctum_token}
```

which exactly matches your Laravel backend.

### Step 14.5.3 — Update Dio client

Update `lib/core/network/dio_client.dart`.

dart

```
import 'package:dio/dio.dart';
import 'package:pretty_dio_logger/pretty_dio_logger.dart';

import '../storage/secure_storage_service.dart';
import 'auth_interceptor.dart';

class DioClient {
  DioClient(this._storage)
      : dio = Dio(
          BaseOptions(
            baseUrl: 'http://10.0.2.2:8000/api',
            connectTimeout: const Duration(seconds: 30),
            receiveTimeout: const Duration(seconds: 30),
            sendTimeout: const Duration(seconds: 30),
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          ),
        ) {
    dio.interceptors.add(AuthInterceptor(_storage));

    dio.interceptors.add(
      PrettyDioLogger(
        requestBody: true,
        responseBody: true,
      ),
    );
  }

  final SecureStorageService _storage;
  final Dio dio;
}
```

For Android Emulator, `10.0.2.2` correctly points to your local Laravel server.

### Step 14.5.4 — Dependency injection

Update `lib/core/di/injection.dart`.

Register storage first.

dart

```
getIt.registerLazySingleton(
  () => SecureStorageService(),
);

getIt.registerLazySingleton(
  () => DioClient(getIt()),
);
```

The order matters because `DioClient` depends on `SecureStorageService`.

### Step 14.5.5 — Router refresh from AuthBloc

GoRouter must react automatically when authentication changes.

Create `lib/core/router/router_refresh_notifier.dart`.

dart

```
import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class RouterRefreshNotifier extends ChangeNotifier {
  RouterRefreshNotifier(Stream<dynamic> stream) {
    _subscription = stream.asBroadcastStream().listen(
          (_) => notifyListeners(),
        );
  }

  late final StreamSubscription<dynamic> _subscription;

  @override
  void dispose() {
    _subscription.cancel();
    super.dispose();
  }
}
```

### Step 14.5.6 — Update app router

Update `lib/core/router/app_router.dart`.

dart

```
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';

import '../../features/auth/presentation/bloc/auth_bloc.dart';
import '../../features/auth/presentation/bloc/auth_state.dart';
import '../../features/auth/presentation/pages/login_page.dart';
import '../../features/auth/presentation/pages/register_page.dart';
import '../../features/auth/presentation/pages/splash_page.dart';
import '../../features/dashboard/presentation/pages/dashboard_page.dart';
import '../di/injection.dart';
import 'router_refresh_notifier.dart';

class AppRouter {
  static final GoRouter router = GoRouter(
    initialLocation: '/splash',
    refreshListenable: RouterRefreshNotifier(
      getIt<AuthBloc>().stream,
    ),
    redirect: (context, state) {
      final authState = getIt<AuthBloc>().state;

      final loggingIn = state.matchedLocation == '/login';
      final registering = state.matchedLocation == '/register';
      final splash = state.matchedLocation == '/splash';

      if (authState is AuthLoading) {
        return splash ? null : '/splash';
      }

      if (authState is AuthAuthenticated) {
        if (loggingIn || registering || splash) {
          return '/dashboard';
        }
        return null;
      }

      if (authState is AuthUnauthenticated) {
        if (loggingIn || registering) {
          return null;
        }
        return '/login';
      }

      return null;
    },
    routes: [
      GoRoute(
        path: '/splash',
        builder: (_, __) => const SplashPage(),
      ),
      GoRoute(
        path: '/login',
        builder: (_, __) => const LoginPage(),
      ),
      GoRoute(
        path: '/register',
        builder: (_, __) => const RegisterPage(),
      ),
      GoRoute(
        path: '/dashboard',
        builder: (_, __) => const DashboardPage(),
      ),
    ],
  );
}
```

This gives us automatic navigation protection.

### Step 14.5.7 — Splash bootstrap

Update `SplashPage`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../../core/theme/app_colors.dart';
import '../bloc/auth_bloc.dart';
import '../bloc/auth_event.dart';

class SplashPage extends StatefulWidget {
  const SplashPage({super.key});

  @override
  State<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends State<SplashPage> {
  @override
  void initState() {
    super.initState();

    Future.microtask(() {
      context.read<AuthBloc>().add(AppStarted());
    });
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      backgroundColor: AppColors.primary,
      body: Center(
        child: Text(
          'YouStayOn',
          style: TextStyle(
            color: Colors.white,
            fontSize: 28,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
    );
  }
}
```

The splash screen immediately asks Laravel whether the stored Sanctum token is still valid.

### Step 14.5.8 — Connect LoginPage

Inside `LoginPage`, wrap with `BlocListener`.

dart

```
BlocListener<AuthBloc, AuthState>(
  listener: (context, state) {
    if (state is AuthFailure) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(state.message)),
      );
    }
  },
  child: BlocBuilder<AuthBloc, AuthState>(
    builder: (context, state) {
      // existing UI
    },
  ),
)
```

The `Sign in` button already dispatches:

dart

```
context.read<AuthBloc>().add(
  LoginRequested(
    email: _email.text.trim(),
    password: _password.text,
  ),
);
```

After successful login, GoRouter automatically redirects to `/dashboard`.

### Step 14.5.9 — Connect RegisterPage

Do exactly the same:

* Validate fields

* Dispatch `RegisterRequested`

* Listen for `AuthFailure`

* Allow GoRouter to redirect automatically

### Step 14.5.10 — Dashboard placeholder

Create `lib/features/dashboard/presentation/pages/dashboard_page.dart`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../auth/presentation/bloc/auth_bloc.dart';
import '../../../auth/presentation/bloc/auth_event.dart';

class DashboardPage extends StatelessWidget {
  const DashboardPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('YouStayOn'),
        actions: [
          IconButton(
            onPressed: () {
              context.read<AuthBloc>().add(
                    LogoutRequested(),
                  );
            },
            icon: const Icon(Icons.logout),
          ),
        ],
      ),
      body: const Center(
        child: Text(
          'Dashboard Connected',
        ),
      ),
    );
  }
}
```

Logout immediately:

* calls `POST /api/auth/logout`

* clears the Sanctum token

* emits `AuthUnauthenticated`

* redirects to `/login`

### Authentication flow

```
App Launch
     |
     v
Splash
     |
     v
Secure Storage
     |
     +-----------------------+
     |                       |
Token Exists           No Token
     |                       |
     v                       v
GET /api/auth/me        Login Page
     |
     +-----------------------+
     |                       |
Valid Token            Invalid Token
     |                       |
     v                       v
Dashboard              Login Page
     |
     v
Logout
     |
     v
POST /api/auth/logout
     |
     v
Token Cleared
     |
     v
Login Page
```

This is exactly the production flow used by Flutter apps connected to Laravel Sanctum.

### Step 14.5.11 — Backend configuration

Ensure Laravel is running.

Bash

```
cd C:\YouStayOn\backend
php artisan serve
```

If using Android Emulator:

```
Base URL:
http://10.0.2.2:8000/api
```

If using a physical Android device on the same Wi-Fi:

```
http://YOUR_PC_LOCAL_IP:8000/api
```

### Test checklist

Run the Flutter app and verify:

* App opens Splash

* No token → Login page

* Register creates a real Laravel user

* Login returns a real Sanctum token

* Dashboard opens

* App restart restores session automatically

* Logout returns to Login

* Invalid token forces re-authentication

When all of these pass, Flutter will be fully authenticated against the Laravel backend, and every subsequent module (Wallet, Subscriptions, Purchases, Radar Intelligence, Notifications, Dashboard, Analytics) will reuse the same authenticated architecture.

The next milestone is Milestone 14.6 — Dashboard Module Foundation, where we will build the premium dashboard home screen connected directly to the Laravel dashboard APIs (`/dashboard/overview`, `/dashboard/snapshot`, `/dashboard/radar-score`, `/dashboard/recent-activity`, `/dashboard/spending-analytics`, and `/dashboard/usage-trends`) with a high-performance card layout, animated metrics, pull-to-refresh, offline caching, and ultra-fast navigation that mirrors the backend exactly.
Excellent. We are now implementing Milestone 14.5 — Authentication Integration & Routing, which is the milestone that turns the Flutter app into a real client for the Laravel Sanctum backend. From this point onward, the mobile app will authenticate directly against your production-ready Laravel API, persist the Sanctum token securely, restore sessions automatically, and protect navigation exactly like a commercial fintech application.

This milestone is intentionally aligned 100% with the Laravel backend we completed. We will not create any Flutter-only authentication logic; every authentication decision will come from the backend.

### Milestone 14.5: Authentication integration & routing

We are connecting the premium authentication UI to the BLoC layer, Sanctum token storage, automatic session restoration, protected routes, and navigation guards so Flutter mirrors the Laravel backend exactly.

### What we are implementing

| Layer                           | Status        |
| ------------------------------- | ------------- |
| Sanctum token persistence       | Implement now |
| Authenticated Dio client        | Implement now |
| Automatic session restoration   | Implement now |
| Splash bootstrap                | Implement now |
| GoRouter route guards           | Implement now |
| Login → Dashboard navigation    | Implement now |
| Register → Dashboard navigation | Implement now |
| Logout → Login navigation       | Implement now |
| Expired token handling          | Implement now |

This is exactly how production Flutter apps integrate with Laravel Sanctum.

### Step 14.5.1 — Secure storage service

Create `lib/core/storage/secure_storage_service.dart`.

dart

```
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SecureStorageService {
  static const _tokenKey = 'auth_token';
  static const _userKey = 'user_json';

  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  Future<void> saveToken(String token) async {
    await _storage.write(key: _tokenKey, value: token);
  }

  Future<String?> getToken() async {
    return _storage.read(key: _tokenKey);
  }

  Future<void> saveUserJson(String json) async {
    await _storage.write(key: _userKey, value: json);
  }

  Future<String?> getUserJson() async {
    return _storage.read(key: _userKey);
  }

  Future<void> clearAll() async {
    await _storage.delete(key: _tokenKey);
    await _storage.delete(key: _userKey);
  }
}
```

This stores the Laravel Sanctum personal access token securely on the device.

### Step 14.5.2 — Auth interceptor

Create `lib/core/network/auth_interceptor.dart`.

dart

```
import 'package:dio/dio.dart';

import '../storage/secure_storage_service.dart';

class AuthInterceptor extends Interceptor {
  AuthInterceptor(this._storage);

  final SecureStorageService _storage;

  @override
  Future<void> onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    final token = await _storage.getToken();

    if (token != null) {
      options.headers['Authorization'] = 'Bearer $token';
      options.headers['Accept'] = 'application/json';
    }

    handler.next(options);
  }

  @override
  Future<void> onError(
    DioException err,
    ErrorInterceptorHandler handler,
  ) async {
    if (err.response?.statusCode == 401) {
      await _storage.clearAll();
    }

    handler.next(err);
  }
}
```

This ensures every authenticated request automatically includes:

http

```
Authorization: Bearer {sanctum_token}
```

which exactly matches your Laravel backend.

### Step 14.5.3 — Update Dio client

Update `lib/core/network/dio_client.dart`.

dart

```
import 'package:dio/dio.dart';
import 'package:pretty_dio_logger/pretty_dio_logger.dart';

import '../storage/secure_storage_service.dart';
import 'auth_interceptor.dart';

class DioClient {
  DioClient(this._storage)
      : dio = Dio(
          BaseOptions(
            baseUrl: 'http://10.0.2.2:8000/api',
            connectTimeout: const Duration(seconds: 30),
            receiveTimeout: const Duration(seconds: 30),
            sendTimeout: const Duration(seconds: 30),
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          ),
        ) {
    dio.interceptors.add(AuthInterceptor(_storage));

    dio.interceptors.add(
      PrettyDioLogger(
        requestBody: true,
        responseBody: true,
      ),
    );
  }

  final SecureStorageService _storage;
  final Dio dio;
}
```

For Android Emulator, `10.0.2.2` correctly points to your local Laravel server.

### Step 14.5.4 — Dependency injection

Update `lib/core/di/injection.dart`.

Register storage first.

dart

```
getIt.registerLazySingleton(
  () => SecureStorageService(),
);

getIt.registerLazySingleton(
  () => DioClient(getIt()),
);
```

The order matters because `DioClient` depends on `SecureStorageService`.

### Step 14.5.5 — Router refresh from AuthBloc

GoRouter must react automatically when authentication changes.

Create `lib/core/router/router_refresh_notifier.dart`.

dart

```
import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class RouterRefreshNotifier extends ChangeNotifier {
  RouterRefreshNotifier(Stream<dynamic> stream) {
    _subscription = stream.asBroadcastStream().listen(
          (_) => notifyListeners(),
        );
  }

  late final StreamSubscription<dynamic> _subscription;

  @override
  void dispose() {
    _subscription.cancel();
    super.dispose();
  }
}
```

### Step 14.5.6 — Update app router

Update `lib/core/router/app_router.dart`.

dart

```
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';

import '../../features/auth/presentation/bloc/auth_bloc.dart';
import '../../features/auth/presentation/bloc/auth_state.dart';
import '../../features/auth/presentation/pages/login_page.dart';
import '../../features/auth/presentation/pages/register_page.dart';
import '../../features/auth/presentation/pages/splash_page.dart';
import '../../features/dashboard/presentation/pages/dashboard_page.dart';
import '../di/injection.dart';
import 'router_refresh_notifier.dart';

class AppRouter {
  static final GoRouter router = GoRouter(
    initialLocation: '/splash',
    refreshListenable: RouterRefreshNotifier(
      getIt<AuthBloc>().stream,
    ),
    redirect: (context, state) {
      final authState = getIt<AuthBloc>().state;

      final loggingIn = state.matchedLocation == '/login';
      final registering = state.matchedLocation == '/register';
      final splash = state.matchedLocation == '/splash';

      if (authState is AuthLoading) {
        return splash ? null : '/splash';
      }

      if (authState is AuthAuthenticated) {
        if (loggingIn || registering || splash) {
          return '/dashboard';
        }
        return null;
      }

      if (authState is AuthUnauthenticated) {
        if (loggingIn || registering) {
          return null;
        }
        return '/login';
      }

      return null;
    },
    routes: [
      GoRoute(
        path: '/splash',
        builder: (_, __) => const SplashPage(),
      ),
      GoRoute(
        path: '/login',
        builder: (_, __) => const LoginPage(),
      ),
      GoRoute(
        path: '/register',
        builder: (_, __) => const RegisterPage(),
      ),
      GoRoute(
        path: '/dashboard',
        builder: (_, __) => const DashboardPage(),
      ),
    ],
  );
}
```

This gives us automatic navigation protection.

### Step 14.5.7 — Splash bootstrap

Update `SplashPage`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../../core/theme/app_colors.dart';
import '../bloc/auth_bloc.dart';
import '../bloc/auth_event.dart';

class SplashPage extends StatefulWidget {
  const SplashPage({super.key});

  @override
  State<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends State<SplashPage> {
  @override
  void initState() {
    super.initState();

    Future.microtask(() {
      context.read<AuthBloc>().add(AppStarted());
    });
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      backgroundColor: AppColors.primary,
      body: Center(
        child: Text(
          'YouStayOn',
          style: TextStyle(
            color: Colors.white,
            fontSize: 28,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
    );
  }
}
```

The splash screen immediately asks Laravel whether the stored Sanctum token is still valid.

### Step 14.5.8 — Connect LoginPage

Inside `LoginPage`, wrap with `BlocListener`.

dart

```
BlocListener<AuthBloc, AuthState>(
  listener: (context, state) {
    if (state is AuthFailure) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(state.message)),
      );
    }
  },
  child: BlocBuilder<AuthBloc, AuthState>(
    builder: (context, state) {
      // existing UI
    },
  ),
)
```

The `Sign in` button already dispatches:

dart

```
context.read<AuthBloc>().add(
  LoginRequested(
    email: _email.text.trim(),
    password: _password.text,
  ),
);
```

After successful login, GoRouter automatically redirects to `/dashboard`.

### Step 14.5.9 — Connect RegisterPage

Do exactly the same:

* Validate fields

* Dispatch `RegisterRequested`

* Listen for `AuthFailure`

* Allow GoRouter to redirect automatically

### Step 14.5.10 — Dashboard placeholder

Create `lib/features/dashboard/presentation/pages/dashboard_page.dart`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../auth/presentation/bloc/auth_bloc.dart';
import '../../../auth/presentation/bloc/auth_event.dart';

class DashboardPage extends StatelessWidget {
  const DashboardPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('YouStayOn'),
        actions: [
          IconButton(
            onPressed: () {
              context.read<AuthBloc>().add(
                    LogoutRequested(),
                  );
            },
            icon: const Icon(Icons.logout),
          ),
        ],
      ),
      body: const Center(
        child: Text(
          'Dashboard Connected',
        ),
      ),
    );
  }
}
```

Logout immediately:

* calls `POST /api/auth/logout`

* clears the Sanctum token

* emits `AuthUnauthenticated`

* redirects to `/login`

### Authentication flow

```
App Launch
     |
     v
Splash
     |
     v
Secure Storage
     |
     +-----------------------+
     |                       |
Token Exists           No Token
     |                       |
     v                       v
GET /api/auth/me        Login Page
     |
     +-----------------------+
     |                       |
Valid Token            Invalid Token
     |                       |
     v                       v
Dashboard              Login Page
     |
     v
Logout
     |
     v
POST /api/auth/logout
     |
     v
Token Cleared
     |
     v
Login Page
```

This is exactly the production flow used by Flutter apps connected to Laravel Sanctum.

### Step 14.5.11 — Backend configuration

Ensure Laravel is running.

Bash

```
cd C:\YouStayOn\backend
php artisan serve
```

If using Android Emulator:

```
Base URL:
http://10.0.2.2:8000/api
```

If using a physical Android device on the same Wi-Fi:

```
http://YOUR_PC_LOCAL_IP:8000/api
```

### Test checklist

Run the Flutter app and verify:

* App opens Splash

* No token → Login page

* Register creates a real Laravel user

* Login returns a real Sanctum token

* Dashboard opens

* App restart restores session automatically

* Logout returns to Login

* Invalid token forces re-authentication

When all of these pass, Flutter will be fully authenticated against the Laravel backend, and every subsequent module (Wallet, Subscriptions, Purchases, Radar Intelligence, Notifications, Dashboard, Analytics) will reuse the same authenticated architecture.

The next milestone is Milestone 14.6 — Dashboard Module Foundation, where we will build the premium dashboard home screen connected directly to the Laravel dashboard APIs (`/dashboard/overview`, `/dashboard/snapshot`, `/dashboard/radar-score`, `/dashboard/recent-activity`, `/dashboard/spending-analytics`, and `/dashboard/usage-trends`) with a high-performance card layout, animated metrics, pull-to-refresh, offline caching, and ultra-fast navigation that mirrors the backend exactly.
Excellent. We are now implementing Milestone 14.5 — Authentication Integration & Routing, which is the milestone that turns the Flutter app into a real client for the Laravel Sanctum backend. From this point onward, the mobile app will authenticate directly against your production-ready Laravel API, persist the Sanctum token securely, restore sessions automatically, and protect navigation exactly like a commercial fintech application.

This milestone is intentionally aligned 100% with the Laravel backend we completed. We will not create any Flutter-only authentication logic; every authentication decision will come from the backend.

### Milestone 14.5: Authentication integration & routing

We are connecting the premium authentication UI to the BLoC layer, Sanctum token storage, automatic session restoration, protected routes, and navigation guards so Flutter mirrors the Laravel backend exactly.

### What we are implementing

| Layer                           | Status        |
| ------------------------------- | ------------- |
| Sanctum token persistence       | Implement now |
| Authenticated Dio client        | Implement now |
| Automatic session restoration   | Implement now |
| Splash bootstrap                | Implement now |
| GoRouter route guards           | Implement now |
| Login → Dashboard navigation    | Implement now |
| Register → Dashboard navigation | Implement now |
| Logout → Login navigation       | Implement now |
| Expired token handling          | Implement now |

This is exactly how production Flutter apps integrate with Laravel Sanctum.

### Step 14.5.1 — Secure storage service

Create `lib/core/storage/secure_storage_service.dart`.

dart

```
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SecureStorageService {
  static const _tokenKey = 'auth_token';
  static const _userKey = 'user_json';

  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  Future<void> saveToken(String token) async {
    await _storage.write(key: _tokenKey, value: token);
  }

  Future<String?> getToken() async {
    return _storage.read(key: _tokenKey);
  }

  Future<void> saveUserJson(String json) async {
    await _storage.write(key: _userKey, value: json);
  }

  Future<String?> getUserJson() async {
    return _storage.read(key: _userKey);
  }

  Future<void> clearAll() async {
    await _storage.delete(key: _tokenKey);
    await _storage.delete(key: _userKey);
  }
}
```

This stores the Laravel Sanctum personal access token securely on the device.

### Step 14.5.2 — Auth interceptor

Create `lib/core/network/auth_interceptor.dart`.

dart

```
import 'package:dio/dio.dart';

import '../storage/secure_storage_service.dart';

class AuthInterceptor extends Interceptor {
  AuthInterceptor(this._storage);

  final SecureStorageService _storage;

  @override
  Future<void> onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    final token = await _storage.getToken();

    if (token != null) {
      options.headers['Authorization'] = 'Bearer $token';
      options.headers['Accept'] = 'application/json';
    }

    handler.next(options);
  }

  @override
  Future<void> onError(
    DioException err,
    ErrorInterceptorHandler handler,
  ) async {
    if (err.response?.statusCode == 401) {
      await _storage.clearAll();
    }

    handler.next(err);
  }
}
```

This ensures every authenticated request automatically includes:

http

```
Authorization: Bearer {sanctum_token}
```

which exactly matches your Laravel backend.

### Step 14.5.3 — Update Dio client

Update `lib/core/network/dio_client.dart`.

dart

```
import 'package:dio/dio.dart';
import 'package:pretty_dio_logger/pretty_dio_logger.dart';

import '../storage/secure_storage_service.dart';
import 'auth_interceptor.dart';

class DioClient {
  DioClient(this._storage)
      : dio = Dio(
          BaseOptions(
            baseUrl: 'http://10.0.2.2:8000/api',
            connectTimeout: const Duration(seconds: 30),
            receiveTimeout: const Duration(seconds: 30),
            sendTimeout: const Duration(seconds: 30),
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          ),
        ) {
    dio.interceptors.add(AuthInterceptor(_storage));

    dio.interceptors.add(
      PrettyDioLogger(
        requestBody: true,
        responseBody: true,
      ),
    );
  }

  final SecureStorageService _storage;
  final Dio dio;
}
```

For Android Emulator, `10.0.2.2` correctly points to your local Laravel server.

### Step 14.5.4 — Dependency injection

Update `lib/core/di/injection.dart`.

Register storage first.

dart

```
getIt.registerLazySingleton(
  () => SecureStorageService(),
);

getIt.registerLazySingleton(
  () => DioClient(getIt()),
);
```

The order matters because `DioClient` depends on `SecureStorageService`.

### Step 14.5.5 — Router refresh from AuthBloc

GoRouter must react automatically when authentication changes.

Create `lib/core/router/router_refresh_notifier.dart`.

dart

```
import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class RouterRefreshNotifier extends ChangeNotifier {
  RouterRefreshNotifier(Stream<dynamic> stream) {
    _subscription = stream.asBroadcastStream().listen(
          (_) => notifyListeners(),
        );
  }

  late final StreamSubscription<dynamic> _subscription;

  @override
  void dispose() {
    _subscription.cancel();
    super.dispose();
  }
}
```

### Step 14.5.6 — Update app router

Update `lib/core/router/app_router.dart`.

dart

```
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';

import '../../features/auth/presentation/bloc/auth_bloc.dart';
import '../../features/auth/presentation/bloc/auth_state.dart';
import '../../features/auth/presentation/pages/login_page.dart';
import '../../features/auth/presentation/pages/register_page.dart';
import '../../features/auth/presentation/pages/splash_page.dart';
import '../../features/dashboard/presentation/pages/dashboard_page.dart';
import '../di/injection.dart';
import 'router_refresh_notifier.dart';

class AppRouter {
  static final GoRouter router = GoRouter(
    initialLocation: '/splash',
    refreshListenable: RouterRefreshNotifier(
      getIt<AuthBloc>().stream,
    ),
    redirect: (context, state) {
      final authState = getIt<AuthBloc>().state;

      final loggingIn = state.matchedLocation == '/login';
      final registering = state.matchedLocation == '/register';
      final splash = state.matchedLocation == '/splash';

      if (authState is AuthLoading) {
        return splash ? null : '/splash';
      }

      if (authState is AuthAuthenticated) {
        if (loggingIn || registering || splash) {
          return '/dashboard';
        }
        return null;
      }

      if (authState is AuthUnauthenticated) {
        if (loggingIn || registering) {
          return null;
        }
        return '/login';
      }

      return null;
    },
    routes: [
      GoRoute(
        path: '/splash',
        builder: (_, __) => const SplashPage(),
      ),
      GoRoute(
        path: '/login',
        builder: (_, __) => const LoginPage(),
      ),
      GoRoute(
        path: '/register',
        builder: (_, __) => const RegisterPage(),
      ),
      GoRoute(
        path: '/dashboard',
        builder: (_, __) => const DashboardPage(),
      ),
    ],
  );
}
```

This gives us automatic navigation protection.

### Step 14.5.7 — Splash bootstrap

Update `SplashPage`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../../core/theme/app_colors.dart';
import '../bloc/auth_bloc.dart';
import '../bloc/auth_event.dart';

class SplashPage extends StatefulWidget {
  const SplashPage({super.key});

  @override
  State<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends State<SplashPage> {
  @override
  void initState() {
    super.initState();

    Future.microtask(() {
      context.read<AuthBloc>().add(AppStarted());
    });
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      backgroundColor: AppColors.primary,
      body: Center(
        child: Text(
          'YouStayOn',
          style: TextStyle(
            color: Colors.white,
            fontSize: 28,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
    );
  }
}
```

The splash screen immediately asks Laravel whether the stored Sanctum token is still valid.

### Step 14.5.8 — Connect LoginPage

Inside `LoginPage`, wrap with `BlocListener`.

dart

```
BlocListener<AuthBloc, AuthState>(
  listener: (context, state) {
    if (state is AuthFailure) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(state.message)),
      );
    }
  },
  child: BlocBuilder<AuthBloc, AuthState>(
    builder: (context, state) {
      // existing UI
    },
  ),
)
```

The `Sign in` button already dispatches:

dart

```
context.read<AuthBloc>().add(
  LoginRequested(
    email: _email.text.trim(),
    password: _password.text,
  ),
);
```

After successful login, GoRouter automatically redirects to `/dashboard`.

### Step 14.5.9 — Connect RegisterPage

Do exactly the same:

* Validate fields

* Dispatch `RegisterRequested`

* Listen for `AuthFailure`

* Allow GoRouter to redirect automatically

### Step 14.5.10 — Dashboard placeholder

Create `lib/features/dashboard/presentation/pages/dashboard_page.dart`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../auth/presentation/bloc/auth_bloc.dart';
import '../../../auth/presentation/bloc/auth_event.dart';

class DashboardPage extends StatelessWidget {
  const DashboardPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('YouStayOn'),
        actions: [
          IconButton(
            onPressed: () {
              context.read<AuthBloc>().add(
                    LogoutRequested(),
                  );
            },
            icon: const Icon(Icons.logout),
          ),
        ],
      ),
      body: const Center(
        child: Text(
          'Dashboard Connected',
        ),
      ),
    );
  }
}
```

Logout immediately:

* calls `POST /api/auth/logout`

* clears the Sanctum token

* emits `AuthUnauthenticated`

* redirects to `/login`

### Authentication flow

```
App Launch
     |
     v
Splash
     |
     v
Secure Storage
     |
     +-----------------------+
     |                       |
Token Exists           No Token
     |                       |
     v                       v
GET /api/auth/me        Login Page
     |
     +-----------------------+
     |                       |
Valid Token            Invalid Token
     |                       |
     v                       v
Dashboard              Login Page
     |
     v
Logout
     |
     v
POST /api/auth/logout
     |
     v
Token Cleared
     |
     v
Login Page
```

This is exactly the production flow used by Flutter apps connected to Laravel Sanctum.

### Step 14.5.11 — Backend configuration

Ensure Laravel is running.

Bash

```
cd C:\YouStayOn\backend
php artisan serve
```

If using Android Emulator:

```
Base URL:
http://10.0.2.2:8000/api
```

If using a physical Android device on the same Wi-Fi:

```
http://YOUR_PC_LOCAL_IP:8000/api
```

### Test checklist

Run the Flutter app and verify:

* App opens Splash

* No token → Login page

* Register creates a real Laravel user

* Login returns a real Sanctum token

* Dashboard opens

* App restart restores session automatically

* Logout returns to Login

* Invalid token forces re-authentication

When all of these pass, Flutter will be fully authenticated against the Laravel backend, and every subsequent module (Wallet, Subscriptions, Purchases, Radar Intelligence, Notifications, Dashboard, Analytics) will reuse the same authenticated architecture.

The next milestone is Milestone 14.6 — Dashboard Module Foundation, where we will build the premium dashboard home screen connected directly to the Laravel dashboard APIs (`/dashboard/overview`, `/dashboard/snapshot`, `/dashboard/radar-score`, `/dashboard/recent-activity`, `/dashboard/spending-analytics`, and `/dashboard/usage-trends`) with a high-performance card layout, animated metrics, pull-to-refresh, offline caching, and ultra-fast navigation that mirrors the backend exactly.
Dashboard Module Foundation, where we will build the premium dashboard home screen connected directly to the Laravel dashboard APIs (`/dashboard/overview`, `/dashboard/snapshot`, `/dashboard/radar-score`, `/dashboard/recent-activity`, `/dashboard/spending-analytics`, and `/dashboard/usage-trends`) with a high-performance card layout, animated metrics, pull-to-refresh, offline caching, and ultra-fast navigation that mirrors the backend exactly.
