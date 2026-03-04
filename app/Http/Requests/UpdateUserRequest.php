<?php

namespace App\Http\Requests;

use App\Models\ParentContact;
use App\Models\Staff;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user->user_id ?? 0;

        $rules = [
            'email' => ['required', 'string', 'email', 'max:255', "unique:users,email,{$userId},user_id"],
            'phone_number' => ['nullable', 'string', 'min:7', 'max:15', 'regex:/^[0-9+\-\s()]+$/'],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:5'],
        ];

        return $rules;
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'phone_number.regex' => 'Phone number may only contain digits, +, -, spaces, and parentheses.',
        ];
    }
}
