import 'package:flutter/material.dart';
import 'core/theme/app_theme.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const YouStayOnApp());
}

class YouStayOnApp extends StatelessWidget {
  const YouStayOnApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'YouStayOn',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.light(),
      home: const Scaffold(
        body: Center(
          child: Text('YouStayOn'),
        ),
      ),
    );
  }
}
