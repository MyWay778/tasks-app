<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;

class TaskController extends Controller
{
    // GET /api/tasks (Список)
    public function index()
    {
        $perPage = request()->integer('per_page', 15);
        $perPage = min(max($perPage, 1), 100); // Limit between 1 and 100

        return TaskResource::collection(
            Task::orderBy('id', 'desc')->paginate($perPage)
        );
    }

    // POST /api/tasks (Создание)
    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());
        return new TaskResource($task);
    }

    // GET /api/tasks/{task} (Просмотр одной)
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    // PUT/PATCH /api/tasks/{task} (Обновление)
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());
        return new TaskResource($task->fresh());
    }

    // DELETE /api/tasks/{task} (Удаление)
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->noContent(); // 204 статус
    }
}
