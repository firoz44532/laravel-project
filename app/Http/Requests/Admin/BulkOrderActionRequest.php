<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BulkOrderActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:update_status,mark_paid,mark_shipped,mark_delivered,mark_cancelled,mark_refunded,delete,export',
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required_if:action,update_status|in:pending,paid,processing,shipped,delivered,cancelled,refunded',
        ];
    }
}
