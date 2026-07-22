import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import 'package:youstayon/shared/widgets/auth/auth_header.dart';
import 'package:youstayon/shared/widgets/auth/auth_primary_button.dart';
import 'package:youstayon/shared/widgets/auth/auth_scaffold.dart';
import 'package:youstayon/shared/widgets/auth/auth_text_field.dart';
import 'package:youstayon/shared/widgets/auth/glass_container.dart';

import '../../../../../core/storage/token_storage.dart';
import '../../../data/services/auth_service.dart';

class RegisterPage extends StatefulWidget {
  const RegisterPage({super.key});

  @override
  State<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final nameController = TextEditingController();
  final emailController = TextEditingController();
  final phoneController = TextEditingController();
  final passwordController = TextEditingController();
  final confirmController = TextEditingController();
  final referralController = TextEditingController();

  final authService = AuthService();

  bool obscurePassword = true;
  bool obscureConfirm = true;
  bool agree = false;
  bool loading = false;

  @override
  void dispose() {
    nameController.dispose();
    emailController.dispose();
    phoneController.dispose();
    passwordController.dispose();
    confirmController.dispose();
    referralController.dispose();
    super.dispose();
  }

  Future<void> _register() async {
    if (!agree) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Please accept the Terms & Privacy Policy."),
        ),
      );
      return;
    }

    setState(() {
      loading = true;
    });

    try {
      final response = await authService.register(
        name: nameController.text.trim(),
        email: emailController.text.trim(),
        phone: phoneController.text.trim(),
        password: passwordController.text,
        confirmPassword: confirmController.text,
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
      child: GlassContainer(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 20),

            IconButton(
              onPressed: () => context.pop(),
              icon: const Icon(
                Icons.arrow_back_ios,
                color: Colors.white,
              ),
            ),

            const SizedBox(height: 20),

            const AuthHeader(
              title: "Create Account",
              subtitle: "Create your YouStayOn account",
            ),

            const SizedBox(height: 40),

            AuthTextField(
              controller: nameController,
              hint: "Full Name",
            ),

            const SizedBox(height: 18),

            AuthTextField(
              controller: emailController,
              hint: "Email",
              keyboardType: TextInputType.emailAddress,
            ),

            const SizedBox(height: 18),

            AuthTextField(
              controller: phoneController,
              hint: "Phone Number",
              keyboardType: TextInputType.phone,
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

            AuthTextField(
              controller: confirmController,
              hint: "Confirm Password",
              obscure: obscureConfirm,
              suffixIcon: IconButton(
                onPressed: () {
                  setState(() {
                    obscureConfirm = !obscureConfirm;
                  });
                },
                icon: Icon(
                  obscureConfirm
                      ? Icons.visibility_off
                      : Icons.visibility,
                  color: Colors.white70,
                ),
              ),
            ),

            const SizedBox(height: 18),

            AuthTextField(
              controller: referralController,
              hint: "Referral Code (Optional)",
            ),

            const SizedBox(height: 24),

            Row(
              children: [
                Checkbox(
                  value: agree,
                  activeColor: const Color(0xff3DDC84),
                  onChanged: (value) {
                    setState(() {
                      agree = value ?? false;
                    });
                  },
                ),
                const Expanded(
                  child: Text(
                    "I agree to the Terms & Privacy Policy",
                    style: TextStyle(
                      color: Colors.white70,
                    ),
                  ),
                ),
              ],
            ),

            const SizedBox(height: 24),

           AuthPrimaryButton(
              text: loading
                  ? "Creating Account..."
                  : "Create Account",
              onPressed: loading
                  ? null
                  : () async {
                      await _register();
                    },
            ),

            const SizedBox(height: 20),

            Center(
              child: TextButton(
                onPressed: () {
                  context.push('/login');
                },
                child: const Text(
                  "Already have an account? Sign In",
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}