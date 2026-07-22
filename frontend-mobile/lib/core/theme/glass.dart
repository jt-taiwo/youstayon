import 'dart:ui';

import 'package:flutter/material.dart';

import 'app_colors.dart';
import 'app_radius.dart';

class Glass {
  Glass._();

  static BoxDecoration decoration = BoxDecoration(
    color: AppColors.glassWhite,
    borderRadius: BorderRadius.circular(AppRadius.xl),
    border: Border.all(
      color: AppColors.glassBorder,
      width: 1,
    ),
  );

  static Widget container({
    required Widget child,
    EdgeInsets padding = const EdgeInsets.all(24),
  }) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(AppRadius.xl),
      child: BackdropFilter(
        filter: ImageFilter.blur(
          sigmaX: 20,
          sigmaY: 20,
        ),
        child: Container(
          padding: padding,
          decoration: decoration,
          child: child,
        ),
      ),
    );
  }
}