import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get_it/get_it.dart';

import '../network/dio_client.dart';
import '../storage/secure_storage_service.dart';

final getIt = GetIt.instance;

Future<void> configureDependencies() async {
  getIt.registerLazySingleton(
    () => const FlutterSecureStorage(),
  );

  getIt.registerLazySingleton(
    () => SecureStorageService(getIt()),
  );

  getIt.registerLazySingleton(
    () => DioClient(getIt()),
  );
}
