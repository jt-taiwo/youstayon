Perfect. This is the right place to slow down and get the UI foundation correct, because Milestone 14.4 will define the visual identity of YouStayOn. Since the Laravel backend is already production-grade and fully tested (226 tests passed), the Flutter UI should feel like a premium Nigerian fintech / telecom intelligence product rather than a generic Flutter app.

We will build a UI that can realistically sit beside Moniepoint, Opay, Carbon, Kuda, or PalmPay in terms of perceived quality: fast navigation, deep spacing consistency, subtle motion, accessible typography, and a clean dark/light theme system.

### Milestone 14.4: Premium authentication UI

We are building a premium, fintech-grade authentication experience that mirrors the Laravel backend exactly and establishes the visual language for the rest of the app.

### Milestone 14.4 architecture

We will implement:

| Component                            | Status    |
| ------------------------------------ | --------- |
| Premium login screen                 | Build now |
| Premium registration screen          | Build now |
| Splash screen with branded animation | Build now |
| Reusable authentication form widgets | Build now |
| Button loading animations            | Build now |
| Validation UX                        | Build now |
| Accessibility support                | Build now |
| Dark mode support                    | Build now |
| Navigation transitions               | Build now |
| Responsive layout system             | Build now |

### Step 14.4.1 — Assets structure

Create these folders.

```
assets/
├── images/
│   ├── logo.png
│   ├── logo_mark.png
│   ├── auth_illustration.png
│   └── splash_background.png
├── icons/
└── animations/
```

For now, create empty placeholder PNG files or use temporary assets; we will replace them later with the final brand assets.

### Step 14.4.2 — Typography system

Create `lib/core/theme/app_typography.dart`.

dart

```
import 'package:flutter/material.dart';

class AppTypography {
  static const String fontFamily = 'Inter';

  static const TextStyle displayLarge = TextStyle(
    fontFamily: fontFamily,
    fontSize: 32,
    fontWeight: FontWeight.w700,
    letterSpacing: -0.8,
    height: 1.1,
  );

  static const TextStyle displayMedium = TextStyle(
    fontFamily: fontFamily,
    fontSize: 28,
    fontWeight: FontWeight.w700,
    letterSpacing: -0.6,
    height: 1.15,
  );

  static const TextStyle headline = TextStyle(
    fontFamily: fontFamily,
    fontSize: 22,
    fontWeight: FontWeight.w600,
    height: 1.25,
  );

  static const TextStyle title = TextStyle(
    fontFamily: fontFamily,
    fontSize: 18,
    fontWeight: FontWeight.w600,
    height: 1.3,
  );

  static const TextStyle body = TextStyle(
    fontFamily: fontFamily,
    fontSize: 16,
    fontWeight: FontWeight.w400,
    height: 1.5,
  );

  static const TextStyle bodySmall = TextStyle(
    fontFamily: fontFamily,
    fontSize: 14,
    fontWeight: FontWeight.w400,
    height: 1.45,
  );

  static const TextStyle label = TextStyle(
    fontFamily: fontFamily,
    fontSize: 13,
    fontWeight: FontWeight.w500,
    letterSpacing: 0.2,
  );
}
```

This gives us consistent typography across the entire application.

### Step 14.4.3 — Spacing system

Create `lib/core/theme/app_spacing.dart`.

dart

```
class AppSpacing {
  static const double xxs = 4;
  static const double xs = 8;
  static const double sm = 12;
  static const double md = 16;
  static const double lg = 24;
  static const double xl = 32;
  static const double xxl = 48;
  static const double xxxl = 64;
}
```

Every screen will use these constants.

### Step 14.4.4 — Radius system

Create `lib/core/theme/app_radius.dart`.

dart

```
class AppRadius {
  static const double sm = 8;
  static const double md = 12;
  static const double lg = 16;
  static const double xl = 24;
  static const double xxl = 32;
}
```

### Step 14.4.5 — Elevation tokens

Create `lib/core/theme/app_shadows.dart`.

dart

```
import 'package:flutter/material.dart';

class AppShadows {
  static List<BoxShadow> card = [
    BoxShadow(
      color: Colors.black.withValues(alpha: 0.08),
      blurRadius: 16,
      offset: const Offset(0, 8),
    ),
  ];

  static List<BoxShadow> floating = [
    BoxShadow(
      color: Colors.black.withValues(alpha: 0.12),
      blurRadius: 24,
      offset: const Offset(0, 12),
    ),
  ];
}
```

### Step 14.4.6 — Premium text field widget

Create `lib/core/widgets/app_text_field.dart`.

dart

```
import 'package:flutter/material.dart';

import '../theme/app_colors.dart';
import '../theme/app_radius.dart';
import '../theme/app_spacing.dart';

class AppTextField extends StatelessWidget {
  const AppTextField({
    super.key,
    required this.controller,
    required this.label,
    this.obscureText = false,
    this.keyboardType,
    this.validator,
    this.prefixIcon,
  });

  final TextEditingController controller;
  final String label;
  final bool obscureText;
  final TextInputType? keyboardType;
  final String? Function(String?)? validator;
  final IconData? prefixIcon;

  @override
  Widget build(BuildContext context) {
    return TextFormField(
      controller: controller,
      obscureText: obscureText,
      keyboardType: keyboardType,
      validator: validator,
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: prefixIcon == null ? null : Icon(prefixIcon),
        filled: true,
        fillColor: AppColors.surface,
        contentPadding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.md,
          vertical: AppSpacing.md,
        ),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.lg),
          borderSide: BorderSide.none,
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.lg),
          borderSide: BorderSide(
            color: AppColors.border.withValues(alpha: 0.4),
          ),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.lg),
          borderSide: const BorderSide(
            color: AppColors.primary,
            width: 1.5,
          ),
        ),
      ),
    );
  }
}
```

