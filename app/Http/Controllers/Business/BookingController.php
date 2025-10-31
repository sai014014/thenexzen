<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings with tabs.
     */
    public function index(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;
        $query = $business->bookings()->with(['customer', 'vehicle']);

        // Filter by status
        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('full_name', 'like', "%{$search}%")
                                   ->orWhere('company_name', 'like', "%{$search}%")
                                   ->orWhere('mobile_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('vehicle', function ($vehicleQuery) use ($search) {
                      $vehicleQuery->where('vehicle_number', 'like', "%{$search}%")
                                   ->orWhere('vehicle_make', 'like', "%{$search}%")
                                   ->orWhere('vehicle_model', 'like', "%{$search}%");
                  });
            });
        }

        // Sort by start date
        $query->orderBy('start_date_time', 'desc');

        $bookings = $query->paginate(15)->withQueryString();

        // Check for draft booking
        $draft = session()->get('booking.flow', []);
        $hasDraft = !empty($draft) && (isset($draft['step_1']) || isset($draft['step_2']) || isset($draft['step_3']) || isset($draft['step_4']) || isset($draft['step_5']));

        return view('business.bookings.index', compact('bookings', 'status', 'business', 'businessAdmin', 'hasDraft'));
    }

    /**
     * Show the form for creating a new booking (5-step flow).
     */
    public function create()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        $business = $businessAdmin->business;

        return view('business.bookings.flow.create', compact('business', 'businessAdmin'));
    }

    // Single-page flow entry
    public function createFlow(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        $business = $businessAdmin->business;
        // Get locations for dropdowns (using business address + city, can be extended)
        $locations = [
            $business->address . ', ' . $business->city,
            'Garage',
            'Nagole agency parking',
        ];

        // Draft state from session
        $draft = session()->get('booking.flow', []);
        $draftStep = 1; // Default to step 1
        
        if (!empty($draft)) {
            // Find highest completed step in draft
            $steps = [];
            foreach ($draft as $key => $val) {
                if (preg_match('/^step_(\d)$/', $key, $m)) {
                    $steps[] = (int)$m[1];
                }
            }
            if (!empty($steps)) {
                $draftStep = max($steps);
            }
        }

        return view('business.bookings.flow.create', compact('business', 'locations', 'draft', 'draftStep'));
    }

    // Save step payload to session / draft
    public function saveFlowStep(Request $request)
    {
        $payload = $request->all();
        $flow = session()->get('booking.flow', []);
        
        if (($payload['step'] ?? null) === 'draft') {
            session()->put('booking.flow', $flow);
            return response()->json(['status' => 'ok']);
        }
        
        $step = (int)($payload['step'] ?? 0);
        if ($step >= 1 && $step <= 5) {
            // Merge with existing step data to preserve all fields
            $existingStepData = $flow['step_'.$step] ?? [];
            $flow['step_'.$step] = array_merge($existingStepData, $payload);
            
            // Remove the 'step' key from the stored data (it's just for routing)
            unset($flow['step_'.$step]['step']);
        }
        
        session()->put('booking.flow', $flow);
        return response()->json(['status' => 'ok', 'saved' => true]);
    }

    // Clear draft from session
    public function clearFlowDraft(Request $request)
    {
        session()->forget('booking.flow');
        return response()->json(['status' => 'ok']);
    }

    // Vehicles list (AJAX) - Get available vehicles for date range
    public function listFlowVehicles(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        if (!$businessAdmin) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $business = $businessAdmin->business;
        
        // Validate datetime formats (accept datetime-local format)
        $pickupDatetime = $request->input('pickup_datetime');
        $dropoffDatetime = $request->input('dropoff_datetime');
        
        if (!$pickupDatetime || !$dropoffDatetime) {
            return response()->json(['error' => 'Pickup and dropoff datetimes are required'], 400);
        }

        try {
            $startDateTime = Carbon::parse($pickupDatetime);
            $endDateTime = Carbon::parse($dropoffDatetime);
            
            if ($endDateTime <= $startDateTime) {
                return response()->json(['error' => 'Drop-off datetime must be after pickup datetime'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid datetime format'], 400);
        }

        // Validate other parameters
        $request->validate([
            'transmission' => 'nullable|string',
            'seats' => 'nullable|integer',
            'fuel' => 'nullable|string',
            'sort' => 'nullable|in:price_asc,price_desc',
        ]);

        $vehicles = Booking::getAvailableVehicles($business->id, $startDateTime, $endDateTime);

        // Apply filters
        if ($request->transmission) {
            $vehicles = $vehicles->where('transmission_type', $request->transmission);
        }
        if ($request->seats) {
            $vehicles = $vehicles->where('seating_capacity', $request->seats);
        }
        if ($request->fuel) {
            $vehicles = $vehicles->where('fuel_type', $request->fuel);
        }

        // Map to display format
        $vehicles = $vehicles->map(function ($v) {
            $firstImage = $v->images()->first();
            return [
                'id' => $v->id,
                'name' => $v->vehicle_make . ' ' . $v->vehicle_model,
                'registration' => $v->vehicle_number,
                'transmission' => ucfirst($v->transmission_type ?? 'N/A'),
                'seats' => $v->seating_capacity ?? 'N/A',
                'fuel' => ucfirst($v->fuel_type ?? 'N/A'),
                'price_per_day' => $v->rental_price_24h ?? 0,
                'image' => $firstImage ? asset('storage/' . $firstImage->image_path) : asset('images/vehicle-brands/default.svg'),
            ];
        });

        // Sort
        if ($request->sort === 'price_asc') {
            $vehicles = $vehicles->sortBy('price_per_day')->values();
        } elseif ($request->sort === 'price_desc') {
            $vehicles = $vehicles->sortByDesc('price_per_day')->values();
        }

        $html = '';
        foreach ($vehicles as $v) {
            $html .= view('business.bookings.flow.partials.vehicle_card', ['vehicle' => $v])->render();
        }
        return response($html);
    }

    // Billing summary compute (AJAX)
    public function billingSummary(Request $request)
    {
        $rent = (float)$request->input('rent_24h', 0);
        $additionalCharges = json_decode($request->input('additional_charges', '[]'), true) ?? [];
        $discountAmount = (float)$request->input('discount_amount', 0);
        $discountType = $request->input('discount_type', 'amount'); // 'amount' or 'percentage'
        $advance = (float)$request->input('advance_payment', 0);
        $pickupDatetime = $request->input('pickup_datetime');
        $dropoffDatetime = $request->input('dropoff_datetime');
        
        // Calculate days from dates
        $days = 1;
        if ($pickupDatetime && $dropoffDatetime) {
            try {
                $start = Carbon::parse($pickupDatetime);
                $end = Carbon::parse($dropoffDatetime);
                $hours = $start->diffInHours($end);
                $days = max(1, ceil($hours / 24)); // Minimum 1 day
            } catch (\Exception $e) {
                $days = 1;
            }
        }
        
        // Calculate base rental
        $baseRental = $rent * $days;
        
        // Sum additional charges
        $totalAdditional = 0;
        foreach ($additionalCharges as $charge) {
            $totalAdditional += (float)($charge['amount'] ?? 0);
        }
        
        // Calculate subtotal
        $subTotal = $baseRental + $totalAdditional;
        
        // Calculate discount
        $discountValue = 0;
        if ($discountType === 'percentage') {
            $discountValue = $subTotal * ($discountAmount / 100);
        } else {
            $discountValue = $discountAmount;
        }
        $discountValue = min($discountValue, $subTotal); // Can't discount more than subtotal
        
        // Calculate final amount
        $totalAfterDiscount = $subTotal - $discountValue;
        $amountDue = max(0, $totalAfterDiscount - $advance);
        
        return response()->json([
            'baseRental' => number_format($baseRental, 2),
            'days' => $days,
            'additionalCharges' => number_format($totalAdditional, 2),
            'subTotal' => number_format($subTotal, 2),
            'discount' => number_format($discountValue, 2),
            'advance' => number_format($advance, 2),
            'totalAfterDiscount' => number_format($totalAfterDiscount, 2),
            'amountDue' => number_format($amountDue, 2),
        ]);
    }

    // Store booking from flow (AJAX)
    public function storeFromFlow(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required. Please log in again.'
            ], 401);
        }
        
        $business = $businessAdmin->business;
        
        try {
            // Check if booking is already being processed (prevent duplicate submissions)
            $processingKey = 'booking.flow.processing';
            if (session()->has($processingKey)) {
                // Check if booking was recently created (within last 5 seconds)
                $processingTime = session()->get($processingKey);
                if (now()->diffInSeconds($processingTime) < 5) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Booking is already being processed. Please wait...'
                    ], 429); // Too Many Requests
                }
            }
            
            // Set processing flag
            session()->put($processingKey, now());
            
            // Get draft data from session
            $flow = session()->get('booking.flow', []);
            
            // Check if booking was already created (prevent duplicate from same session data)
            $bookingHash = md5(json_encode([
                $flow['step_1'] ?? [],
                $flow['step_2'] ?? [],
                $flow['step_3'] ?? [],
                $flow['step_4'] ?? []
            ]));
            
            $lastBookingHash = session()->get('booking.flow.last_hash');
            if ($lastBookingHash === $bookingHash) {
                // Same booking data, check if it was recently created
                $lastBookingTime = session()->get('booking.flow.last_created');
                if ($lastBookingTime && now()->diffInSeconds($lastBookingTime) < 10) {
                    session()->forget($processingKey);
                    return response()->json([
                        'success' => false,
                        'message' => 'This booking was already created. Please refresh the page.'
                    ], 400);
                }
            }
            
            // Validate required data is present
            $step1 = $flow['step_1'] ?? [];
            $step2 = $flow['step_2'] ?? [];
            $step3 = $flow['step_3'] ?? [];
            $step4 = $flow['step_4'] ?? [];
            
            if (empty($step1['pickup_datetime']) || empty($step1['dropoff_datetime'])) {
                session()->forget($processingKey);
                return response()->json([
                    'success' => false,
                    'message' => 'Pickup and dropoff dates are required.'
                ], 400);
            }
            
            if (empty($step2['vehicle_id'])) {
                session()->forget($processingKey);
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a vehicle.'
                ], 400);
            }
            
            if (empty($step3['customer_id'])) {
                session()->forget($processingKey);
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a customer.'
                ], 400);
            }
            
            // Parse dates
            $startDateTime = Carbon::parse($step1['pickup_datetime']);
            $endDateTime = Carbon::parse($step1['dropoff_datetime']);
            
            // Validate customer belongs to business
            $customer = Customer::where('id', $step3['customer_id'])
                ->where('business_id', $business->id)
                ->first();
            
            if (!$customer) {
                session()->forget($processingKey);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid customer selected.'
                ], 400);
            }
            
            // Validate vehicle belongs to business
            $vehicle = Vehicle::where('id', $step2['vehicle_id'])
                ->where('business_id', $business->id)
                ->first();
            
            if (!$vehicle) {
                session()->forget($processingKey);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid vehicle selected.'
                ], 400);
            }
            
            // Check vehicle availability
            if (!Booking::isVehicleAvailable($vehicle->id, $startDateTime, $endDateTime)) {
                session()->forget($processingKey);
                return response()->json([
                    'success' => false,
                    'message' => 'Selected vehicle is not available for the chosen time period.'
                ], 400);
            }
            
            // Check for duplicate booking created in last 10 seconds (same vehicle, customer, dates)
            $recentDuplicate = Booking::where('business_id', $business->id)
                ->where('vehicle_id', $step2['vehicle_id'])
                ->where('customer_id', $step3['customer_id'])
                ->where('start_date_time', $startDateTime)
                ->where('end_date_time', $endDateTime)
                ->where('created_at', '>=', now()->subSeconds(10))
                ->first();
            
            if ($recentDuplicate) {
                session()->forget($processingKey);
                return response()->json([
                    'success' => false,
                    'message' => 'A similar booking was just created. Please refresh the page.',
                    'booking_id' => $recentDuplicate->id,
                    'redirect_url' => route('business.bookings.show', $recentDuplicate)
                ], 409); // Conflict
            }
            
            // Calculate pricing from Step 4 data
            $rent24h = (float)($step4['rent_24h'] ?? $vehicle->rental_price_24h ?? 1000);
            $hours = $startDateTime->diffInHours($endDateTime);
            $days = max(1, ceil($hours / 24));
            $baseRental = $rent24h * $days;
            
            // Additional charges
            $additionalCharges = $step4['additional_charges'] ?? [];
            $totalAdditional = 0;
            if (is_array($additionalCharges)) {
                foreach ($additionalCharges as $charge) {
                    $totalAdditional += (float)($charge['amount'] ?? 0);
                }
            }
            
            // Discount
            $discountAmount = (float)($step4['discount_amount'] ?? 0);
            $discountType = $step4['discount_type'] ?? 'amount';
            $discountValue = 0;
            $subTotal = $baseRental + $totalAdditional;
            if ($discountType === 'percentage') {
                $discountValue = $subTotal * ($discountAmount / 100);
            } else {
                $discountValue = $discountAmount;
            }
            $discountValue = min($discountValue, $subTotal);
            
            // Final amounts
            $totalAmount = $subTotal - $discountValue;
            $advanceAmount = (float)($step4['advance_payment'] ?? $step4['advance_amount'] ?? 0);
            $amountDue = max(0, $totalAmount - $advanceAmount);
            
            // Create booking
            $booking = Booking::create([
                'business_id' => $business->id,
                'customer_id' => $step3['customer_id'],
                'vehicle_id' => $step2['vehicle_id'],
                'booking_number' => Booking::generateBookingNumber(),
                'start_date_time' => $startDateTime,
                'end_date_time' => $endDateTime,
                'pickup_location' => $step1['pickup_location'] ?? null,
                'dropoff_location' => $step1['dropoff_location'] ?? null,
                'base_rental_price' => $baseRental,
                'extra_charges' => $totalAdditional,
                'discount_amount' => $discountValue,
                'total_amount' => $totalAmount,
                'advance_amount' => $advanceAmount,
                'advance_payment_method' => $step4['payment_method'] ?? null,
                'amount_due' => $amountDue,
                'status' => 'upcoming',
            ]);
            
            // Save hash and timestamp to prevent duplicates
            session()->put('booking.flow.last_hash', $bookingHash);
            session()->put('booking.flow.last_created', now());
            
            // Clear draft session and processing flag
            session()->forget('booking.flow');
            session()->forget($processingKey);
            
            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully!',
                'booking_id' => $booking->id,
                'redirect_url' => route('business.bookings.show', $booking)
            ]);
            
        } catch (\Exception $e) {
            session()->forget('booking.flow.processing');
            \Log::error('Error creating booking from flow: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the quick create booking form.
     */
    public function quickCreate()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        $business = $businessAdmin->business;
        $customers = $business->customers()->where('status', 'active')->get();
        $vehicles = $business->vehicles()->where('is_available', true)->get();

        return view('business.bookings.quick-create', compact('customers', 'vehicles', 'business', 'businessAdmin'));
    }

    /**
     * Show the 5-stage booking flow - Step 1: Dates
     */
    public function createStep1()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;
        
        return view('business.bookings.flow.step1-dates', compact('business', 'businessAdmin'));
    }

    /**
     * Process Step 1: Dates and proceed to Step 2
     */
    public function processStep1(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        $request->validate([
            'start_date_time' => 'required|date|after:now',
            'end_date_time' => 'required|date|after:start_date_time',
        ]);

        // Store dates in session
        session([
            'booking_flow.start_date_time' => $request->start_date_time,
            'booking_flow.end_date_time' => $request->end_date_time,
        ]);

        return redirect()->route('business.bookings.flow.step2');
    }

    /**
     * Show Step 2: Vehicle Selection
     */
    public function createStep2()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        // Check if dates are set
        if (!session('booking_flow.start_date_time') || !session('booking_flow.end_date_time')) {
            return redirect()->route('business.bookings.flow.step1');
        }

        $business = $businessAdmin->business;
        $startDateTime = Carbon::parse(session('booking_flow.start_date_time'));
        $endDateTime = Carbon::parse(session('booking_flow.end_date_time'));

        // Get available vehicles for the selected date range
        $vehicles = $business->vehicles()
            ->where('is_available', true)
            ->whereDoesntHave('bookings', function ($query) use ($startDateTime, $endDateTime) {
                $query->whereIn('status', ['ongoing', 'upcoming'])
                      ->where(function ($q) use ($startDateTime, $endDateTime) {
                          $q->whereBetween('start_date_time', [$startDateTime, $endDateTime])
                            ->orWhereBetween('end_date_time', [$startDateTime, $endDateTime])
                            ->orWhere(function ($q2) use ($startDateTime, $endDateTime) {
                                $q2->where('start_date_time', '<=', $startDateTime)
                                   ->where('end_date_time', '>=', $endDateTime);
                            });
                      });
            })
            ->get();

        return view('business.bookings.flow.step2-vehicles', compact('vehicles', 'startDateTime', 'endDateTime', 'business', 'businessAdmin'));
    }

    /**
     * Process Step 2: Vehicle Selection and proceed to Step 3
     */
    public function processStep2(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        // Store vehicle selection in session
        session(['booking_flow.vehicle_id' => $request->vehicle_id]);

        return redirect()->route('business.bookings.flow.step3');
    }

    /**
     * Show Step 3: Customer Selection
     */
    public function createStep3()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        // Check if previous steps are completed
        if (!session('booking_flow.vehicle_id')) {
            return redirect()->route('business.bookings.flow.step2');
        }

        $business = $businessAdmin->business;
        $customers = $business->customers()->get();
        $vehicle = Vehicle::find(session('booking_flow.vehicle_id'));

        return view('business.bookings.flow.step3-customer', compact('customers', 'vehicle', 'business', 'businessAdmin'));
    }

    /**
     * Process Step 3: Customer Selection and proceed to Step 4
     */
    public function processStep3(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        // Store customer selection in session
        session(['booking_flow.customer_id' => $request->customer_id]);

        return redirect()->route('business.bookings.flow.step4');
    }

    /**
     * Show Step 4: Billing Information
     */
    public function createStep4()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        // Check if previous steps are completed
        if (!session('booking_flow.customer_id')) {
            return redirect()->route('business.bookings.flow.step3');
        }

        $vehicle = Vehicle::find(session('booking_flow.vehicle_id'));
        $customer = Customer::find(session('booking_flow.customer_id'));
        $startDateTime = Carbon::parse(session('booking_flow.start_date_time'));
        $endDateTime = Carbon::parse(session('booking_flow.end_date_time'));

        // Calculate base pricing
        $hours = $startDateTime->diffInHours($endDateTime);
        $days = ceil($hours / 24);
        $baseRentalPrice = $vehicle->rental_price_24h ?? 1000;
        $totalAmount = $baseRentalPrice * $days;

        return view('business.bookings.flow.step4-billing', compact('vehicle', 'customer', 'startDateTime', 'endDateTime', 'baseRentalPrice', 'totalAmount', 'days', 'business', 'businessAdmin'));
    }

    /**
     * Process Step 4: Billing Information and proceed to Step 5
     */
    public function processStep4(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        $request->validate([
            'base_rental_price' => 'required|numeric|min:0',
            'extra_charges' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'advance_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
        ]);

        // Store billing information in session
        session([
            'booking_flow.base_rental_price' => $request->base_rental_price,
            'booking_flow.extra_charges' => $request->extra_charges ?? 0,
            'booking_flow.discount_amount' => $request->discount_amount ?? 0,
            'booking_flow.advance_amount' => $request->advance_amount ?? 0,
            'booking_flow.payment_method' => $request->payment_method,
        ]);

        return redirect()->route('business.bookings.flow.step5');
    }

    /**
     * Show Step 5: Confirmation
     */
    public function createStep5()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        // Check if all previous steps are completed
        if (!session('booking_flow.advance_amount')) {
            return redirect()->route('business.bookings.flow.step4');
        }

        $vehicle = Vehicle::find(session('booking_flow.vehicle_id'));
        $customer = Customer::find(session('booking_flow.customer_id'));
        $startDateTime = Carbon::parse(session('booking_flow.start_date_time'));
        $endDateTime = Carbon::parse(session('booking_flow.end_date_time'));

        // Calculate final pricing
        $hours = $startDateTime->diffInHours($endDateTime);
        $days = ceil($hours / 24);
        $baseRentalPrice = session('booking_flow.base_rental_price');
        $extraCharges = session('booking_flow.extra_charges', 0);
        $discountAmount = session('booking_flow.discount_amount', 0);
        $advanceAmount = session('booking_flow.advance_amount', 0);
        
        $totalAmount = $baseRentalPrice + $extraCharges;
        $amountAfterDiscount = $totalAmount - $discountAmount;
        $amountDue = $amountAfterDiscount - $advanceAmount;

        return view('business.bookings.flow.step5-confirm', compact(
            'vehicle', 'customer', 'startDateTime', 'endDateTime', 
            'baseRentalPrice', 'extraCharges', 'discountAmount', 'advanceAmount',
            'totalAmount', 'amountAfterDiscount', 'amountDue', 'days', 'business', 'businessAdmin'
        ));
    }

    /**
     * Process Step 5: Final confirmation and create booking
     */
    public function processStep5(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        $business = $businessAdmin->business;

        // Validate all required data is present
        $requiredData = [
            'start_date_time', 'end_date_time', 'vehicle_id', 'customer_id',
            'base_rental_price', 'extra_charges', 'discount_amount', 'advance_amount'
        ];

        foreach ($requiredData as $key) {
            if (!session("booking_flow.{$key}")) {
                return redirect()->route('business.bookings.flow.step1')
                    ->with('error', 'Please complete all booking steps.');
            }
        }

        // Create the booking
        $booking = new Booking();
        $booking->business_id = $business->id;
        $booking->customer_id = session('booking_flow.customer_id');
        $booking->vehicle_id = session('booking_flow.vehicle_id');
        $booking->start_date_time = Carbon::parse(session('booking_flow.start_date_time'));
        $booking->end_date_time = Carbon::parse(session('booking_flow.end_date_time'));
        $booking->base_rental_price = session('booking_flow.base_rental_price');
        $booking->extra_charges = session('booking_flow.extra_charges', 0);
        $booking->total_amount = session('booking_flow.base_rental_price') + session('booking_flow.extra_charges', 0);
        $booking->advance_amount = session('booking_flow.advance_amount', 0);
        $booking->amount_due = $booking->total_amount - $booking->advance_amount;
        $booking->payment_method = session('booking_flow.payment_method');
        $booking->status = 'upcoming';
        $booking->save();

        // Clear session data
        session()->forget('booking_flow');

        return redirect()->route('business.bookings.show', $booking)
            ->with('success', 'Booking created successfully!');
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required. Please log in again.'
                ], 401);
            }
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        try {
            $validatedData = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'vehicle_id' => 'required|exists:vehicles,id',
                'start_date_time' => 'required|date|after:now',
                'end_date_time' => 'required|date|after:start_date_time',
                'advance_amount' => 'nullable|numeric|min:0',
                'advance_payment_method' => 'nullable|in:cash,credit_card,debit_card,upi,bank_transfer,cheque',
                'payment_method' => 'nullable|in:cash,credit_card,debit_card,upi,bank_transfer,cheque',
                'customer_notes' => 'nullable|string|max:1000',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Check if customer belongs to this business
            $customer = Customer::where('id', $validatedData['customer_id'])
                ->where('business_id', $business->id)
                ->first();
            
            if (!$customer) {
                throw new \Exception('Invalid customer selected.');
            }

            // Check if vehicle belongs to this business
            $vehicle = Vehicle::where('id', $validatedData['vehicle_id'])
                ->where('business_id', $business->id)
                ->first();
            
            if (!$vehicle) {
                throw new \Exception('Invalid vehicle selected.');
            }

            // Check vehicle availability
            $startDateTime = Carbon::parse($validatedData['start_date_time']);
            $endDateTime = Carbon::parse($validatedData['end_date_time']);
            
            if (!Booking::isVehicleAvailable($vehicle->id, $startDateTime, $endDateTime)) {
                throw new \Exception('Selected vehicle is not available for the chosen time period.');
            }

            // Calculate pricing
            $baseRentalPrice = $vehicle->rental_price_24h ?? 1000; // Default price if not set
            $hours = $startDateTime->diffInHours($endDateTime);
            $days = ceil($hours / 24);
            $totalAmount = $baseRentalPrice * $days;
            $amountDue = $totalAmount - ($validatedData['advance_amount'] ?? 0);

            // Create booking
            $booking = Booking::create([
                'business_id' => $business->id,
                'customer_id' => $validatedData['customer_id'],
                'vehicle_id' => $validatedData['vehicle_id'],
                'booking_number' => Booking::generateBookingNumber(),
                'start_date_time' => $startDateTime,
                'end_date_time' => $endDateTime,
                'base_rental_price' => $baseRentalPrice,
                'total_amount' => $totalAmount,
                'amount_due' => $amountDue,
                'advance_amount' => $validatedData['advance_amount'] ?? 0,
                'advance_payment_method' => $validatedData['advance_payment_method'] ?? null,
                'payment_method' => $validatedData['payment_method'] ?? null,
                'customer_notes' => $validatedData['customer_notes'] ?? null,
                'notes' => $validatedData['notes'] ?? null,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking created successfully!',
                    'redirect_url' => route('business.bookings.show', $booking)
                ]);
            }

            return redirect()->route('business.bookings.show', $booking)
                ->with('success', 'Booking created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the booking: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'An error occurred while creating the booking: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the booking belongs to this business
        if ($booking->business_id !== $business->id) {
            abort(403, 'Unauthorized access to booking.');
        }

        $booking->load(['customer', 'vehicle']);

        return view('business.bookings.show', compact('booking', 'business', 'businessAdmin'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit(Booking $booking)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the booking belongs to this business
        if ($booking->business_id !== $business->id) {
            abort(403, 'Unauthorized access to booking.');
        }

        // Only allow editing of upcoming bookings
        if ($booking->status !== 'upcoming') {
            return redirect()->route('business.bookings.show', $booking)
                ->with('error', 'Only upcoming bookings can be edited.');
        }

        $customers = $business->customers()->where('status', 'active')->get();
        $vehicles = $business->vehicles()->where('is_available', true)->get();

        return view('business.bookings.edit', compact('booking', 'customers', 'vehicles', 'business', 'businessAdmin'));
    }

    /**
     * Update the specified booking.
     */
    public function update(Request $request, Booking $booking)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required. Please log in again.'
                ], 401);
            }
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the booking belongs to this business
        if ($booking->business_id !== $business->id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to booking.'
                ], 403);
            }
            abort(403, 'Unauthorized access to booking.');
        }

        // Only allow updating of upcoming bookings
        if ($booking->status !== 'upcoming') {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only upcoming bookings can be updated.'
                ], 422);
            }
            return redirect()->route('business.bookings.show', $booking)
                ->with('error', 'Only upcoming bookings can be updated.');
        }

        try {
            $validatedData = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'vehicle_id' => 'required|exists:vehicles,id',
                'start_date_time' => 'required|date|after:now',
                'end_date_time' => 'required|date|after:start_date_time',
                'advance_amount' => 'nullable|numeric|min:0',
                'advance_payment_method' => 'nullable|in:cash,credit_card,debit_card,upi,bank_transfer,cheque',
                'payment_method' => 'nullable|in:cash,credit_card,debit_card,upi,bank_transfer,cheque',
                'customer_notes' => 'nullable|string|max:1000',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Check if customer belongs to this business
            $customer = Customer::where('id', $validatedData['customer_id'])
                ->where('business_id', $business->id)
                ->first();
            
            if (!$customer) {
                throw new \Exception('Invalid customer selected.');
            }

            // Check if vehicle belongs to this business
            $vehicle = Vehicle::where('id', $validatedData['vehicle_id'])
                ->where('business_id', $business->id)
                ->first();
            
            if (!$vehicle) {
                throw new \Exception('Invalid vehicle selected.');
            }

            // Check vehicle availability (excluding current booking)
            $startDateTime = Carbon::parse($validatedData['start_date_time']);
            $endDateTime = Carbon::parse($validatedData['end_date_time']);
            
            if (!Booking::isVehicleAvailable($vehicle->id, $startDateTime, $endDateTime, $booking->id)) {
                throw new \Exception('Selected vehicle is not available for the chosen time period.');
            }

            // Calculate pricing
            $baseRentalPrice = $vehicle->rental_price_24h ?? 1000;
            $hours = $startDateTime->diffInHours($endDateTime);
            $days = ceil($hours / 24);
            $totalAmount = $baseRentalPrice * $days;
            $amountDue = $totalAmount - ($validatedData['advance_amount'] ?? 0);

            // Update booking
            $booking->update([
                'customer_id' => $validatedData['customer_id'],
                'vehicle_id' => $validatedData['vehicle_id'],
                'start_date_time' => $startDateTime,
                'end_date_time' => $endDateTime,
                'base_rental_price' => $baseRentalPrice,
                'total_amount' => $totalAmount,
                'amount_due' => $amountDue,
                'advance_amount' => $validatedData['advance_amount'] ?? 0,
                'advance_payment_method' => $validatedData['advance_payment_method'] ?? null,
                'payment_method' => $validatedData['payment_method'] ?? null,
                'customer_notes' => $validatedData['customer_notes'] ?? null,
                'notes' => $validatedData['notes'] ?? null,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking updated successfully!',
                    'redirect_url' => route('business.bookings.show', $booking)
                ]);
            }

            return redirect()->route('business.bookings.show', $booking)
                ->with('success', 'Booking updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the booking: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'An error occurred while updating the booking: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified booking.
     */
    public function destroy(Booking $booking)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the booking belongs to this business
        if ($booking->business_id !== $business->id) {
            abort(403, 'Unauthorized access to booking.');
        }

        // Only allow deletion of upcoming bookings
        if ($booking->status !== 'upcoming') {
            return redirect()->route('business.bookings.show', $booking)
                ->with('error', 'Only upcoming bookings can be deleted.');
        }

        $booking->delete();

        return redirect()->route('business.bookings.index')
            ->with('success', 'Booking deleted successfully!');
    }

    /**
     * Start a booking (upcoming -> ongoing).
     */
    public function start(Booking $booking)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the booking belongs to this business
        if ($booking->business_id !== $business->id) {
            abort(403, 'Unauthorized access to booking.');
        }

        if ($booking->start()) {
            return redirect()->route('business.bookings.show', $booking)
                ->with('success', 'Booking started successfully!');
        }

        return redirect()->route('business.bookings.show', $booking)
            ->with('error', 'Unable to start booking. Please check the booking status.');
    }

    /**
     * Complete a booking (ongoing -> completed).
     */
    public function complete(Request $request, Booking $booking)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the booking belongs to this business
        if ($booking->business_id !== $business->id) {
            abort(403, 'Unauthorized access to booking.');
        }

        $request->validate([
            'final_amount_paid' => 'required|numeric|min:0',
            'completion_notes' => 'nullable|string|max:1000',
            'actual_return_datetime' => 'nullable|date',
            'additional_charges' => 'nullable|string', // JSON array
        ]);

        // Optionally adjust end date and totals if actual return differs
        if ($request->filled('actual_return_datetime')) {
            try {
                $actualEnd = Carbon::parse($request->actual_return_datetime);
                $booking->end_date_time = $actualEnd;

                // Recompute totals based on days
                $basePerDay = (float)($booking->base_rental_price ?? 0);
                $hours = $booking->start_date_time ? $booking->start_date_time->diffInHours($actualEnd) : 0;
                $days = max(1, (int)ceil($hours / 24));

                $existingExtras = (float)($booking->extra_charges ?? 0);
                $newExtras = 0.0;
                if ($request->filled('additional_charges')) {
                    $charges = json_decode($request->additional_charges, true);
                    if (is_array($charges)) {
                        foreach ($charges as $c) {
                            $newExtras += (float)($c['amount'] ?? 0);
                        }
                    }
                }

                $booking->extra_charges = $existingExtras + $newExtras;
                $booking->total_amount = ($basePerDay * $days) + $booking->extra_charges;
                $booking->updateAmountDue();
            } catch (\Exception $e) {
                // ignore parse errors; proceed with current values
            }
        }

        // Apply final payment
        $booking->amount_paid = $booking->amount_paid + $request->final_amount_paid;
        $booking->updateAmountDue();

        // Add completion notes
        if ($request->completion_notes) {
            $booking->notes = ($booking->notes ? $booking->notes . "\n\n" : '') .
                             "Completion Notes: " . $request->completion_notes;
            $booking->save();
        }

        if ($booking->complete()) {
            return redirect()->route('business.bookings.show', $booking)
                ->with('success', 'Booking completed successfully!');
        }

        return redirect()->route('business.bookings.show', $booking)
            ->with('error', 'Unable to complete booking. Please check the booking status.');
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, Booking $booking)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the booking belongs to this business
        if ($booking->business_id !== $business->id) {
            abort(403, 'Unauthorized access to booking.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        if ($booking->cancel($request->cancellation_reason)) {
            return redirect()->route('business.bookings.show', $booking)
                ->with('success', 'Booking cancelled successfully!');
        }

        return redirect()->route('business.bookings.show', $booking)
            ->with('error', 'Unable to cancel booking. Please check the booking status.');
    }

    /**
     * Get available vehicles for a given time period.
     */
    public function getAvailableVehicles(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $business = $businessAdmin->business;

        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'vehicle_type' => 'nullable|string',
            'seating_capacity' => 'nullable|integer',
            'fuel_type' => 'nullable|string',
            'transmission_type' => 'nullable|string',
        ]);

        // Parse dates with time information if provided
        if ($request->start_time && $request->end_time) {
            $startDateTime = Carbon::parse($request->start_date . ' ' . $request->start_time);
            $endDateTime = Carbon::parse($request->end_date . ' ' . $request->end_time);
        } else {
            // Fallback to full day range
            $startDateTime = Carbon::parse($request->start_date)->startOfDay();
            $endDateTime = Carbon::parse($request->end_date)->endOfDay();
        }

        $vehicles = Booking::getAvailableVehicles($business->id, $startDateTime, $endDateTime);

        // Apply filters
        if ($request->vehicle_type) {
            $vehicles = $vehicles->where('vehicle_type', $request->vehicle_type);
        }
        if ($request->seating_capacity) {
            $vehicles = $vehicles->where('seating_capacity', $request->seating_capacity);
        }
        if ($request->fuel_type) {
            $vehicles = $vehicles->where('fuel_type', $request->fuel_type);
        }
        if ($request->transmission_type) {
            $vehicles = $vehicles->where('transmission_type', $request->transmission_type);
        }

        return response()->json([
            'vehicles' => $vehicles->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'vehicle_number' => $vehicle->vehicle_number,
                    'vehicle_make' => $vehicle->vehicle_make,
                    'vehicle_model' => $vehicle->vehicle_model,
                    'vehicle_type' => $vehicle->vehicle_type,
                    'seating_capacity' => $vehicle->seating_capacity,
                    'fuel_type' => $vehicle->fuel_type,
                    'transmission_type' => $vehicle->transmission_type,
                    'rental_price_24h' => $vehicle->rental_price_24h ?? 1000,
                ];
            })
        ]);
    }

    /**
     * Calculate booking pricing.
     */
    public function calculatePricing(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date_time' => 'required|date',
            'end_date_time' => 'required|date|after:start_date_time',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $startDateTime = Carbon::parse($request->start_date_time);
        $endDateTime = Carbon::parse($request->end_date_time);

        $baseRentalPrice = $vehicle->rental_price_24h ?? 1000;
        $hours = $startDateTime->diffInHours($endDateTime);
        $days = ceil($hours / 24);
        $totalAmount = $baseRentalPrice * $days;

        return response()->json([
            'base_rental_price' => $baseRentalPrice,
            'duration_hours' => $hours,
            'duration_days' => $days,
            'total_amount' => $totalAmount,
        ]);
    }


    /**
     * Search customers (API endpoint)
     */
    public function searchCustomers(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = trim($request->get('q', ''));
        $business = $businessAdmin->business;
        
        $customersQuery = $business->customers()->where('status', 'active');
        
        // If query is empty or less than 2 characters, return all active customers (limited)
        if (strlen($query) < 2) {
            $customers = $customersQuery
                ->select('id', 'full_name', 'company_name', 'mobile_number', 'email_address')
                ->orderBy('full_name')
                ->orderBy('company_name')
                ->limit(50)
                ->get();
        } else {
            // Case-insensitive search across multiple fields
            $searchQuery = strtolower($query);
            $customers = $customersQuery
                ->where(function ($q) use ($searchQuery) {
                    $q->whereRaw('LOWER(full_name) LIKE ?', ["%{$searchQuery}%"])
                      ->orWhereRaw('LOWER(company_name) LIKE ?', ["%{$searchQuery}%"])
                      ->orWhereRaw('LOWER(mobile_number) LIKE ?', ["%{$searchQuery}%"])
                      ->orWhereRaw('LOWER(email_address) LIKE ?', ["%{$searchQuery}%"]);
                })
                ->select('id', 'full_name', 'company_name', 'mobile_number', 'email_address')
                ->orderByRaw('CASE 
                    WHEN LOWER(full_name) LIKE ? THEN 1 
                    WHEN LOWER(company_name) LIKE ? THEN 2 
                    ELSE 3 
                END', ["{$searchQuery}%", "{$searchQuery}%"])
                ->limit(50)
                ->get();
        }

        return response()->json(['customers' => $customers]);
    }

    /**
     * Get vehicle details for billing (API endpoint)
     */
    public function getVehicleForBilling(Request $request, $vehicleId)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $business = $businessAdmin->business;
        $vehicle = \App\Models\Vehicle::where('business_id', $business->id)
            ->where('id', $vehicleId)
            ->first();

        if (!$vehicle) {
            return response()->json(['error' => 'Vehicle not found'], 404);
        }

        return response()->json([
            'id' => $vehicle->id,
            'rental_price_24h' => $vehicle->rental_price_24h ?? 0,
            'km_limit_per_booking' => $vehicle->km_limit_per_booking ?? 0,
            'extra_rental_price_per_hour' => $vehicle->extra_rental_price_per_hour ?? 0,
            'extra_price_per_km' => $vehicle->extra_price_per_km ?? 0,
        ]);
    }
}