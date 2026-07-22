class TransactionModel {
  final int id;
  final String reference;
  final String type;
  final double amount;
  final String status;
  final String description;

  TransactionModel({
    required this.id,
    required this.reference,
    required this.type,
    required this.amount,
    required this.status,
    required this.description,
  });

  factory TransactionModel.fromJson(Map<String, dynamic> json) {
    return TransactionModel(
      id: json['id'],
      reference: json['reference'],
      type: json['type'],
      amount: double.parse(json['amount'].toString()),
      status: json['status'],
      description: json['description'],
    );
  }
}