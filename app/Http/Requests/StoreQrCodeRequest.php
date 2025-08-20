<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQrCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && $user->employee && in_array($user->employee->role, ['admin', 'hrd']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'office_location_id' => 'required|exists:office_locations,id',
            'expires_at' => 'required|date|after:now',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'office_location_id.required' => 'Office location is required.',
            'office_location_id.exists' => 'Selected office location is invalid.',
            'expires_at.required' => 'Expiration date is required.',
            'expires_at.date' => 'Please provide a valid expiration date.',
            'expires_at.after' => 'Expiration date must be in the future.',
        ];
    }
}