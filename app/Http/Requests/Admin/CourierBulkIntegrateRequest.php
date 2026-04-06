<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CourierBulkIntegrateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'courier_type' => 'required|in:steadfast,pathao,ecourier,redx,paperfly,sundarban,saparibahan,janani',
        ];
    }
}
