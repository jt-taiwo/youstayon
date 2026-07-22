import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../transactions/presentation/providers/transaction_provider.dart';

class RecentActivity extends ConsumerWidget {
  const RecentActivity({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final transactions = ref.watch(transactionProvider);

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          "Recent Activity",
          style: TextStyle(
            color: Colors.white,
            fontSize: 22,
            fontWeight: FontWeight.bold,
          ),
        ),

        const SizedBox(height: 18),

        transactions.when(
          loading: () => const Center(
            child: Padding(
              padding: EdgeInsets.all(24),
              child: CircularProgressIndicator(),
            ),
          ),

          error: (error, stack) => Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: const Color(0xff1A231C),
              borderRadius: BorderRadius.circular(18),
            ),
            child: Text(
              error.toString(),
              style: const TextStyle(color: Colors.red),
            ),
          ),

          data: (items) {
            if (items.isEmpty) {
              return Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: const Color(0xff1A231C),
                  borderRadius: BorderRadius.circular(18),
                ),
                child: const Center(
                  child: Text(
                    "No transactions yet",
                    style: TextStyle(color: Colors.white70),
                  ),
                ),
              );
            }

            return Column(
              children: items.map((tx) {
                return _tile(
                  _icon(tx.type),
                  tx.description,
                  tx.amount,
                  tx.type,
                );
              }).toList(),
            );
          },
        ),
      ],
    );
  }

  IconData _icon(String type) {
    switch (type.toLowerCase()) {
      case 'airtime':
        return Icons.phone_android;

      case 'data':
        return Icons.wifi;

      case 'wallet':
      case 'wallet funding':
        return Icons.account_balance_wallet;

      case 'electricity':
        return Icons.bolt;

      case 'cable':
        return Icons.tv;

      default:
        return Icons.receipt_long;
    }
  }

  Widget _tile(
    IconData icon,
    String title,
    double amount,
    String type,
  ) {
    final positive =
        type.toLowerCase().contains('fund') ||
        type.toLowerCase().contains('deposit');

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: const Color(0xff1A231C),
        borderRadius: BorderRadius.circular(18),
      ),
      child: Row(
        children: [
          CircleAvatar(
            backgroundColor: const Color(0xff3DDC84),
            child: Icon(
              icon,
              color: Colors.black,
            ),
          ),

          const SizedBox(width: 16),

          Expanded(
            child: Text(
              title,
              style: const TextStyle(
                color: Colors.white,
              ),
            ),
          ),

          Text(
            "${positive ? '+' : '-'} ₦${amount.toStringAsFixed(2)}",
            style: const TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }
}