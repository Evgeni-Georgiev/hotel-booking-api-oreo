<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomerNotFoundException;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse A JSON response indicating operation message.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'customers',
            'data' => Customer::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreCustomerRequest $request The request containing the input data for the new customer.
     * @return JsonResponse A JSON response indicating operation message.
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());
        return response()->json([
            'message' => 'Customer created successfully!',
            'data' => $customer
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Customer $customer The customer instance to be fetched.
     * @return JsonResponse A JSON response indicating operation message.
     * @throws CustomerNotFoundException If searched customer is not found.
     */
    public function show(Customer $customer): JsonResponse
    {
        if(!$this->foundCustomer($customer)) {
            throw new CustomerNotFoundException('Customer not found!');
        }
        return response()->json([
            'customer' => $this->foundCustomer($customer)
        ]);
    }

    /**
     * Search for a customer by id.
     *
     * @param Customer $customer The customer instance to be found.
     * @return Customer The found customer.
     * @throws CustomerNotFoundException If the customer is not found.
     */
    private function foundCustomer(Customer $customer): Customer
    {
        $foundCustomer = Customer::find($customer->id);
        if(!$foundCustomer) {
            throw new CustomerNotFoundException('Customer not found!');
        }
        return $foundCustomer;
    }
}
