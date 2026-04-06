<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariantBulkUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'variant_ids' => 'required|array',
            'variant_ids.*' => 'exists:product_variants,id',
            'action' => 'required|in:activate,deactivate,delete,update_stock,update_price',
            'stock_quantity' => 'required_if:action,update_stock|integer|min:0',
            'price' => 'required_if:action,update_price|numeric|min:0',
        ];
    }
}
