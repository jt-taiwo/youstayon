import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/models/profile_model.dart';
import '../../data/services/profile_service.dart';

final profileProvider =
    FutureProvider<ProfileModel>((ref) async {
  return ProfileService().getProfile();
});