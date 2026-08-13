<?php

namespace App\Http\Requests;

use App\Enums\CallResult;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCallRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'duration' => ['required', 'integer', 'min:0'],
            'result' => ['required', Rule::enum(CallResult::class)],
            'manager_id' => ['required', 'integer', Rule::exists('managers', 'id')],
        ];
    }
}
