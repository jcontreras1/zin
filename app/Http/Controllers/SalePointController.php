<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SalePoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SalePointController extends Controller
{
    public function index(): Response
    {
        $salePoints = SalePoint::withCount('categories')
            ->with('categories:id,name')
            ->orderBy('name')
            ->get()
            ->map(fn (SalePoint $salePoint) => [
                'id' => $salePoint->id,
                'name' => $salePoint->name,
                'logo' => $salePoint->logo ? Storage::url($salePoint->logo) : null,
                'phone' => $salePoint->phone,
                'instagram' => $salePoint->instagram,
                'facebook' => $salePoint->facebook,
                'webpage' => $salePoint->webpage,
                'address' => $salePoint->address,
                'locality' => $salePoint->locality,
                'province' => $salePoint->province,
                'categories_count' => $salePoint->categories_count,
                'categories' => $salePoint->categories->map(fn ($cat) => [
                    'id' => $cat->id,
                    'name' => $cat->name,
                ]),
                'created_at' => $salePoint->created_at?->toDateTimeString(),
            ]);

        $categories = Category::orderBy('name')->get()->map(fn (Category $category) => [
            'id' => $category->id,
            'name' => $category->name,
        ]);

        return Inertia::render('SalePoints/Index', [
            'salePoints' => $salePoints,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'phone' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'webpage' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'locality' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ]);

        $salePoint = SalePoint::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'instagram' => $validated['instagram'] ?? null,
            'facebook' => $validated['facebook'] ?? null,
            'webpage' => $validated['webpage'] ?? null,
            'address' => $validated['address'] ?? null,
            'locality' => $validated['locality'] ?? null,
            'province' => $validated['province'] ?? null,
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('sale-points', 'public');
            $salePoint->update(['logo' => $path]);
        }

        if (! empty($validated['category_ids'])) {
            $salePoint->categories()->sync($validated['category_ids']);
        }

        return redirect()->route('sale-points.index')->with('success', 'Punto de venta creado correctamente.');
    }

    public function update(Request $request, SalePoint $salePoint): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'phone' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'webpage' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'locality' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'remove_logo' => ['nullable', 'boolean'],
        ]);

        $salePoint->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'instagram' => $validated['instagram'] ?? null,
            'facebook' => $validated['facebook'] ?? null,
            'webpage' => $validated['webpage'] ?? null,
            'address' => $validated['address'] ?? null,
            'locality' => $validated['locality'] ?? null,
            'province' => $validated['province'] ?? null,
        ]);

        if ($request->boolean('remove_logo') && $salePoint->logo) {
            Storage::disk('public')->delete($salePoint->logo);
            $salePoint->update(['logo' => null]);
        }

        if ($request->hasFile('logo')) {
            if ($salePoint->logo) {
                Storage::disk('public')->delete($salePoint->logo);
            }
            $path = $request->file('logo')->store('sale-points', 'public');
            $salePoint->update(['logo' => $path]);
        }

        $salePoint->categories()->sync($validated['category_ids'] ?? []);

        return redirect()->route('sale-points.index')->with('success', 'Punto de venta actualizado correctamente.');
    }

    public function destroy(SalePoint $salePoint): RedirectResponse
    {
        if ($salePoint->logo) {
            Storage::disk('public')->delete($salePoint->logo);
        }

        $salePoint->delete();

        return redirect()->route('sale-points.index')->with('success', 'Punto de venta eliminado correctamente.');
    }
}
