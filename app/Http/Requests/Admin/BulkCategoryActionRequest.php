<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BulkCategoryActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:activate,deactivate,delete,update_parent',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'parent_id' => 'nullable|required_if:action,update_parent|exists:categories,id',
        ];
    }
}
