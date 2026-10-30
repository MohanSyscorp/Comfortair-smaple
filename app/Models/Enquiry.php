<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mobile_number',
        'email',
        'service_required',
        'product_interested_in',
        'location',
        'message',
        'status',
    ];

    /**
     * Allowed enquiry statuses according to Section 6.1 & 9.4.
     */
    public const STATUSES = [
        'New',
        'Contacted',
        'In Progress',
        'Completed',
        'Closed',
    ];

    /**
     * Approved baseline services list.
     */
    public const SERVICES = [
        'AC Installation',
        'Repair',
        'Service & Maintenance',
        'Gas Filling',
        'Cleaning & Troubleshooting',
        'Replacement',
        'Annual Maintenance',
        'Commercial AC Services',
        'Other',
    ];

    /**
     * Location dropdown options.
     */
    public const LOCATIONS = [
        'Pondicherry',
        'Villupuram',
        'Cuddalore',
        'Other',
    ];

    /**
     * Scope for status filtering.
     */
    public function scopeWithStatus($query, ?string $status)
    {
        if ($status && in_array($status, self::STATUSES)) {
            return $query->where('status', $status);
        }

        return $query;
    }
}
