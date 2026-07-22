import 'package:flutter/material.dart';

class AuthTextField extends StatelessWidget {
  final String hint;
  final bool obscure;
  final Widget? suffixIcon;
  final TextEditingController? controller;
  final TextInputType keyboardType;

  const AuthTextField({
    super.key,
    required this.hint,
    this.obscure = false,
    this.suffixIcon,
    this.controller,
    this.keyboardType = TextInputType.text,
  });

  @override
  Widget build(BuildContext context) {
    return TextField(
      controller: controller,
      obscureText: obscure,
      keyboardType: keyboardType,
      style: const TextStyle(
        color: Colors.white,
        fontFamily: "Geist",
      ),
      decoration: InputDecoration(
        hintText: hint,
        hintStyle: const TextStyle(
          color: Colors.white54,
        ),
        filled: true,
        fillColor: const Color(0xff1A231C),
        suffixIcon: suffixIcon,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(18),
          borderSide: BorderSide.none,
        ),
      ),
    );
  }
}