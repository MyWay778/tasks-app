<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;

class StoreTaskRequest extends BaseTaskRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->commonRules();
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default status if not provided
        if (!$this->has('status')) {
            $this->merge([
                'status' => TaskStatus::NOT_COMPLETED->value,
            ]);
        }
    }
}

