import 'package:flutter/material.dart';

class QuickActions extends StatelessWidget {

  const QuickActions({super.key});

  @override
  Widget build(BuildContext context) {

    return Column(

      crossAxisAlignment: CrossAxisAlignment.start,

      children: [

        const Text(
          "Quick Actions",
          style: TextStyle(
            color: Colors.white,
            fontSize: 22,
            fontWeight: FontWeight.bold,
          ),
        ),

        const SizedBox(height: 20),

        GridView.count(

          shrinkWrap: true,

          physics: const NeverScrollableScrollPhysics(),

          crossAxisCount: 2,

          crossAxisSpacing: 16,

          mainAxisSpacing: 16,

          childAspectRatio: 1.6,

          children: const [

            _ActionCard("Airtime", Icons.phone_android),

            _ActionCard("Data", Icons.wifi),

            _ActionCard("Electricity", Icons.bolt),

            _ActionCard("Cable TV", Icons.tv),

          ],
        ),
      ],
    );
  }
}

class _ActionCard extends StatelessWidget {

  final String title;

  final IconData icon;

  const _ActionCard(this.title, this.icon);

  @override
  Widget build(BuildContext context) {

    return Container(

      decoration: BoxDecoration(

        color: const Color(0xff1A231C),

        borderRadius: BorderRadius.circular(18),

      ),

      child: Column(

        mainAxisAlignment: MainAxisAlignment.center,

        children: [

          Icon(
            icon,
            color: Color(0xff3DDC84),
            size: 34,
          ),

          SizedBox(height: 10),

          Text(
            title,
            style: TextStyle(
              color: Colors.white,
            ),
          ),

        ],
      ),
    );
  }
}