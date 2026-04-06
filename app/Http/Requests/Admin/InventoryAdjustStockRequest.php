<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InventoryAdjustStockRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'quantity' => 'required|integer|min:1',
            'action' => 'required|in:stock_in,stock_out,adjustment',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
