<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomerNotFoundException;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
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
            'data' => CustomerResource::collection(Customer::all())
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
            'data' => new CustomerResource($customer)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Customer $customer The model of the searched booking.
     * @return JsonResponse A JSON response indicating operation message.
     * @throws CustomerNotFoundException If searched customer is not found.
     */
    public function show(Customer $customer): JsonResponse
    {
        $foundCustomer = Customer::find($customer->id);
        if(!$foundCustomer) {
            throw new CustomerNotFoundException();
        }
        return response()->json([
            'customer' => new CustomerResource($foundCustomer)
        ]);
    }
}
