<?php

namespace Tests\Unit;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that completed_at is set when status changes to completed.
     */
    public function test_completed_at_is_set_when_status_changes_to_completed(): void
    {
        $task = Task::factory()->create([
            'status' => TaskStatus::NOT_COMPLETED->value,
        ]);

        $this->assertNull($task->completed_at);

        $task->status = TaskStatus::COMPLETED;
        $task->save();

        $this->assertNotNull($task->completed_at);
    }

    /**
     * Test that completed_at is cleared when status changes from completed.
     */
    public function test_completed_at_is_cleared_when_status_changes_from_completed(): void
    {
        $task = Task::factory()->create([
            'status' => TaskStatus::COMPLETED->value,
            'completed_at' => now(),
        ]);

        $this->assertNotNull($task->completed_at);

        $task->status = TaskStatus::NOT_COMPLETED;
        $task->save();

        $this->assertNull($task->completed_at);
    }

    /**
     * Test that updating completed task's title throws exception.
     */
    public function test_updating_completed_task_title_throws_exception(): void
    {
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'status' => TaskStatus::COMPLETED->value,
            'completed_at' => now(),
        ]);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Нельзя изменять данные завершенной задачи. Сначала смените статус.');

        $task->title = 'New Title';
        $task->save();
    }

    /**
     * Test that updating completed task's description throws exception.
     */
    public function test_updating_completed_task_description_throws_exception(): void
    {
        $task = Task::factory()->create([
            'description' => 'Original Description',
            'status' => TaskStatus::COMPLETED->value,
            'completed_at' => now(),
        ]);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Нельзя изменять данные завершенной задачи. Сначала смените статус.');

        $task->description = 'New Description';
        $task->save();
    }

    /**
     * Test that updating completed task's status is allowed.
     */
    public function test_updating_completed_task_status_is_allowed(): void
    {
        $task = Task::factory()->create([
            'status' => TaskStatus::COMPLETED->value,
            'completed_at' => now(),
        ]);

        $task->status = TaskStatus::NOT_COMPLETED;
        $task->save();

        $this->assertEquals(TaskStatus::NOT_COMPLETED, $task->fresh()->status);
        $this->assertNull($task->fresh()->completed_at);
    }

    /**
     * Test that updating non-completed task works normally.
     */
    public function test_updating_non_completed_task_works_normally(): void
    {
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'description' => 'Original Description',
            'status' => TaskStatus::NOT_COMPLETED->value,
        ]);

        $task->title = 'New Title';
        $task->description = 'New Description';
        $task->save();

        $this->assertEquals('New Title', $task->fresh()->title);
        $this->assertEquals('New Description', $task->fresh()->description);
    }

    /**
     * Test that status enum is cast correctly.
     */
    public function test_status_is_cast_to_enum(): void
    {
        $task = Task::factory()->create([
            'status' => TaskStatus::COMPLETED->value,
        ]);

        $this->assertInstanceOf(TaskStatus::class, $task->status);
        $this->assertEquals(TaskStatus::COMPLETED, $task->status);
    }

    /**
     * Test that completed_at is cast to datetime.
     */
    public function test_completed_at_is_cast_to_datetime(): void
    {
        $completedAt = now();
        $task = Task::factory()->create([
            'status' => TaskStatus::COMPLETED->value,
            'completed_at' => $completedAt,
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $task->completed_at);
    }

    /**
     * Test that creating task with completed status sets completed_at.
     */
    public function test_creating_task_with_completed_status_sets_completed_at(): void
    {
        $task = Task::create([
            'title' => 'Completed Task',
            'status' => TaskStatus::COMPLETED->value,
        ]);

        $this->assertNotNull($task->completed_at);
    }

    /**
     * Test that creating task with not completed status leaves completed_at null.
     */
    public function test_creating_task_with_not_completed_status_leaves_completed_at_null(): void
    {
        $task = Task::create([
            'title' => 'Not Completed Task',
            'status' => TaskStatus::NOT_COMPLETED->value,
        ]);

        $this->assertNull($task->completed_at);
    }
}
