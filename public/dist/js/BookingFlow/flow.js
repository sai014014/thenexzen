(() => {
  const state = {
    step: 1,
    data: {},
  };

  function qs(sel) { return document.querySelector(sel); }
  function qsa(sel) { return Array.from(document.querySelectorAll(sel)); }

  function showStep(step, skipValidation = false) {
    // Validate previous steps before allowing navigation (unless skipValidation is true)
    if (!skipValidation) {
      // Check step 1 completion before allowing step 2+
      if (step >= 2) {
        const pickupDt = qs('#pickup_datetime')?.value;
        const dropoffDt = qs('#dropoff_datetime')?.value;
        const pickupLoc = qs('#pickup_location')?.value;
        const dropoffLoc = qs('#dropoff_location')?.value;
        
        if (!pickupDt || !dropoffDt || !pickupLoc || !dropoffLoc) {
          alert('Please complete Step 1 (pickup/drop-off dates and locations) before proceeding.');
          showStep(1);
          return;
        }
      }
      
      // Check step 2 completion before allowing step 3+
      if (step >= 3) {
        if (!state.data.selectedVehicleId) {
          alert('Please select a vehicle in Step 2 before proceeding.');
          showStep(2, true);
          return;
        }
      }
      
      // Check step 3 completion before allowing step 4+
      if (step >= 4) {
        const customerId = state.data.selectedCustomerId || selectedCustomerId;
        if (!customerId) {
          alert('Please select a customer in Step 3 before proceeding.');
          showStep(3, true);
          return;
        }
      }
      
      // Check step 4 completion before allowing step 5
      if (step >= 5) {
        const rent24h = qs('#rent_24h')?.value;
        if (!rent24h || parseFloat(rent24h) <= 0) {
          alert('Please complete Step 4 (vehicle rental charges) before proceeding.');
          showStep(4, true);
          return;
        }
      }
    }
    
    state.step = step;
    qsa('.flow-step').forEach(s => s.classList.add('d-none'));
    const el = qs(`#step-${step}`);
    if (el) el.classList.remove('d-none');
    qsa('.progress-steps .step').forEach(s => s.classList.toggle('active', Number(s.dataset.step) === step));
    
    // Initialize step-specific functionality
    if (step === 3) {
      setTimeout(() => {
        initCustomerDropdown();
      }, 100);
    }
    if (step === 4) {
      setTimeout(() => {
        loadVehicleDataForBilling();
      }, 100);
    }
  }

  async function saveStep(stepPayload) {
    try {
      await fetch(window.bookingFlow?.saveStepUrl || '/business/bookings/flow/save-step', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify(stepPayload),
      });
    } catch (e) {
      // no-op
    }
  }

  // Restore draft on load - auto-restore if draft exists
  function initDraftRestore() {
    const draft = window.bookingFlow?.draft;
    // Check if draft exists and has data
    const hasDraft = draft?.exists && draft?.data && (
      draft.data.step_1 || draft.data.step_2 || draft.data.step_3 || 
      draft.data.step_4 || draft.data.step_5
    );
    
    if (hasDraft) {
      // Wait for DOM to be fully ready
      const waitForReady = () => {
        // Check if critical elements exist
        if (!qs('#pickup_datetime') || !qs('#step-1')) {
          setTimeout(waitForReady, 100);
          return;
        }
        
        // Restore draft data
        applyDraft(draft);
        
        // Show modal as option to start fresh (after a small delay)
        setTimeout(() => {
          const modalEl = document.getElementById('resumeDraftModal');
          if (modalEl && window.bootstrap) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
            const resumeBtn = document.getElementById('resumeDraftBtn');
            const newBtn = document.getElementById('newBookingBtn');
            
            // Resume button - draft already restored, just hide modal
            if (resumeBtn) {
              resumeBtn.addEventListener('click', () => {
                modal.hide();
                // Re-apply draft in case something was missed
                setTimeout(() => {
                  applyDraft(draft);
                }, 100);
              });
            }
            
            // New booking button - clear draft and reset
            if (newBtn) {
              newBtn.addEventListener('click', async () => {
                try {
                  await fetch(window.bookingFlow?.clearDraftUrl || '/business/bookings/flow/clear-draft', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/json',
                      'X-Requested-With': 'XMLHttpRequest',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                  });
                } catch (_) {}
                // Reset UI to step 1
                state.step = 1;
                state.data = {};
                selectedCustomerId = null;
                selectedCustomerData = null;
                // Clear form fields
                if (qs('#pickup_datetime')) qs('#pickup_datetime').value = '';
                if (qs('#dropoff_datetime')) qs('#dropoff_datetime').value = '';
                if (qs('#pickup_location')) qs('#pickup_location').value = '';
                if (qs('#dropoff_location')) qs('#dropoff_location').value = '';
                showStep(1);
                modal.hide();
                // Reload page to clear draft completely
                window.location.reload();
              });
            }
          }
        }, 500);
      };
      
      waitForReady();
    }
  }

  // Run on DOM ready (works with defer attribute)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDraftRestore);
  } else {
    // DOM already loaded
    setTimeout(initDraftRestore, 200);
  }

  function applyDraft(draft) {
    if (!draft || !draft.data) {
      console.warn('No draft data to apply');
      return;
    }
    
    const d = draft.data || {};
    
    // Step 1 - Restore date and location fields (restore even if empty)
    if (d.step_1) {
      if (qs('#pickup_datetime')) {
        qs('#pickup_datetime').value = d.step_1.pickup_datetime || '';
      }
      if (qs('#dropoff_datetime')) {
        qs('#dropoff_datetime').value = d.step_1.dropoff_datetime || '';
      }
      if (qs('#pickup_location')) {
        qs('#pickup_location').value = d.step_1.pickup_location || '';
      }
      if (qs('#dropoff_location')) {
        qs('#dropoff_location').value = d.step_1.dropoff_location || '';
      }
      // Trigger change events to update summary
      ['pickup_datetime', 'dropoff_datetime', 'pickup_location', 'dropoff_location'].forEach(id => {
        const el = qs('#' + id);
        if (el && el.value) {
          el.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });
      updateSummaryDates(d.step_1);
    }
    
    // Step 2 - Restore selected vehicle
    if (d.step_2 && d.step_2.vehicle_id) {
      state.data.selectedVehicleId = d.step_2.vehicle_id;
    }
    
    // Step 3 - Restore selected customer
    if (d.step_3 && d.step_3.customer_id) {
      state.data.selectedCustomerId = d.step_3.customer_id;
      selectedCustomerId = d.step_3.customer_id;
      if (d.step_3.customer_name) {
        const inp = qs('#booking_customer_select') || qs('#customer_select');
        if (inp) {
          inp.value = d.step_3.customer_name;
          selectedCustomerData = {
            id: d.step_3.customer_id,
            name: d.step_3.customer_name,
            phone: d.step_3.customer_phone || '',
            email: d.step_3.customer_email || '',
          };
        }
      }
    }
    
    // Step 4 - Restore billing fields (restore even if empty or zero)
    if (d.step_4) {
      if (qs('#rent_24h')) {
        qs('#rent_24h').value = d.step_4.rent_24h || '';
      }
      if (qs('#km_limit')) {
        qs('#km_limit').value = d.step_4.km_limit || '';
      }
      if (qs('#extra_per_hour')) {
        qs('#extra_per_hour').value = d.step_4.extra_per_hour || '';
      }
      if (qs('#extra_per_km')) {
        qs('#extra_per_km').value = d.step_4.extra_per_km || '';
      }
      if (qs('#discount_amount')) {
        qs('#discount_amount').value = d.step_4.discount_amount || '';
      }
      if (qs('#discount_type')) {
        qs('#discount_type').value = d.step_4.discount_type || 'amount';
      }
      if (qs('#advance_payment')) {
        qs('#advance_payment').value = d.step_4.advance_payment || '';
      }
      if (qs('#payment_method')) {
        qs('#payment_method').value = d.step_4.payment_method || '';
      }
      // Restore additional charges if stored
      if (d.step_4.additional_charges && Array.isArray(d.step_4.additional_charges) && d.step_4.additional_charges.length > 0) {
        // Restore additional charges rows
        const addBtn = qs('#addAdditionalCharge');
        if (addBtn && additionalChargeCounter === 0) {
          d.step_4.additional_charges.forEach((charge, idx) => {
            if (idx > 0) {
              addBtn.click(); // Trigger add row for each charge after first
            }
            setTimeout(() => {
              const rows = qsa('.charge-description');
              if (rows[idx]) {
                rows[idx].value = charge.description || '';
              }
              const amounts = qsa('.charge-amount');
              if (amounts[idx]) {
                amounts[idx].value = charge.amount || '';
              }
            }, 100);
          });
        }
      }
    }
    
    // Determine the correct step to navigate to by validating each step
    // Start from step 1 and find the first incomplete step
    let targetStep = 1;
    
    // Check if step 1 is complete
    const step1Complete = d.step_1 && 
      d.step_1.pickup_datetime && 
      d.step_1.dropoff_datetime && 
      d.step_1.pickup_location && 
      d.step_1.dropoff_location;
    
    if (step1Complete) {
      targetStep = 2;
      
      // Check if step 2 is complete
      const step2Complete = d.step_2 && d.step_2.vehicle_id;
      
      if (step2Complete) {
        targetStep = 3;
        
        // Check if step 3 is complete
        const step3Complete = d.step_3 && d.step_3.customer_id;
        
        if (step3Complete) {
          targetStep = 4;
          
          // Check if step 4 is complete (at least rent_24h should be filled)
          const step4Complete = d.step_4 && d.step_4.rent_24h && parseFloat(d.step_4.rent_24h) > 0;
          
          if (step4Complete) {
            targetStep = 5;
          }
        }
      }
    }
    
    // Show the validated step (first incomplete step or last complete step)
    // Skip validation since we've already validated which step to show
    showStep(targetStep, true);
    
    // Load vehicles if on step 2 or later
    if (targetStep >= 2) {
      setTimeout(() => {
        fetchVehicles();
      }, 200);
    }
    
    // Load vehicle billing data if on step 4
    if (targetStep === 4) {
      setTimeout(() => {
        loadVehicleDataForBilling();
        // Re-apply Step 4 values after vehicle data loads (in case vehicle data overwrote them)
        if (d.step_4) {
          setTimeout(() => {
            if (qs('#rent_24h') && d.step_4.rent_24h) qs('#rent_24h').value = d.step_4.rent_24h;
            if (qs('#km_limit') && d.step_4.km_limit) qs('#km_limit').value = d.step_4.km_limit;
            if (qs('#extra_per_hour') && d.step_4.extra_per_hour) qs('#extra_per_hour').value = d.step_4.extra_per_hour;
            if (qs('#extra_per_km') && d.step_4.extra_per_km) qs('#extra_per_km').value = d.step_4.extra_per_km;
            if (qs('#discount_amount') && d.step_4.discount_amount) qs('#discount_amount').value = d.step_4.discount_amount;
            if (qs('#discount_type') && d.step_4.discount_type) qs('#discount_type').value = d.step_4.discount_type;
            if (qs('#advance_payment') && d.step_4.advance_payment) qs('#advance_payment').value = d.step_4.advance_payment;
            if (qs('#payment_method') && d.step_4.payment_method) qs('#payment_method').value = d.step_4.payment_method;
          }, 600);
        }
      }, 400);
    }
    
    // Initialize customer dropdown if on step 3
    if (targetStep === 3) {
      setTimeout(() => {
        initCustomerDropdown();
      }, 200);
    }
  }

  // Step 1
  const step1Next = qs('#step1Next');
  if (step1Next) {
    step1Next.addEventListener('click', async () => {
      const payload = {
        step: 1,
        pickup_datetime: qs('#pickup_datetime').value,
        dropoff_datetime: qs('#dropoff_datetime').value,
        pickup_location: qs('#pickup_location').value,
        dropoff_location: qs('#dropoff_location').value,
      };
      // minimal validation
      if (!payload.pickup_datetime || !payload.dropoff_datetime || !payload.pickup_location || !payload.dropoff_location) {
        alert('Please fill pickup/drop dates and locations');
        return;
      }
      const pd = new Date(payload.pickup_datetime);
      const dd = new Date(payload.dropoff_datetime);
      if (isFinite(pd) && isFinite(dd) && dd <= pd) {
        alert('Drop-off must be after pickup');
        return;
      }
      await saveStep(payload);
      updateSummaryDates(payload);
      showStep(2);
      fetchVehicles();
    });
  }

  // Autosave Step 1 on change
  ['#pickup_datetime','#dropoff_datetime','#pickup_location','#dropoff_location'].forEach(sel => {
    const el = qs(sel);
    if (el) {
      el.addEventListener('change', () => {
        saveStep({
          step: 1,
          pickup_datetime: qs('#pickup_datetime').value,
          dropoff_datetime: qs('#dropoff_datetime').value,
          pickup_location: qs('#pickup_location').value,
          dropoff_location: qs('#dropoff_location').value,
        });
      });
    }
  });

  // Navigation buttons
  qsa('[data-prev]').forEach(btn => {
    btn.addEventListener('click', () => {
      const prevStep = Math.max(1, state.step - 1);
      // Going back doesn't need validation - skip it
      showStep(prevStep, true);
      if (prevStep === 3) setTimeout(initCustomerDropdown, 100);
      if (prevStep === 4) setTimeout(loadVehicleDataForBilling, 100);
    });
  });
  
  // Generic next buttons - validation handled in showStep()
  qsa('[data-next]').forEach(btn => {
    // Skip if it's step4Next or step3Next or step1Next - they have their own handlers
    if (btn.id === 'step4Next' || btn.id === 'step3Next' || btn.id === 'step1Next') return;
    btn.addEventListener('click', () => {
      const nextStep = Math.min(5, state.step + 1);
      // showStep() will validate previous steps
      showStep(nextStep);
    });
  });

  // Vehicles list
  let currentSort = 'price_asc';
  async function fetchVehicles() {
    const pickupDt = qs('#pickup_datetime')?.value;
    const dropoffDt = qs('#dropoff_datetime')?.value;
    if (!pickupDt || !dropoffDt) {
      qs('#vehicleList').innerHTML = '<p class="text-muted">Please complete Step 1 first.</p>';
      return;
    }
    const params = new URLSearchParams({
      pickup_datetime: pickupDt,
      dropoff_datetime: dropoffDt,
      transmission: qs('#filterTransmission')?.value || '',
      seats: qs('#filterSeats')?.value || '',
      fuel: qs('#filterFuel')?.value || '',
      sort: currentSort || '',
    });
    try {
      qs('#vehicleList').innerHTML = '<p class="text-muted">Loading vehicles...</p>';
      const url = window.bookingFlow?.vehiclesUrl || '/business/bookings/flow/vehicles/list';
      const res = await fetch(url + '?' + params.toString(), { 
        headers: { 
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });
      if (!res.ok) {
        throw new Error(`HTTP ${res.status}: ${res.statusText}`);
      }
      const html = await res.text();
      qs('#vehicleList').innerHTML = html || '<p class="text-muted">No vehicles available for selected dates.</p>';
      
      // Highlight previously selected vehicle if draft exists
      const selectedVehicleId = state.data.selectedVehicleId;
      if (selectedVehicleId) {
        const selectedCard = qs(`[data-vehicle-id="${selectedVehicleId}"]`);
        if (selectedCard) {
          selectedCard.classList.add('selected', 'border-primary');
        }
      }
      
      qs('#vehicleList').querySelectorAll('.select-vehicle, .book-now-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
          e.preventDefault();
          const card = e.target.closest('.vehicle-card') || e.target.closest('[data-vehicle-id]');
          const id = card?.dataset.vehicleId || card?.getAttribute('data-vehicle-id');
          if (!id) return;
          
          // Remove previous selection
          qsa('.vehicle-card.selected, [data-vehicle-id].selected').forEach(c => {
            c.classList.remove('selected', 'border-primary');
          });
          
          // Mark new selection
          if (card) {
            card.classList.add('selected', 'border-primary');
          }
          
          // Save selection
          state.data.selectedVehicleId = id;
          await saveStep({ step: 2, vehicle_id: id });
          updateSummaryVehicle(id);
          showStep(3); // This will trigger initCustomerDropdown
        });
      });
    } catch (e) {
      qs('#vehicleList').innerHTML = '<p class="text-danger">Error loading vehicles. Please try again.</p>';
    }
  }

  // Sort toggle
  const sortPriceBtn = qs('#sortPrice');
  if (sortPriceBtn) {
    sortPriceBtn.addEventListener('click', (e) => {
      e.preventDefault();
      currentSort = currentSort === 'price_asc' ? 'price_desc' : 'price_asc';
      sortPriceBtn.textContent = currentSort === 'price_asc' ? 'low to high' : 'high to low';
      fetchVehicles();
    });
  }

  ['#filterTransmission','#filterSeats','#filterFuel'].forEach(sel => {
    const el = qs(sel); if (el) el.addEventListener('change', fetchVehicles);
  });

  // Customer dropdown functionality
  let selectedCustomerId = null;
  let selectedCustomerData = null;
  let customerDropdownInitialized = false;

  function initCustomerDropdown() {
    const customerInput = qs('#booking_customer_select') || qs('#customer_select');
    const customerContainer = qs('.customer-dropdown-container');
    const customerOptions = qs('.customer-dropdown-options');
    const customerSearchFilter = qs('.customer-search-filter');
    const customerOptionsList = qs('.customer-options-list');

    if (!customerInput || !customerContainer || !customerOptions || !customerSearchFilter || !customerOptionsList) {
      // Elements not ready yet, try again later
      return;
    }

    // Prevent duplicate event listeners
    if (customerDropdownInitialized) {
      return;
    }

    customerDropdownInitialized = true;
    let searchTimeout;
    let customerData = [];
    let filteredCustomers = [];
    let selectedIndex = -1;

    // Open dropdown on input focus or when typing
    customerInput.addEventListener('focus', () => {
      customerOptions.classList.remove('d-none');
      customerSearchFilter.focus();
      loadCustomers();
    });

    // Also open dropdown when user starts typing in main input
    customerInput.addEventListener('input', function() {
      const query = this.value.trim();
      customerOptions.classList.remove('d-none');
      // Copy input value to search filter
      customerSearchFilter.value = query;
      clearTimeout(searchTimeout);
      
      if (query.length === 0) {
        if (customerData.length > 0) {
          filteredCustomers = [...customerData];
          renderCustomerOptions();
        } else {
          loadCustomers();
        }
        return;
      }

      // Search if query is at least 2 characters
      if (query.length >= 2) {
        searchTimeout = setTimeout(() => {
          searchCustomers(query);
        }, 300);
      }
    });

    // Search with debounce in the search filter input
    customerSearchFilter.addEventListener('input', function() {
      const query = this.value.trim();
      clearTimeout(searchTimeout);
      
      if (query.length === 0) {
        if (customerData.length > 0) {
          filteredCustomers = [...customerData];
          renderCustomerOptions();
        } else {
          loadCustomers();
        }
        return;
      }

      searchTimeout = setTimeout(() => {
        searchCustomers(query);
      }, 300);
    });

    // Keyboard navigation
    customerSearchFilter.addEventListener('keydown', function(e) {
      switch(e.key) {
        case 'ArrowDown':
          e.preventDefault();
          selectedIndex = Math.min(selectedIndex + 1, filteredCustomers.length - 1);
          updateSelection();
          break;
        case 'ArrowUp':
          e.preventDefault();
          selectedIndex = Math.max(selectedIndex - 1, -1);
          updateSelection();
          break;
        case 'Enter':
          e.preventDefault();
          if (selectedIndex >= 0 && selectedIndex < filteredCustomers.length) {
            selectCustomer(filteredCustomers[selectedIndex]);
          }
          break;
        case 'Escape':
          closeCustomerDropdown();
          break;
      }
    });

    // Option clicks
    customerOptionsList.addEventListener('click', function(e) {
      const option = e.target.closest('.customer-option');
      if (option && option.dataset.customerId) {
        const customer = filteredCustomers.find(c => c.id === parseInt(option.dataset.customerId));
        if (customer) selectCustomer(customer);
      }
    });

    // Close on outside click
    document.addEventListener('click', function(e) {
      if (!customerContainer.contains(e.target)) {
        closeCustomerDropdown();
      }
    });

    function loadCustomers() {
      customerOptionsList.innerHTML = '<div class="p-2 text-muted small"><i class="fas fa-spinner fa-spin me-2"></i>Loading customers...</div>';
      searchCustomers('');
    }

    async function searchCustomers(query) {
      try {
        const searchQuery = query ? query.trim() : '';
        const url = (window.bookingFlow?.customersSearchUrl || '/business/api/customers/search') + `?q=${encodeURIComponent(searchQuery)}`;
        const res = await fetch(url, {
          headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        });
        
        if (!res.ok) {
          throw new Error(`HTTP ${res.status}`);
        }
        
        const data = await res.json();
        customerData = (data.customers || []).map(c => ({
          id: c.id,
          name: c.full_name || c.company_name || '',
          phone: c.mobile_number || '',
          email: c.email_address || c.email || '',
        }));
        filteredCustomers = [...customerData];
        renderCustomerOptions();
      } catch (e) {
        console.error('Customer search error:', e);
        customerOptionsList.innerHTML = '<div class="p-2 text-danger small">Error loading customers. Please try again.</div>';
      }
    }

    function renderCustomerOptions() {
      if (filteredCustomers.length === 0) {
        customerOptionsList.innerHTML = '<div class="p-2 text-muted small">No customers found</div>';
        return;
      }
      customerOptionsList.innerHTML = filteredCustomers.map(c => `
        <div class="customer-option p-2 border-bottom" data-customer-id="${c.id}" style="cursor: pointer;">
          <div class="fw-semibold">${c.name}</div>
          <div class="text-muted small">${c.phone}${c.email ? ' • ' + c.email : ''}</div>
        </div>
      `).join('');
      selectedIndex = -1;
      updateSelection();
    }

    function updateSelection() {
      const options = customerOptionsList.querySelectorAll('.customer-option');
      options.forEach((opt, idx) => {
        opt.classList.toggle('bg-light', idx === selectedIndex);
      });
    }

    function selectCustomer(customer) {
      selectedCustomerId = customer.id;
      selectedCustomerData = customer;
      customerInput.value = customer.name;
      closeCustomerDropdown();
      updateSummaryCustomer(customer);
      qs('#step3Next').disabled = false;
      state.data.selectedCustomerId = customer.id;
      state.data.selectedCustomer = customer;
      // Save customer data with name for draft restoration
      saveStep({ 
        step: 3, 
        customer_id: customer.id,
        customer_name: customer.name,
        customer_phone: customer.phone || '',
        customer_email: customer.email || ''
      });
    }

    function closeCustomerDropdown() {
      customerOptions.classList.add('d-none');
      selectedIndex = -1;
    }
  }

  // Quick customer create - modal management
  let customerModalInstance = null;
  const customerModalEl = qs('#quickCustomerModal');
  if (customerModalEl) {
    customerModalInstance = new bootstrap.Modal(customerModalEl, {
      backdrop: true,
      keyboard: true
    });

    // Ensure backdrop is removed when modal is hidden
    customerModalEl.addEventListener('hidden.bs.modal', function() {
      // Remove any lingering backdrop elements
      const backdrops = document.querySelectorAll('.modal-backdrop');
      backdrops.forEach(backdrop => backdrop.remove());
      // Remove modal-open class from body
      document.body.classList.remove('modal-open');
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
    });
  }

  const createNewCustomer = qs('#createNewCustomer');
  if (createNewCustomer && customerModalInstance) {
    createNewCustomer.addEventListener('click', (e) => {
      e.preventDefault();
      customerModalInstance.show();
    });
  }

  const saveQuickCustomer = qs('#saveQuickCustomer');
  if (saveQuickCustomer) {
    saveQuickCustomer.addEventListener('click', async () => {
      const name = qs('#quick_cust_name')?.value;
      const mobile = qs('#quick_cust_mobile')?.value;
      const email = qs('#quick_cust_email')?.value;
      const type = qs('#quick_cust_type')?.value;

      if (!name || !mobile) {
        alert('Please fill required fields');
        return;
      }

      try {
        const res = await fetch(window.bookingFlow?.quickCustomerUrl || '/business/customers/quick-create', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          },
          body: JSON.stringify({
            full_name: name,
            mobile_number: mobile,
            email_address: email,
            customer_type: type,
          }),
        });
        const data = await res.json();
        if (data.success) {
          // Close modal properly
          if (customerModalInstance) {
            customerModalInstance.hide();
          }
          // Ensure backdrop cleanup
          setTimeout(() => {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
          }, 300);
          // Reset form
          qs('#quickCustomerForm').reset();
          // Auto-select the new customer
          selectedCustomerId = data.customer.id;
          selectedCustomerData = {
            id: data.customer.id,
            name: data.customer.full_name || data.customer.company_name,
            phone: data.customer.mobile_number,
            email: data.customer.email_address,
          };
          const customerInput = qs('#booking_customer_select') || qs('#customer_select');
          if (customerInput) customerInput.value = selectedCustomerData.name;
          updateSummaryCustomer(selectedCustomerData);
          qs('#step3Next').disabled = false;
          state.data.selectedCustomerId = data.customer.id;
          state.data.selectedCustomer = selectedCustomerData;
          await saveStep({ step: 3, customer_id: data.customer.id });
        } else {
          alert('Error: ' + (data.message || 'Failed to create customer'));
        }
      } catch (e) {
        alert('Error creating customer. Please try again.');
      }
    });
  }


  function updateSummaryCustomer(customer) {
    const sumCustomerDiv = qs('#sumCustomer');
    if (sumCustomerDiv) {
      sumCustomerDiv.classList.remove('d-none');
      if (qs('#sumCustomerName')) {
        qs('#sumCustomerName').textContent = customer.name || '';
      }
    }
  }

  // Step 3 Next button
  const step3Next = qs('#step3Next');
  if (step3Next) {
    step3Next.addEventListener('click', async () => {
      // Check both local variable and state
      const customerId = selectedCustomerId || state.data.selectedCustomerId;
      if (!customerId) {
        alert('Please select a customer');
        return;
      }
      // Ensure both are set
      selectedCustomerId = customerId;
      state.data.selectedCustomerId = customerId;
      
      // Save customer data with all details for draft restoration
      const customer = selectedCustomerData || state.data.selectedCustomer || {};
      await saveStep({ 
        step: 3, 
        customer_id: customerId,
        customer_name: customer.name || (qs('#booking_customer_select') || qs('#customer_select'))?.value || '',
        customer_phone: customer.phone || '',
        customer_email: customer.email || ''
      });
      showStep(4);
    });
  }

  // Step 4: Billing Info
  let additionalCharges = [];
  let additionalChargeCounter = 0;

  // Load vehicle data when Step 4 is shown
  async function loadVehicleDataForBilling() {
    const vehicleId = state.data.selectedVehicleId;
    if (!vehicleId) {
      console.warn('No vehicle selected for billing');
      return;
    }

    // Fetch vehicle details from API
    try {
      const urlTemplate = window.bookingFlow?.vehicleBillingUrl || '/business/bookings/flow/vehicle/:vehicleId/billing';
      const url = urlTemplate.replace(':vehicleId', vehicleId);
      const res = await fetch(url, {
        headers: { 
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });
      
      if (!res.ok) {
        throw new Error(`HTTP ${res.status}`);
      }
      
      const vehicleData = await res.json();
      
      // Populate fields only if they're empty (allow user to edit if needed)
      if (vehicleData.rental_price_24h && !qs('#rent_24h')?.value) {
        qs('#rent_24h').value = vehicleData.rental_price_24h;
      }
      if (vehicleData.km_limit_per_booking && !qs('#km_limit')?.value) {
        qs('#km_limit').value = vehicleData.km_limit_per_booking;
      }
      if (vehicleData.extra_rental_price_per_hour && !qs('#extra_per_hour')?.value) {
        qs('#extra_per_hour').value = vehicleData.extra_rental_price_per_hour;
      }
      if (vehicleData.extra_price_per_km && !qs('#extra_per_km')?.value) {
        qs('#extra_per_km').value = vehicleData.extra_price_per_km;
      }
      
      // Trigger recompute summary after loading data
      setTimeout(() => {
        recomputeSummary();
      }, 100);
      
    } catch (e) {
      console.error('Error loading vehicle data:', e);
      // Fallback: try to get price from vehicle card if visible
      const vehicleCard = qs(`[data-vehicle-id="${vehicleId}"]`);
      if (vehicleCard) {
        const priceText = vehicleCard.querySelector('.vehicle-price')?.textContent || '';
        const priceMatch = priceText.match(/₹([\d,]+)/);
        if (priceMatch && !qs('#rent_24h')?.value) {
          const price = priceMatch[1].replace(/,/g, '');
          qs('#rent_24h').value = price;
        }
      }
    }
  }

  // Add additional charge row
  const addAdditionalChargeBtn = qs('#addAdditionalCharge');
  if (addAdditionalChargeBtn) {
    addAdditionalChargeBtn.addEventListener('click', () => {
      const id = `charge_${++additionalChargeCounter}`;
      const chargeRow = document.createElement('div');
      chargeRow.className = 'card';
      chargeRow.id = id;
      chargeRow.innerHTML = `
        <div class="card-body">
          <div class="row g-2 align-items-end">
            <div class="col-md-5">
              <label class="form-label small">Charge Description</label>
              <input type="text" class="form-control form-control-sm charge-description" placeholder="e.g., Driver fee, GPS" />
            </div>
            <div class="col-md-4">
              <label class="form-label small">Amount</label>
              <input type="number" step="0.01" class="form-control form-control-sm charge-amount" placeholder="0.00" />
            </div>
            <div class="col-md-3">
              <button type="button" class="btn btn-sm btn-outline-danger w-100 remove-charge" data-charge-id="${id}">Remove</button>
            </div>
          </div>
        </div>
      `;
      qs('#additionalChargesList').appendChild(chargeRow);
      
      // Add remove handler
      chargeRow.querySelector('.remove-charge').addEventListener('click', () => {
        chargeRow.remove();
        additionalCharges = additionalCharges.filter(c => c.id !== id);
        recomputeSummary();
      });
      
      // Add input handlers for live calculation
      chargeRow.querySelector('.charge-amount').addEventListener('input', recomputeSummary);
    });
  }

  // Discount calculation
  const discountAmountInput = qs('#discount_amount');
  const discountTypeSelect = qs('#discount_type');
  
  function updateDiscountDisplay() {
    const amount = parseFloat(discountAmountInput?.value || 0);
    const type = discountTypeSelect?.value || 'amount';
    const display = qs('#discount_display');
    if (display) {
      if (type === 'percentage') {
        display.value = `${amount}%`;
      } else {
        display.value = `₹${amount.toFixed(2)}`;
      }
    }
    recomputeSummary();
  }
  
  if (discountAmountInput) discountAmountInput.addEventListener('input', updateDiscountDisplay);
  if (discountTypeSelect) discountTypeSelect.addEventListener('change', updateDiscountDisplay);

  // Billing summary recomputation
  async function recomputeSummary() {
    // Collect additional charges
    const charges = [];
    qsa('.charge-amount').forEach((input, idx) => {
      const row = input.closest('.card');
      const desc = row.querySelector('.charge-description')?.value || `Charge ${idx + 1}`;
      const amount = parseFloat(input.value || 0);
      if (amount > 0) {
        charges.push({ description: desc, amount: amount });
      }
    });

    const payload = {
      rent_24h: Number(qs('#rent_24h')?.value || 0),
      km_limit: Number(qs('#km_limit')?.value || 0),
      extra_per_hour: Number(qs('#extra_per_hour')?.value || 0),
      extra_per_km: Number(qs('#extra_per_km')?.value || 0),
      additional_charges: JSON.stringify(charges),
      discount_amount: Number(qs('#discount_amount')?.value || 0),
      discount_type: qs('#discount_type')?.value || 'amount',
      advance_payment: Number(qs('#advance_payment')?.value || 0),
      pickup_datetime: qs('#pickup_datetime')?.value || '',
      dropoff_datetime: qs('#dropoff_datetime')?.value || '',
    };
    
    try {
      const res = await fetch(window.bookingFlow?.billingSummaryUrl || '/business/bookings/flow/billing/summary', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (qs('#sumSubTotal')) qs('#sumSubTotal').textContent = `₹${data.subTotal}`;
      if (qs('#sumDiscount')) qs('#sumDiscount').textContent = `₹${data.discount}`;
      if (qs('#sumAdvance')) qs('#sumAdvance').textContent = `₹${data.advance}`;
      if (qs('#sumDue')) qs('#sumDue').textContent = `₹${data.amountDue}`;
    } catch (e) {
      console.error('Error computing summary:', e);
    }
  }

  // Auto-save billing fields
  ['#rent_24h', '#km_limit', '#extra_per_hour', '#extra_per_km', '#discount_amount', '#advance_payment', '#payment_method'].forEach(sel => {
    const el = qs(sel);
    if (el) {
      el.addEventListener('input', async () => {
        await saveStep({ step: 4, [sel.replace('#', '')]: el.value });
        recomputeSummary();
      });
      el.addEventListener('change', async () => {
        await saveStep({ step: 4, [sel.replace('#', '')]: el.value });
        recomputeSummary();
      });
    }
  });

  // Step 4 Next button - override generic data-next handler
  const step4Next = qs('#step4Next');
  if (step4Next) {
    step4Next.addEventListener('click', async (e) => {
      e.preventDefault();
      const rent24h = qs('#rent_24h')?.value;
      if (!rent24h || parseFloat(rent24h) <= 0) {
        alert('Please enter vehicle rent for 24 hrs');
        return;
      }
      
      // Collect all billing data
      const charges = [];
      qsa('.charge-amount').forEach((input, idx) => {
        const row = input.closest('.card');
        const desc = row.querySelector('.charge-description')?.value || `Charge ${idx + 1}`;
        const amount = parseFloat(input.value || 0);
        if (amount > 0) {
          charges.push({ description: desc, amount: amount });
        }
      });
      
      const billingData = {
        rent_24h: qs('#rent_24h')?.value,
        km_limit: qs('#km_limit')?.value,
        extra_per_hour: qs('#extra_per_hour')?.value,
        extra_per_km: qs('#extra_per_km')?.value,
        discount_amount: qs('#discount_amount')?.value,
        discount_type: qs('#discount_type')?.value,
        advance_payment: qs('#advance_payment')?.value,
        payment_method: qs('#payment_method')?.value,
        additional_charges: charges,
      };
      
      await saveStep({ step: 4, ...billingData });
      showStep(5);
    });
  }

  // Don't auto-show step 1 if draft exists (draft restore will handle it)
  // Only show step 1 if no draft exists - but wait a bit to let draft restore run first
  setTimeout(() => {
    if (!window.bookingFlow?.draft?.exists || !(window.bookingFlow?.draft?.data?.step_1 || window.bookingFlow?.draft?.data?.step_2 || window.bookingFlow?.draft?.data?.step_3 || window.bookingFlow?.draft?.data?.step_4 || window.bookingFlow?.draft?.data?.step_5)) {
      showStep(1);
    }
  }, 1000);

  // Save draft functionality - save all current step data + preserve previous steps
  async function saveDraft() {
    const currentStep = state.step;
    
    // First, collect data from ALL steps (not just current)
    let allDraftData = {};
    
    // Step 1 - always collect if fields exist
    const pickupDt = qs('#pickup_datetime')?.value;
    const dropoffDt = qs('#dropoff_datetime')?.value;
    const pickupLoc = qs('#pickup_location')?.value;
    const dropoffLoc = qs('#dropoff_location')?.value;
    if (pickupDt || dropoffDt || pickupLoc || dropoffLoc) {
      allDraftData.step_1 = {
        pickup_datetime: pickupDt || '',
        dropoff_datetime: dropoffDt || '',
        pickup_location: pickupLoc || '',
        dropoff_location: dropoffLoc || '',
      };
    }
    
    // Step 2 - ALWAYS check and save if vehicle selected (check multiple sources)
    const vehicleId = state.data.selectedVehicleId || 
                     (qs('[data-vehicle-id]') && qs('.vehicle-card.selected')?.dataset?.vehicleId) ||
                     (qs('[data-vehicle-id]') && qs('[data-vehicle-id].selected')?.dataset?.vehicleId);
    if (vehicleId) {
      // Ensure it's saved in state too
      state.data.selectedVehicleId = vehicleId;
      allDraftData.step_2 = {
        vehicle_id: vehicleId,
      };
    }
    
    // Step 3 - ALWAYS check and save if customer selected (check multiple sources)
    const customerId = state.data.selectedCustomerId || selectedCustomerId;
    const customerName = selectedCustomerData?.name || 
                         (qs('#booking_customer_select')?.value) || 
                         (qs('#customer_select')?.value) || '';
    if (customerId) {
      // Ensure it's saved in state too
      state.data.selectedCustomerId = customerId;
      if (!selectedCustomerId) selectedCustomerId = customerId;
      allDraftData.step_3 = {
        customer_id: customerId,
        customer_name: customerName || selectedCustomerData?.name || '',
        customer_phone: selectedCustomerData?.phone || '',
        customer_email: selectedCustomerData?.email || '',
      };
    } else if (customerName && customerName.trim()) {
      // Even if ID is missing, save the name for restoration
      // This handles edge cases where dropdown was used but ID wasn't set
      const customerInput = qs('#booking_customer_select') || qs('#customer_select');
      if (customerInput && customerInput.value.trim()) {
        // Try to find customer from dropdown options or restore later
        allDraftData.step_3 = {
          customer_id: '', // Will need to be resolved on restore
          customer_name: customerInput.value.trim(),
          customer_phone: selectedCustomerData?.phone || '',
          customer_email: selectedCustomerData?.email || '',
        };
      }
    }
    
    // Step 4 - collect all billing fields (always collect, even if empty)
    const rent24h = qs('#rent_24h')?.value;
    const kmLimit = qs('#km_limit')?.value;
    const extraHour = qs('#extra_per_hour')?.value;
    const extraKm = qs('#extra_per_km')?.value;
    const discountAmt = qs('#discount_amount')?.value;
    const discountType = qs('#discount_type')?.value;
    const advancePay = qs('#advance_payment')?.value;
    const payMethod = qs('#payment_method')?.value;
    
    if (rent24h || kmLimit || extraHour || extraKm || discountAmt || advancePay || payMethod) {
      const charges = [];
      qsa('.charge-amount').forEach((input, idx) => {
        const row = input.closest('.card');
        const desc = row.querySelector('.charge-description')?.value || '';
        const amount = parseFloat(input.value || 0);
        if (amount > 0 || desc) {
          charges.push({ description: desc, amount: amount });
        }
      });
      
      allDraftData.step_4 = {
        rent_24h: rent24h || '',
        km_limit: kmLimit || '',
        extra_per_hour: extraHour || '',
        extra_per_km: extraKm || '',
        discount_amount: discountAmt || '',
        discount_type: discountType || 'amount',
        advance_payment: advancePay || '',
        payment_method: payMethod || '',
        additional_charges: charges,
      };
    }
    
    // Save all steps data at once
    for (const [stepKey, stepData] of Object.entries(allDraftData)) {
      const stepNum = stepKey.replace('step_', '');
      await saveStep({ step: parseInt(stepNum), ...stepData });
    }
    
    return allDraftData;
  }

  // Add save draft button handlers to all steps
  qsa('#saveDraftBtn, .save-draft-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      
      // Show feedback
      const originalText = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Saving...';
      
      try {
        await saveDraft();
        
        // Small delay for UX
        await new Promise(resolve => setTimeout(resolve, 300));
        
        btn.disabled = false;
        btn.textContent = 'Draft Saved!';
        setTimeout(() => {
          btn.textContent = originalText;
        }, 2000);
      } catch (error) {
        console.error('Error saving draft:', error);
        btn.disabled = false;
        btn.textContent = originalText;
        alert('Failed to save draft. Please try again.');
      }
    });
  });

  async function updateSummary(){
    // Placeholder to refresh right pane – can be expanded with dates/vehicle/customer
    updateSummaryDates();
    if (state.data.selectedVehicleId) {
      updateSummaryVehicle(state.data.selectedVehicleId);
    }
  }

  async function updateSummaryVehicle(vehicleId) {
    // Fetch vehicle details and update summary - placeholder for now
    // In real implementation, fetch from API or use cached data
    const vehicleCard = qs(`[data-vehicle-id="${vehicleId}"]`);
    if (vehicleCard) {
      const name = vehicleCard.querySelector('.vehicle-name')?.textContent || '';
      const price = vehicleCard.querySelector('.vehicle-price')?.textContent || '';
      const sumVehicleDiv = qs('#sumVehicle');
      if (sumVehicleDiv) {
        sumVehicleDiv.classList.remove('d-none');
        if (qs('#sumVehicleName')) qs('#sumVehicleName').textContent = name;
        if (qs('#sumVehicleAmount')) qs('#sumVehicleAmount').textContent = price;
      }
    }
  }

  function formatDate(dt){
    try{ const d = new Date(dt); return d.toLocaleDateString(undefined,{weekday:'short', month:'short', day:'numeric', year:'numeric'}); }catch{ return ''; }
  }
  function formatTime(dt){
    try{ const d = new Date(dt); return d.toLocaleTimeString(undefined,{hour:'2-digit',minute:'2-digit'}); }catch{ return ''; }
  }
  function updateSummaryDates(payload){
    const p = payload || {
      pickup_datetime: qs('#pickup_datetime')?.value,
      dropoff_datetime: qs('#dropoff_datetime')?.value,
      pickup_location: qs('#pickup_location')?.value,
      dropoff_location: qs('#dropoff_location')?.value,
    };
    if (!p) return;
    const [pd, dd] = [p.pickup_datetime, p.dropoff_datetime];
    if (qs('#sumPickupDate')) qs('#sumPickupDate').textContent = formatDate(pd);
    if (qs('#sumPickupTime')) qs('#sumPickupTime').textContent = `Time: ${formatTime(pd)}`;
    if (qs('#sumPickupLoc')) qs('#sumPickupLoc').textContent = `Location: ${p.pickup_location || ''}`;
    if (qs('#sumDropDate')) qs('#sumDropDate').textContent = formatDate(dd);
    if (qs('#sumDropTime')) qs('#sumDropTime').textContent = `Time: ${formatTime(dd)}`;
    if (qs('#sumDropLoc')) qs('#sumDropLoc').textContent = `Location: ${p.dropoff_location || ''}`;
  }
  ['#pickup_datetime','#dropoff_datetime','#pickup_location','#dropoff_location'].forEach(sel=>{
    const el = qs(sel); if (el) el.addEventListener('change', ()=>updateSummaryDates());
  });

  // Step 5: Preview and Confirm
  function renderConfirmPreview() {
    const el = qs('#confirmPreview');
    if (!el) return;

    const pickup = qs('#pickup_datetime')?.value || '';
    const dropoff = qs('#dropoff_datetime')?.value || '';
    const pickupLoc = qs('#pickup_location')?.value || '';
    const dropoffLoc = qs('#dropoff_location')?.value || '';

    const vehicleName = qs('#sumVehicleName')?.textContent || '';
    const vehicleAmount = qs('#sumVehicleAmount')?.textContent || '';

    const customerName = qs('#sumCustomerName')?.textContent || '';

    const subTotal = qs('#sumSubTotal')?.textContent || '₹0.00';
    const discount = qs('#sumDiscount')?.textContent || '₹0.00';
    const advance = qs('#sumAdvance')?.textContent || '₹0.00';
    const amountDue = qs('#sumDue')?.textContent || '₹0.00';

    el.innerHTML = `
      <div class="vstack gap-3">
        <div>
          <div class="text-muted small">Trip</div>
          <div class="small">Pickup: ${pickup} • ${pickupLoc}</div>
          <div class="small">Drop: ${dropoff} • ${dropoffLoc}</div>
        </div>
        <div>
          <div class="text-muted small">Vehicle</div>
          <div class="small fw-semibold">${vehicleName}</div>
          <div class="small">${vehicleAmount}</div>
        </div>
        <div>
          <div class="text-muted small">Customer</div>
          <div class="small fw-semibold">${customerName}</div>
        </div>
        <div>
          <div class="text-muted small">Billing</div>
          <div class="d-flex justify-content-between small"><span>Sub Total</span><span>${subTotal}</span></div>
          <div class="d-flex justify-content-between small"><span>Discount</span><span>${discount}</span></div>
          <div class="d-flex justify-content-between small"><span>Advance</span><span>${advance}</span></div>
          <hr class="my-2">
          <div class="d-flex justify-content-between fw-semibold"><span>Amount Due</span><span>${amountDue}</span></div>
        </div>
      </div>
    `;
  }

  // When entering Step 5, render preview
  qsa('[data-next]').forEach(btn => {
    if (btn.id === 'step4Next') return;
    btn.addEventListener('click', () => {
      const nextStep = Math.min(5, state.step + 1);
      if (nextStep === 5) {
        renderConfirmPreview();
      }
    });
  });
  qsa('[data-prev]').forEach(btn => {
    btn.addEventListener('click', () => {
      const prevStep = Math.max(1, state.step - 1);
      if (prevStep === 5) {
        renderConfirmPreview();
      }
    });
  });

  function showError(message){
    alert(message);
  }

  function validateStep(step){
    if (step === 1) {
      const p = qs('#pickup_datetime')?.value;
      const d = qs('#dropoff_datetime')?.value;
      const pl = qs('#pickup_location')?.value;
      const dl = qs('#dropoff_location')?.value;
      if (!p || !d || !pl || !dl) { showError('Please fill pickup/drop dates and locations'); return false; }
      const pd = new Date(p), dd = new Date(d);
      if (isFinite(pd) && isFinite(dd) && dd <= pd) { showError('Drop-off must be after pickup'); return false; }
      return true;
    }
    if (step === 2) {
      if (!state.data.selectedVehicleId) { showError('Please select a vehicle to continue'); return false; }
      return true;
    }
    if (step === 3) {
      if (!state.data.selectedCustomerId) { showError('Please select a customer'); return false; }
      return true;
    }
    if (step === 4) {
      const rent = parseFloat(qs('#rent_24h')?.value || '0');
      if (!(rent > 0)) { showError('Please enter a valid rent for 24 hrs'); return false; }
      return true;
    }
    return true;
  }

  // Autosave Step 4 on input changes (debounced)
  const step4AutosaveFields = ['#rent_24h', '#discount_amount', '#discount_type', '#advance_amount', '#payment_method'];
  let step4SaveTimer = null;
  step4AutosaveFields.forEach(sel => {
    const el = qs(sel);
    if (el) {
      el.addEventListener('input', () => {
        clearTimeout(step4SaveTimer);
        step4SaveTimer = setTimeout(() => {
          saveStep({
            step: 4,
            rent_24h: qs('#rent_24h')?.value || '',
            discount_amount: qs('#discount_amount')?.value || '',
            discount_type: qs('#discount_type')?.value || '',
            advance_amount: qs('#advance_amount')?.value || '',
            payment_method: qs('#payment_method')?.value || '',
          });
        }, 400);
      });
    }
  });

  // Intercept generic next buttons to validate
  qsa('[data-next]').forEach(btn => {
    if (btn.id === 'step4Next' || btn.id === 'confirmBookingBtn') return;
    btn.addEventListener('click', (e) => {
      const current = state.step;
      if (!validateStep(current)) { e.preventDefault(); return; }
    });
  });

  // Validate before moving from Step 4 in its handler
  // (Step 4 Next button handled below)

  // Prevent double submission flag
  let isBookingInProgress = false;

  const confirmBtn = qs('#confirmBookingBtn');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      e.stopPropagation();
      
      // Prevent double submission
      if (isBookingInProgress) {
        console.warn('Booking already in progress, ignoring duplicate click');
        return;
      }
      
      if (!validateStep(4)) { return; }
      
      // Set flag and disable button
      isBookingInProgress = true;
      confirmBtn.disabled = true;
      confirmBtn.style.pointerEvents = 'none';
      const originalText = confirmBtn.textContent;
      confirmBtn.textContent = 'Creating Booking...';
      
      try {
        const res = await fetch(window.bookingFlow?.storeUrl || '/business/bookings/flow/store', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        
        const data = await res.json();
        
        if (res.ok && data.success) {
          // Clear draft immediately to prevent re-submission
          try {
            await fetch(window.bookingFlow?.clearDraftUrl || '/business/bookings/flow/clear-draft', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              }
            });
          } catch (_) {}
          
          // Success - redirect immediately
          if (data.redirect_url) {
            window.location.href = data.redirect_url;
          } else {
            window.location.href = window.bookingFlow?.successRedirect || '/business/bookings';
          }
        } else if (res.status === 409 && data.redirect_url) {
          // Duplicate booking detected - redirect to existing booking
          isBookingInProgress = false;
          confirmBtn.disabled = false;
          confirmBtn.style.pointerEvents = '';
          confirmBtn.textContent = originalText;
          alert('This booking was already created. Redirecting...');
          window.location.href = data.redirect_url;
        } else if (res.status === 429) {
          // Too many requests - already processing
          isBookingInProgress = false;
          confirmBtn.disabled = false;
          confirmBtn.style.pointerEvents = '';
          confirmBtn.textContent = originalText;
          showError(data.message || 'Booking is already being processed. Please wait...');
        } else {
          // Error - re-enable button
          isBookingInProgress = false;
          confirmBtn.disabled = false;
          confirmBtn.style.pointerEvents = '';
          confirmBtn.textContent = originalText;
          showError(data.message || 'Failed to confirm booking. Please try again.');
        }
      } catch (e) {
        // Error - re-enable button
        isBookingInProgress = false;
        confirmBtn.disabled = false;
        confirmBtn.style.pointerEvents = '';
        confirmBtn.textContent = originalText;
        showError('Error confirming booking. Please check your connection and try again.');
        console.error('Booking creation error:', e);
      }
    });
  }

  // Init - only show step 1 if no draft exists (draft restore handles it otherwise)
  if (!window.bookingFlow?.draft?.exists) {
    showStep(1);
  }
})();


