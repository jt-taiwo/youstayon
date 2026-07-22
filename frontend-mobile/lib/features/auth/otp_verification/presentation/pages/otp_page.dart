import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../widgets/otp_digit_box.dart';
import '../widgets/resend_button.dart';

class OtpPage extends StatefulWidget {
  const OtpPage({super.key});

  @override
  State<OtpPage> createState() => _OtpPageState();
}

class _OtpPageState extends State<OtpPage> {

  final controllers =
      List.generate(6, (_) => TextEditingController());

  final nodes =
      List.generate(6, (_) => FocusNode());

  @override
  void dispose() {
    for (final c in controllers) {
      c.dispose();
    }

    for (final n in nodes) {
      n.dispose();
    }

    super.dispose();
  }

  @override
  Widget build(BuildContext context) {

    return Scaffold(
      backgroundColor: const Color(0xff0E150F),

      body: SafeArea(

        child: Padding(

          padding: const EdgeInsets.all(24),

          child: Column(

            crossAxisAlignment: CrossAxisAlignment.start,

            children: [

              IconButton(
                onPressed: () => context.pop(),
                icon: const Icon(
                  Icons.arrow_back_ios_new,
                  color: Colors.white,
                ),
              ),

              const SizedBox(height: 20),

              const Text(
                "OTP Verification",
                style: TextStyle(
                  color: Colors.white,
                  fontFamily: "Geist",
                  fontSize: 30,
                  fontWeight: FontWeight.bold,
                ),
              ),

              const SizedBox(height: 8),

              const Text(
                "Enter the 6-digit verification code.",
                style: TextStyle(
                  color: Color(0xff9FB2A4),
                ),
              ),

              const SizedBox(height: 45),

              Row(
                mainAxisAlignment:
                    MainAxisAlignment.spaceBetween,

                children: List.generate(6, (index) {

                  return OtpDigitBox(

                    controller: controllers[index],

                    currentFocus: nodes[index],

                    previousFocus:
                        index == 0 ? null : nodes[index - 1],

                    nextFocus:
                        index == 5 ? null : nodes[index + 1],
                  );

                }),
              ),

              const SizedBox(height: 40),

              SizedBox(
                width: double.infinity,
                height: 58,

                child: FilledButton(

                  style: FilledButton.styleFrom(
                    backgroundColor:
                        const Color(0xff3DDC84),

                    shape: RoundedRectangleBorder(
                      borderRadius:
                          BorderRadius.circular(18),
                    ),
                  ),

                  onPressed: () {
                    context.go('/dashboard');
                  },

                  child: const Text(
                    "Verify",
                    style: TextStyle(
                      color: Colors.black,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ),

              const SizedBox(height: 28),

              const Center(
                child: ResendButton(),
              ),
            ],
          ),
        ),
      ),
    );
  }
}