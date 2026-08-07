<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Controllers;

use App\Domains\Purchase\Contracts\CheckoutPurchaseServiceInterface;
use App\Domains\Purchase\Requests\CheckoutRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class CheckoutPurchaseController extends Controller
{
    public function __construct(
        private readonly CheckoutPurchaseServiceInterface $checkout
    ) {
    }

    public function __invoke(
        CheckoutRequest $request
    ): JsonResponse {

        $result = $this->checkout->execute(
            user: $request->user(),
            serviceType: $request->validated('service_type'),
            amount: (float) $request->validated('amount'),
            paymentMethod: $request->validated('payment_method'),
            payload: $request->validated('payload'),
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
