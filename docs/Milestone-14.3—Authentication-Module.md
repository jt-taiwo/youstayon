Excellent. We are now entering Milestone 14.3 — Authentication Module, and this is one of the most important milestones because it establishes the entire authenticated user experience for YouStayOn. We are going to implement it exactly against the Laravel Sanctum backend you already completed, not a mock authentication flow.

This milestone will mirror your Laravel backend architecture one-to-one.

### Milestone 14.3: Authentication module

We are implementing the complete Laravel Sanctum authentication flow with BLoC, repository abstraction, token persistence, session restoration, authenticated routing, validation, and polished UX.

### Backend endpoints we are connecting to

These are the Laravel endpoints already implemented and tested.

| Backend Endpoint               | Flutter Use                    |
| ------------------------------ | ------------------------------ |
| POST /api/auth/register        | Registration                   |
| POST /api/auth/login           | Login                          |
| POST /api/auth/logout          | Logout                         |
| GET /api/auth/me               | Current user / session restore |
| POST /api/auth/forgot-password | Password reset request         |

The Flutter authentication state must remain synchronized with Laravel Sanctum at all times.

### Step 14.3.1 — Feature structure

Create the full authentication feature structure.

```
lib/features/auth/
├── data/
│   ├── datasources/
│   │   └── auth_remote_data_source.dart
│   ├── models/
│   │   ├── auth_token_model.dart
│   │   └── user_model.dart
│   └── repositories/
│       └── auth_repository_impl.dart
├── domain/
│   ├── entities/
│   │   └── user.dart
│   ├── repositories/
│   │   └── auth_repository.dart
│   └── usecases/
│       ├── login_usecase.dart
│       ├── register_usecase.dart
│       ├── logout_usecase.dart
│       └── get_current_user_usecase.dart
└── presentation/
    ├── bloc/
    │   ├── auth_bloc.dart
    │   ├── auth_event.dart
    │   └── auth_state.dart
    └── pages/
        ├── login_page.dart
        ├── register_page.dart
        └── splash_page.dart
```

This exactly mirrors the Laravel separation of Controller → Service → Repository.

### Step 14.3.2 — User entity

Create `lib/features/auth/domain/entities/user.dart`.

dart

```
class User {
  const User({
    required this.uuid,
    required this.firstName,
    required this.lastName,
    required this.email,
    required this.phone,
    required this.emailVerified,
    this.avatarUrl,
  });

  final String uuid;
  final String firstName;
  final String lastName;
  final String email;
  final String phone;
  final bool emailVerified;
  final String? avatarUrl;

  String get fullName => '$firstName $lastName';
}
```

This matches the Laravel User resource.

### Step 14.3.3 — User model

Create `lib/features/auth/data/models/user_model.dart`.

dart

```
import '../../domain/entities/user.dart';

class UserModel extends User {
  const UserModel({
    required super.uuid,
    required super.firstName,
    required super.lastName,
    required super.email,
    required super.phone,
    required super.emailVerified,
    super.avatarUrl,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      uuid: json['uuid'] as String,
      firstName: json['first_name'] as String,
      lastName: json['last_name'] as String,
      email: json['email'] as String,
      phone: json['phone'] as String,
      emailVerified: json['email_verified'] as bool? ?? false,
      avatarUrl: json['avatar_url'] as String?,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'uuid': uuid,
      'first_name': firstName,
      'last_name': lastName,
      'email': email,
      'phone': phone,
      'email_verified': emailVerified,
      'avatar_url': avatarUrl,
    };
  }
}
```

### Step 14.3.4 — Auth token model

Create `lib/features/auth/data/models/auth_token_model.dart`.

dart

```
class AuthTokenModel {
  const AuthTokenModel({
    required this.token,
    required this.user,
  });

  final String token;
  final Map<String, dynamic> user;

  factory AuthTokenModel.fromJson(Map<String, dynamic> json) {
    return AuthTokenModel(
      token: json['data']['token'] as String,
      user: json['data']['user'] as Map<String, dynamic>,
    );
  }
}
```

This matches the Laravel login response:

JSON

```
{
  "success": true,
  "data": {
    "token": "1|abc123...",
    "user": {
      "uuid": "..."
    }
  }
}
```

### Step 14.3.5 — Repository contract

Create `lib/features/auth/domain/repositories/auth_repository.dart`.

dart

```
import '../entities/user.dart';

abstract class AuthRepository {
  Future<User> login({
    required String email,
    required String password,
  });

  Future<User> register({
    required String firstName,
    required String lastName,
    required String email,
    required String phone,
    required String password,
  });

  Future<User?> getCurrentUser();

  Future<void> logout();

  Future<bool> isAuthenticated();
}
```

