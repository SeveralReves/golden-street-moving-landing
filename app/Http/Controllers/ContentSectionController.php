<?php

namespace App\Http\Controllers;

use App\Models\ContentSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentSectionController extends Controller
{
    public function index()
    {
        $sections = ContentSection::whereIn('key', ContentSection::KEYS)->get()->keyBy('key');

        return view('dashboard-content', compact('sections'));
    }

    public function update(Request $request, string $key)
    {
        abort_unless(in_array($key, ContentSection::KEYS, true), 404);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'items' => 'nullable|json',
        ]);

        $section = ContentSection::firstOrCreate(['key' => $key]);
        $section->title = $validated['title'] ?? null;
        $section->description = $validated['description'] ?? null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store("landing/{$key}", 'public');
            $section->image = Storage::disk('public')->url($path);
        } elseif ($request->boolean('remove_image')) {
            $section->image = null;
        }

        if (array_key_exists('items', $validated) && $validated['items'] !== null) {
            $items = json_decode($validated['items'], true) ?? [];

            foreach ($items as $index => &$item) {
                if ($request->hasFile("item_image_{$index}")) {
                    $path = $request->file("item_image_{$index}")->store("landing/{$key}", 'public');
                    $item['image'] = Storage::disk('public')->url($path);
                }
            }
            unset($item);

            $section->items = $items;
        }

        $section->save();

        return response()->json(['section' => $section]);
    }
}
