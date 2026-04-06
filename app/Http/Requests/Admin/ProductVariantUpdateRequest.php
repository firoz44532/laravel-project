<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariantUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $variantId = $this->route('variant') ?? $this->route('variantId');

        return [
            'name' => 'required|string|max:255',
            'variant_type' => 'required|in:simple,color,size,material,style',
            'sku' => 'required|string|max:255|unique:product_variants,sku,' . $variantId,
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'track_stock' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'attributes' => 'nullable|array',
        ];
    }
}
