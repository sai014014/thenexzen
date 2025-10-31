@extends('business.layouts.app')

@section('title', 'Booking Details')
@section('page-title', 'Booking Details')

@push('styles')
    @vite(['resources/css/bookings.css'])
@endpush

@section('content')
<div class="row">
	<div class="col-lg-8">
		<div class="card mb-3">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-start">
					<div>
						<h5 class="mb-1">Booking #{{ $booking->booking_number ?? $booking->id }}</h5>
						<div class="text-muted small">Status:
							<span class="badge bg-{{ $booking->status === 'upcoming' ? 'warning' : ($booking->status === 'ongoing' ? 'primary' : ($booking->status === 'completed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'secondary'))) }}">{{ ucfirst($booking->status) }}</span>
						</div>
					</div>
					<div class="d-flex gap-2">
						@if ($booking->status === 'upcoming')
							<a href="{{ route('business.bookings.edit', $booking) }}" class="btn btn-outline-secondary btn-pill">Edit</a>
							<form action="{{ route('business.bookings.start', $booking) }}" method="POST">
								@csrf
								<button type="submit" class="btn btn-primary btn-pill">Start Vehicle</button>
							</form>
							<button type="button" class="btn btn-outline-danger btn-pill" data-bs-toggle="modal" data-bs-target="#cancelBookingModal">Cancel Booking</button>
						@endif
						@if ($booking->status === 'ongoing')
							<button type="button" class="btn btn-success btn-pill" data-bs-toggle="modal" data-bs-target="#completeBookingModal">Complete Booking</button>
							<button type="button" class="btn btn-outline-danger btn-pill" data-bs-toggle="modal" data-bs-target="#cancelBookingModal">Cancel Booking</button>
						@endif
					</div>
				</div>
				<hr>
				<div class="row g-3">
					<div class="col-md-6">
						<div class="text-muted small mb-1">Pickup</div>
						<div class="small">{{ optional($booking->start_date_time)->format('D, d M Y h:i A') }}</div>
					</div>
					<div class="col-md-6">
						<div class="text-muted small mb-1">Drop-off</div>
						<div class="small">{{ optional($booking->end_date_time)->format('D, d M Y h:i A') }}</div>
					</div>
					<div class="col-md-6">
						<div class="text-muted small mb-1">Customer</div>
						<div class="small fw-semibold">{{ $booking->customer->full_name ?? $booking->customer->company_name ?? '-' }}</div>
						<div class="text-muted small">{{ $booking->customer->mobile_number ?? '' }} @if(!empty($booking->customer->email_address)) • {{ $booking->customer->email_address }} @endif</div>
					</div>
					<div class="col-md-6">
						<div class="text-muted small mb-1">Vehicle</div>
						<div class="small fw-semibold">{{ $booking->vehicle->vehicle_make ?? '' }} {{ $booking->vehicle->vehicle_model ?? '' }}</div>
						<div class="text-muted small">Reg: {{ $booking->vehicle->vehicle_number ?? '-' }}</div>
					</div>
				</div>
			</div>
		</div>

		@if($booking->status === 'cancelled' && $booking->cancellation_reason)
		<div class="card mb-3 border-danger">
			<div class="card-body">
				<div class="d-flex align-items-center mb-2">
					<i class="fas fa-times-circle text-danger me-2 fs-5"></i>
					<h6 class="mb-0 text-danger">Cancellation Details</h6>
				</div>
				<hr class="my-2">
				<div class="mb-2">
					<div class="text-muted small mb-1">Cancelled On</div>
					<div class="small">{{ optional($booking->cancelled_at)->format('D, d M Y h:i A') ?? '—' }}</div>
				</div>
				<div>
					<div class="text-muted small mb-1">Cancellation Reason</div>
					<div class="small">{!! nl2br(e($booking->cancellation_reason)) !!}</div>
				</div>
			</div>
		</div>
		@endif

		<div class="card">
			<div class="card-body">
				<h6 class="mb-3">Billing</h6>
				<div class="d-flex justify-content-between small"><span>Base Rental</span><span>₹{{ number_format($booking->base_rental_price ?? 0, 2) }}</span></div>
				<div class="d-flex justify-content-between small"><span>Additional Charges</span><span>₹{{ number_format($booking->extra_charges ?? 0, 2) }}</span></div>
				<hr class="my-2">
				<div class="d-flex justify-content-between small"><span>Total Amount</span><span>₹{{ number_format($booking->total_amount ?? 0, 2) }}</span></div>
				<div class="d-flex justify-content-between small"><span>Amount Paid</span><span>₹{{ number_format($booking->amount_paid ?? 0, 2) }}</span></div>
				<div class="d-flex justify-content-between fw-semibold"><span>Amount Due</span><span>₹{{ number_format($booking->amount_due ?? 0, 2) }}</span></div>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="card">
			<div class="card-body">
				<h6 class="mb-3">Notes</h6>
				<div class="small">{!! nl2br(e($booking->notes ?? '—')) !!}</div>
			</div>
		</div>
	</div>
