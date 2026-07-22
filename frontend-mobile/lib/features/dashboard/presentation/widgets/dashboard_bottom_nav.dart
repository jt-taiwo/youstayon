import 'package:flutter/material.dart';

class DashboardBottomNav extends StatelessWidget {

  final int currentIndex;
  final ValueChanged<int> onTap;

  const DashboardBottomNav({
    super.key,
    required this.currentIndex,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {

    return NavigationBar(

      selectedIndex: currentIndex,

      onDestinationSelected: onTap,

      destinations: const [

        NavigationDestination(
          icon: Icon(Icons.home_outlined),
          selectedIcon: Icon(Icons.home),
          label: "Home",
        ),

        NavigationDestination(
          icon: Icon(Icons.wifi),
          label: "Radar",
        ),

        NavigationDestination(
          icon: Icon(Icons.account_balance_wallet_outlined),
          label: "Wallet",
        ),

        NavigationDestination(
          icon: Icon(Icons.receipt_long),
          label: "History",
        ),

        NavigationDestination(
          icon: Icon(Icons.person_outline),
          label: "Profile",
        ),
      ],
    );
  }
}