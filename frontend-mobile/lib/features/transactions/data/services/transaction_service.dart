import '../../../../core/network/dio_client.dart';
import '../models/transaction_model.dart';

class TransactionService {
  Future<List<TransactionModel>> getTransactions() async {
    final response = await DioClient.dio.get('/transactions');

    return (response.data['transactions'] as List)
        .map((e) => TransactionModel.fromJson(e))
        .toList();
  }
}