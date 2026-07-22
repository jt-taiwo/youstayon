import '../../../../core/network/dio_client.dart';
import '../models/wallet_model.dart';

class WalletService {
  Future<WalletModel> getWallet() async {
    final response = await DioClient.dio.get('/wallet');

    return WalletModel.fromJson(
      response.data['wallet'],
    );
  }
}