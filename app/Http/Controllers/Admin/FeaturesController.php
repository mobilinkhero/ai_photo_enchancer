<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppFeature;
use Illuminate\Http\Request;

class FeaturesController extends Controller
{
    public function index()
    {
        $features = AppFeature::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.features.index', compact('features'));
    }

    public function update(Request $request, AppFeature $feature)
    {
        $data = $request->validate([
            'title' => 'required|string|max:80',
            'description' => 'nullable|string|max:200',
            'icon' => 'nullable|string|max:80',
            'color' => 'nullable|string|max:20',
            'is_premium' => 'nullable|boolean',
            'enabled' => 'nullable|boolean',
            'coins' => 'required|integer|min:0|max:100',
            'sort_order' => 'required|integer|min:0',
            'before_url' => 'nullable|url|max:500',
            'after_url' => 'nullable|url|max:500',
            'benefits' => 'nullable|string',
        ]);

        // Parse newline-separated benefits into array
        if (isset($data['benefits'])) {
            $data['benefits'] = array_values(array_filter(
                array_map('trim', explode("\n", $data['benefits']))
            ));
        }

        $data['is_premium'] = $request->boolean('is_premium');
        $data['enabled'] = $request->boolean('enabled');

        $feature->update($data);

        return redirect()->route('admin.features.index')
            ->with('success', "Feature \"{$feature->title}\" updated.");
    }

    public function toggleEnabled(AppFeature $feature)
    {
        $feature->update(['enabled' => !$feature->enabled]);
        return back()->with('success', "Feature " . ($feature->enabled ? 'enabled' : 'disabled') . ".");
    }
}
