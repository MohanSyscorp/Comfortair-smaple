<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use App\Models\Enquiry;
use App\Models\GalleryItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main administrative dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();

        $stats = [
            'products_count' => Product::count(),
            'published_products' => Product::published()->count(),
            'gallery_count' => GalleryItem::count(),
            'published_gallery' => GalleryItem::published()->count(),
            'total_enquiries' => 0,
            'new_enquiries_today' => 0,
            'recent_enquiries' => collect(),
            'enquiries_by_status' => [],
            'total_users' => 0,
        ];

        // Only query enquiries if user has access to enquiries module
        if ($user->hasModuleAccess('enquiries')) {
            $enquiryCounts = Enquiry::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $stats['total_enquiries'] = array_sum($enquiryCounts);
            $stats['new_enquiries_today'] = Enquiry::where('status', 'New')
                ->whereDate('created_at', today())
                ->count();
            $stats['recent_enquiries'] = Enquiry::latest()->take(5)->get();

            foreach (Enquiry::STATUSES as $status) {
                $stats['enquiries_by_status'][$status] = $enquiryCounts[$status] ?? 0;
            }
        }

        // Only query users count if Super Admin
        if ($user->isSuperAdmin()) {
            $stats['total_users'] = User::count();
        }

        $contact = ContactSetting::first();

        return view('admin.dashboard', compact('stats', 'contact'));
    }
}
