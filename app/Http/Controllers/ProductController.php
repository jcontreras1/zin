<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        $products = Product::with(['photos', 'category'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'title' => $product->title,
                'description_one' => $product->description_one,
                'description_two' => $product->description_two,
                'price' => $product->price,
                'price_formatted' => number_format((float) $product->price, 2, ',', '.'),
                'category_id' => $product->category_id,
                'category_name' => $product->category?->name,
                'created_at' => $product->created_at?->toDateTimeString(),
                'photos' => $product->photos->map(fn (ProductPhoto $photo) => [
                    'id' => $photo->id,
                    'url' => Storage::url($photo->path),
                ]),
            ]);

        $categories = Category::orderBy('name')->get()->map(fn (Category $category) => [
            'id' => $category->id,
            'name' => $category->name,
        ]);

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description_one' => ['required', 'string'],
            'description_two' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['file', 'image', 'max:6144'],
        ]);

        DB::transaction(function () use ($request, $validated): void {
            $product = Product::create([
                'category_id' => $validated['category_id'] ?? null,
                'title' => $validated['title'],
                'description_one' => $validated['description_one'],
                'description_two' => $validated['description_two'] ?? null,
                'price' => $validated['price'],
            ]);

            foreach ($request->file('photos', []) as $index => $file) {
                $path = $file->store('products', 'public');
                $product->photos()->create([
                    'path' => $path,
                    'sort_order' => $index,
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description_one' => ['required', 'string'],
            'description_two' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'photos' => ['sometimes', 'array'],
            'photos.*' => ['file', 'image', 'max:6144'],
            'removed_photos' => ['sometimes', 'array'],
            'removed_photos.*' => ['integer', 'exists:product_photos,id'],
        ]);

        DB::transaction(function () use ($request, $product, $validated): void {
            $product->update([
                'category_id' => $validated['category_id'] ?? null,
                'title' => $validated['title'],
                'description_one' => $validated['description_one'],
                'description_two' => $validated['description_two'] ?? null,
                'price' => $validated['price'],
            ]);

            if (! empty($validated['removed_photos'])) {
                ProductPhoto::where('product_id', $product->id)
                    ->whereIn('id', $validated['removed_photos'])
                    ->get()
                    ->each->delete();
            }

            $existingMax = $product->photos()->max('sort_order') ?? 0;

            foreach ($request->file('photos', []) as $offset => $file) {
                $path = $file->store('products', 'public');
                $product->photos()->create([
                    'path' => $path,
                    'sort_order' => $existingMax + $offset + 1,
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado.');
    }
}
