import 'package:go_router/go_router.dart';

import '../features/auth/onboarding/presentation/pages/onboarding_page.dart';
import '../features/auth/splash_screen/presentation/pages/splash_page.dart';
import '../features/auth/register/presentation/pages/register_page.dart';
import '../features/auth/otp_verification/presentation/pages/otp_page.dart';
import '../features/auth/login/presentation/pages/login_page.dart';
import '../features/auth/forgot_password/presentation/pages/forgot_password_page.dart';
import '../features/dashboard/presentation/pages/dashboard_page.dart';

final GoRouter appRouter = GoRouter(
  initialLocation: '/',
  routes: [
    GoRoute(
      path: '/',
      builder: (_, __) => const SplashPage(),
    ),
    GoRoute(
      path: '/onboarding',
      builder: (_, __) => const OnboardingPage(),
    ),
    GoRoute(
      path: '/register',
      builder: (_, __) => const RegisterPage(),
    ),
    GoRoute(
      path: '/otp',
      builder: (_, __) => const OtpPage(),
    ),
    GoRoute(
      path: '/login',
      builder: (_, __) => const LoginPage(),
    ),
    GoRoute(
      path: '/dashboard',
      builder: (_, __) => const DashboardPage(),
    ),

    GoRoute(
      path: '/forgot-password',
      builder: (_, __) => const ForgotPasswordPage(),
    ),
  ],
);