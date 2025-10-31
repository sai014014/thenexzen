<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Booking Summary</h6>
    </div>
    <div class="card-body" id="summaryBox">
        <div class="row">
            <div class="col-6">
                <div class="text-muted small">Pickup</div>
                <div id="sumPickupDate" class="small"></div>
                <div id="sumPickupTime" class="small"></div>
                <div id="sumPickupLoc" class="small"></div>
            </div>
            <div class="col-6">
                <div class="text-muted small">Drop</div>
                <div id="sumDropDate" class="small"></div>
                <div id="sumDropTime" class="small"></div>
                <div id="sumDropLoc" class="small"></div>
            </div>
        </div>
        <hr>
        <div id="sumVehicle" class="mb-2 small d-none">
            <div class="fw-semibold" id="sumVehicleName"></div>
            <div class="text-muted" id="sumVehicleAmount"></div>
        </div>
        <div id="sumCustomer" class="mb-2 small d-none">
            <div class="text-muted small">Customer</div>
            <div class="fw-semibold" id="sumCustomerName"></div>
        </div>
        <div class="d-flex justify-content-between small"><span>Sub Total</span><span id="sumSubTotal">₹0.00</span></div>
        <div class="d-flex justify-content-between small text-muted"><span>Discount</span><span id="sumDiscount">₹0.00</span></div>
        <div class="d-flex justify-content-between small"><span>Advance Payment</span><span id="sumAdvance">₹0.00</span></div>
        <hr>
        <div class="d-flex justify-content-between fw-semibold"><span>Amount Due</span><span id="sumDue">₹0.00</span></div>
    </div>
</div>

