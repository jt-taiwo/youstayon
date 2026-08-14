import 'package:flutter/material.dart';

final class AppRadius {
  AppRadius._();

  static const Radius sm = Radius.circular(8);
  static const Radius md = Radius.circular(12);
  static const Radius lg = Radius.circular(16);
  static const Radius xl = Radius.circular(20);
  static const Radius pill = Radius.circular(999);

  static const BorderRadius radiusSm = BorderRadius.all(sm);
  static const BorderRadius radiusMd = BorderRadius.all(md);
  static const BorderRadius radiusLg = BorderRadius.all(lg);
  static const BorderRadius radiusXl = BorderRadius.all(xl);
  static const BorderRadius radiusPill = BorderRadius.all(pill);
}