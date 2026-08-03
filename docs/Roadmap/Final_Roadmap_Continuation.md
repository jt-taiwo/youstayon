Agreed. We are now entering Milestone 12 — Utility Commerce Layer (Wallet, Payments & Telecom API Integration), and we will follow the same production-grade workflow we have used from the beginning:

1. Domain-first architecture

2. Contracts

3. DTOs

4. Services

5. Repositories

6. Controllers

7. API Resources

8. Feature tests

9. Unit tests

10. Full test suite

11. Git commit

12. Prompt to the next milestone

We will not build a fintech platform. We will build a utility commerce engine that supports:

* Wallet (optional stored-value wallet)

* Direct payment (Pay Now)

* Provider abstraction layer

* Airtime purchases

* Data purchases

* Electricity purchases

* Cable TV purchases

* Transaction settlement

* Webhook reconciliation

The key architectural principle is:

### Utility commerce flow

### User

Selects a utility service.

### Payment selector

Wallet or Pay Now.

### Commerce service

Orchestrates payment, provider fulfillment, transactions, and subscription updates.

### Provider adapter

Routes to the configured VTU or utility provider implementation.

### Provider API

Executes the actual airtime, data, electricity, cable TV, or internet purchase.

This keeps YouStayOn provider-neutral and future-proof.

### Milestone 12 Roadmap

| Step  | Feature                          |
| ----- | -------------------------------- |
| 12.1  | Wallet foundation                |
| 12.2  | Wallet funding transactions      |
| 12.3  | Payment gateway abstraction      |
| 12.4  | Provider abstraction layer       |
| 12.5  | Airtime purchase engine          |
| 12.6  | Data purchase engine             |
| 12.7  | Electricity purchase engine      |
| 12.8  | Cable TV purchase engine         |
| 12.9  | Wallet vs direct payment routing |
| 12.10 | Webhook reconciliation           |
| 12.11 | Unified commerce endpoint        |

We begin with Step 12.1 — Wallet Foundation.

### Step 12.1 — Wallet Foundation

### Goal

Create a minimal stored-value wallet that supports:

* current balance

* credit

* debit

* immutable wallet ledger foundation

* future payment integration

This wallet is not a bank account. It exists solely to pay for utility purchases.

### 1. Create the Wallet domain

Run:

Bash

```
php artisan make:model Domains/Wallet/Models/Wallet -m
php artisan make:model Domains/Wallet/Models/WalletTransaction -m
```

You should now have:

* `app/Domains/Wallet/Models/Wallet.php`

* `app/Domains/Wallet/Models/WalletTransaction.php`

* wallet migration

* wallet transaction migration

Stop here and tell me “Wallet models created” (or paste the generated migration filenames). Then I’ll give you the production-grade wallet schema, model implementation, repository contracts, services, and tests for Step 12.1, ending with a green test suite and the commit before we move to Step 12.2 — Wallet Funding Transactions.
