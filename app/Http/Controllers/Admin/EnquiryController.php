<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    /**
     * Display a listing of enquiries.
     */
    public function index(Request $request): View
    {
        $query = Enquiry::query()->latest();

        // Search by name, mobile, or email
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by service required
        if ($service = $request->input('service')) {
            $query->where('service_required', $service);
        }

        $enquiries = $query->paginate(15)->withQueryString();

        // Optimized single-query status counts for triage badges
        $groupedCounts = Enquiry::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusCounts = [
            'All' => array_sum($groupedCounts),
        ];
        foreach (Enquiry::STATUSES as $st) {
            $statusCounts[$st] = $groupedCounts[$st] ?? 0;
        }

        $services = Enquiry::SERVICES;
        $statuses = Enquiry::STATUSES;

        return view('admin.enquiries.index', compact('enquiries', 'statusCounts', 'services', 'statuses'));
    }

    /**
     * Display the specified enquiry (for AJAX detail modal).
     */
    public function show(Enquiry $enquiry): JsonResponse
    {
        return response()->json([
            'id' => $enquiry->id,
            'name' => $enquiry->name,
            'mobile_number' => $enquiry->mobile_number,
            'email' => $enquiry->email,
            'service_required' => $enquiry->service_required,
            'product_interested_in' => $enquiry->product_interested_in,
            'location' => $enquiry->location,
            'message' => $enquiry->message,
            'status' => $enquiry->status,
            'created_at' => $enquiry->created_at->format('d M Y, h:i A'),
        ]);
    }

    /**
     * Update enquiry progression status.
     */
    public function updateStatus(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Enquiry::STATUSES)],
        ]);

        $oldStatus = $enquiry->status;
        $enquiry->update(['status' => $validated['status']]);

        return back()->with('success', "Lead from '{$enquiry->name}' moved from {$oldStatus} to {$enquiry->status}.");
    }

    /**
     * Remove an enquiry record.
     */
    public function destroy(Enquiry $enquiry): RedirectResponse
    {
        $name = $enquiry->name;
        $enquiry->delete();

        return redirect()->route('admin.enquiries.index')
            ->with('success', "Enquiry from '{$name}' has been deleted.");
    }
}
