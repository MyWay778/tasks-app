<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status', 'completed_at'];

    protected $casts = [
        'status' => TaskStatus::class,
        'completed_at' => 'datetime',
    ];

    /**
     * Автоматическое управление датой завершения
     */
    protected static function booted()
    {
        static::updating(function ($task) {

            // Если текущий статус задачи завершена
            if ($task->status->value === TaskStatus::COMPLETED->value) {

                // Получаем список всех измененных полей
                $dirtyFields = array_keys($task->getDirty());

                // Оставляем в списке только те поля, которые НЕ являются статусом
                $forbiddenChanges = array_diff($dirtyFields, ['status']);

                // Если есть изменения в любых полях, кроме статуса
                if (!empty($forbiddenChanges)) {
                    // Получаем оригинальный (старый) статус из базы данных
                    $originalStatus = $task->getOriginal('status');

                    // Если оригинальный статус - завершенная задача
                    if ($originalStatus->value === TaskStatus::COMPLETED->value) {
                        abort(422, 'Нельзя изменять данные завершенной задачи. Сначала смените статус.');
                    }
                }
            }
        });

        static::saving(function ($task) {
            // Устанавливаем дату завершения задачи
            if ($task->isDirty('status')) {
                $task->completed_at = ($task->status->value === TaskStatus::COMPLETED->value) ? now() : null;
            }
        });
    }
}
