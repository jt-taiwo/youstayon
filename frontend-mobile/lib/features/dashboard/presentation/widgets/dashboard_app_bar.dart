import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../profile/presentation/providers/profile_provider.dart';
class DashboardAppBar extends ConsumerWidget
    implements PreferredSizeWidget {
  const DashboardAppBar({super.key});

  @override
  Size get preferredSize => const Size.fromHeight(70);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final profile = ref.watch(profileProvider);

    return AppBar(
      backgroundColor: const Color(0xff0E150F),
      elevation: 0,

      title: profile.when(
        data: (user) => Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              "Good Evening",
              style: TextStyle(
                color: Colors.white70,
                fontSize: 14,
              ),
            ),
            Text(
              "${user.name} 👋",
              style: const TextStyle(
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),

        loading: () => const Text(
          "Loading...",
          style: TextStyle(fontSize: 16),
        ),

        error: (_, __) => const Text(
          "Welcome",
          style: TextStyle(fontSize: 18),
        ),
      ),

      actions: [
        IconButton(
          onPressed: () {},
          icon: const Icon(Icons.notifications_none),
        ),
        const SizedBox(width: 8),
      ],
    );
  }
}