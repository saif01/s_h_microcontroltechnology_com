<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $query = Setting::query();

        if ($request->has('group')) {
            $query->where('group', $request->group);
        }

        $settings = $query->orderBy('group')->orderBy('key')->get();

        // Group settings by group
        $grouped = $settings->groupBy('group')->map(function ($items) {
            return $items->mapWithKeys(function ($item) {
                return [$item->key => [
                    'id' => $item->id,
                    'key' => $item->key,
                    'value' => $item->value ?? '',
                    'type' => $item->type ?? 'text',
                    'group' => $item->group ?? 'general',
                    'description' => $item->description,
                ]];
            });
        });

        return response()->json($grouped);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($validated['settings'] as $key => $settingData) {
            if (is_array($settingData) && (array_key_exists('value', $settingData) || isset($settingData['type']))) {
                $value = $settingData['value'] ?? null;
                $type = $settingData['type'] ?? 'text';
                $group = $settingData['group'] ?? 'general';
            } else {
                // Backward compatibility: if just a value is passed
                $value = $settingData;
                $type = 'text';
                $group = 'general';
            }

            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => $type,
                    'group' => $group,
                ]
            );
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }
}
