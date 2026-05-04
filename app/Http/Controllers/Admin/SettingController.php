<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'notification_email' => Setting::get('notification_email', 'info@southtravels.com'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'notification_email' => 'required|email|max:255',
        ]);

        Setting::set('notification_email', $validated['notification_email']);

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
    }
}