</div>

<!-- Cancel Booking Modal -->
@if (in_array($booking->status, ['upcoming', 'ongoing']))
<div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Cancel Booking</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{ route('business.bookings.cancel', $booking) }}" method="POST" id="cancelBookingForm">
				@csrf
				<div class="modal-body">
					<div class="alert alert-warning">
						<i class="fas fa-exclamation-triangle me-2"></i>
						<strong>Warning:</strong> This action cannot be undone. The booking will be marked as cancelled.
					</div>
					<div class="mb-3">
						<label class="form-label">Cancellation Reason <span class="text-danger">*</span></label>
						<textarea name="cancellation_reason" id="cancellation_reason" class="form-control" rows="4" placeholder="Please provide a reason for cancelling this booking..." required maxlength="500"></textarea>
						<small class="text-muted">
							<span id="charCount">0</span> / 500 characters
						</small>
					</div>
					<div class="small text-muted">
						<p class="mb-1"><strong>Booking Details:</strong></p>
						<ul class="mb-0 ps-3">
							<li>Booking #{{ $booking->booking_number ?? $booking->id }}</li>
							<li>Customer: {{ $booking->customer->full_name ?? $booking->customer->company_name ?? '-' }}</li>
							<li>Vehicle: {{ $booking->vehicle->vehicle_make ?? '' }} {{ $booking->vehicle->vehicle_model ?? '' }}</li>
							@if($booking->status === 'ongoing')
							<li class="text-warning">Note: Vehicle will be marked as available after cancellation</li>
							@endif
						</ul>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary btn-pill" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-danger btn-pill">Cancel Booking</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endif

@if ($booking->status === 'ongoing')
<!-- Complete Booking Modal -->
<div class="modal fade" id="completeBookingModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Complete Booking</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="{{ route('business.bookings.complete', $booking) }}" method="POST" id="completeBookingForm">
				@csrf
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label">Actual Return Date & Time</label>
						<input type="datetime-local" name="actual_return_datetime" id="actual_return_datetime" class="form-control" value="{{ optional($booking->end_date_time)->format('Y-m-d\\TH:i') }}" />
					</div>
					<div class="mb-3">
						<div class="d-flex justify-content-between align-items-center mb-2">
							<label class="form-label mb-0">Additional Charges</label>
							<button type="button" class="btn btn-sm btn-outline-primary" id="addCompleteCharge">+ Add</button>
						</div>
						<div id="completeCharges" class="vstack gap-2"></div>
					</div>
					<div class="row g-3">
						<div class="col-md-6">
							<label class="form-label">Final Amount Received</label>
							<input type="number" step="0.01" name="final_amount_paid" id="final_amount_paid" class="form-control" required />
						</div>
						<div class="col-md-6">
							<label class="form-label">Amount Due (after recalculation)</label>
							<input type="text" id="final_amount_due" class="form-control" value="₹{{ number_format($booking->amount_due ?? 0, 2) }}" readonly />
						</div>
					</div>
					<hr class="my-3">
					<div class="small text-muted">Recalculated using per-day rent × days + additional charges, minus payments.</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary btn-pill" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-success btn-pill">Mark as Completed</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endif
@endsection

