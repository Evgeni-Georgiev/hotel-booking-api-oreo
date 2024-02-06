<?php

namespace App\Http\Controllers;

use App\Exceptions\PaymentNotFoundException;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse A JSON response indicating operation message.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'payments',
            'data' => Payment::select(
                'booking_id',
                'amount',
                'payment_date',
                'status',
            )
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePaymentRequest $request The request containing the input data.
     * @return JsonResponse A JSON response indicating operation message.
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $payment = Payment::create($request->validated());
        return response()->json([
            'message' => 'Payment proceeded!',
            'data' => new PaymentResource($payment)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id The ID of the searched payment.
     * @return JsonResponse A JSON response indicating operation message.
     * @throws PaymentNotFoundException If searched payment is not found.
     */
    public function show(Payment $payment): JsonResponse
    {
        $paymentFound = Payment::find($payment->id);
        if (!$paymentFound) {
            throw new PaymentNotFoundException();
        }

        return response()->json([
            'message' => 'Payment found!',
            'data' => new PaymentResource($paymentFound)
        ]);
    }
}
