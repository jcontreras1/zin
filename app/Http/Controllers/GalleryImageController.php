<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class GalleryImageController extends Controller
{
    public function index(): Response
    {
        $images = GalleryImage::orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (GalleryImage $image) => [
                'id' => $image->id,
                'title' => $image->title,
                'caption' => $image->caption,
                'sort_order' => $image->sort_order,
                'is_active' => $image->is_active,
                'url' => Storage::url($image->path),
                'created_at' => $image->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('GalleryImages/Index', [
            'images' => $images,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['required', 'image', 'max:6144'],
        ]);

        $path = $request->file('image')->store('gallery', 'public');

        GalleryImage::create([
            'title' => $validated['title'] ?? null,
            'caption' => $validated['caption'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'path' => $path,
        ]);

        return redirect()->route('images.index')->with('success', 'Imagen creada correctamente.');
    }

    public function update(Request $request, GalleryImage $image): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:6144'],
        ]);

        if ($request->hasFile('image')) {
            if ($image->path && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            $image->path = $request->file('image')->store('gallery', 'public');
        }

        $image->fill([
            'title' => $validated['title'] ?? null,
            'caption' => $validated['caption'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        $image->save();

        return redirect()->route('images.index')->with('success', 'Imagen actualizada correctamente.');
    }

    public function destroy(GalleryImage $image): RedirectResponse
    {
        $image->delete();

        return redirect()->route('images.index')->with('success', 'Imagen eliminada correctamente.');
    }
}