@push('scripts')
<script>
(function(){
	const perDay = {{ (float)($booking->base_rental_price ?? ($booking->vehicle->rental_price_24h ?? 0)) }};
	const startAt = new Date(@json(optional($booking->start_date_time)?->format('c')));
	let paidSoFar = {{ (float)($booking->amount_paid ?? 0) }};
	let existingExtras = {{ (float)($booking->extra_charges ?? 0) }};

	function computeDays(end) {
		const ms = Math.max(0, end - startAt);
		const hours = ms / 36e5;
		return Math.max(1, Math.ceil(hours / 24));
	}
	function formatINR(n){ return '₹' + Number(n).toFixed(2); }

	function recomputeFinal(){
		const endInput = document.getElementById('actual_return_datetime');
		const finalPaidInput = document.getElementById('final_amount_paid');
		const dueInput = document.getElementById('final_amount_due');
		let endAt = endInput && endInput.value ? new Date(endInput.value) : new Date(@json(optional($booking->end_date_time)?->format('c')));
		const days = computeDays(endAt);
		let addedCharges = 0;
		document.querySelectorAll('#completeCharges .charge-amount').forEach(inp=>{ addedCharges += parseFloat(inp.value||0); });
		const total = perDay * days + existingExtras + addedCharges;
		const finalPaid = parseFloat(finalPaidInput?.value || 0);
		const due = Math.max(0, total - (paidSoFar + finalPaid));
		if (dueInput) dueInput.value = formatINR(due);
	}
	
	const addBtn = document.getElementById('addCompleteCharge');
	if (addBtn){
		addBtn.addEventListener('click', ()=>{
			const row = document.createElement('div');
			row.className = 'row g-2 align-items-end';
			row.innerHTML = '\
				<div class="col-7">\
					<input type="text" class="form-control form-control-sm charge-desc" placeholder="Description">\
				</div>\
				<div class="col-4">\
					<input type="number" step="0.01" class="form-control form-control-sm charge-amount" placeholder="0.00">\
				</div>\
				<div class="col-1">\
					<button type="button" class="btn btn-sm btn-outline-danger remove-charge">×</button>\
				</div>';
			document.getElementById('completeCharges').appendChild(row);
			row.querySelector('.charge-amount').addEventListener('input', recomputeFinal);
			row.querySelector('.remove-charge').addEventListener('click', ()=>{ row.remove(); recomputeFinal(); });
		});
	}
	
	['actual_return_datetime','final_amount_paid'].forEach(id=>{
		const el = document.getElementById(id); if (el) el.addEventListener('input', recomputeFinal);
	});

	document.getElementById('completeBookingModal')?.addEventListener('shown.bs.modal', recomputeFinal);

	// On submit, serialize additional charges fields
	document.getElementById('completeBookingForm')?.addEventListener('submit', function(e){
		const charges = [];
		document.querySelectorAll('#completeCharges .row').forEach(row=>{
			const desc = row.querySelector('.charge-desc')?.value || '';
			const amt = parseFloat(row.querySelector('.charge-amount')?.value || 0);
			if (amt > 0) charges.push({ description: desc, amount: amt });
		});
		const hidden = document.createElement('input');
		hidden.type = 'hidden';
		hidden.name = 'additional_charges';
		hidden.value = JSON.stringify(charges);
		this.appendChild(hidden);
	});

	// Cancel Booking Form - Character count
	const cancelReasonTextarea = document.getElementById('cancellation_reason');
	const charCount = document.getElementById('charCount');
	if (cancelReasonTextarea && charCount) {
		cancelReasonTextarea.addEventListener('input', function(){
			charCount.textContent = this.value.length;
			if (this.value.length > 500) {
				charCount.classList.add('text-danger');
			} else {
				charCount.classList.remove('text-danger');
			}
		});
		
		// Initialize count on modal show
		document.getElementById('cancelBookingModal')?.addEventListener('shown.bs.modal', function(){
			const len = cancelReasonTextarea.value.length;
			charCount.textContent = len;
			if (len > 500) {
				charCount.classList.add('text-danger');
			}
		});
		
		// Validate on submit
		document.getElementById('cancelBookingForm')?.addEventListener('submit', function(e){
			const reason = cancelReasonTextarea.value.trim();
			if (!reason) {
				e.preventDefault();
				alert('Please provide a cancellation reason.');
				cancelReasonTextarea.focus();
				return false;
			}
			if (reason.length > 500) {
				e.preventDefault();
				alert('Cancellation reason cannot exceed 500 characters.');
				cancelReasonTextarea.focus();
				return false;
			}
		});
	}
})();
</script>
@endpush