### Step 14.3.6 — Remote data source

Create `lib/features/auth/data/datasources/auth_remote_data_source.dart`.

dart

```
import 'package:dio/dio.dart';

import '../../../../core/network/api_endpoints.dart';
import '../../../../core/network/dio_client.dart';
import '../models/auth_token_model.dart';
import '../models/user_model.dart';

class AuthRemoteDataSource {
  AuthRemoteDataSource(this._client);

  final DioClient _client;

  Dio get dio => _client.dio;

  Future<AuthTokenModel> login({
    required String email,
    required String password,
  }) async {
    final response = await dio.post(
      ApiEndpoints.login,
      data: {
        'email': email,
        'password': password,
      },
    );

    return AuthTokenModel.fromJson(response.data);
  }

  Future<AuthTokenModel> register({
    required String firstName,
    required String lastName,
    required String email,
    required String phone,
    required String password,
  }) async {
    final response = await dio.post(
      ApiEndpoints.register,
      data: {
        'first_name': firstName,
        'last_name': lastName,
        'email': email,
        'phone': phone,
        'password': password,
        'password_confirmation': password,
      },
    );

    return AuthTokenModel.fromJson(response.data);
  }

  Future<UserModel> getCurrentUser() async {
    final response = await dio.get(ApiEndpoints.me);

    return UserModel.fromJson(response.data['data']);
  }

  Future<void> logout() async {
    await dio.post(ApiEndpoints.logout);
  }
}
```

### Step 14.3.7 — Repository implementation

Create `lib/features/auth/data/repositories/auth_repository_impl.dart`.

dart

```
import 'dart:convert';

import '../../../../core/storage/secure_storage_service.dart';
import '../../domain/entities/user.dart';
import '../../domain/repositories/auth_repository.dart';
import '../datasources/auth_remote_data_source.dart';
import '../models/user_model.dart';

class AuthRepositoryImpl implements AuthRepository {
  AuthRepositoryImpl({
    required AuthRemoteDataSource remote,
    required SecureStorageService storage,
  })  : _remote = remote,
        _storage = storage;

  final AuthRemoteDataSource _remote;
  final SecureStorageService _storage;

  @override
  Future<User> login({
    required String email,
    required String password,
  }) async {
    final auth = await _remote.login(
      email: email,
      password: password,
    );

    await _storage.saveToken(auth.token);
    await _storage.saveUserJson(jsonEncode(auth.user));

    return UserModel.fromJson(auth.user);
  }

  @override
  Future<User> register({
    required String firstName,
    required String lastName,
    required String email,
    required String phone,
    required String password,
  }) async {
    final auth = await _remote.register(
      firstName: firstName,
      lastName: lastName,
      email: email,
      phone: phone,
      password: password,
    );

    await _storage.saveToken(auth.token);
    await _storage.saveUserJson(jsonEncode(auth.user));

    return UserModel.fromJson(auth.user);
  }

  @override
  Future<User?> getCurrentUser() async {
    final token = await _storage.getToken();

    if (token == null) return null;

    try {
      return await _remote.getCurrentUser();
    } catch (_) {
      await _storage.clearAll();
      return null;
    }
  }

  @override
  Future<void> logout() async {
    try {
      await _remote.logout();
    } finally {
      await _storage.clearAll();
    }
  }

  @override
  Future<bool> isAuthenticated() async {
    return await _storage.getToken() != null;
  }
}
```

### Step 14.3.8 — BLoC state management

Create `auth_event.dart`.

dart

```
import 'package:equatable/equatable.dart';

abstract class AuthEvent extends Equatable {
  const AuthEvent();

  @override
  List<Object?> get props => [];
}

class AppStarted extends AuthEvent {}

class LoginRequested extends AuthEvent {
  const LoginRequested({
    required this.email,
    required this.password,
  });

  final String email;
  final String password;

  @override
  List<Object?> get props => [email, password];
}

class RegisterRequested extends AuthEvent {
  const RegisterRequested({
    required this.firstName,
    required this.lastName,
    required this.email,
    required this.phone,
    required this.password,
  });

  final String firstName;
  final String lastName;
  final String email;
  final String phone;
  final String password;

  @override
  List<Object?> get props => [
        firstName,
        lastName,
        email,
        phone,
        password,
      ];
}

class LogoutRequested extends AuthEvent {}
```

Create `auth_state.dart`.

dart

