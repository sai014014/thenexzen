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

        return view('business.bookings.index', compact('bookings', 'status', 'business', 'businessAdmin'));
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
        ]);

        // Update amount paid
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

        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json(['customers' => []]);
        }

        $business = $businessAdmin->business;
        $customers = $business->customers()
            ->where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('full_name', 'like', "%{$query}%")
                  ->orWhere('company_name', 'like', "%{$query}%")
                  ->orWhere('mobile_number', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->select('id', 'full_name', 'company_name', 'mobile_number', 'email')
            ->limit(10)
            ->get();

        return response()->json(['customers' => $customers]);
    }
}