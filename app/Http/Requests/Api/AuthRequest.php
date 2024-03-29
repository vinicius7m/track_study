<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $commonRules = [
            'email' => [
                'required',
                'email',
                'max:255',
            ],
            'password' => [
                'required',
                'max:255',
            ],
            'device_name' => [
                'required',
                'max:255',
            ],
        ];

        if ($this->isMethod('post') && $this->route()->getName() === 'register') {
            return array_merge($commonRules, [
                'name' => [
                    'required',
                    'max:255',
                ],
                'password_confirmation' => [
                    'required',
                    'same:password',
                ],
            ]);
        }

        return $commonRules;
    }
}
