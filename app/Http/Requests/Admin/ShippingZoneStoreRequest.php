<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ShippingZoneStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code' => 'required|string|unique:shipping_zones,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_cost' => 'required|numeric|min:0',
            'express_cost' => 'required|numeric|min:0',
            'delivery_days' => 'nullable|string|max:50',
            'express_days' => 'nullable|string|max:50',
            'cities' => 'nullable|string',
            'areas' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'methods' => 'nullable|array',
            'method_costs' => 'nullable|array',
        ];
    }
}
