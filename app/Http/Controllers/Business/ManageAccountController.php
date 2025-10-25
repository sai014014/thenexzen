<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ManageAccountController extends Controller
{
    public function index()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;
        
        // Get all business admins for this business
        $businessAdmins = BusinessAdmin::where('business_id', $business->id)->get();
        
        // Get subscription details
        $subscription = $business->subscriptions()
            ->with('subscriptionPackage')
            ->whereIn('status', ['active', 'trial'])
            ->first();
        
        // Debug: Log subscription data
        \Log::info('Subscription Data:', [
            'subscription' => $subscription ? $subscription->toArray() : null,
            'package' => $subscription && $subscription->subscriptionPackage ? $subscription->subscriptionPackage->toArray() : null
        ]);
        
        return view('business.manage-account.index', compact(
            'business',
            'businessAdmin',
            'businessAdmins',
            'subscription'
        ));
    }
    
    public function updateBusinessDetails(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;
        
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|in:individual,company',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'gst_number' => 'nullable|string|max:50',
            'phone' => 'required|string|max:20',
            'contact_number' => 'nullable|string|max:20',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $business->update([
                'business_name' => $request->business_name,
                'business_type' => $request->business_type,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'gst_number' => $request->gst_number,
                'phone' => $request->phone,
                'contact_number' => $request->contact_number,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Business details updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update business details'
            ], 500);
        }
    }
    
    public function updateLogo(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;
        
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Delete old logo if exists
            if ($business->logo && Storage::disk('public')->exists($business->logo)) {
                Storage::disk('public')->delete($business->logo);
            }
            
            // Store new logo
            $logoPath = $request->file('logo')->store('business-logos', 'public');
            
            $business->update(['logo' => $logoPath]);
            
            return response()->json([
                'success' => true,
                'message' => 'Logo updated successfully',
                'logo_url' => asset('storage/' . $logoPath)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update logo'
            ], 500);
        }
    }
    
    public function addUser(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;
        
        // Debug: Log request data
        \Log::info('Add User Request Data:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:business_admins,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,employee'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Create business admin directly
            $newUser = BusinessAdmin::create([
                'business_id' => $business->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            
            // Log user creation activity
            \App\Traits\LogsActivity::logActivity(
                'user_added',
                "Added new user: {$newUser->name} ({$newUser->email}) with role: {$newUser->role}",
                get_class($newUser),
                $newUser->id,
                null,
                $newUser->toArray()
            );
            
            return response()->json([
                'success' => true,
                'message' => 'User added successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add user'
            ], 500);
        }
    }
    
    public function updateUser(Request $request, $id)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;
        
        // Debug: Log request data
        \Log::info('Update User Request Data:', $request->all());
        
        $targetBusinessAdmin = BusinessAdmin::where('id', $id)
            ->where('business_id', $business->id)
            ->first();
            
        if (!$targetBusinessAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:business_admins,email,' . $targetBusinessAdmin->id,
            'role' => 'required|in:admin,manager,employee',
            'is_active' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Store old values for logging
            $oldValues = $targetBusinessAdmin->toArray();
            
            // Update business admin directly
            $targetBusinessAdmin->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'is_active' => $request->is_active ?? true,
            ]);
            
            // Log user update activity
            \App\Traits\LogsActivity::logActivity(
                'user_updated',
                "Updated user: {$targetBusinessAdmin->name} ({$targetBusinessAdmin->email})",
                get_class($targetBusinessAdmin),
                $targetBusinessAdmin->id,
                $oldValues,
                $targetBusinessAdmin->getChanges()
            );
            
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user'
            ], 500);
        }
    }
    
    public function deleteUser($id)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;
        
        $targetBusinessAdmin = BusinessAdmin::where('id', $id)
            ->where('business_id', $business->id)
            ->first();
            
        if (!$targetBusinessAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        // Prevent deleting the current user
        if ($targetBusinessAdmin->id === $businessAdmin->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete your own account'
            ], 400);
        }
        
        try {
            // Store user data for logging before deletion
            $userData = $targetBusinessAdmin->toArray();
            
            // Delete business admin directly
            $targetBusinessAdmin->delete();
            
            // Log user deletion activity
            \App\Traits\LogsActivity::logActivity(
                'user_deleted',
                "Deleted user: {$userData['name']} ({$userData['email']})",
                BusinessAdmin::class,
                $id,
                $userData,
                null
            );
            
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user'
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Verify current password
        if (!Hash::check($request->current_password, $businessAdmin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }
        
        try {
            $businessAdmin->update([
                'password' => Hash::make($request->new_password)
            ]);
            
            // Log the password change activity
            \App\Traits\LogsActivity::logActivity(
                'password_changed',
                'Changed password',
                get_class($businessAdmin),
                $businessAdmin->id
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password'
            ], 500);
        }
    }
}
