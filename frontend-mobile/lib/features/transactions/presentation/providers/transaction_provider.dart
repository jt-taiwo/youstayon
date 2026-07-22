import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/models/transaction_model.dart';
import '../../data/services/transaction_service.dart';

final transactionProvider =
    FutureProvider<List<TransactionModel>>((ref) async {
  return TransactionService().getTransactions();
});