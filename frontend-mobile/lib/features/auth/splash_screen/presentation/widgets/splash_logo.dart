import 'package:flutter/material.dart';

import '../../../../../core/theme/app_typography.dart';

class SplashLogo extends StatelessWidget {
  const SplashLogo({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [

        Text(
          'YouStayOn',
          style: AppTypography.headline.copyWith(
            color: const Color(0xFF60F99E),
          ),
        ),

        const SizedBox(height: 6),

        Text(
          'Stay Connected. Stay Smart.',
          style: AppTypography.bodySmall,
        ),
      ],
    );
  }
}