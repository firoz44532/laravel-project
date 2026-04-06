<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TrackingIndexRequest extends FormRequest
{
    public function authorize()
    {
        // Authorization handled by admin middleware
        return true;
    }

    public function rules()
    {
        return [
            'search_method' => 'nullable|in:order_number,customer_details',
            'order_number' => 'nullable|string|max:100',
            'customer_name' => 'nullable|string|max:255',
            'phone' => ['nullable','string','max:30','regex:/^[0-9\s+()\-]{3,30}$/'],
            'email' => 'nullable|email|max:255',
        ];
    }
}
