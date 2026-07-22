import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/models/wallet_model.dart';
import '../../data/services/wallet_service.dart';

final walletProvider = FutureProvider<WalletModel>((ref) async {
  return WalletService().getWallet();
});