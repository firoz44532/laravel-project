<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ShippingMethodStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code' => 'required|string|unique:shipping_methods,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_days' => 'nullable|string|max:50',
            'base_cost' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'tracking_available' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];
    }
}
