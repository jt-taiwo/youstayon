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