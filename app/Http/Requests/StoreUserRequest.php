<?php

namespace App\Http\Requests;

use App\Models\ParentContact;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
        $rules = [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'min:7', 'max:15', 'regex:/^[0-9+\-\s()]+$/'],
            'role_name' => ['required', 'string', 'in:staff,admin,parent'],
        ];

        // Password rules depend on role
        $roleName = $this->input('role_name');
        if ($roleName === 'parent') {
            // Password optional for parents (they may get a reset email)
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'];
            $rules['full_name'] = ['nullable', 'string', 'max:255'];
        } else {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'];
        }

        // Name fields (always present, used to build full_name for parents)
        $rules['first_name'] = ['required', 'string', 'max:255'];
        $rules['last_name'] = ['required', 'string', 'max:255'];
        $rules['middle_initial'] = ['nullable', 'string', 'max:5'];

        return $rules;
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'password.required' => 'Password is required for staff and admin accounts.',
            'email.unique' => 'This email address is already registered.',
            'phone_number.regex' => 'Phone number may only contain digits, +, -, spaces, and parentheses.',
        ];
    }
}
