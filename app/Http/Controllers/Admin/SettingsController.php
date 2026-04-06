<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function index()
    {
        $groups = Setting::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        $settings = Setting::orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.settings.index', compact('settings', 'groups'));
    }

    public function create()
    {
        $groups = Setting::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        $types = ['text', 'number', 'boolean', 'json', 'image'];

        return view('admin.settings.create', compact('groups', 'types'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:settings,key',
            'title' => 'required|string|max:255',
            'type' => 'required|in:text,number,boolean,json,image',
            'group' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'sort_order' => 'integer|min:0',
            'value' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $setting = Setting::create($validator->validated());

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Setting created successfully.');
    }

    public function show(Setting $setting)
    {
        return view('admin.settings.show', compact('setting'));
    }

    public function edit(Setting $setting)
    {
        $groups = Setting::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        $types = ['text', 'number', 'boolean', 'json', 'image'];

        return view('admin.settings.edit', compact('setting', 'groups', 'types'));
    }

    public function update(Request $request, Setting $setting)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:settings,key,' . $setting->id,
            'title' => 'required|string|max:255',
            'type' => 'required|in:text,number,boolean,json,image',
            'group' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'sort_order' => 'integer|min:0',
            'value' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $setting->update($validator->validated());

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Setting updated successfully.');
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Setting deleted successfully.');
    }

    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.id' => 'required|exists:settings,id',
            'settings.*.value' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        foreach ($request->settings as $settingData) {
            $setting = Setting::find($settingData['id']);
            if ($setting) {
                $setting->setValue($settingData['value'] ?? '');
                $setting->save();
            }
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    public function getPublicSettings()
    {
        $settings = Setting::getPublicSettings();
        
        return response()->json([
            'settings' => $settings->map(function ($setting) {
                return [
                    'key' => $setting->key,
                    'value' => $setting->getValue(),
                    'type' => $setting->type,
                ];
            })
        ]);
    }
}
