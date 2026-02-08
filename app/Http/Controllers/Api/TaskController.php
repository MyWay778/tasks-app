<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;

class TaskController extends Controller
{
    // GET /api/tasks (Список)
    public function index()
    {
        return Task::orderBy('id', 'desc')->get();
    }

    // POST /api/tasks (Создание)
    public function store(StoreTaskRequest $request)
    {
        return Task::create($request->validated());
    }

    // GET /api/tasks/{task} (Просмотр одной)
    public function show(Task $task)
    {
        return $task;
    }

    // PUT/PATCH /api/tasks/{task} (Обновление)
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());
        return $task;
    }

    // DELETE /api/tasks/{task} (Удаление)
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->noContent(); // 204 статус
    }
}
