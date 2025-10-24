<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionPackageController extends Controller
{
    public function index(Request $request)
    {
        $query = SubscriptionPackage::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('package_name', 'like', '%' . $request->search . '%');
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'package_name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['package_name', 'subscription_fee', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $packages = $query->paginate(15);

        return view('super-admin.subscription-packages.index', compact('packages'));
    }

    public function create()
    {
        return view('super-admin.subscription-packages.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_name' => 'required|string|max:255|unique:subscription_packages,package_name',
            'subscription_fee' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'trial_period_days' => 'required|integer|min:0|max:365',
            'onboarding_fee' => 'required|numeric|min:0',
            'vehicle_capacity' => 'nullable|integer|min:1',
            'is_unlimited_vehicles' => 'boolean',
            'billing_cycles' => 'required|array|min:1',
            'billing_cycles.*' => 'in:monthly,quarterly,yearly,custom',
            'payment_methods' => 'required|array|min:1',
            'payment_methods.*' => 'in:direct_debit,credit_card,bank_transfer,cash',
            'renewal_type' => 'required|in:auto_renew,manual_renewal',
            'status' => 'required|in:active,inactive,draft',
            'support_type' => 'required|in:standard,chat_only,full_support,enterprise_level',
            'description' => 'nullable|string',
            'features_summary' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Handle unlimited vehicles
        if ($request->is_unlimited_vehicles) {
            $data['vehicle_capacity'] = null;
        }

        $package = SubscriptionPackage::create($data);

        return redirect()->route('super-admin.subscription-packages.index')
            ->with('success', 'Subscription package created successfully!');
    }

    public function show(SubscriptionPackage $subscriptionPackage)
    {
        return view('super-admin.subscription-packages.show', compact('subscriptionPackage'));
    }

    public function edit(SubscriptionPackage $subscriptionPackage)
    {
        return view('super-admin.subscription-packages.edit', compact('subscriptionPackage'));
    }

    public function update(Request $request, SubscriptionPackage $subscriptionPackage)
    {
        $validator = Validator::make($request->all(), [
            'package_name' => 'required|string|max:255|unique:subscription_packages,package_name,' . $subscriptionPackage->id,
            'subscription_fee' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'trial_period_days' => 'required|integer|min:0|max:365',
            'onboarding_fee' => 'required|numeric|min:0',
            'vehicle_capacity' => 'nullable|integer|min:1',
            'is_unlimited_vehicles' => 'boolean',
            'billing_cycles' => 'required|array|min:1',
            'billing_cycles.*' => 'in:monthly,quarterly,yearly,custom',
            'payment_methods' => 'required|array|min:1',
            'payment_methods.*' => 'in:direct_debit,credit_card,bank_transfer,cash',
            'renewal_type' => 'required|in:auto_renew,manual_renewal',
            'status' => 'required|in:active,inactive,draft',
            'support_type' => 'required|in:standard,chat_only,full_support,enterprise_level',
            'description' => 'nullable|string',
            'features_summary' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Handle unlimited vehicles
        if ($request->is_unlimited_vehicles) {
            $data['vehicle_capacity'] = null;
        }

        $subscriptionPackage->update($data);

        return redirect()->route('super-admin.subscription-packages.index')
            ->with('success', 'Subscription package updated successfully!');
    }

    public function destroy(SubscriptionPackage $subscriptionPackage)
    {
        $subscriptionPackage->delete();

        return redirect()->route('super-admin.subscription-packages.index')
            ->with('success', 'Subscription package deleted successfully!');
    }

    public function toggleStatus(SubscriptionPackage $subscriptionPackage)
    {
        $newStatus = $subscriptionPackage->status === 'active' ? 'inactive' : 'active';
        $subscriptionPackage->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Package status updated successfully!',
            'new_status' => $newStatus
        ]);
    }
}
