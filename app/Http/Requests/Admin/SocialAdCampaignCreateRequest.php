<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SocialAdCampaignCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'platform' => 'required|in:facebook,google,twitter,linkedin,tiktok',
            'objective' => 'required|string',
            'budget' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'target_audience' => 'required|array',
            'creative_assets' => 'required|array',
            'bid_strategy' => 'required|string',
            'status' => 'required|in:active,paused',
        ];
    }
}
