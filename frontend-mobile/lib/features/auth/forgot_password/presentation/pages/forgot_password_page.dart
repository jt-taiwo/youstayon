import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import 'package:youstayon/shared/widgets/auth/auth_header.dart';
import 'package:youstayon/shared/widgets/auth/auth_primary_button.dart';
import 'package:youstayon/shared/widgets/auth/auth_scaffold.dart';
import 'package:youstayon/shared/widgets/auth/auth_text_field.dart';
import 'package:youstayon/shared/widgets/auth/glass_container.dart';

class ForgotPasswordPage extends StatelessWidget {
  const ForgotPasswordPage({super.key});

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
              title: "Forgot Password",
              subtitle: "Enter your email or phone number",
            ),

            const SizedBox(height: 40),

            const AuthTextField(
              hint: "Email or Phone Number",
            ),

            const SizedBox(height: 30),

            AuthPrimaryButton(
              text: "Send OTP",
              onPressed: () {
                context.push('/otp');
              },
            ),
          ],
        ),
      ),
    );
  }
}