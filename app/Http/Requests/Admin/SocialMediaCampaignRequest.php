<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SocialMediaCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'platform' => 'required|in:facebook,twitter,instagram,linkedin,youtube,tiktok',
            'type' => 'required|in:post,video,story,ad',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:10240',
            'scheduled_at' => 'nullable|date',
            'hashtags' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
        ];
    }
}
