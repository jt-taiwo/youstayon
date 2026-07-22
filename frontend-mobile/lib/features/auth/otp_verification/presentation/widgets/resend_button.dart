import 'package:flutter/material.dart';

class ResendButton extends StatelessWidget {
  const ResendButton({super.key});

  @override
  Widget build(BuildContext context) {
    return TextButton(
      onPressed: null,
      child: const Text(
        "Resend Code (60s)",
        style: TextStyle(
          color: Colors.white54,
          fontFamily: "Geist",
        ),
      ),
    );
  }
}