Excellent. This is the final major user-account milestone before we move into polish, notifications, offline optimization, and production readiness. We are now implementing everything related to the authenticated user profile, and because your Laravel backend already passed the full test suite, Flutter can mirror it 100%.

This milestone will connect directly to the backend APIs you already implemented and tested:

* `GET /user/profile`

* `PUT /user/profile`

* `POST /user/avatar`

* `DELETE /user/avatar`

* `POST /user/change-password`

* `DELETE /user/account`

* Notification preference APIs

* Quiet hours APIs

* Security/session APIs

The goal is a premium settings experience comparable to Revolut, Monzo, or Apple Wallet, while remaining perfectly synchronized with Laravel.

### Milestone 14.13 — Settings, Profile & Account Management

Implement profile editing, avatar upload and removal, password change, notification preferences, quiet hours, security settings, account deletion, and complete user preference synchronization directly against the Laravel APIs that already passed the backend test suite.

### What we will build

### Profile management

* View profile

* Edit first name

* Edit last name

* Edit phone number

* Email display

* Validation

* Optimistic updates

* Avatar upload

* Avatar removal

### Security

* Change password

* Current password verification

* Strong password validation

* Session security

* Device management placeholder

* Biometric-ready architecture

### Notifications

* Push notifications

* Email notifications

* SMS notifications

* Radar alerts

* Renewal reminders

* Marketing preferences

### Quiet hours

* Enable/disable quiet hours

* Start time

* End time

* Overnight scheduling

* Timezone awareness

### Account

* Export account data placeholder

* Privacy settings

* Delete account

* Confirmation dialogs

* Backend synchronization

* Logout all sessions

### Flutter architecture

```
lib/features/settings/
├── data/
│   ├── datasources/
│   ├── models/
│   └── repositories/
├── domain/
│   ├── entities/
│   ├── repositories/
│   └── usecases/
├── presentation/
│   ├── bloc/
│   ├── pages/
│   └── widgets/
└── settings_module.dart
```

### Step 14.13.1 — Settings entities

Create:

```
lib/features/settings/domain/entities/user_profile_entity.dart
```

dart

```
class UserProfileEntity {
  final String uuid;
  final String firstName;
  final String lastName;
  final String email;
  final String phone;
  final String? avatarUrl;
  final bool emailVerified;

  const UserProfileEntity({
    required this.uuid,
    required this.firstName,
    required this.lastName,
    required this.email,
    required this.phone,
    this.avatarUrl,
    required this.emailVerified,
  });
}
```

Create:

```
lib/features/settings/domain/entities/notification_preferences_entity.dart
```

dart

```
class NotificationPreferencesEntity {
  final bool pushEnabled;
  final bool emailEnabled;
  final bool smsEnabled;
  final bool radarAlerts;
  final bool renewalReminders;
  final bool marketingEnabled;

  const NotificationPreferencesEntity({
    required this.pushEnabled,
    required this.emailEnabled,
    required this.smsEnabled,
    required this.radarAlerts,
    required this.renewalReminders,
    required this.marketingEnabled,
  });
}
```

Create:

```
lib/features/settings/domain/entities/quiet_hours_entity.dart
```

dart

```
class QuietHoursEntity {
  final bool enabled;
  final String startTime;
  final String endTime;

  const QuietHoursEntity({
    required this.enabled,
    required this.startTime,
    required this.endTime,
  });
}
```

### Step 14.13.2 — Repository interface

Create:

```
lib/features/settings/domain/repositories/settings_repository.dart
```

dart

```
import '../entities/user_profile_entity.dart';
import '../entities/notification_preferences_entity.dart';
import '../entities/quiet_hours_entity.dart';

abstract class SettingsRepository {
  Future<UserProfileEntity> getProfile();

  Future<UserProfileEntity> updateProfile({
    required String firstName,
    required String lastName,
    required String phone,
  });

  Future<String?> uploadAvatar(String path);

  Future<void> removeAvatar();

  Future<void> changePassword({
    required String currentPassword,
    required String newPassword,
    required String confirmation,
  });

  Future<NotificationPreferencesEntity> getNotificationPreferences();

  Future<void> updateNotificationPreferences(
      NotificationPreferencesEntity preferences);

  Future<QuietHoursEntity> getQuietHours();

  Future<void> updateQuietHours(QuietHoursEntity quietHours);

  Future<void> deleteAccount();
}
```

### Step 14.13.3 — Remote datasource

Create:

```
lib/features/settings/data/datasources/settings_remote_data_source.dart
```

Connect directly to Laravel:

dart

```
Future<dynamic> profile() async =>
    (await api.get('/user/profile')).data['data'];

Future<dynamic> updateProfile(Map<String, dynamic> body) async =>
    (await api.put('/user/profile', data: body)).data['data'];

Future<void> removeAvatar() async =>
    await api.delete('/user/avatar');

Future<void> changePassword(Map<String, dynamic> body) async =>
    await api.post('/user/change-password', data: body);

Future<void> deleteAccount() async =>
    await api.delete('/user/account');
```

These endpoints match the backend exactly.

### Step 14.13.4 — Settings BLoC

Create:

```
lib/features/settings/presentation/bloc/settings_bloc.dart
```

Events:

* `LoadSettings`

* `UpdateProfile`

* `UploadAvatar`

* `RemoveAvatar`

* `ChangePassword`

* `UpdateNotificationPreferences`

* `UpdateQuietHours`

* `DeleteAccount`

State:

dart

