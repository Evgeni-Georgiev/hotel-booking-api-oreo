<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(['message' => Payment::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $paymentValidationData = $request->validated();
        $payment = Payment::create($paymentValidationData);
        return response()->json(['message' => 'Payment proceeded!', 'data' => $payment], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment): JsonResponse
    {
        $paymentFound = Payment::find($payment->id);
        return response()->json(['message' => 'Payment found!', 'data' => $paymentFound]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(StorePaymentRequest $request, Payment $payment): JsonResponse
    {
        $paymentFound = Payment::find($payment->id);
        $paymentFound->update($request->validated());
        return response()->json(['message' => 'Payment Updated successfully!'], 202);
    }
}
