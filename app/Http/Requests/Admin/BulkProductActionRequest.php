<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BulkProductActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:activate,deactivate,delete,update_price,update_stock,update_category,update_brand',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'price_type' => 'required_if:action,update_price|in:fixed,percentage',
            'price_value' => 'required_if:action,update_price|numeric|min:0',
            'stock_action' => 'required_if:action,update_stock|in:set,add,subtract',
            'stock_value' => 'required_if:action,update_stock|integer|min:0',
            'category_id' => 'required_if:action,update_category|exists:categories,id',
            'brand_id' => 'required_if:action,update_brand|exists:brands,id',
        ];
    }
}
