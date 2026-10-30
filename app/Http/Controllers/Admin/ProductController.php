<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $query = Product::query()->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $products = $query->paginate(12)->withQueryString();

        $statusCounts = Product::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $metrics = [
            'total' => array_sum($statusCounts),
            'published' => $statusCounts['published'] ?? 0,
            'unpublished' => $statusCounts['unpublished'] ?? 0,
        ];

        return view('admin.products.index', compact('products', 'metrics'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:100'],
            'specifications' => ['required', 'string'],
            'features' => ['required', 'string'],
            'image' => ['required', 'image', 'mimes:jpeg,png,webp,jpg', 'max:5120'],
            'status' => ['required', Rule::in(['published', 'unpublished'])],
        ]);

        $imagePath = $request->file('image')->store('products', 'public');

        Product::create([
            'name' => $validated['name'],
            'model' => $validated['model'],
            'specifications' => $validated['specifications'],
            'features' => $validated['features'],
            'image' => $imagePath,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', "Carrier product '{$validated['name']}' has been added to the catalogue.");
    }

    /**
     * Display the specified product dossier.
     */
    public function show(Product $product): View
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:100'],
            'specifications' => ['required', 'string'],
            'features' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:5120'],
            'status' => ['required', Rule::in(['published', 'unpublished'])],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'model' => $validated['model'],
            'specifications' => $validated['specifications'],
            'features' => $validated['features'],
            'status' => $validated['status'],
        ];

        if ($request->hasFile('image')) {
            // Delete old file if local
            if (!str_starts_with($product->image, 'http') && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $updateData['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($updateData);

        return redirect()->route('admin.products.index')
            ->with('success', "Product '{$product->name}' was updated successfully.");
    }

    /**
     * Toggle published / unpublished status.
     */
    public function togglePublish(Product $product): RedirectResponse
    {
        $newStatus = $product->status === 'published' ? 'unpublished' : 'published';
        $product->update(['status' => $newStatus]);

        $action = $newStatus === 'published' ? 'published to website' : 'unpublished from website';
        return back()->with('success', "Product '{$product->name}' is now {$action}.");
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;
        

        if (!empty($product->image) && !str_starts_with($product->image, 'http') && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Product '{$name}' has been deleted.");
    }
}
