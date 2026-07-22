import 'package:flutter/material.dart';

class SplashBackground extends StatelessWidget {
  final Widget child;

  const SplashBackground({
    super.key,
    required this.child,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(

        gradient: LinearGradient(

          begin: Alignment.topCenter,

          end: Alignment.bottomCenter,

          colors: [

            Color(0xFF0E150F),

            Color(0xFF112515),

          ],
        ),
      ),

      child: child,
    );
  }
}