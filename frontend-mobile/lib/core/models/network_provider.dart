enum NetworkProvider {
  mtn,
  airtel,
  glo,
  nineMobile,
}

extension NetworkProviderExtension on NetworkProvider {
  String get name {
    switch (this) {
      case NetworkProvider.mtn:
        return "MTN";

      case NetworkProvider.airtel:
        return "Airtel";

      case NetworkProvider.glo:
        return "Glo";

      case NetworkProvider.nineMobile:
        return "9mobile";
    }
  }

  String get asset {
    switch (this) {
      case NetworkProvider.mtn:
        return "assets/networks/mtn.png";

      case NetworkProvider.airtel:
        return "assets/networks/airtel.png";

      case NetworkProvider.glo:
        return "assets/networks/glo.png";

      case NetworkProvider.nineMobile:
        return "assets/networks/9mobile.png";
    }
  }
}