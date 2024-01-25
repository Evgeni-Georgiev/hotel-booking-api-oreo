<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];
    }

    public function testRegisterWithCorrectCredentials(): void
    {
        // Given
        $user = User::factory()->make([
            'name' => $this->userData['name'],
            'email' => $this->userData['email'],
        ]);
        Sanctum::actingAs($user, ['*']);

        // When
        $response = $this->postJson(route('auth.register'), $this->userData);

        // Then
        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token'])
            ->assertJson([
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        $this->assertDatabaseHas('user', [
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function testLoginWithCorrectCredentials(): void
    {
        // Given
        $user = User::factory()->create([
            'name' => $this->userData['name'],
            'email' => $this->userData['email'],
            'password' => bcrypt($this->userData['password']),
        ]);
        Sanctum::actingAs($user, ['*']);

        // When
        $response = $this->postJson(route('auth.login'), $this->userData);

        // Then
        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token'])
            ->assertJson([
                'user' => [
                    'email' => $user->email,
                ],
            ]);
    }

    public function testLogoutUserInSession(): void
    {
        // Given
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        // When
        $response = $this->postJson(route('auth.logout'));

        // Then
        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out!']);
        $this->assertCount(0, $user->tokens);
    }

    public function testLoginWithIncorrectEmail(): void
    {
        // Given
        User::factory()->create([
            'email' => 'foo@mail.com',
            'password' => bcrypt('password')
        ]);

        // When
        $response = $this->postJson(route('auth.login'), [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        // Then
        $response->assertStatus(401)
            ->assertJson(['message' => 'Wrong email or password']);
    }

    public function testLoginWithIncorrectPassword(): void
    {
        // Given
        $user = User::factory()->create([
            'name' => $this->userData['name'],
            'email' => $this->userData['email'],
            'password' => bcrypt("123password"),
        ]);
        Sanctum::actingAs($user, ['*']);

        // When
        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => $user->password,
        ]);

        // Then
        $response->assertStatus(401)
            ->assertJson(['message' => 'Wrong email or password']);
    }

    public function testLoginWithMissingEmail(): void
    {
        // When
        $response = $this->postJson(route('auth.login'), [
            'password' => 'password123',
        ]);

        // Then
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function testLoginWithMissingPassword(): void
    {

        // When
        $response = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
        ]);

        // Then
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function testLoginWhenEmailIsRequired(): void
    {
        // Given
        User::factory()->create([
            'email' => 'foo@mail.com',
            'password' => bcrypt('password')
        ]);

        // When
        $response = $this->postJson(route('auth.login'), [
            'email' => null,
            'password' => 'password'
        ]);

        // Then
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function testWhenLoginEmailMustBeValid(): void
    {
        // Given
        User::factory()->create([
            'email' => 'foo@mail.com',
            'password' => bcrypt('password')
        ]);

        // When
        $response = $this->postJson(route('auth.login'), [
            'email' => 'foo',
            'password' => 'password'
        ]);

        // Then
        $response->assertStatus(422);
    }

    public function testWhenLoginPasswordIsRequired(): void
    {
        // Given
        User::factory()->create([
            'email' => 'foo@mail.com',
            'password' => bcrypt('password')
        ]);

        // When
        $response = $this->postJson(route('auth.login'), [
            'email' => 'foo@mail.com',
            'password' => null,
        ]);

        // Then
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

}
