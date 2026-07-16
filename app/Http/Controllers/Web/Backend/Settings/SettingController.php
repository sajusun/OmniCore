<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('backend.layouts.settings.general_settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:png,ico|max:1024',
        ]);

        $setting = Setting::first() ?? new Setting();

        $data = $request->except(['_token', '_method', 'logo', 'favicon']);

        if ($request->hasFile('logo')) {
            if ($setting->logo) {
                Helper::fileDelete(public_path($setting->logo));
            }
            $data['logo'] = Helper::fileUpload($request->file('logo'), 'settings', 'logo');
        }

        if ($request->hasFile('favicon')) {
            if ($setting->favicon) {
                Helper::fileDelete(public_path($setting->favicon));
            }
            $data['favicon'] = Helper::fileUpload($request->file('favicon'), 'settings', 'favicon');
        }

        $setting->fill($data)->save();

        return redirect()->back()->with('t-success', 'Settings updated successfully');
    }
}