```
class SettingsState {
  final bool loading;
  final UserProfileEntity? profile;
  final NotificationPreferencesEntity? preferences;
  final QuietHoursEntity? quietHours;
  final String? error;

  const SettingsState({
    this.loading = false,
    this.profile,
    this.preferences,
    this.quietHours,
    this.error,
  });
}
```

### Step 14.13.5 — Premium Settings page

Create:

```
lib/features/settings/presentation/pages/settings_page.dart
```

Layout:

### Settings

![How To Change the Account Name of a Local Account | SoftwareKeep](https://images.openai.com/static-rsc-4/5tlPdjHPkRxG3ihA8KIvwEDvNOjNfew2l41siQqSx4mwjtn9iMpcGWnq495nLUKqsYGzRjNN9Bxzu3xfC16QZrXZlyL_I9a_oXdxA-T66lxYgIxzLgicei3f3SdF6UevDYNJdZ1gYIO9CD_v0jhXjyeAYwdm8LHyKsBeMvmZFcsFvFk-67iWvBnEM6FS8zOW?purpose=fullsize)

### John Doe

[john@example.com](mailto:john@example.com)

### Account

Edit profile

Avatar

Change password

### Notifications

Push notifications

Email notifications

Quiet hours

### Security

Logout

Delete account

This should feel like iOS Settings + Revolut.

### Step 14.13.6 — Edit profile page

Create:

```
lib/features/settings/presentation/pages/edit_profile_page.dart
```

Features:

* Prefilled form

* First name

* Last name

* Phone

* Email (read-only)

* Save button

* Validation

* Loading overlay

* Success animation

Connect to:

```
PUT /user/profile
```

### Step 14.13.7 — Avatar upload

Use:

* `image_picker`

* `dio multipart upload`

Add dependency:

YAML

```
dependencies:
  image_picker: ^1.1.2
```

Run:

Bash

```
flutter pub get
```

Upload:

dart

```
FormData.fromMap({
  'avatar': await MultipartFile.fromFile(path),
});
```

Connect to:

```
POST /user/avatar
```

Support:

* Camera

* Gallery

* Crop-ready architecture

* Remove avatar

### Step 14.13.8 — Change password

Create:

```
lib/features/settings/presentation/pages/change_password_page.dart
```

Fields:

* Current password

* New password

* Confirm password

Validation:

* Minimum length

* Uppercase

* Lowercase

* Number

* Special character

* Confirmation match

Connect to:

```
POST /user/change-password
```

After success:

* Show confirmation

* Clear fields

* Optionally force re-authentication

### Step 14.13.9 — Notification preferences

Create:

```
lib/features/settings/presentation/pages/notification_preferences_page.dart
```

Toggles:

* Push notifications

* Email notifications

* SMS notifications

* Radar alerts

* Renewal reminders

* Marketing messages

Connect to Laravel notification preference APIs.

Use optimistic updates for instant UX.

### Step 14.13.10 — Quiet hours

Create:

```
lib/features/settings/presentation/pages/quiet_hours_page.dart
```

Features:

* Enable switch

* Start time picker

* End time picker

* Overnight support

* Preview text

Example:

Notifications will be silenced from 10:00 PM to 7:00 AM.

Connect directly to Laravel quiet-hours APIs.

### Step 14.13.11 — Delete account

Create:

```
lib/features/settings/presentation/pages/delete_account_page.dart
```

Flow:

1. Warning screen

2. Consequences listed

3. Type DELETE confirmation

4. Final confirmation dialog

5. Call:

   DELETE /user/account

After success:

* Clear secure storage

* Reset Hive

* Clear BLoC

* Navigate to login

* Remove navigation history

This mirrors the backend account deletion implementation.

### Step 14.13.12 — Routing

Add routes:

dart

```
GoRoute(
  path: '/settings',
  builder: (_, __) => const SettingsPage(),
),
GoRoute(
  path: '/settings/profile',
  builder: (_, __) => const EditProfilePage(),
),
GoRoute(
  path: '/settings/password',
  builder: (_, __) => const ChangePasswordPage(),
),
GoRoute(
  path: '/settings/notifications',
  builder: (_, __) => const NotificationPreferencesPage(),
),
GoRoute(
  path: '/settings/quiet-hours',
  builder: (_, __) => const QuietHoursPage(),
),
GoRoute(
  path: '/settings/delete-account',
  builder: (_, __) => const DeleteAccountPage(),
),
```

### Step 14.13.13 — Dashboard integration

Add a settings entry from:

* Dashboard

* Profile avatar

* Drawer

* Bottom navigation profile tab

The transition should use:

* Fade

* Scale

* Cupertino push

* Hero animation for avatar

### Step 14.13.14 — Backend parity checklist

| Laravel backend          | Flutter module            |
| ------------------------ | ------------------------- |
| Current user             | Profile screen            |
| Update profile           | Edit profile              |
| Upload avatar            | Avatar picker/upload      |
| Remove avatar            | Avatar removal            |
| Change password          | Password screen           |
| Notification preferences | Notification settings     |
| Quiet hours              | Quiet hours UI            |
| Delete account           | Account deletion flow     |
| Sanctum logout           | Logout integration        |
| Session restoration      | Bootstrap synchronization |

This milestone gives YouStayOn a complete production-grade account management system with premium UX and full backend synchronization.

### Git commit

After implementation:

Bash

```
git add .
git commit -m "feat(mobile): implement settings profile and account management module"
git push origin main
```

Once this is complete, we proceed to Milestone.14 — Notifications & Real-Time Experience, where we will implement push notifications, local notifications, Radar alerts, renewal reminders, background notification handling, deep linking from notifications, unread badge counts, notification center synchronization, and real-time event integration with the Laravel notification engine and scheduler you already built.
