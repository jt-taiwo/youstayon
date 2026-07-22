import 'package:flutter/material.dart';

class OtpDigitBox extends StatelessWidget {
  final TextEditingController controller;
  final FocusNode currentFocus;
  final FocusNode? nextFocus;
  final FocusNode? previousFocus;

  const OtpDigitBox({
    super.key,
    required this.controller,
    required this.currentFocus,
    this.nextFocus,
    this.previousFocus,
  });

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: 48,
      child: TextField(
        controller: controller,
        focusNode: currentFocus,
        keyboardType: TextInputType.number,
        textAlign: TextAlign.center,
        maxLength: 1,
        style: const TextStyle(
          color: Colors.white,
          fontSize: 22,
          fontWeight: FontWeight.bold,
        ),
        decoration: InputDecoration(
          counterText: "",
          filled: true,
          fillColor: const Color(0xff1A231C),
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16),
            borderSide: BorderSide.none,
          ),
        ),

        onChanged: (value) {
          if (value.isNotEmpty && nextFocus != null) {
            FocusScope.of(context).requestFocus(nextFocus);
          }

          if (value.isEmpty && previousFocus != null) {
            FocusScope.of(context).requestFocus(previousFocus);
          }
        },
      ),
    );
  }
}