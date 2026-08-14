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
