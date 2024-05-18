<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class ClassroomTest extends TestCase
{
    use RefreshDatabase;

    private $faker;
    public function setUp(): void
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

        Log::info($registerResponse->getContent());

        return json_decode($registerResponse->getContent())->user->id;
    }

    private function createClassroom()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post($this->baseUrl.'/classrooms', [
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'subject' => $this->faker->randomElement(['Sains', 'Matematika', 'Bahasa', 'Teknologi', 'Sosial', 'Seni']),
            'photo' => null
        ]);

        Log::info($response->getContent());

        return json_decode($response->getContent())->data->id;
    }

    private function createMaterial()
    {
        $classroomId = $this->createClassroom();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post($this->baseUrl.'/materials', [
            'class_id' => $classroomId,
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'file' => null,
            'type' => 'material',
            'deadline' => null
        ]);

        Log::info($response->getContent());

        return json_decode($response->getContent())->data->id;
    }

    private function createSubmission()
    {
        $userId = $this->registerNewUser();
        $materialId = $this->createMaterial();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post($this->baseUrl.'/submissions', [
            'user_id' => $userId,
            'material_id' => $materialId,
            'comment' => "I did this task!"
        ]);

        Log::info($response->getContent());

        return json_decode($response->getContent())->data->id;
    }

    public function test_get_classrooms()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get($this->baseUrl.'/classrooms');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('data', $response->getContent());
    }

    public function test_get_classroom_by_id()
    {
        $classroomId = $this->createClassroom();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get($this->baseUrl."/classrooms/{$classroomId}");

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('data', $response->getContent());
    }

    public function test_update_classroom()
    {
        $classroomId = $this->createClassroom();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->put($this->baseUrl."/classrooms/{$classroomId}", [
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'subject' => $this->faker->randomElement(['Sains', 'Matematika', 'Bahasa', 'Teknologi', 'Sosial', 'Seni']),
            'photo' => null
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_classroom()
    {
        $classroomId = $this->createClassroom();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->delete($this->baseUrl."/classrooms/{$classroomId}");

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_get_materials()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get($this->baseUrl.'/materials');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('data', $response->getContent());
    }

    public function test_get_material_by_id()
    {
        $materialId = $this->createMaterial();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get($this->baseUrl."/materials/{$materialId}");

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('data', $response->getContent());
    }

    public function test_update_material()
    {
        $materialId = $this->createMaterial();
        $classroomId = $this->createClassroom();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->put($this->baseUrl."/materials/{$materialId}", [
            'class_id' => $classroomId,
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'file' => null,
            'type' => 'assignment',
            'deadline' => $this->faker->date
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_material()
    {
        $materialId = $this->createMaterial();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->delete($this->baseUrl."/materials/{$materialId}");

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_get_submission_by_id()
    {
        $submissionId = $this->createSubmission();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get($this->baseUrl."/submissions/{$submissionId}");

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_update_submission()
    {
        $submissionId = $this->createSubmission();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->put($this->baseUrl."/submissions/{$submissionId}", [
            'grade' => 100
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_submission()
    {
        $submissionId = $this->createSubmission();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->delete($this->baseUrl."/submissions/{$submissionId}");

        $this->assertEquals(200, $response->getStatusCode());
    }
}
