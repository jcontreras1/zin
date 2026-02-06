<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalePointController;
use App\Models\Product;
use App\Models\SalePoint;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

Route::get('/', function () {
    $products = Product::with('photos')
        ->orderByDesc('created_at')
        ->take(4)
        ->get()
        ->map(fn ($product) => [
            'id' => $product->id,
            'title' => $product->title,
            'description_one' => $product->description_one,
            'description_two' => $product->description_two,
            'price' => $product->price,
            'price_formatted' => number_format((float) $product->price, 2, ',', '.'),
            'photos' => $product->photos->map(fn ($photo) => [
                'id' => $photo->id,
                'url' => Storage::url($photo->path),
            ])->values(),
        ])->values();

    $salePoints = SalePoint::with('categories:id,name')
        ->orderBy('name')
        ->get()
        ->map(fn ($salePoint) => [
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
            'categories' => $salePoint->categories->pluck('name')->toArray(),
        ]);

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'products' => $products,
        'salePoints' => $salePoints,
    ]);
});

Route::get('/shop', function () {
    $products = Product::with(['photos', 'category'])
        ->orderByDesc('created_at')
        ->paginate(10)
        ->through(fn ($product) => [
            'id' => $product->id,
            'title' => $product->title,
            'description_one' => $product->description_one,
            'description_two' => $product->description_two,
            'price' => $product->price,
            'price_formatted' => number_format((float) $product->price, 2, ',', '.'),
            'category_name' => $product->category?->name,
            'created_at' => $product->created_at?->toDateTimeString(),
            'photos' => $product->photos->map(fn ($photo) => [
                'id' => $photo->id,
                'url' => Storage::url($photo->path),
            ])->values(),
        ]);

    return Inertia::render('Shop/Index', [
        'products' => $products,
    ]);
})->name('shop');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('sale-points', SalePointController::class)->only(['index', 'store', 'update', 'destroy']);
});

require __DIR__.'/auth.php';