```
import 'package:equatable/equatable.dart';

import '../../domain/entities/user.dart';

abstract class AuthState extends Equatable {
  const AuthState();

  @override
  List<Object?> get props => [];
}

class AuthInitial extends AuthState {}

class AuthLoading extends AuthState {}

class AuthAuthenticated extends AuthState {
  const AuthAuthenticated(this.user);

  final User user;

  @override
  List<Object?> get props => [user];
}

class AuthUnauthenticated extends AuthState {}

class AuthFailure extends AuthState {
  const AuthFailure(this.message);

  final String message;

  @override
  List<Object?> get props => [message];
}
```

Create `auth_bloc.dart`.

dart

```
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../domain/repositories/auth_repository.dart';
import 'auth_event.dart';
import 'auth_state.dart';

class AuthBloc extends Bloc<AuthEvent, AuthState> {
  AuthBloc(this._repository) : super(AuthInitial()) {
    on<AppStarted>(_onStarted);
    on<LoginRequested>(_onLogin);
    on<RegisterRequested>(_onRegister);
    on<LogoutRequested>(_onLogout);
  }

  final AuthRepository _repository;

  Future<void> _onStarted(
    AppStarted event,
    Emitter<AuthState> emit,
  ) async {
    emit(AuthLoading());

    final user = await _repository.getCurrentUser();

    if (user == null) {
      emit(AuthUnauthenticated());
    } else {
      emit(AuthAuthenticated(user));
    }
  }

  Future<void> _onLogin(
    LoginRequested event,
    Emitter<AuthState> emit,
  ) async {
    emit(AuthLoading());

    try {
      final user = await _repository.login(
        email: event.email,
        password: event.password,
      );

      emit(AuthAuthenticated(user));
    } catch (e) {
      emit(AuthFailure('Login failed'));
    }
  }

  Future<void> _onRegister(
    RegisterRequested event,
    Emitter<AuthState> emit,
  ) async {
    emit(AuthLoading());

    try {
      final user = await _repository.register(
        firstName: event.firstName,
        lastName: event.lastName,
        email: event.email,
        phone: event.phone,
        password: event.password,
      );

      emit(AuthAuthenticated(user));
    } catch (_) {
      emit(AuthFailure('Registration failed'));
    }
  }

  Future<void> _onLogout(
    LogoutRequested event,
    Emitter<AuthState> emit,
  ) async {
    await _repository.logout();

    emit(AuthUnauthenticated());
  }
}
```

### Step 14.3.9 — Register dependencies

Open `lib/core/di/injection.dart` and extend it.

Add imports:

dart

```
import '../../features/auth/data/datasources/auth_remote_data_source.dart';
import '../../features/auth/data/repositories/auth_repository_impl.dart';
import '../../features/auth/domain/repositories/auth_repository.dart';
import '../../features/auth/presentation/bloc/auth_bloc.dart';
```

Then register:

dart

```
getIt.registerLazySingleton(
  () => AuthRemoteDataSource(getIt()),
);

getIt.registerLazySingleton<AuthRepository>(
  () => AuthRepositoryImpl(
    remote: getIt(),
    storage: getIt(),
  ),
);

getIt.registerFactory(
  () => AuthBloc(getIt()),
);
```

### Step 14.3.10 — Bootstrap authentication

Update `SplashPage` to dispatch `AppStarted`.

Update `GoRouter` to redirect based on authentication state.

The flow becomes:

```
App Launch
      |
      v
Splash Screen
      |
      v
Read Secure Token
      |
      +------------------------+
      |                        |
Token Exists              No Token
      |                        |
      v                        v
GET /api/auth/me          Login Page
      |
      +------------------------+
      |                        |
Valid Token               Invalid Token
      |                        |
      v                        v
Home Dashboard            Login Page
```

This is exactly how production Flutter applications integrate with Laravel Sanctum.

### What we now have

After completing Milestone 14.3, the Flutter application will support:

* Secure Sanctum token persistence

* Automatic session restoration

* Login

* Registration

* Logout

* Current authenticated user

* BLoC authentication state

* Protected navigation

* Error handling

* Loading states

* Repository abstraction identical to Laravel

This becomes the foundation for Wallet, Dashboard, Subscriptions, Purchases, Radar Intelligence, Notifications, and Analytics, all of which will reuse the authenticated Dio client and dependency injection graph we have already established.

The next milestone after this is Milestone 14.4 — Premium Authentication UI, where we will build a premium, fintech-grade login and registration experience with YouStayOn branding, animations, validation, accessibility, dark mode support, and ultra-fast navigation.
