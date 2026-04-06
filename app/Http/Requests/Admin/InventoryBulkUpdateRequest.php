<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InventoryBulkUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'updates' => 'required|array',
            'updates.*.product_id' => 'required|exists:products,id',
            'updates.*.quantity' => 'required|integer|min:0',
            'updates.*.action' => 'required|in:stock_in,stock_out,adjustment',
            'updates.*.reason' => 'required|string|max:255',
        ];
    }
}
