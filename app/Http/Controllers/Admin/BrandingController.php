<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BrandingController extends Controller
{
    public function index()
    {
        $settings = [
            'site_name' => SettingsService::get('site_name', 'E-Commerce Store'),
            'site_tagline' => SettingsService::get('site_tagline'),
            'site_description' => SettingsService::get('site_description'),
            'site_logo' => SettingsService::get('site_logo'),
            'contact_email' => SettingsService::get('contact_email'),
            'contact_phone' => SettingsService::get('contact_phone'),
        ];
        
        return view('admin.branding', compact('settings'));
    }

    public function uploadLogo(Request $request)
    {
        // Handle logo removal
        if ($request->has('remove_logo')) {
            SettingsService::set('site_logo', '');
            return redirect()
                ->route('admin.branding.index')
                ->with('success', 'Logo removed successfully.');
        }

        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.branding.index')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                
                // Create directory if it doesn't exist
                $directory = 'logos';
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }
                
                // Generate unique filename
                $filename = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
                
                // Store the file
                $path = $logo->storeAs($directory, $filename, 'public');
                
                // Get the full URL
                $logoUrl = Storage::disk('public')->url($path);
                
                // Update setting
                SettingsService::set('site_logo', $logoUrl);
                
                return redirect()
                    ->route('admin.branding.index')
                    ->with('success', 'Logo uploaded successfully.');
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.branding.index')
                ->with('error', 'Error uploading logo: ' . $e->getMessage());
        }

        return redirect()
            ->route('admin.branding.index')
            ->with('error', 'No logo file provided.');
    }

    public function updateBranding(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.branding.index')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update all branding settings
            SettingsService::set('site_name', $request->site_name);
            SettingsService::set('site_tagline', $request->site_tagline);
            SettingsService::set('site_description', $request->site_description);
            SettingsService::set('contact_email', $request->contact_email);
            SettingsService::set('contact_phone', $request->contact_phone);

            return redirect()
                ->route('admin.branding.index')
                ->with('success', 'Branding settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.branding.index')
                ->with('error', 'Error updating branding: ' . $e->getMessage());
        }
    }
}
