<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ShippingSettingsUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'default_shipping_cost' => 'required|numeric|min:0',
            'free_shipping_threshold' => 'required|numeric|min:0',
            'tax_enabled' => 'boolean',
            'vat_rate' => 'required|numeric|min:0|max:100',
            'shipping_taxable' => 'boolean',
            'tax_inclusive' => 'boolean',
            'weight_based_enabled' => 'boolean',
            'order_value_based_enabled' => 'boolean',
        ];
    }
}
