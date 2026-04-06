<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SocialAdCampaignUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'budget' => 'sometimes|numeric|min:1',
            'status' => 'sometimes|in:active,paused,deleted',
            'bid_strategy' => 'sometimes|string',
        ];
    }
}
