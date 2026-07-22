import 'package:flutter/material.dart';

import '../../../../../core/theme/app_colors.dart';

class SplashLoader extends StatelessWidget {
  const SplashLoader({super.key});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: 140,
      child: ClipRRect(
        borderRadius: BorderRadius.circular(100),
        child: const LinearProgressIndicator(
          minHeight: 3,
          backgroundColor: Colors.white10,
          valueColor: AlwaysStoppedAnimation(
            AppColors.primary,
          ),
        ),
      ),
    );
  }
}