import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../../../../../core/storage/token_storage.dart';
import '../../../data/services/auth_service.dart';

import '../../../../../shared/widgets/auth/auth_header.dart';
import '../../../../../shared/widgets/auth/auth_primary_button.dart';
import '../../../../../shared/widgets/auth/auth_scaffold.dart';
import '../../../../../shared/widgets/auth/auth_text_field.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final emailController = TextEditingController();
  final passwordController = TextEditingController();

  final authService = AuthService();

  bool obscurePassword = true;
  bool rememberMe = false;
  bool loading = false;

  @override
  void dispose() {
    emailController.dispose();
    passwordController.dispose();
    super.dispose();
  }

  Future<void> _login() async {
    setState(() {
      loading = true;
    });

    try {
      final response = await authService.login(
        email: emailController.text.trim(),
        password: passwordController.text,
      );

      await TokenStorage.save(response["token"]);

      if (!mounted) return;

      context.go('/dashboard');
    } catch (e) {
      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(e.toString()),
        ),
      );
    }

    if (mounted) {
      setState(() {
        loading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return AuthScaffold(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const SizedBox(height: 20),

          IconButton(
            onPressed: () => context.pop(),
            icon: const Icon(
              Icons.arrow_back_ios_new,
              color: Colors.white,
            ),
          ),

          const SizedBox(height: 20),

          const AuthHeader(
            title: "Welcome Back",
            subtitle: "Login to your YouStayOn account",
          ),

          const SizedBox(height: 40),

          AuthTextField(
            controller: emailController,
            hint: "Email",
            keyboardType: TextInputType.emailAddress,
          ),

          const SizedBox(height: 18),

          AuthTextField(
            controller: passwordController,
            hint: "Password",
            obscure: obscurePassword,
            suffixIcon: IconButton(
              onPressed: () {
                setState(() {
                  obscurePassword = !obscurePassword;
                });
              },
              icon: Icon(
                obscurePassword
                    ? Icons.visibility_off
                    : Icons.visibility,
                color: Colors.white70,
              ),
            ),
          ),

          const SizedBox(height: 18),

          Row(
            children: [
              Checkbox(
                value: rememberMe,
                activeColor: const Color(0xff3DDC84),
                onChanged: (value) {
                  setState(() {
                    rememberMe = value ?? false;
                  });
                },
              ),

              const Text(
                "Remember me",
                style: TextStyle(color: Colors.white70),
              ),

              const Spacer(),

              TextButton(
                onPressed: () {
                  context.push('/forgot-password');
                },
                child: const Text("Forgot Password?"),
              ),
            ],
          ),

          const SizedBox(height: 30),

          AuthPrimaryButton(
            text: loading ? "Signing In..." : "Login",
            onPressed: loading
                ? null
                : () async {
                    await _login();
                  },
          ),

          const SizedBox(height: 22),

          Center(
            child: TextButton(
              onPressed: () {
                context.push('/register');
              },
              child: const Text(
                "Don't have an account? Register",
              ),
            ),
          ),
        ],
      ),
    );
  }
}