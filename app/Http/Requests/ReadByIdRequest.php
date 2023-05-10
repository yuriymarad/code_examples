<?php

namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class ReadByIdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'exists:notifications']
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->request->all(), [
            'id' => Route::input('id'),
        ]);
    }
}
