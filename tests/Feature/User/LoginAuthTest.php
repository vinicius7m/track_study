<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginAuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_login_with_http_verb_get(): void
    {
        $user = User::factory()->connection('sqlite')->create(['password' => bcrypt('12345678')]);

        $data = [
            'email' => $user->email,
            'password' => '12345678',
            'device_name' => 'PHP Unit Tests',
        ];
        // Act (Rodar o teste)
        $response = $this->get('/api/login', $data);
        // Assert (Verificar asserções)
        $response->assertStatus(405);
    }

    public function test_login_with_signed_user_in_database(): void
    {
        // Arrange (Preparar teste)
        $user = User::factory()->connection('sqlite')->create(['password' => bcrypt('12345678')]);

        $data = [
            'email' => $user->email,
            'password' => '12345678',
            'device_name' => 'PHP Unit Tests',
        ];
        // Act (Rodar o teste)
        $response = $this->post('/api/login', $data);

        // Assert (Verificar asserções)
        $jsonResponse = substr($response->content(), strpos($response->content(), '{'));
        $responseData = json_decode($jsonResponse, true);

        $response->assertStatus(200);
        $this->assertArrayHasKey('token', $responseData);
    }

    public function test_login_with_signed_user_in_database_with_credential_password_wrong(): void
    {
        $user = User::factory()->connection('sqlite')->create(['password' => bcrypt('12345678')]);

        $data = [
            'email' => $user->email,
            'password' => '123456789',
            'device_name' => 'PHP Unit Tests',
        ];

        $response = $this->post('/api/login', $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'As credenciais enviadas estão incorretas',
        ]);
    }

    public function test_login_with_signed_user_in_database_with_credential_email_wrong(): void
    {
        $user = User::factory()->connection('sqlite')->create(['password' => bcrypt('12345678')]);

        $data = [
            'email' => 'user.undefined109@email.com',
            'password' => '12345678',
            'device_name' => 'PHP Unit Tests',
        ];

        $response = $this->post('/api/login', $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'As credenciais enviadas estão incorretas',
        ]);
    }

    public function test_login_with_signed_user_in_database_with_credentials_wrong(): void
    {
        $user = User::factory()->connection('sqlite')->create(['password' => bcrypt('12345678')]);

        $data = [
            'email' => 'user.undefined109@email.com',
            'password' => '123456789',
            'device_name' => 'PHP Unit Tests',
        ];

        $response = $this->post('/api/login', $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'As credenciais enviadas estão incorretas',
        ]);
    }
}
