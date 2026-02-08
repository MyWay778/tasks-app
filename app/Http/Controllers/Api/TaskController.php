<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Enums\TaskStatus;
use Illuminate\Validation\Rules\Enum;

class TaskController extends Controller
{
    // GET /api/tasks (Список)
    public function index()
    {
        return Task::orderBy('id', 'desc')->get();
    }

    // POST /api/tasks (Создание)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => [new Enum(TaskStatus::class)],
        ]);

        return Task::create($validated);
    }

    // GET /api/tasks/{task} (Просмотр одной)
    public function show(Task $task)
    {
        return $task;
    }

    // PUT/PATCH /api/tasks/{task} (Обновление)
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => [new Enum(TaskStatus::class)],
        ]);

        $task->update($validated);
        return $task;
    }

    // DELETE /api/tasks/{task} (Удаление)
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->noContent(); // 204 статус
    }
}
