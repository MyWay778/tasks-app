<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

abstract class BaseTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Общие правила для всех запросов к задачам
     * @param array $options ['sometimes' => true]
     */
    protected function commonRules(array $options = []): array
    {

        // Проверяем наличие ключа 'sometimes' в массиве
        $isSometimes = $options['sometimes'] ?? false;
        $prefix = $isSometimes ? ['sometimes'] : [];

        return [
            'title' => array_merge($prefix, ['required', 'string', 'max:255']),
            'description' => ['nullable', 'string'],
            'status' => array_merge($prefix, [new Enum(TaskStatus::class)]),
        ];
    }

    // Общие сообщения об ошибках
    public function messages(): array
    {
        return [
            'title.required' => 'Название задачи обязательно для заполнения.',
            'title.string'   => 'Название задачи должно быть строкой.',
            'title.max'      => 'Название задачи не должно превышать 255 символов.',
            'description.string' => 'Описание должно быть строкой.',
            'status.' . Enum::class => 'Выбран недопустимый статус задачи.',
            'status.required' => 'Статус задачи обязателен.',
        ];
    }
}
