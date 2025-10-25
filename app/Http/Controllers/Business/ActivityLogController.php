<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        $query = ActivityLog::where('business_id', $business->id)
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activityLogs = $query->paginate(20);

        // Get filter options
        $actions = ActivityLog::where('business_id', $business->id)
            ->distinct()
            ->pluck('action')
            ->map(function($action) {
                return [
                    'value' => $action,
                    'label' => ucfirst(str_replace('_', ' ', $action))
                ];
            });

        $users = $business->businessAdmins()
            ->select('id', 'name')
            ->get();

        return view('business.activity-log.index', compact(
            'activityLogs',
            'actions',
            'users',
            'business',
            'businessAdmin'
        ));
    }

    public function show(ActivityLog $activityLog)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        // Ensure the activity log belongs to this business
        if ($activityLog->business_id !== $businessAdmin->business->id) {
            abort(403, 'Unauthorized access');
        }

        $activityLog->load('user');

        return view('business.activity-log.show', compact('activityLog', 'businessAdmin'));
    }
}