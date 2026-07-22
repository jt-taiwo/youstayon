import 'package:flutter/material.dart';
import 'app_colors.dart';

class AppTypography {
  AppTypography._();

  static const display = TextStyle(
    fontFamily: 'Geist',
    fontSize: 40,
    fontWeight: FontWeight.w700,
    color: AppColors.textPrimary,
    letterSpacing: -1,
  );

  static const headline = TextStyle(
    fontFamily: 'Geist',
    fontSize: 32,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
  );

  static const title = TextStyle(
    fontFamily: 'Geist',
    fontSize: 20,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
  );

  static const body = TextStyle(
    fontFamily: 'Geist',
    fontSize: 16,
    color: AppColors.textPrimary,
  );

  static const bodySmall = TextStyle(
    fontFamily: 'Geist',
    fontSize: 14,
    color: AppColors.textSecondary,
  );

  static const label = TextStyle(
    fontFamily: 'Geist',
    fontSize: 12,
    fontWeight: FontWeight.w500,
    color: AppColors.textSecondary,
    letterSpacing: 1,
  );

  static const mono = TextStyle(
    fontFamily: 'Geist',
    fontSize: 14,
    fontWeight: FontWeight.w500,
    color: AppColors.textPrimary,
  );
}