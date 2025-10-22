@extends('business.layouts.app')

@section('title', 'Booking Details - ' . $booking->booking_number)
@section('page-title', 'Booking Details')

@push('styles')
    @vite(['resources/css/bookings.css'])
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Section -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>Booking #{{ $booking->booking_number }}
                        </h5>
                        <small class="text-muted">Created on {{ $booking->created_at->format('M d, Y H:i') }}</small>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="badge {{ $booking->status_badge_class }} fs-6">
                            {{ $booking->status_label }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Booking Information -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Booking Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><strong>Booking ID:</strong></div>
                            <div class="col-sm-8">#{{ $booking->booking_number }}</div>
                            
                            <div class="col-sm-4"><strong>Start Date & Time:</strong></div>
                            <div class="col-sm-8">{{ $booking->start_date_time->format('M d, Y H:i') }}</div>
                            
                            <div class="col-sm-4"><strong>End Date & Time:</strong></div>
                            <div class="col-sm-8">{{ $booking->end_date_time->format('M d, Y H:i') }}</div>
                            
                            <div class="col-sm-4"><strong>Duration:</strong></div>
                            <div class="col-sm-8">{{ $booking->formatted_duration }}</div>
                            
                            <div class="col-sm-4"><strong>Status:</strong></div>
                            <div class="col-sm-8">
                                <span class="badge {{ $booking->status_badge_class }}">
                                    {{ $booking->status_label }}
                                </span>
                            </div>
                            
                            @if($booking->started_at)
                            <div class="col-sm-4"><strong>Started At:</strong></div>
                            <div class="col-sm-8">{{ $booking->started_at->format('M d, Y H:i') }}</div>
                            @endif
                            
                            @if($booking->completed_at)
                            <div class="col-sm-4"><strong>Completed At:</strong></div>
                            <div class="col-sm-8">{{ $booking->completed_at->format('M d, Y H:i') }}</div>
                            @endif
                            
                            @if($booking->cancelled_at)
                            <div class="col-sm-4"><strong>Cancelled At:</strong></div>
                            <div class="col-sm-8">{{ $booking->cancelled_at->format('M d, Y H:i') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-user me-2"></i>Customer Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><strong>Name:</strong></div>
                            <div class="col-sm-8">{{ $booking->customer->display_name }}</div>
                            
                            <div class="col-sm-4"><strong>Type:</strong></div>
                            <div class="col-sm-8">
                                @if($booking->customer->customer_type === 'individual')
                                    <span class="badge bg-info">Individual</span>
                                @else
                                    <span class="badge bg-warning">Corporate</span>
                                @endif
                            </div>
                            
                            <div class="col-sm-4"><strong>Mobile:</strong></div>
                            <div class="col-sm-8">{{ $booking->customer->mobile_number }}</div>
                            
                            @if($booking->customer->email_address)
                            <div class="col-sm-4"><strong>Email:</strong></div>
                            <div class="col-sm-8">{{ $booking->customer->email_address }}</div>
                            @endif
                            
                            @if($booking->customer->alternate_contact_number)
                            <div class="col-sm-4"><strong>Alternate:</strong></div>
                            <div class="col-sm-8">{{ $booking->customer->alternate_contact_number }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle Information -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-car me-2"></i>Vehicle Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><strong>Make & Model:</strong></div>
                            <div class="col-sm-8">{{ $booking->vehicle->vehicle_make }} {{ $booking->vehicle->vehicle_model }}</div>
                            
                            <div class="col-sm-4"><strong>Registration:</strong></div>
                            <div class="col-sm-8">{{ $booking->vehicle->vehicle_number }}</div>
                            
                            <div class="col-sm-4"><strong>Type:</strong></div>
                            <div class="col-sm-8">{{ ucfirst(str_replace('_', ' ', $booking->vehicle->vehicle_type)) }}</div>
                            
                            @if($booking->vehicle->seating_capacity)
                            <div class="col-sm-4"><strong>Seating:</strong></div>
                            <div class="col-sm-8">{{ $booking->vehicle->seating_capacity }} Seater</div>
                            @endif
                            
                            <div class="col-sm-4"><strong>Fuel Type:</strong></div>
                            <div class="col-sm-8">{{ ucfirst($booking->vehicle->fuel_type) }}</div>
                            
                            <div class="col-sm-4"><strong>Transmission:</strong></div>
                            <div class="col-sm-8">{{ ucfirst($booking->vehicle->transmission_type) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Payment Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><strong>Base Rental Price:</strong></div>
                            <div class="col-sm-8">₹{{ number_format($booking->base_rental_price, 2) }}</div>
                            
                            <div class="col-sm-4"><strong>Extra Charges:</strong></div>
                            <div class="col-sm-8">₹{{ number_format($booking->extra_charges, 2) }}</div>
                            
                            <div class="col-sm-4"><strong>Total Amount:</strong></div>
                            <div class="col-sm-8">
                                <strong class="text-primary">₹{{ number_format($booking->total_amount, 2) }}</strong>
                            </div>
                            
                            <div class="col-sm-4"><strong>Amount Paid:</strong></div>
                            <div class="col-sm-8">
                                <span class="text-success">₹{{ number_format($booking->amount_paid, 2) }}</span>
                            </div>
                            
                            <div class="col-sm-4"><strong>Amount Due:</strong></div>
                            <div class="col-sm-8">
                                <span class="{{ $booking->amount_due > 0 ? 'text-warning' : 'text-success' }}">
                                    <strong>₹{{ number_format($booking->amount_due, 2) }}</strong>
                                </span>
                            </div>
                            
                            @if($booking->advance_amount > 0)
                            <div class="col-sm-4"><strong>Advance Amount:</strong></div>
                            <div class="col-sm-8">₹{{ number_format($booking->advance_amount, 2) }}</div>
                            
                            @if($booking->advance_payment_method)
                            <div class="col-sm-4"><strong>Advance Method:</strong></div>
                            <div class="col-sm-8">{{ $booking->advance_payment_method_label }}</div>
                            @endif
                            @endif
                            
                            @if($booking->payment_method)
                            <div class="col-sm-4"><strong>Payment Method:</strong></div>
                            <div class="col-sm-8">{{ $booking->payment_method_label }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            @if($booking->customer_notes || $booking->notes)
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Notes
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($booking->customer_notes)
                        <div class="mb-3">
                            <strong>Customer Notes:</strong>
                            <p class="text-muted">{{ $booking->customer_notes }}</p>
                        </div>
                        @endif
                        
                        @if($booking->notes)
                        <div class="mb-0">
                            <strong>Internal Notes:</strong>
                            <p class="text-muted">{{ $booking->notes }}</p>
                        </div>
                        @endif
                        
                        @if($booking->cancellation_reason)
                        <div class="mb-0">
                            <strong>Cancellation Reason:</strong>
                            <p class="text-danger">{{ $booking->cancellation_reason }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('business.bookings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Bookings
                            </a>
                            
                            <div class="btn-group" role="group">
                                @if($booking->status === 'upcoming')
                                <a href="{{ route('business.bookings.edit', $booking) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Edit Booking
                                </a>
                                <button type="button" class="btn btn-primary" onclick="startBooking()">
                                    <i class="fas fa-play me-2"></i>Start Booking
                                </button>
                                <button type="button" class="btn btn-danger" onclick="cancelBooking()">
                                    <i class="fas fa-times me-2"></i>Cancel Booking
                                </button>
                                @elseif($booking->status === 'ongoing')
                                <button type="button" class="btn btn-success" onclick="completeBooking()">
                                    <i class="fas fa-check me-2"></i>Complete Booking
                                </button>
                                <button type="button" class="btn btn-danger" onclick="cancelBooking()">
                                    <i class="fas fa-times me-2"></i>Cancel Booking
                                </button>
                                @elseif($booking->status === 'completed')
                                <button type="button" class="btn btn-primary" onclick="printInvoice()">
                                    <i class="fas fa-print me-2"></i>Print Invoice
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Start Booking Modal -->
<div class="modal fade" id="startBookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Start Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to start this booking?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    This will change the booking status to "Ongoing".
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('business.bookings.start', $booking) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary">Start Booking</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Complete Booking Modal -->
<div class="modal fade" id="completeBookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('business.bookings.complete', $booking) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="final_amount_paid" class="form-label">Final Amount Paid *</label>
                        <input type="number" class="form-control" id="final_amount_paid" name="final_amount_paid" 
                               value="{{ $booking->amount_due }}" min="0" step="0.01" required>
                        <div class="form-text">Amount due: ₹{{ number_format($booking->amount_due, 2) }}</div>
                    </div>
                    <div class="mb-3">
                        <label for="completion_notes" class="form-label">Completion Notes</label>
                        <textarea class="form-control" id="completion_notes" name="completion_notes" rows="3" 
                                  placeholder="Any notes about the completion..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Complete Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Booking Modal -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('business.bookings.cancel', $booking) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Cancellation Reason *</label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" 
                                  placeholder="Please provide a reason for cancellation..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Cancel Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function startBooking() {
    const modal = new bootstrap.Modal(document.getElementById('startBookingModal'));
    modal.show();
}

function completeBooking() {
    const modal = new bootstrap.Modal(document.getElementById('completeBookingModal'));
    modal.show();
}

function cancelBooking() {
    const modal = new bootstrap.Modal(document.getElementById('cancelBookingModal'));
    modal.show();
}

function printInvoice() {
    // Implement print functionality
    window.print();
}
</script>
@endpush
