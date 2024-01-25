<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->customer = Customer::factory()->create();
        $this->customerData = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_number' => '931-858-4071'
        ];
    }

    public function tearDown(): void
    {
        parent::tearDown();

        // Close the mock
        Mockery::close();
    }

    public function getUserToken(): string
    {
        $user = User::factory()->create();
        return $user->createToken('appToken')->plainTextToken;
    }

    public function testShowCustomerViaGetRequestReturnsJsonResponseWhenValidaData(): void
    {
        // When
        $response = $this->getJson(route('customer.show', ['id' => $this->customer->id]));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(['customer']);
        $responseData = $response->decodeResponseJson();
        $response->assertJson([
            'customer' => [
                'name' => $responseData['customer']['name'],
                'email' => $responseData['customer']['email'],
                'phone_number' => $responseData['customer']['phone_number'],
            ],
        ]);
    }

    public function testIndexCustomerViaGetRequestReturnsJsonResponseWhenValidaData(): void
    {
        // When
        $response = $this->getJson(route('customer.index'));

        // Then
        $responseData = $response->decodeResponseJson();
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(['data']);
        $this->assertDatabaseHas('customer', [
            'name' => $responseData['data'][0]['name'],
            'email' => $responseData['data'][0]['email'],
            'phone_number' => $responseData['data'][0]['phone_number'],
        ]);
    }

    public function testCreateCustomerViaPostRequestReturnsJsonResponseWhenValidaData()
    {
        // When
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->postJson(route('customer.store'), $this->customerData);

        // Then
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Customer created successfully!',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'name',
                    'email',
                    'phone_number',
                ],
            ]);
        $this->assertDatabaseHas('customer', [
            'name' => $response['data']['name'],
            'email' => $response['data']['email'],
            'phone_number' => $response['data']['phone_number'],
        ]);
    }

    public function testInvalidCreateCustomerViaPostRequestReturnsErrorWhenMissingName()
    {
        // When
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->postJson(route('customer.store'), [
                'email' => $this->customerData['email'],
                'phone_number' => $this->customerData['phone_number'],
            ]);

        // Then
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment(['The name is required.']);
    }

    public function testInvalidCreateCustomerViaPostRequestReturnsErrorWhenMissingEmail()
    {
        // When
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->postJson(route('customer.store'), [
                'name' => $this->customerData['name'],
                'phone_number' => $this->customerData['phone_number'],
            ]);

        // Then
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email'])
            ->assertJsonFragment(['The email is required.']);
    }

    public function testInvalidCreateCustomerViaPostRequestReturnsErrorWhenInvalidEmail()
    {
        // When
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->postJson(route('customer.store'), [
                'name' => $this->customerData['name'],
                'email' => 'invalid-email',
                'phone_number' => $this->customerData['phone_number']
            ]);

        // When
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email'])
            ->assertJsonFragment(['Invalid email format.']);
    }

    public function testInvalidCreateCustomerViaPostRequestReturnsErrorWhenDuplicateEmail()
    {
        // Given
        $existingCustomer = Customer::factory()->create();

        // When
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->postJson(route('customer.store'), [
                'name' => $this->customerData['name'],
                'email' => $existingCustomer->email,
                'phone_number' => $this->customerData['phone_number'],
            ]);

        // Then
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email'])
            ->assertJsonFragment(['The email has already been taken.']);
    }

    public function testInvalidCreateCustomerViaPostRequestReturnsErrorWhenMissingPhoneNumber()
    {
        // When
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->postJson(route('customer.store'), [
                'name' => $this->customerData['name'],
                'email' => $this->customerData['email'],
            ]);

        // Then
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['phone_number'])
            ->assertJsonFragment(['The phone number is required.']);
    }

    public function testInvalidCreateCustomerViaPostRequestReturnsErrorWhenInvalidPhoneNumber()
    {
        // When
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->postJson(route('customer.store'), [
                'name' => $this->customerData['name'],
                'email' => $this->customerData['email'],
                'phone_number' => 'invalid-phone-number',
            ]);

        // Then
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['phone_number'])
            ->assertJsonFragment(['The phone number format is invalid.']);
    }

}
