<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Display a listing of gallery portfolio items.
     */
    public function index(Request $request): View
    {
        $query = GalleryItem::query()->latest();

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where('caption', 'like', "%{$search}%");
        }

        $items = $query->paginate(16)->withQueryString();
        $categories = GalleryItem::CATEGORIES;

        $categoryCounts = GalleryItem::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
        $totalCount = GalleryItem::count();

        return view('admin.gallery.index', compact('items', 'categories', 'categoryCounts', 'totalCount'));
    }

    /**
     * Show the form for uploading a new gallery photo.
     */
    public function create(): View
    {
        $categories = GalleryItem::CATEGORIES;
        return view('admin.gallery.create', compact('categories'));
    }

    /**
     * Store a newly created gallery photo in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,webp,jpg', 'max:8192'],
            'caption' => ['nullable', 'string', 'max:255'],
            'category' => ['required', Rule::in(GalleryItem::CATEGORIES)],
            'status' => ['required', Rule::in(['published', 'unpublished'])],
        ]);

        $imagePath = $request->file('image')->store('gallery', 'public');

        GalleryItem::create([
            'image' => $imagePath,
            'caption' => $validated['caption'],
            'category' => $validated['category'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Portfolio photo has been uploaded successfully.');
    }

    /**
     * Show the form for editing the specified gallery photo.
     */
    public function edit(GalleryItem $gallery): View
    {
        $categories = GalleryItem::CATEGORIES;
        return view('admin.gallery.edit', compact('gallery', 'categories'));
    }

    /**
     * Update the specified gallery photo in storage.
     */
    public function update(Request $request, GalleryItem $gallery): RedirectResponse
    {
        $validated = $request->validate([
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:8192'],
            'caption' => ['nullable', 'string', 'max:255'],
            'category' => ['required', Rule::in(GalleryItem::CATEGORIES)],
            'status' => ['required', Rule::in(['published', 'unpublished'])],
        ]);

        $updateData = [
            'caption' => $validated['caption'],
            'category' => $validated['category'],
            'status' => $validated['status'],
        ];

        if ($request->hasFile('image')) {
            if (!str_starts_with($gallery->image, 'http') && Storage::disk('public')->exists($gallery->image)) {
                Storage::disk('public')->delete($gallery->image);
            }
            $updateData['image'] = $request->file('image')->store('gallery', 'public');
        }

        $gallery->update($updateData);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery item has been updated successfully.');
    }

    /**
     * Toggle published / unpublished status.
     */
    public function togglePublish(GalleryItem $gallery): RedirectResponse
    {
        $newStatus = $gallery->status === 'published' ? 'unpublished' : 'published';
        $gallery->update(['status' => $newStatus]);

        $action = $newStatus === 'published' ? 'published to website' : 'unpublished from website';
        return back()->with('success', "Gallery image is now {$action}.");
    }

    /**
     * Remove the specified gallery item from storage.
     */
    public function destroy(GalleryItem $gallery): RedirectResponse
    {
        if (!empty($gallery->image) && !str_starts_with($gallery->image, 'http') && Storage::disk('public')->exists($gallery->image)) {
            Storage::disk('public')->delete($gallery->image);
        }

        $gallery->delete();

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery photo has been deleted.');
    }
}
