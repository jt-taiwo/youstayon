import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

class OnboardingPage extends StatefulWidget {
  const OnboardingPage({super.key});

  @override
  State<OnboardingPage> createState() => _OnboardingPageState();
}

class _OnboardingPageState extends State<OnboardingPage> {
  final PageController _pageController = PageController();

  int currentPage = 0;

  final List<Map<String, String>> slides = [
    {
      "title": "Stay Connected",
      "subtitle":
          "Automatically find the fastest and most affordable internet around you.",
    },
    {
      "title": "One Wallet",
      "subtitle":
          "Buy airtime, data, electricity, cable TV and more from one secure wallet.",
    },
    {
      "title": "AI Connectivity",
      "subtitle":
          "Radar Intelligence recommends the best network based on your location.",
    },
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xff0E150F),
      body: SafeArea(
        child: Column(
          children: [
            const SizedBox(height: 24),

            Align(
              alignment: Alignment.centerRight,
              child: Padding(
                padding: const EdgeInsets.only(right: 24),
                child: TextButton(
                  onPressed: () {
                    context.go('/login');
                  },
                  child: const Text("Skip"),
                ),
              ),
            ),

            Expanded(
              child: PageView.builder(
                controller: _pageController,
                itemCount: slides.length,
                onPageChanged: (index) {
                  setState(() {
                    currentPage = index;
                  });
                },
                itemBuilder: (_, index) {
                  final slide = slides[index];

                  return Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 28),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Container(
                          height: 260,
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(24),
                            color: const Color(0xff1B241D),
                          ),
                          child: const Center(
                            child: Icon(
                              Icons.wifi,
                              color: Color(0xff3DDC84),
                              size: 80,
                            ),
                          ),
                        ),

                        const SizedBox(height: 40),

                        Text(
                          slide["title"]!,
                          textAlign: TextAlign.center,
                          style: const TextStyle(
                            fontFamily: "Geist",
                            fontSize: 30,
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                          ),
                        ),

                        const SizedBox(height: 16),

                        Text(
                          slide["subtitle"]!,
                          textAlign: TextAlign.center,
                          style: const TextStyle(
                            fontFamily: "Geist",
                            fontSize: 16,
                            color: Color(0xffB9C8BB),
                          ),
                        ),
                      ],
                    ),
                  );
                },
              ),
            ),

            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(
                slides.length,
                (index) => AnimatedContainer(
                  duration: const Duration(milliseconds: 250),
                  margin: const EdgeInsets.symmetric(horizontal: 4),
                  width: currentPage == index ? 28 : 8,
                  height: 8,
                  decoration: BoxDecoration(
                    color: currentPage == index
                        ? const Color(0xff3DDC84)
                        : Colors.white24,
                    borderRadius: BorderRadius.circular(20),
                  ),
                ),
              ),
            ),

            const SizedBox(height: 36),

            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24),
              child: SizedBox(
                width: double.infinity,
                height: 58,
                child: FilledButton(
                  style: FilledButton.styleFrom(
                    backgroundColor: const Color(0xff3DDC84),
                  ),
                  onPressed: () {
                    if (currentPage < slides.length - 1) {
                      _pageController.nextPage(
                        duration: const Duration(milliseconds: 350),
                        curve: Curves.ease,
                      );
                    } else {

                      // Register screen comes next
                      context.go('/register');
                    }
                  },
                  child: Text(
                    currentPage == slides.length - 1
                        ? "Get Started"
                        : "Next",
                    style: const TextStyle(
                      color: Colors.black,
                      fontFamily: "Geist",
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ),
            ),

            const SizedBox(height: 30),
          ],
        ),
      ),
    );
  }
}