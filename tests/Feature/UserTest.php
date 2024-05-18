<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private $faker;
    protected function setUp(): void
    {
        $this->faker = Faker\Factory::create();
        parent::setUp();
    }

    private function registerNewUser()
    {
        $registerResponse = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post($this->baseUrl.'/auth/register', [
            'email' => $this->faker->unique()->safeEmail,
            'username' => $this->faker->userName,
            'password' => $this->faker->password,
            'method' => 'Email'
        ]);

        return json_decode($registerResponse->getContent())->user->id;
    }

    public function test_get_users()
    {
        $usersResponse = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get($this->baseUrl.'/users');

        $this->assertEquals(200, $usersResponse->getStatusCode());
        $this->assertStringContainsString('data', $usersResponse->getContent());
    }

    public function test_get_user_by_id()
    {
        $userId = $this->registerNewUser();

        $userResponse = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get($this->baseUrl."/users/{$userId}");

        $this->assertEquals(200, $userResponse->getStatusCode());
        $this->assertStringContainsString('user', $userResponse->getContent());
    }

    public function test_update_user()
    {
        $userId = $this->registerNewUser();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->put($this->baseUrl."/users/{$userId}", [
            'username' => $this->faker->userName,
            'institution' => $this->faker->word,
            'photo' => null
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_user()
    {
        $userId = $this->registerNewUser();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->delete($this->baseUrl."/users/{$userId}");

        $this->assertEquals(200, $response->getStatusCode());
    }
}
