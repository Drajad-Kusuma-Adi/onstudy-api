<?php

use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ClassroomTest extends TestCase
{
    private $faker;
    public function setUp(): void
    {
        $this->faker = Faker\Factory::create();
        parent::setUp();
    }

    private function create_classroom()
    {
        $registerResponse = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post($this->baseUrl.'/auth/register', [
            'email' => $this->faker->unique()->safeEmail,
            'username' => $this->faker->userName,
            'password' => $this->faker->password,
            'method' => 'Email'
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post($this->baseUrl.'/classrooms', [
            'user_id' => json_decode($registerResponse->getContent())->user->id,
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'subject' => $this->faker->randomElement(['Sains', 'Matematika', 'Bahasa', 'Teknologi', 'Sosial', 'Seni']),
            'photo' => null
        ]);

        return json_decode($response->getContent())->classroom->id;
    }

    private function create_material()
    {
        $user = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post($this->baseUrl.'/auth/register', [
            'username' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password,
            'method' => 'Email'
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post($this->baseUrl.'/materials', [
            'user_id' => json_decode($user->getContent())->user->id,
            'class_id' => $this->create_classroom(),
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'file' => null,
            // 'type' => $this->faker->randomElement(['assignment', 'material']),
            'type' => 'material',
            'deadline' => null
        ]);

        return json_decode($response->getContent())->material->id;
    }

    public function create_submission()
    {
        $user = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post($this->baseUrl.'/auth/register', [
            'username' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password,
            'method' => 'Email'
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post($this->baseUrl.'/submissions', [
            'user_id' => json_decode($user->getContent())->user->id,
            'material_id' => $this->create_material(),
            'comment' => "I did this task!"
        ]);

        return json_decode($response->getContent())->submission->id;
    }

    // Classroom happy path tests

    public function test_get_classrooms()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get($this->baseUrl.'/classrooms');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('classrooms', $response->getContent());
    }

    public function test_get_classroom_by_id()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get($this->baseUrl.'/classrooms/'.$this->create_classroom());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('classroom', $response->getContent());
    }

    public function test_update_classroom()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post($this->baseUrl.'/classrooms/'.$this->create_classroom(), [
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'subject' => $this->faker->randomElement(['Sains', 'Matematika', 'Bahasa', 'Teknologi', 'Sosial', 'Seni']),
            'photo' => null
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_classroom()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->delete($this->baseUrl.'/classrooms/'.$this->create_classroom());

        $this->assertEquals(200, $response->getStatusCode());
    }

    // Materials happy path tests
    public function test_get_materials()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get($this->baseUrl.'/materials');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('materials', $response->getContent());
    }

    public function test_get_material_by_id()
    {
        // Create new material
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get($this->baseUrl.'/materials/'.$this->create_material());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('material', $response->getContent());
    }

    public function test_update_material()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post($this->baseUrl.'/materials/'.$this->create_material(), [
            'class_id' => $this->create_classroom(),
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'file' => null,
            // 'type' => $this->faker->randomElement(['assignment', 'material']),
            'type' => 'assignment',
            'deadline' => $this->faker->date
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_material()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->delete($this->baseUrl.'/materials/'.$this->create_material());

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_read_submissions_on_a_material()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get($this->baseUrl.'/materials/'.$this->create_material().'/submissions');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_read_submission_by_id()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get($this->baseUrl.'/submissions/'.$this->create_submission());

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_update_submission()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post($this->baseUrl.'/submissions/'.$this->create_submission(), [
            'grade' => 100
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_submission()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->delete($this->baseUrl.'/submissions/'.$this->create_submission());

        $this->assertEquals(200, $response->getStatusCode());
    }
}
