<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::where('seller_id', Auth::id())->first();
        $title = 'Settings';
        $breadcrumbs = [
            'Home' => route('dashboard'),
            'Settings' => route('settings.index'),
        ];

        $settingIsNull = is_null($setting);
        $action = $settingIsNull ? route('settings.store') : route('settings.update', $setting);
        $isLogoExists = !$settingIsNull && 
                        isset($setting->custom_logo) && 
                        Storage::disk('public')->exists($setting->custom_logo);

        $isIconExists = !$settingIsNull && 
                        isset($setting->custom_icon) && 
                        Storage::disk('public')->exists($setting->custom_icon);

        return view('pages.settings.index', compact('setting', 'title', 'breadcrumbs', 'action', 'settingIsNull', 'isLogoExists', 'isIconExists'));

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'custom_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2040',
            'custom_icon' => 'nullable|image|mimes:png,jpg,jpeg|max:2040',
        ], [
            'custom_logo.mimes' => 'Image must be in a format of jpeg, png, or jpg',
            'custom_icon.mimes' => 'Image must be in a format of jpeg, png, or jpg',
        ]);

        $setting = new Setting();

        $setting->seller_id = Auth::id();

        if ($request->hasFile('custom_logo')) {
            $setting->custom_logo = $request->file('custom_logo')->store('logos', 'public');
            
        }

        if ($request->hasFile('custom_icon')) {
            $setting->custom_icon = $request->file('custom_icon')->store('icons', 'public');
        }

        $setting->save();

        return back()->with('success', 'Setting save successfully');

    }

    public function update(Request $request, Setting $setting)
    {

        $validated = $request->validate([
            'custom_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2040',
            'custom_icon' => 'nullable|image|mimes:png,jpg,jpeg|max:2040',
        ], [
            'custom_logo.mimes' => 'Image must be in a format of jpeg, png, or jpg',
            'custom_icon.mimes' => 'Image must be in a format of jpeg, png, or jpg',
        ]);

        if($request->has('deleteLogo')) {
            Storage::delete($setting->custom_logo);
            $setting->custom_logo = null;
        }

        if ($request->has('deleteIcon')) {
            Storage::delete($setting->custom_icon);
            $setting->custom_icon = null;
        }

        if ($request->hasFile('custom_logo')) {
            $setting->custom_logo = $request->file('custom_logo')->store('logos', 'public');
            
        }

        if ($request->hasFile('custom_icon')) {
            $setting->custom_icon = $request->file('custom_icon')->store('icons', 'public');
        }

        $setting->save();

        return back()->with('success', 'Setting updated successfully');

    }
}
