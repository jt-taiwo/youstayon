I think this is an important checkpoint, and I’m glad you raised it now rather than after Milestone 14. I reviewed the roadmap you uploaded and the architecture direction we’ve been following, and I think we should clarify the roadmap rather than change it. The key insight is that we have actually built more backend intelligence than the original roadmap anticipated, but we have not yet built the utility commerce layer (payments + telecom/provider integrations), which is essential for the MVP you want.

The clarification I recommend is that Wallet / Payments Integration should become Milestone 14, and Flutter Mobile Foundation should move to Milestone 15. This does not invalidate or alter any milestone we have already completed; it simply inserts the missing commerce layer before the frontend consumes it.

### Clarified MVP roadmap

Backend intelligence is complete; the missing commerce layer comes next before Flutter.

| Milestone                                                    | Status   |
| ------------------------------------------------------------ | -------- |
| 01 — Product & Architecture Foundation                       | Complete |
| 02 — Backend Foundation (Laravel + MySQL)                    | Complete |
| 03 — User Domain                                             | Complete |
| 04 — Subscription Domain                                     | Complete |
| 05 — Radar Intelligence Phase 1                              | Complete |
| 06 — Reminder & Notification Intelligence                    | Complete |
| 07 — Scheduler & Queue Automation                            | Complete |
| 08 — Notification Delivery Engine                            | Complete |
| 09 — Dashboard & Analytics                                   | Complete |
| 10 — Radar Intelligence Phase 2                              | Complete |
| 11 — Advanced Subscription Intelligence & Automation         | Complete |
| 12 — Utility Commerce Layer (Wallet, Payments, Telecom APIs) | Next     |
| 13 — Flutter Mobile Foundation                               | Pending  |
| 14 — CI/CD & Production Deployment                           | Pending  |

This preserves every milestone we have completed while placing the missing payment and provider layer in the correct position.

### Why payments must come before Flutter

Your later product reflection is the right architecture. YouStayOn is not a fintech app; it is a utility platform. The wallet exists only to reduce payment friction.

The final MVP payment model should be:

### Dual payment model

### Wallet

Fund once, purchase instantly from stored value.

### Pay now

Card, bank transfer, USSD, or account transfer via a payment gateway such as Monnify, Paystack, or Flutterwave.

This means wallet is optional, not mandatory.

### Telecom/provider integrations

You are also correct that “Payments” is not just wallet funding. It includes the utility provider integrations that actually deliver services.

For the MVP, I recommend we build a Provider Abstraction Layer rather than hardcode MTN, Airtel, Glo, or 9mobile directly.

Architecture:

### Utility commerce flow

### User

Chooses Airtime, Data, Electricity, Cable TV, or Internet.

### Payment selector

Wallet or Pay Now.

### Provider abstraction layer

Routes requests through a common interface instead of provider-specific logic.

### VTU provider adapter

VTpass, ClubKonnect, Recharge2Cash, or another provider implementation.

### Provider API

Actual telecom or utility fulfillment endpoint.

This gives us:

* MTN airtime

* Airtel airtime

* Glo airtime

* 9mobile airtime

* Data bundles

* Electricity tokens

* Cable TV renewals

* Internet subscriptions

without coupling the app to a single provider.

### What we already have

We are not starting from scratch. We already have:

* Authentication

* User profile

* Subscription management

* Radar intelligence

* Prediction engine

* Notifications

* Dashboard analytics

* Budget forecasting

* AI-ready recommendation engine

* Unified intelligence endpoint

The commerce layer will connect directly into this intelligence layer.

For example:

Buy 15GB MTN Data

* Payment succeeds

* Provider confirms purchase

* Transaction is recorded

* Subscription is created or renewed

* Usage limit is initialized

* Radar score updates

* Reminder schedule updates

* Notification is generated

This is exactly the architecture we have been preparing for.

### Revised MVP scope

The MVP I would ship is:

* Authentication

* Dashboard

* Services

  * Airtime

  * Data

  * Electricity

  * Cable TV

  * Internet



* Optional Wallet

  * Balance

  * Fund Wallet

  * Wallet History

* Direct Payment (Pay Now)

* Transactions

* Radar Intelligence

* Notifications

* Profile & Settings

This keeps the app focused on “The fastest way to stay connected.”

### My recommendation

I recommend that we do not move to Flutter yet.

We should first build Milestone 12 — Utility Commerce Layer, which includes:

1. Wallet (simplified stored-value wallet)

2. Payment gateway integration (Monnify first, abstraction-ready)

3. Provider abstraction architecture

4. Airtime purchase engine

5. Data purchase engine

6. Electricity purchase engine

7. Cable TV purchase engine

8. Transaction settlement

9. Wallet deduction / direct payment routing

10. Purchase webhooks and reconciliation

Then Flutter will integrate against a complete production backend, which is far cleaner than building UI against placeholder purchase endpoints.

I think this is the strongest product decision we can make at this stage.
---------------------------------------------------------
--------------------------------------------------------