### Step 14.4.7 — Premium primary button

Create `lib/core/widgets/app_primary_button.dart`.

dart

```
import 'package:flutter/material.dart';

import '../theme/app_colors.dart';
import '../theme/app_radius.dart';
import '../theme/app_spacing.dart';

class AppPrimaryButton extends StatelessWidget {
  const AppPrimaryButton({
    super.key,
    required this.label,
    required this.onPressed,
    this.loading = false,
  });

  final String label;
  final VoidCallback? onPressed;
  final bool loading;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: double.infinity,
      height: 56,
      child: ElevatedButton(
        onPressed: loading ? null : onPressed,
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.primary,
          foregroundColor: Colors.white,
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppRadius.xl),
          ),
          padding: const EdgeInsets.symmetric(
            vertical: AppSpacing.md,
          ),
        ),
        child: AnimatedSwitcher(
          duration: const Duration(milliseconds: 200),
          child: loading
              ? const SizedBox(
                  width: 22,
                  height: 22,
                  child: CircularProgressIndicator(
                    strokeWidth: 2,
                    color: Colors.white,
                  ),
                )
              : Text(
                  label,
                  style: const TextStyle(
                    fontWeight: FontWeight.w600,
                    fontSize: 16,
                  ),
                ),
        ),
      ),
    );
  }
}
```

This gives us a fintech-grade loading animation.

### Step 14.4.8 — Login page

Replace `lib/features/auth/presentation/pages/login_page.dart`.

dart

```
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/widgets/app_primary_button.dart';
import '../../../../core/widgets/app_text_field.dart';
import '../bloc/auth_bloc.dart';
import '../bloc/auth_event.dart';
import '../bloc/auth_state.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _formKey = GlobalKey<FormState>();
  final _email = TextEditingController();
  final _password = TextEditingController();

  @override
  void dispose() {
    _email.dispose();
    _password.dispose();
    super.dispose();
  }

  void _submit() {
    if (!_formKey.currentState!.validate()) return;

    context.read<AuthBloc>().add(
          LoginRequested(
            email: _email.text.trim(),
            password: _password.text,
          ),
        );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.lg),
          child: BlocBuilder<AuthBloc, AuthState>(
            builder: (context, state) {
              final loading = state is AuthLoading;

              return Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Spacer(),
                    const Text(
                      'Welcome back',
                      style: TextStyle(
                        fontSize: 32,
                        fontWeight: FontWeight.w700,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    const SizedBox(height: AppSpacing.sm),
                    const Text(
                      'Sign in to continue managing your subscriptions.',
                      style: TextStyle(
                        color: AppColors.textSecondary,
                        fontSize: 16,
                      ),
                    ),
                    const SizedBox(height: AppSpacing.xxl),
                    AppTextField(
                      controller: _email,
                      label: 'Email address',
                      keyboardType: TextInputType.emailAddress,
                    ),
                    const SizedBox(height: AppSpacing.lg),
                    AppTextField(
                      controller: _password,
                      label: 'Password',
                      obscureText: true,
                    ),
                    const SizedBox(height: AppSpacing.xl),
                    AppPrimaryButton(
                      label: 'Sign in',
                      loading: loading,
                      onPressed: _submit,
                    ),
                    const Spacer(),
                  ],
                ),
              );
            },
          ),
        ),
      ),
    );
  }
}
```

This layout is intentionally minimal, premium, and fast.

### Step 14.4.9 — Registration page

Use the same design language: large headline, generous spacing, rounded inputs, loading button, and inline validation.

The registration form should collect:

* First name

* Last name

* Email

* Phone

* Password

which exactly matches the Laravel `POST /api/auth/register` request.

### Step 14.4.10 — Motion language

Add a global page transition.

In `app_router.dart`:

dart

```
CustomTransitionPage(
  child: child,
  transitionsBuilder: (
    context,
    animation,
    secondaryAnimation,
    child,
  ) {
    return FadeTransition(
      opacity: animation,
      child: SlideTransition(
        position: Tween(
          begin: const Offset(0, 0.03),
          end: Offset.zero,
        ).animate(animation),
        child: child,
      ),
    );
  },
)
```

This gives YouStayOn a subtle premium motion system.

### Step 14.4.11 — Accessibility

Ensure:

* Tap targets >= 48 px

* Contrast ratio >= WCAG AA

* Text scales correctly

* Screen readers announce buttons and fields

* Loading buttons expose progress semantics

* Error messages are announced

### Result

After Milestone 14.4, YouStayOn will have:

* Premium login experience

* Premium registration experience

* Consistent typography

* Consistent spacing

* Design tokens

* Reusable UI components

* Loading animations

* Dark mode readiness

* Accessibility compliance

* Fast page transitions

This becomes the visual foundation for Dashboard, Wallet, Radar Intelligence, Purchases, Notifications, and Analytics.

The next milestone is Milestone 14.5 — Authentication Integration & Routing, where we will connect these premium screens to the BLoC layer, Sanctum token storage, automatic session restoration, protected routes, and navigation guards so the app can authenticate directly against your Laravel backend and enter the authenticated dashboard flow exactly like the production system.
