<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SuspiciousCustomerBulkActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:suspicious_customers,id',
            'action' => 'required|in:ban,unban',
            'reason' => 'required_if:action,ban|string|max:500',
            'banned_until' => 'nullable|date|after:now',
        ];
    }
}
