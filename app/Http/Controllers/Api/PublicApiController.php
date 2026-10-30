<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\EnquiryReceivedMail;
use App\Models\ContactSetting;
use App\Models\Enquiry;
use App\Models\GalleryItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PublicApiController extends Controller
{
    /**
     * Get synchronized business contact reachability details.
     * Consumed by Next.js Header, Footer, and Contact Us page.
     */
    public function getContact(): JsonResponse
    {
        $contact = ContactSetting::first();

        if (!$contact) {
            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'phone' => $contact->phone,
                'whatsapp' => $contact->whatsapp,
                'email' => $contact->email,
                'address' => $contact->address,
                'working_hours' => $contact->working_hours,
                'google_maps_link' => $contact->google_maps_link,
                'facebook_url' => $contact->facebook_url,
                'instagram_url' => $contact->instagram_url,
                'twitter_url' => $contact->twitter_url,
                'youtube_url' => $contact->youtube_url,
            ],
        ]);
    }

    /**
     * Get published Carrier AC products for catalogue cards & enquiry dropdown.
     * Consumed by Next.js /products page and enquiry form.
     */
    public function getProducts(): JsonResponse
    {
        $products = Product::published()
            ->latest()
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'model' => $product->model,
                    'specifications' => $product->specifications,
                    'features' => $product->features,
                    'image_url' => $product->image_url,
                ];
            });

        return response()->json([
            'success' => true,
            'count' => $products->count(),
            'data' => $products,
        ]);
    }

    /**
     * Get published portfolio gallery photos.
     * Consumed by Next.js /gallery page with optional ?category= filter.
     */
    public function getGallery(Request $request): JsonResponse
    {
        $query = GalleryItem::published()->latest();

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        $items = $query->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'image_url' => $item->image_url,
                'caption' => $item->caption,
                'category' => $item->category,
            ];
        });

        return response()->json([
            'success' => true,
            'categories' => GalleryItem::CATEGORIES,
            'count' => $items->count(),
            'data' => $items,
        ]);
    }

    /**
     * Submit an enquiry lead through the shared 7-field form.
     * Consumed by Next.js enquiry modals, product cards, and contact form.
     */
    public function submitEnquiry(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.]+$/'],
            'mobile_number' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'service_required' => ['required', 'string', 'max:100'],
            'product_interested_in' => ['nullable', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:500'],
        ], [
            'name.regex' => 'Please enter a valid customer full name (letters and spaces only).',
            'mobile_number.regex' => 'Please enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.',
            'message.max' => 'Your message cannot exceed 500 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please correct the validation errors and try again.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $enquiry = Enquiry::create([
            'name' => trim($validated['name']),
            'mobile_number' => trim($validated['mobile_number']),
            'email' => !empty($validated['email']) ? trim($validated['email']) : null,
            'service_required' => $validated['service_required'],
            'product_interested_in' => !empty($validated['product_interested_in']) ? $validated['product_interested_in'] : null,
            'location' => $validated['location'],
            'message' => !empty($validated['message']) ? trim($validated['message']) : null,
            'status' => 'New',
        ]);

        // Send email alert to the business email configured in Contact Management
        try {
            $contact = ContactSetting::first();
            $recipientEmail = $contact?->email ?: config('mail.from.address');

            if ($recipientEmail) {
                Mail::to($recipientEmail)->send(new EnquiryReceivedMail($enquiry));
            }
        } catch (\Throwable $e) {
            Log::error("Failed to dispatch enquiry notification email: {$e->getMessage()}");
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you! Your enquiry has been received. Our team will contact you shortly.',
            'enquiry_id' => $enquiry->id,
        ], 201);
    }
}
