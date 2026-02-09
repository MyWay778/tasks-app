<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting list of tasks.
     */
    public function test_can_get_list_of_tasks(): void
    {
        $tasks = Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'completed_at',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);

        // Verify tasks are ordered by id desc
        $responseData = $response->json('data');
        $this->assertEquals($tasks->last()->id, $responseData[0]['id']);
    }

    /**
     * Test creating a task with valid data.
     */
    public function test_can_create_task_with_valid_data(): void
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => TaskStatus::NOT_COMPLETED->value,
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'completed_at',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'title' => 'Test Task',
                    'description' => 'Test Description',
                    'status' => TaskStatus::NOT_COMPLETED->value,
                    'completed_at' => null,
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => TaskStatus::NOT_COMPLETED->value,
        ]);
    }

    /**
     * Test creating a task without status defaults to NOT_COMPLETED.
     */
    public function test_creating_task_without_status_defaults_to_not_completed(): void
    {
        $taskData = [
            'title' => 'Test Task',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'status' => TaskStatus::NOT_COMPLETED->value,
                    'completed_at' => null,
                ],
            ]);
    }

    /**
     * Test creating a task with completed status sets completed_at.
     */
    public function test_creating_completed_task_sets_completed_at(): void
    {
        $taskData = [
            'title' => 'Completed Task',
            'status' => TaskStatus::COMPLETED->value,
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201);

        $task = Task::find($response->json('data.id'));
        $this->assertNotNull($task->completed_at);
    }

    /**
     * Test creating a task requires title.
     */
    public function test_cannot_create_task_without_title(): void
    {
        $response = $this->postJson('/api/tasks', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test creating a task with title exceeding max length.
     */
    public function test_cannot_create_task_with_title_exceeding_max_length(): void
    {
        $taskData = [
            'title' => str_repeat('a', 256), // 256 characters
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test creating a task with invalid status.
     */
    public function test_cannot_create_task_with_invalid_status(): void
    {
        $taskData = [
            'title' => 'Test Task',
            'status' => 'invalid_status',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /**
     * Test getting a single task.
     */
    public function test_can_get_single_task(): void
    {
        $task = Task::factory()->create([
            'title' => 'Single Task',
            'description' => 'Single Description',
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'completed_at',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => 'Single Task',
                    'description' => 'Single Description',
                ],
            ]);
    }

    /**
     * Test getting non-existent task returns 404.
     */
    public function test_getting_nonexistent_task_returns_404(): void
    {
        $response = $this->getJson('/api/tasks/99999');

        $response->assertStatus(404);
    }

    /**
     * Test updating a task with valid data.
     */
    public function test_can_update_task_with_valid_data(): void
    {
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'status' => TaskStatus::NOT_COMPLETED->value,
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Updated Title',
                    'description' => 'Updated Description',
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ]);
    }

    /**
     * Test updating task status to completed sets completed_at.
     */
    public function test_updating_task_to_completed_sets_completed_at(): void
    {
        $task = Task::factory()->create([
            'status' => TaskStatus::NOT_COMPLETED->value,
        ]);

        $this->assertNull($task->completed_at);

        $response = $this->patchJson("/api/tasks/{$task->id}", [
            'status' => TaskStatus::COMPLETED->value,
        ]);

        $response->assertStatus(200);

        $task->refresh();
        $this->assertNotNull($task->completed_at);
    }

    /**
     * Test updating task status from completed to not completed clears completed_at.
     */
    public function test_updating_task_from_completed_to_not_completed_clears_completed_at(): void
    {
        $task = Task::factory()->create([
            'status' => TaskStatus::COMPLETED->value,
            'completed_at' => now(),
        ]);

        $this->assertNotNull($task->completed_at);

        $response = $this->patchJson("/api/tasks/{$task->id}", [
            'status' => TaskStatus::NOT_COMPLETED->value,
        ]);

        $response->assertStatus(200);

        $task->refresh();
        $this->assertNull($task->completed_at);
    }

    /**
     * Test cannot update completed task's title or description.
     */
    public function test_cannot_update_completed_task_title_or_description(): void
    {
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'status' => TaskStatus::COMPLETED->value,
            'completed_at' => now(),
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'New Title',
            'description' => 'New Description',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Нельзя изменять данные завершенной задачи. Сначала смените статус.',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Original Title',
        ]);
    }

    /**
     * Test can update completed task's status.
     */
    public function test_can_update_completed_task_status(): void
    {
        $task = Task::factory()->create([
            'status' => TaskStatus::COMPLETED->value,
            'completed_at' => now(),
        ]);

        $response = $this->patchJson("/api/tasks/{$task->id}", [
            'status' => TaskStatus::NOT_COMPLETED->value,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test can update title and description if status is also changed from completed.
     */
    public function test_can_update_title_when_changing_status_from_completed(): void
    {
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'status' => TaskStatus::COMPLETED->value,
            'completed_at' => now(),
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'New Title',
            'status' => TaskStatus::NOT_COMPLETED->value,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'New Title',
                    'status' => TaskStatus::NOT_COMPLETED->value,
                ],
            ]);
    }

    /**
     * Test updating task with invalid status.
     */
    public function test_cannot_update_task_with_invalid_status(): void
    {
        $task = Task::factory()->create();

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /**
     * Test updating task with title exceeding max length.
     */
    public function test_cannot_update_task_with_title_exceeding_max_length(): void
    {
        $task = Task::factory()->create();

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => str_repeat('a', 256),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test deleting a task.
     */
    public function test_can_delete_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * Test deleting non-existent task returns 404.
     */
    public function test_deleting_nonexistent_task_returns_404(): void
    {
        $response = $this->deleteJson('/api/tasks/99999');

        $response->assertStatus(404);
    }

    /**
     * Test partial update (PATCH) works correctly.
     */
    public function test_can_partially_update_task(): void
    {
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'status' => TaskStatus::NOT_COMPLETED->value,
            'description' => 'Original Description',
        ]);

        $response = $this->patchJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Updated Title',
                    'description' => 'Original Description', // Should remain unchanged
                ],
            ]);
    }

    /**
     * Test API response structure matches TaskResource format.
     */
    public function test_api_response_structure_matches_resource_format(): void
    {
        $task = Task::factory()->create([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => TaskStatus::COMPLETED->value,
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'completed_at',
                    'created_at',
                    'updated_at',
                ],
            ]);

        // Verify dates are in ISO8601 format
        $data = $response->json('data');
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $data['created_at']);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $data['updated_at']);
    }
}
