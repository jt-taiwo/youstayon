import 'package:flutter/material.dart';

import 'router.dart';

class YouStayOnApp extends StatelessWidget {
  const YouStayOnApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(
      debugShowCheckedModeBanner: false,
      routerConfig: appRouter,
    );
  }
}