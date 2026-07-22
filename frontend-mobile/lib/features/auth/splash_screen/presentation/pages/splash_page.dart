import 'dart:async';
import 'package:youstayon/core/storage/token_storage.dart';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

class SplashPage extends StatefulWidget {
  const SplashPage({super.key});

  @override
  State<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends State<SplashPage>
    with SingleTickerProviderStateMixin {
@override
void initState() {
  super.initState();

  Future.delayed(
    const Duration(seconds: 2),
    () async {
      final token = await TokenStorage.get();

      if (!mounted) return;

      if (token != null && token.isNotEmpty) {
        context.go('/dashboard');
      } else {
        context.go('/onboarding');
      }
    },
  );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        width: double.infinity,
        decoration: const BoxDecoration(
          color: Color(0xff0E150F),
        ),
        child: SafeArea(
          child: Column(
            children: [
              const Spacer(),

              Hero(
                tag: 'logo',
                child: Image.asset(
                  'assets/logos/logo.png',
                  width: 140,
                ),
              ),

              const SizedBox(height: 28),

              const Text(
                "YouStayOn",
                style: TextStyle(
                  fontFamily: "Geist",
                  fontSize: 34,
                  fontWeight: FontWeight.w700,
                  color: Colors.white,
                ),
              ),

              const SizedBox(height: 8),

              const Text(
                "Nigeria's Intelligent Connectivity Platform",
                textAlign: TextAlign.center,
                style: TextStyle(
                  color: Color(0xff9FB2A4),
                  fontFamily: "Geist",
                ),
              ),

              const Spacer(),

              const SizedBox(
                width: 40,
                height: 40,
                child: CircularProgressIndicator(
                  strokeWidth: 3,
                  color: Color(0xff3DDC84),
                ),
              ),

              const SizedBox(height: 50),
            ],
          ),
        ),
      ),
    );
  }
}