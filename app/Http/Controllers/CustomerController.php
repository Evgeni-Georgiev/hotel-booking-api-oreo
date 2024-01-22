<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(['message' => 'customers', 'data' => Customer::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($this->customerDataValidated($request));
        return response()->json(['message' => 'Customer created successfully!', 'data' => $customer], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): JsonResponse
    {
        return response()->json(['customer' => $this->foundCustomer($customer)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCustomerRequest $request, Customer $customer): JsonResponse
    {
        $this->foundCustomer($customer)->update($this->customerDataValidated($request));
        return response()->json(['message' => 'Customer updated successfully!'], 202);
    }

    private function foundCustomer(Customer $customer) {
        return Customer::find($customer->id);
    }

    private function customerDataValidated(StoreCustomerRequest $request) {
        // also handle validation error exceptions
        return $request->validated();
    }
}
