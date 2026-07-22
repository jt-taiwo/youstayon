class ProfileModel {
  final int id;
  final String name;
  final String email;
  final String phone;

  const ProfileModel({
    required this.id,
    required this.name,
    required this.email,
    required this.phone,
  });

  factory ProfileModel.fromJson(Map<String, dynamic> json) {
    return ProfileModel(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      phone: json['phone'],
    );
  }
}