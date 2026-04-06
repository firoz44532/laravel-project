<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SuspiciousCustomerStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:suspicious_customers,email',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'reason' => 'required|string|max:500',
            'risk_score' => 'nullable|numeric|min:0|max:100',
            'risk_factors' => 'nullable|array',
            'detection_method' => 'nullable|string|max:50',
        ];
    }
}
