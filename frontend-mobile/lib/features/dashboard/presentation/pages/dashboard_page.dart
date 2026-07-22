import 'package:flutter/material.dart';

import '../widgets/dashboard_app_bar.dart';
import '../widgets/dashboard_bottom_nav.dart';
import '../widgets/quick_actions.dart';
import '../widgets/wallet_card.dart';
import '../widgets/radar_card.dart';
import '../widgets/recent_activity.dart';

class DashboardPage extends StatefulWidget {
  const DashboardPage({super.key});

  @override
  State<DashboardPage> createState() => _DashboardPageState();
}

class _DashboardPageState extends State<DashboardPage> {

  int currentIndex = 0;

  @override
  Widget build(BuildContext context) {

    return Scaffold(

      backgroundColor: const Color(0xff0E150F),

      appBar: const DashboardAppBar(),

      body: SafeArea(

        child: ListView(

          padding: const EdgeInsets.all(24),

          children: const [

            SizedBox(height: 12),

            WalletCard(),

            SizedBox(height: 28),

            QuickActions(),

            SizedBox(height: 28),

            RadarCard(),

            SizedBox(height: 28),

            RecentActivity(),

            SizedBox(height: 30),

          ],
        ),
      ),

      bottomNavigationBar: DashboardBottomNav(
        currentIndex: currentIndex,
        onTap: (index) {
          setState(() {
            currentIndex = index;
          });
        },
      ),
    );
  }
}