<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterAuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_register_with_http_verb_get(): void
    {
        $data = [
            'name' => 'Example 1',
            'email' => 'example1@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'device_name' => 'PHP Unit Tests',
        ];

        $response = $this->get('/api/register', $data);

        $response->assertStatus(405);
    }

    public function test_register_with_credentials_corrects(): void
    {
        $data = [
            'name' => 'Example 1',
            'email' => 'example1@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'device_name' => 'PHP Unit Tests',
        ];

        $response = $this->post('/api/register', $data);

        $response->assertStatus(200)->assertJsonStructure(['user', 'token']);

        $this->assertDatabaseHas('users', [
            'name' => 'Example 1',
            'email' => 'example1@gmail.com',
        ]);
    }

    public function test_register_with_credentials_passwords_wrong(): void
    {
        $data = [
            'name' => 'Example 1',
            'email' => 'example1@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'device_name' => 'PHP Unit Tests',
        ];

        $response = $this->post('/api/login', $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'As credenciais enviadas estão incorretas',
        ]);
    }

    public function test_register_with_credential_email_type_wrong(): void
    {
        $data = [
            'name' => 'ucom',
            'email' => 'iurruen',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'device_name' => 'PHP Unit Tests',
        ];

        $response = $this->post('/api/register', $data);

        dd($response->content());

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
