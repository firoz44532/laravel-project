<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SocialMediaSchedulePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform' => 'required|in:facebook,twitter,instagram,linkedin,youtube,tiktok',
            'content' => 'required|string',
            'scheduled_at' => 'required|date|after:now',
            'media' => 'nullable|array',
            'hashtags' => 'nullable|string',
        ];
    }
}
