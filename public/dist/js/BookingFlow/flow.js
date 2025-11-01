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
        
        if (!pickupDt || !dropoffDt) {
          alert('Please complete Step 1 (pickup/drop-off dates) before proceeding.');
          showStep(1);
          return;
        }
        
        // Validate dates are correct (end date must be after start date)
        const pickupDate = new Date(pickupDt);
        const dropoffDate = new Date(dropoffDt);
        if (isNaN(pickupDate.getTime()) || isNaN(dropoffDate.getTime())) {
          alert('Invalid date format. Please check your pickup and drop-off dates.');
          showStep(1);
          return;
        }
        if (dropoffDate <= pickupDate) {
          alert('Drop-off date and time must be after pickup date and time. Please correct the dates in Step 1.');
          showStep(1);
          return;
        }
      }
      
      // Check step 1 completion (vehicle selection) before allowing step 3+
      if (step >= 3) {
        const pickupDt = qs('#pickup_datetime')?.value;
        const dropoffDt = qs('#dropoff_datetime')?.value;
        if (!pickupDt || !dropoffDt) {
          alert('Please complete Step 1 (pickup/drop-off dates) before proceeding.');
          showStep(1);
          return;
        }
        
        // Validate dates are correct (end date must be after start date)
        const pickupDate = new Date(pickupDt);
        const dropoffDate = new Date(dropoffDt);
        if (isNaN(pickupDate.getTime()) || isNaN(dropoffDate.getTime())) {
          alert('Invalid date format. Please check your pickup and drop-off dates.');
          showStep(1);
          return;
        }
        if (dropoffDate <= pickupDate) {
          alert('Drop-off date and time must be after pickup date and time. Please correct the dates in Step 1.');
          showStep(1);
          return;
        }
        
        if (!state.data.selectedVehicleId) {
          alert('Please select a vehicle in Step 1 before proceeding.');
          showStep(1, true);
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
        let rent24h = qs('#rent_24h')?.value;
        
        // If rent_24h is empty, try to get from vehicle data or summary
        if (!rent24h || parseFloat(rent24h) <= 0) {
          // Try to get from vehicle card if available
          if (state.data.selectedVehicleId) {
            const vehicleCard = qs(`[data-vehicle-id="${state.data.selectedVehicleId}"]`);
            if (vehicleCard) {
              const priceText = vehicleCard.querySelector('.vehicle-price')?.textContent || '';
              const priceMatch = priceText.match(/₹([\d,]+)/);
              if (priceMatch) {
                rent24h = priceMatch[1].replace(/,/g, '');
                // Auto-populate the field
                if (qs('#rent_24h')) {
                  qs('#rent_24h').value = rent24h;
                }
              }
            }
          }
        }
        
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
    
    // Show/hide booking summary sidebar (hide for step 1, show for other steps)
    const mainContent = qs('#mainContent');
    const summarySidebar = qs('#summarySidebar');
    if (mainContent && summarySidebar) {
      if (step === 1) {
        // Step 1: Full width, hide summary
        mainContent.classList.remove('col-lg-8');
        mainContent.classList.add('col-12');
        summarySidebar.classList.add('d-none');
      } else {
        // Other steps: Show summary sidebar
        mainContent.classList.remove('col-12');
        mainContent.classList.add('col-lg-8');
        summarySidebar.classList.remove('d-none');
      }
    }
    // Update progress steps - map internal step numbers to display steps
    // Step 1 (dates & vehicles) = display step 1
    // Step 3 (customer) = display step 3
    // Step 4 (billing) = display step 4
    // Step 5 (confirm) = display step 5
    qsa('.progress-steps .step').forEach(s => {
      const stepNum = Number(s.dataset.step);
      const isActive = stepNum === step;
      s.classList.toggle('active', isActive);
      // Mark as completed if we're on a later step
      if (stepNum < step) {
        s.classList.add('completed');
      } else {
        s.classList.remove('completed');
      }
    });
    
    // Initialize step-specific functionality
    if (step === 1) {
      // Load vehicles if dates are set
      const pickupDt = qs('#pickup_datetime')?.value;
      const dropoffDt = qs('#dropoff_datetime')?.value;
      if (pickupDt && dropoffDt) {
        setTimeout(() => {
          fetchVehicles();
        }, 100);
      }
    }
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
    if (step === 5) {
      setTimeout(() => {
        // Ensure Step 4 billing fields are restored if draft exists
        const step4Data = state.data.step4RestoreData;
        if (step4Data) {
          // Restore Step 4 fields even if we're on step 5 (needed for preview)
          if (qs('#rent_24h')) qs('#rent_24h').value = step4Data.rent_24h || '';
          if (qs('#km_limit')) qs('#km_limit').value = step4Data.km_limit || '';
          if (qs('#extra_per_hour')) qs('#extra_per_hour').value = step4Data.extra_per_hour || '';
          if (qs('#extra_per_km')) qs('#extra_per_km').value = step4Data.extra_per_km || '';
          if (qs('#discount_amount')) qs('#discount_amount').value = step4Data.discount_amount || '';
          if (qs('#discount_type')) qs('#discount_type').value = step4Data.discount_type || 'amount';
          if (qs('#advance_payment')) qs('#advance_payment').value = step4Data.advance_payment || '';
          if (qs('#payment_method')) qs('#payment_method').value = step4Data.payment_method || '';
        }
        
        // ALWAYS ensure Step 4 billing fields are loaded from vehicle if rent_24h is missing or 0
        if (state.data.selectedVehicleId) {
          const rent24hValue = qs('#rent_24h')?.value;
          if (!rent24hValue || parseFloat(rent24hValue) <= 0) {
            // Load vehicle data to populate billing fields
            loadVehicleDataForBilling().then(() => {
              // Wait a bit for fields to be populated
              setTimeout(() => {
                // Re-apply step4 data after vehicle data loads (draft values take precedence)
                if (step4Data) {
                  if (qs('#rent_24h') && step4Data.rent_24h) qs('#rent_24h').value = step4Data.rent_24h;
                  if (qs('#km_limit') && step4Data.km_limit) qs('#km_limit').value = step4Data.km_limit;
                  if (qs('#extra_per_hour') && step4Data.extra_per_hour) qs('#extra_per_hour').value = step4Data.extra_per_hour;
                  if (qs('#extra_per_km') && step4Data.extra_per_km) qs('#extra_per_km').value = step4Data.extra_per_km;
                  if (qs('#discount_amount') && step4Data.discount_amount) qs('#discount_amount').value = step4Data.discount_amount;
                  if (qs('#discount_type') && step4Data.discount_type) qs('#discount_type').value = step4Data.discount_type;
                  if (qs('#advance_payment') && step4Data.advance_payment) qs('#advance_payment').value = step4Data.advance_payment;
                  if (qs('#payment_method') && step4Data.payment_method) qs('#payment_method').value = step4Data.payment_method;
                }
                
                recomputeSummary();
              }, 300);
            });
          }
        }
        
        // Also ensure vehicle and customer summaries are updated for preview
        if (state.data.selectedVehicleId) {
          updateSummaryVehicle(state.data.selectedVehicleId);
        }
        if (state.data.selectedCustomerId || selectedCustomerId) {
          const customer = selectedCustomerData || state.data.selectedCustomer;
          if (customer && customer.name) {
            updateSummaryCustomer(customer);
          }
        }
        
        // Recompute summary to ensure all values are current
        recomputeSummary();
        
        // Render preview
        renderConfirmPreview();
        // Refresh preview again after summaries are updated
        setTimeout(() => {
          recomputeSummary();
          renderConfirmPreview();
        }, 400);
      }, 100);
    }
  }

  async function saveStep(stepPayload) {
    try {
      // Ensure additional_charges is properly formatted (should be array, not JSON string)
      if (stepPayload.additional_charges && typeof stepPayload.additional_charges === 'string') {
        try {
          stepPayload.additional_charges = JSON.parse(stepPayload.additional_charges);
        } catch (e) {
          // If parsing fails, set to empty array
          stepPayload.additional_charges = [];
        }
      }
      
      const res = await fetch(window.bookingFlow?.saveStepUrl || '/business/bookings/flow/save-step', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify(stepPayload),
      });
      
      if (!res.ok) {
        const errorText = await res.text();
        console.error('Error saving step:', res.status, errorText);
      }
    } catch (e) {
      console.error('Error in saveStep:', e);
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
            // Create modal instance but don't show it yet
            const modal = new bootstrap.Modal(modalEl, {
              backdrop: true,
              keyboard: true,
              focus: false  // Disable auto-focus to prevent aria-hidden issues
            });
            
            // Attach event listeners BEFORE showing modal
            const resumeBtn = document.getElementById('resumeDraftBtn');
            const newBtn = document.getElementById('newBookingBtn');
            
            // Resume button - draft already restored, just hide modal
            if (resumeBtn) {
              // Remove existing listeners first
              const newResumeBtn = resumeBtn.cloneNode(true);
              resumeBtn.parentNode.replaceChild(newResumeBtn, resumeBtn);
              
              newResumeBtn.addEventListener('click', () => {
                modal.hide();
                // Re-apply draft in case something was missed
                setTimeout(() => {
                  applyDraft(draft);
                }, 100);
              });
            }
            
            // New booking button - clear draft and reset
            if (newBtn) {
              // Remove existing listeners first
              const newNewBtn = newBtn.cloneNode(true);
              newBtn.parentNode.replaceChild(newNewBtn, newBtn);
              
              newNewBtn.addEventListener('click', async () => {
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
                showStep(1);
                modal.hide();
                // Reload page to clear draft completely
                window.location.reload();
              });
            }
            
            // Show modal only after listeners are attached and a small delay
            // This ensures DOM is ready and prevents aria-hidden focus issues
            setTimeout(() => {
              modal.show();
            }, 100);
          }
        }, 500);
      }
    }
  }

  // Run on DOM ready (works with defer attribute)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDraftRestore);
  } else {
    // DOM already loaded
    setTimeout(initDraftRestore, 200);
  }

  // Flag to prevent autosave during draft restoration
  let isRestoringDraft = false;

  function applyDraft(draft) {
    if (!draft || !draft.data) {
      console.warn('No draft data to apply');
      return;
    }
    
    // Set flag to prevent autosave during restoration
    isRestoringDraft = true;
    
    const d = draft.data || {};
    
    // Step 1 - Restore date and location fields (restore even if empty)
    if (d.step_1) {
      if (qs('#pickup_datetime')) {
        qs('#pickup_datetime').value = d.step_1.pickup_datetime || '';
      }
      if (qs('#dropoff_datetime')) {
        qs('#dropoff_datetime').value = d.step_1.dropoff_datetime || '';
      }
      // Update summary without triggering autosave (we'll update manually)
      updateSummaryDates(d.step_1);
      
      // Trigger change events AFTER a delay to allow flag to be set, but only to update UI
      setTimeout(() => {
        if (qs('#pickup_datetime')?.value || qs('#dropoff_datetime')?.value) {
          // Only reload vehicles if dates are set, but don't save (isRestoringDraft is still true)
          fetchVehicles();
        }
        // Clear flag after restoration is complete
        setTimeout(() => {
          isRestoringDraft = false;
        }, 500);
      }, 100);
    } else {
      // If no step 1 data, clear flag immediately
      setTimeout(() => {
        isRestoringDraft = false;
      }, 100);
    }
    
    // Step 2 - Restore selected vehicle
    if (d.step_2 && d.step_2.vehicle_id) {
      state.data.selectedVehicleId = d.step_2.vehicle_id;
      // Update vehicle summary after vehicles are loaded (handled in showStep for step 2+)
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
          // Update customer summary immediately
          updateSummaryCustomer(selectedCustomerData);
        }
      }
    }
    
    // Step 4 - Restore billing fields (restore even if empty or zero)
    // This restoration happens immediately, but will be re-applied after vehicle data loads if needed
    if (d.step_4) {
      // Store Step 4 data for later restoration (after vehicle data loads)
      // This ensures values aren't overwritten by vehicle defaults
      const step4Data = {
        rent_24h: d.step_4.rent_24h || '',
        km_limit: d.step_4.km_limit || '',
        extra_per_hour: d.step_4.extra_per_hour || '',
        extra_per_km: d.step_4.extra_per_km || '',
        discount_amount: d.step_4.discount_amount || '',
        discount_type: d.step_4.discount_type || 'amount',
        advance_payment: d.step_4.advance_payment || '',
        payment_method: d.step_4.payment_method || '',
        additional_charges: d.step_4.additional_charges || []
      };
      
      // Store in state for later use
      state.data.step4RestoreData = step4Data;
      
      // Try to restore immediately if fields exist
      if (qs('#rent_24h')) qs('#rent_24h').value = step4Data.rent_24h;
      if (qs('#km_limit')) qs('#km_limit').value = step4Data.km_limit;
      if (qs('#extra_per_hour')) qs('#extra_per_hour').value = step4Data.extra_per_hour;
      if (qs('#extra_per_km')) qs('#extra_per_km').value = step4Data.extra_per_km;
      if (qs('#discount_amount')) qs('#discount_amount').value = step4Data.discount_amount;
      if (qs('#discount_type')) qs('#discount_type').value = step4Data.discount_type;
      if (qs('#advance_payment')) qs('#advance_payment').value = step4Data.advance_payment;
      if (qs('#payment_method')) qs('#payment_method').value = step4Data.payment_method;
    }
    
    // Determine the correct step to navigate to by validating each step
    // Start from step 1 and find the first incomplete step
    let targetStep = 1;
    
    // Check if step 1 is complete
    const step1Complete = d.step_1 && 
      d.step_1.pickup_datetime && 
      d.step_1.dropoff_datetime;
    
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
    
    // Load vehicles if on step 2 or later, then update summary
    if (targetStep >= 2) {
      setTimeout(() => {
        fetchVehicles().then(() => {
          // After vehicles are loaded, update vehicle summary if vehicle was selected
          if (d.step_2 && d.step_2.vehicle_id) {
            setTimeout(() => {
              updateSummaryVehicle(d.step_2.vehicle_id);
            }, 300);
          }
        });
      }, 200);
    }
    
    // Load vehicle billing data and restore Step 4 fields if on step 4 or step 5
    if (targetStep >= 4) {
      setTimeout(() => {
        // Load vehicle billing data first (to populate default values if needed)
        // But we'll restore Step 4 values after it loads
        loadVehicleDataForBilling();
        
        // Restore Step 4 values after vehicle data loads (this ensures draft values take precedence)
        const restoreStep4Values = () => {
          const step4Data = state.data.step4RestoreData || d.step_4;
          if (!step4Data) return;
          
          // Restore all Step 4 billing fields (always restore, even if empty)
          if (qs('#rent_24h')) qs('#rent_24h').value = step4Data.rent_24h || '';
          if (qs('#km_limit')) qs('#km_limit').value = step4Data.km_limit || '';
          if (qs('#extra_per_hour')) qs('#extra_per_hour').value = step4Data.extra_per_hour || '';
          if (qs('#extra_per_km')) qs('#extra_per_km').value = step4Data.extra_per_km || '';
          if (qs('#discount_amount')) qs('#discount_amount').value = step4Data.discount_amount || '';
          if (qs('#discount_type')) qs('#discount_type').value = step4Data.discount_type || 'amount';
          if (qs('#advance_payment')) qs('#advance_payment').value = step4Data.advance_payment || '';
          if (qs('#payment_method')) qs('#payment_method').value = step4Data.payment_method || '';
          
          // Restore additional charges if stored
          if (step4Data.additional_charges && Array.isArray(step4Data.additional_charges) && step4Data.additional_charges.length > 0) {
            const addBtn = qs('#addAdditionalCharge');
            if (addBtn) {
              // Wait a bit for DOM to be ready (especially if step 4 is not visible)
              setTimeout(() => {
                // Clear existing additional charge rows first
                const existingRemoveBtns = qsa('.remove-charge');
                if (existingRemoveBtns.length > 0) {
                  existingRemoveBtns.forEach((btn, idx) => {
                    setTimeout(() => {
                      if (btn && btn.parentElement) {
                        btn.click();
                      }
                    }, idx * 50);
                  });
                }
                
                // Wait for rows to be cleared, then add saved charges
                setTimeout(() => {
                  step4Data.additional_charges.forEach((charge, idx) => {
                    // Click add button for each charge (skip first if using existing row)
                    setTimeout(() => {
                      if (idx >= 1) {
                        addBtn.click();
                      }
                      // Wait for new row to be added
                      setTimeout(() => {
                        const rows = qsa('.charge-description');
                        const amounts = qsa('.charge-amount');
                        const targetIdx = idx < rows.length ? idx : rows.length - 1;
                        if (rows[targetIdx]) rows[targetIdx].value = charge.description || '';
                        if (amounts[targetIdx]) amounts[targetIdx].value = charge.amount || '';
                      }, 100);
                    }, idx * 200);
                  });
                  // Recompute after all charges are restored
                  setTimeout(() => {
                    recomputeSummary();
                  }, step4Data.additional_charges.length * 250);
                }, 500);
              }, 400);
            }
          }
          
          // Trigger discount display update
          if (qs('#discount_amount') && qs('#discount_type')) {
            const discountAmount = parseFloat(qs('#discount_amount').value || 0);
            const discountType = qs('#discount_type').value || 'amount';
            const display = qs('#booking_discount_display') || qs('#discount_display');
            if (display) {
              if (discountType === 'percentage') {
                display.value = `${discountAmount}%`;
              } else {
                display.value = `₹${discountAmount.toFixed(2)}`;
              }
            }
          }
          
          // Recalculate summary after restoring Step 4 values
          recomputeSummary();
        };
        
        // Apply restoration after vehicle data loads (multiple attempts to ensure it works)
        setTimeout(restoreStep4Values, 800);
        setTimeout(restoreStep4Values, 1200);
      }, 400);
    }
    
    // Initialize customer dropdown if on step 3
    if (targetStep === 3) {
      setTimeout(() => {
        initCustomerDropdown();
      }, 200);
    }
    
    // If restoring to step 4 or 5, also update vehicle summary (vehicle list might already be loaded)
    if (targetStep >= 4 && d.step_2 && d.step_2.vehicle_id) {
      setTimeout(() => {
        // Try to update from vehicle card if available
        updateSummaryVehicle(d.step_2.vehicle_id);
        // If vehicle card not found, fetch vehicles first
        const vehicleCard = qs(`[data-vehicle-id="${d.step_2.vehicle_id}"]`);
        if (!vehicleCard && targetStep >= 2) {
          fetchVehicles().then(() => {
            setTimeout(() => {
              updateSummaryVehicle(d.step_2.vehicle_id);
            }, 500);
          });
        }
      }, 800);
    }
  }

  // Set default dates on page load
  function setDefaultDates() {
    const pickupInput = qs('#pickup_datetime');
    const dropoffInput = qs('#dropoff_datetime');
    
    // Set min attribute for pickup date to today (prevent selecting past dates)
    if (pickupInput) {
      const now = new Date();
      // Remove seconds and milliseconds for datetime-local format
      now.setSeconds(0, 0);
      const year = now.getFullYear();
      const month = String(now.getMonth() + 1).padStart(2, '0');
      const day = String(now.getDate()).padStart(2, '0');
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
      pickupInput.setAttribute('min', minDateTime);
      
      // If pickup input is empty, set to current date and time
      if (!pickupInput.value) {
        pickupInput.value = minDateTime;
      } else {
        // If pickup has a value but it's in the past, reset to now
        const pickupDate = new Date(pickupInput.value);
        if (pickupDate < now) {
          pickupInput.value = minDateTime;
        }
      }
    }
    
    // Update dropoff min when pickup changes
    if (pickupInput && dropoffInput) {
      // Set initial dropoff min based on pickup value
      if (pickupInput.value) {
        dropoffInput.setAttribute('min', pickupInput.value);
      }
    }
    
    if (dropoffInput && !dropoffInput.value && pickupInput?.value) {
      // Set to start date + 24 hours
      const pickupDate = new Date(pickupInput.value);
      pickupDate.setHours(pickupDate.getHours() + 24);
      const year = pickupDate.getFullYear();
      const month = String(pickupDate.getMonth() + 1).padStart(2, '0');
      const day = String(pickupDate.getDate()).padStart(2, '0');
      const hours = String(pickupDate.getHours()).padStart(2, '0');
      const minutes = String(pickupDate.getMinutes()).padStart(2, '0');
      dropoffInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
    }
    
    // Auto-load vehicles if both dates are set
    if (pickupInput?.value && dropoffInput?.value) {
      setTimeout(() => {
        fetchVehicles();
        updateSummaryDates();
      }, 300);
    }
  }
  
  // Set default dates when page loads
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setDefaultDates);
  } else {
    setTimeout(setDefaultDates, 100);
  }

  // Autosave Step 1 on change and reload vehicles
  ['#pickup_datetime','#dropoff_datetime'].forEach(sel => {
    const el = qs(sel);
    if (el) {
      el.addEventListener('change', () => {
        // Don't autosave during draft restoration
        if (!isRestoringDraft) {
          saveStep({
            step: 1,
            pickup_datetime: qs('#pickup_datetime').value,
            dropoff_datetime: qs('#dropoff_datetime').value,
          });
        }
        updateSummaryDates();
        
        // If both dates are set, reload vehicles
        const pickupDt = qs('#pickup_datetime')?.value;
        const dropoffDt = qs('#dropoff_datetime')?.value;
        if (pickupDt && dropoffDt) {
          // Validate dates
          const pd = new Date(pickupDt);
          const dd = new Date(dropoffDt);
          if (isFinite(pd) && isFinite(dd) && dd > pd) {
            fetchVehicles();
          }
        }
      });
      
      // Also update dropoff when pickup changes (auto-set to +24hrs if empty)
      if (sel === '#pickup_datetime') {
        el.addEventListener('change', () => {
          const dropoffInput = qs('#dropoff_datetime');
          
          // Update dropoff min to be at least pickup date
          if (dropoffInput && el.value) {
            dropoffInput.setAttribute('min', el.value);
          }
          
          // Auto-set dropoff to +24hrs if empty
          if (dropoffInput && !dropoffInput.value && el.value) {
            const pickupDate = new Date(el.value);
            pickupDate.setHours(pickupDate.getHours() + 24);
            const year = pickupDate.getFullYear();
            const month = String(pickupDate.getMonth() + 1).padStart(2, '0');
            const day = String(pickupDate.getDate()).padStart(2, '0');
            const hours = String(pickupDate.getHours()).padStart(2, '0');
            const minutes = String(pickupDate.getMinutes()).padStart(2, '0');
            dropoffInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
            dropoffInput.dispatchEvent(new Event('change', { bubbles: true }));
          }
        });
      }
    }
  });

  // Navigation buttons
  qsa('[data-prev]').forEach(btn => {
    btn.addEventListener('click', () => {
      // Map previous steps: from step 3 go to step 1, from step 4 go to step 3, from step 5 go to step 4
      let prevStep = state.step - 1;
      if (state.step === 3) prevStep = 1; // From customer, go back to dates & vehicles
      prevStep = Math.max(1, prevStep);
      // Going back doesn't need validation - skip it
      showStep(prevStep, true);
      if (prevStep === 3) setTimeout(initCustomerDropdown, 100);
      if (prevStep === 4) setTimeout(loadVehicleDataForBilling, 100);
      if (prevStep === 1) {
        // Reload vehicles when going back to step 1
        const pickupDt = qs('#pickup_datetime')?.value;
        const dropoffDt = qs('#dropoff_datetime')?.value;
        if (pickupDt && dropoffDt) {
          setTimeout(() => fetchVehicles(), 300);
        }
      }
    });
  });
  
  // Clickable progress steps for navigation
  qsa('.clickable-step').forEach(stepEl => {
    stepEl.addEventListener('click', (e) => {
      const targetStep = Number(stepEl.dataset.step);
      if (targetStep && targetStep !== state.step) {
        // Validate dates first before allowing any navigation from Step 1
        if (targetStep >= 2 || state.step >= 2) {
          const pickupDt = qs('#pickup_datetime')?.value;
          const dropoffDt = qs('#dropoff_datetime')?.value;
          
          if (pickupDt && dropoffDt) {
            const pickupDate = new Date(pickupDt);
            const dropoffDate = new Date(dropoffDt);
            
            if (isNaN(pickupDate.getTime()) || isNaN(dropoffDate.getTime())) {
              alert('Invalid date format. Please check your pickup and drop-off dates in Step 1.');
              showStep(1);
              return;
            }
            
            if (dropoffDate <= pickupDate) {
              alert('Drop-off date and time must be after pickup date and time. Please correct the dates in Step 1.');
              showStep(1);
              return;
            }
          }
        }
        // Allow navigation - validation will happen in showStep
        showStep(targetStep);
      }
    });
  });
  
  // Generic next buttons - validation handled in showStep()
  qsa('[data-next]').forEach(btn => {
    // Skip if it's step4Next or step3Next - they have their own handlers
    if (btn.id === 'step4Next' || btn.id === 'step3Next') return;
    btn.addEventListener('click', () => {
      // Map steps: after step 1, next is step 3 (customer), then 4, then 5
      let nextStep = state.step + 1;
      if (state.step === 1) nextStep = 3; // Skip step 2 (merged into step 1)
      nextStep = Math.min(5, nextStep);
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
      qs('#vehicleList').innerHTML = '<div class="text-center text-muted py-4"><div class="spinner-border spinner-border-sm me-2" role="status"><span class="visually-hidden">Loading...</span></div>Loading vehicles...</div>';
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
          // Navigate directly to step 3 (customer) - no next button needed
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
      
      // Handle both response formats (vehicle object or direct properties)
      const vehicle = vehicleData.vehicle || vehicleData;
      
      // Populate fields ONLY if they're completely empty (don't overwrite user-entered values)
      // This allows users to customize rental info without vehicle defaults overwriting them
      if (vehicle.rental_price_24h) {
        const currentValue = qs('#rent_24h')?.value?.trim();
        // Only populate if field is truly empty (not 0, empty string, or whitespace)
        if (!currentValue) {
          qs('#rent_24h').value = vehicle.rental_price_24h;
        }
      }
      if (vehicle.km_limit_per_booking) {
        const currentKmLimit = qs('#km_limit')?.value?.trim();
        if (!currentKmLimit) {
          qs('#km_limit').value = vehicle.km_limit_per_booking;
        }
      }
      if (vehicle.extra_rental_price_per_hour) {
        const currentExtraHour = qs('#extra_per_hour')?.value?.trim();
        if (!currentExtraHour) {
          qs('#extra_per_hour').value = vehicle.extra_rental_price_per_hour;
        }
      }
      if (vehicle.extra_price_per_km) {
        const currentExtraKm = qs('#extra_per_km')?.value?.trim();
        if (!currentExtraKm) {
          qs('#extra_per_km').value = vehicle.extra_price_per_km;
        }
      }
      
      // Also update vehicle summary if vehicle data is available
      if (vehicle.vehicle_make && vehicle.vehicle_model) {
        const vehicleName = `${vehicle.vehicle_make} ${vehicle.vehicle_model}`.trim();
        const priceText = `₹${parseFloat(vehicle.rental_price_24h || 0).toFixed(2)} / 24hrs`;
        const sumVehicleDiv = qs('#sumVehicle');
        if (sumVehicleDiv) {
          sumVehicleDiv.classList.remove('d-none');
          if (qs('#sumVehicleName')) qs('#sumVehicleName').textContent = vehicleName;
          if (qs('#sumVehicleAmount')) qs('#sumVehicleAmount').textContent = priceText;
        }
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
        if (priceMatch) {
          const price = priceMatch[1].replace(/,/g, '');
          const currentValue = qs('#rent_24h')?.value;
          // Populate if field is empty or value is 0
          if (!currentValue || parseFloat(currentValue) <= 0) {
            qs('#rent_24h').value = price;
          }
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
      if (!res.ok) {
        throw new Error(`HTTP ${res.status}`);
      }
      
      // Check if response has content before parsing JSON
      const text = await res.text();
      if (!text || text.trim() === '') {
        throw new Error('Empty response from server');
      }
      
      const data = JSON.parse(text);
      if (qs('#sumSubTotal')) qs('#sumSubTotal').textContent = `₹${data.subTotal}`;
      if (qs('#sumDiscount')) qs('#sumDiscount').textContent = `₹${data.discount}`;
      if (qs('#sumAdvance')) qs('#sumAdvance').textContent = `₹${data.advance}`;
      if (qs('#sumDue')) qs('#sumDue').textContent = `₹${data.amountDue}`;
    } catch (e) {
      console.error('Error computing summary:', e);
      // Set default values on error
      if (qs('#sumSubTotal')) qs('#sumSubTotal').textContent = '₹0.00';
      if (qs('#sumDiscount')) qs('#sumDiscount').textContent = '₹0.00';
      if (qs('#sumAdvance')) qs('#sumAdvance').textContent = '₹0.00';
      if (qs('#sumDue')) qs('#sumDue').textContent = '₹0.00';
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
    if (pickupDt || dropoffDt) {
      allDraftData.step_1 = {
        pickup_datetime: pickupDt || '',
        dropoff_datetime: dropoffDt || '',
      };
    }
    
    // Step 1 also includes vehicle selection (merged from previous step 2)
    // ALWAYS check and save if vehicle selected (check multiple sources)
    const vehicleId = state.data.selectedVehicleId || 
                     (qs('[data-vehicle-id]') && qs('.vehicle-card.selected')?.dataset?.vehicleId) ||
                     (qs('[data-vehicle-id]') && qs('[data-vehicle-id].selected')?.dataset?.vehicleId);
    if (vehicleId) {
      // Ensure it's saved in state too
      state.data.selectedVehicleId = vehicleId;
      // Save vehicle as step_2 for backend compatibility (backend still expects step_2 for vehicle)
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
    
    // Step 4 - Collect ALL billing data (always collect if on step 4 or 5, or if any field has data)
    const rent24h = qs('#rent_24h')?.value;
    const kmLimit = qs('#km_limit')?.value;
    const extraHour = qs('#extra_per_hour')?.value;
    const extraKm = qs('#extra_per_km')?.value;
    const discountAmt = qs('#discount_amount')?.value;
    const discountType = qs('#discount_type')?.value;
    const advancePay = qs('#advance_payment')?.value;
    const payMethod = qs('#payment_method')?.value;
    
    // Collect additional charges
    const charges = [];
    qsa('.charge-description').forEach((descInput, idx) => {
      const amountInput = qsa('.charge-amount')[idx];
      if (descInput && amountInput) {
        const desc = descInput.value?.trim() || '';
        const amount = parseFloat(amountInput.value || 0);
        if (desc || amount > 0) {
          charges.push({ description: desc, amount: amount });
        }
      }
    });
    
    // Always save step 4 data if we're on step 4 or 5, or if any billing field has data
    if (currentStep >= 4 || rent24h || kmLimit || extraHour || extraKm || discountAmt || advancePay || payMethod || charges.length > 0) {
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
    
    // Save all steps data to backend
    // Note: We have 4 pages: Step 1 (dates + vehicle merged), Step 3 (customer), Step 4 (billing), Step 5 (confirm - no data)
    // Step 2 is saved separately for backend compatibility (vehicle selection)
    for (const [stepKey, stepData] of Object.entries(allDraftData)) {
      const stepNum = stepKey.replace('step_', '');
      try {
        await saveStep({ step: parseInt(stepNum), ...stepData });
      } catch (error) {
        console.error(`Error saving ${stepKey}:`, error);
        // Continue saving other steps even if one fails
      }
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
    if (!vehicleId) return;
    
    const sumVehicleDiv = qs('#sumVehicle');
    if (!sumVehicleDiv) return;
    
    // Try to get from vehicle card in DOM first (fastest)
    const vehicleCard = qs(`[data-vehicle-id="${vehicleId}"]`);
    if (vehicleCard) {
      const name = vehicleCard.querySelector('.vehicle-name')?.textContent || '';
      const price = vehicleCard.querySelector('.vehicle-price')?.textContent || '';
      sumVehicleDiv.classList.remove('d-none');
      if (qs('#sumVehicleName')) qs('#sumVehicleName').textContent = name;
      if (qs('#sumVehicleAmount')) qs('#sumVehicleAmount').textContent = price;
      return;
    }
    
    // If vehicle card not in DOM, fetch from billing API (which includes vehicle name and price)
    try {
      const url = window.bookingFlow?.vehicleBillingUrl?.replace(':vehicleId', vehicleId) || 
                  `/business/bookings/flow/vehicle/${vehicleId}/billing`;
      const res = await fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });
      if (res.ok) {
        const data = await res.json();
        if (data.vehicle) {
          const vehicleName = `${data.vehicle.vehicle_make || ''} ${data.vehicle.vehicle_model || ''}`.trim();
          const priceText = `₹${parseFloat(data.vehicle.rental_price_24h || 0).toFixed(2)} / 24hrs`;
          sumVehicleDiv.classList.remove('d-none');
          if (qs('#sumVehicleName')) qs('#sumVehicleName').textContent = vehicleName;
          if (qs('#sumVehicleAmount')) qs('#sumVehicleAmount').textContent = priceText;
        }
      }
    } catch (e) {
      console.warn('Could not fetch vehicle details for summary:', e);
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
    };
    if (!p) return;
    const [pd, dd] = [p.pickup_datetime, p.dropoff_datetime];
    if (qs('#sumPickupDate')) qs('#sumPickupDate').textContent = formatDate(pd);
    if (qs('#sumPickupTime')) qs('#sumPickupTime').textContent = `Time: ${formatTime(pd)}`;
    if (qs('#sumDropDate')) qs('#sumDropDate').textContent = formatDate(dd);
    if (qs('#sumDropTime')) qs('#sumDropTime').textContent = `Time: ${formatTime(dd)}`;
  }
  ['#pickup_datetime','#dropoff_datetime'].forEach(sel=>{
    const el = qs(sel); if (el) el.addEventListener('change', ()=>updateSummaryDates());
  });

  // Helper function to format datetime-local value for display
  function formatDateTimeForPreview(dtString) {
    if (!dtString) return '';
    try {
      const dt = new Date(dtString);
      if (isNaN(dt.getTime())) return dtString;
      const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
      return dt.toLocaleDateString('en-US', options);
    } catch (e) {
      return dtString;
    }
  }

  // Step 5: Preview and Confirm
  function renderConfirmPreview() {
    const el = qs('#confirmPreview');
    if (!el) {
      console.warn('confirmPreview element not found');
      return;
    }

    // Get dates
    const pickupDt = qs('#pickup_datetime')?.value || '';
    const dropoffDt = qs('#dropoff_datetime')?.value || '';
    
    // Format dates for display
    const pickupFormatted = formatDateTimeForPreview(pickupDt);
    const dropoffFormatted = formatDateTimeForPreview(dropoffDt);

    // Get vehicle info from summary or fallback
    let vehicleName = qs('#sumVehicleName')?.textContent?.trim() || '';
    let vehicleAmount = qs('#sumVehicleAmount')?.textContent?.trim() || '';
    
    // Fallback: try to get from vehicle card if summary is empty
    if (!vehicleName && state.data.selectedVehicleId) {
      const vehicleCard = qs(`[data-vehicle-id="${state.data.selectedVehicleId}"]`);
      if (vehicleCard) {
        vehicleName = vehicleCard.querySelector('.vehicle-name')?.textContent?.trim() || '';
        vehicleAmount = vehicleCard.querySelector('.vehicle-price')?.textContent?.trim() || '';
      }
    }

    // Get customer info
    let customerName = qs('#sumCustomerName')?.textContent?.trim() || '';
    if (!customerName) {
      const customerInput = qs('#booking_customer_select') || qs('#customer_select');
      customerName = customerInput?.value?.trim() || selectedCustomerData?.name || '';
    }

    // Get billing info from summary, or calculate from form fields
    let subTotal = qs('#sumSubTotal')?.textContent?.trim() || '₹0.00';
    let discount = qs('#sumDiscount')?.textContent?.trim() || '₹0.00';
    let advance = qs('#sumAdvance')?.textContent?.trim() || '₹0.00';
    let amountDue = qs('#sumDue')?.textContent?.trim() || '₹0.00';

    // Fallback: calculate from form fields if summary is empty
    if (subTotal === '₹0.00' || !subTotal.includes('₹')) {
      const rent24h = parseFloat(qs('#rent_24h')?.value || 0);
      const pickup = new Date(pickupDt);
      const dropoff = new Date(dropoffDt);
      if (!isNaN(pickup.getTime()) && !isNaN(dropoff.getTime())) {
        const hours = Math.max(24, (dropoff - pickup) / (1000 * 60 * 60));
        const days = Math.ceil(hours / 24);
        const baseRental = rent24h * days;
        
        // Additional charges
        let additionalCharges = 0;
        qsa('.charge-amount').forEach(input => {
          additionalCharges += parseFloat(input.value || 0);
        });
        
        const calculatedSubTotal = baseRental + additionalCharges;
        subTotal = `₹${calculatedSubTotal.toFixed(2)}`;
        
        // Discount
        const discountAmount = parseFloat(qs('#discount_amount')?.value || 0);
        const discountType = qs('#discount_type')?.value || 'amount';
        let discountValue = 0;
        if (discountType === 'percentage') {
          discountValue = calculatedSubTotal * (discountAmount / 100);
        } else {
          discountValue = discountAmount;
        }
        discountValue = Math.min(discountValue, calculatedSubTotal);
        discount = `₹${discountValue.toFixed(2)}`;
        
        // Advance
        const advanceAmount = parseFloat(qs('#advance_payment')?.value || 0);
        advance = `₹${advanceAmount.toFixed(2)}`;
        
        // Amount due
        const totalAmount = calculatedSubTotal - discountValue;
        const amountDueValue = Math.max(0, totalAmount - advanceAmount);
        amountDue = `₹${amountDueValue.toFixed(2)}`;
      }
    }

    el.innerHTML = `
      <div class="vstack gap-3">
        <div>
          <div class="text-muted small mb-1">Trip Details</div>
          <div class="small mb-1"><strong>Pickup:</strong> ${pickupFormatted || pickupDt}</div>
          <div class="small mb-1"><strong>Drop-off:</strong> ${dropoffFormatted || dropoffDt}</div>
        </div>
        <div>
          <div class="text-muted small mb-1">Vehicle</div>
          <div class="small fw-semibold">${vehicleName || 'Not selected'}</div>
          ${vehicleAmount ? `<div class="small text-muted">${vehicleAmount}</div>` : ''}
        </div>
        <div>
          <div class="text-muted small mb-1">Customer</div>
          <div class="small fw-semibold">${customerName || 'Not selected'}</div>
        </div>
        <div>
          <div class="text-muted small mb-2">Billing Summary</div>
          <div class="d-flex justify-content-between small mb-1"><span>Sub Total</span><span>${subTotal}</span></div>
          <div class="d-flex justify-content-between small mb-1 text-muted"><span>Discount</span><span>${discount}</span></div>
          <div class="d-flex justify-content-between small mb-1"><span>Advance Payment</span><span>${advance}</span></div>
          <hr class="my-2">
          <div class="d-flex justify-content-between fw-semibold"><span>Amount Due</span><span class="text-primary">${amountDue}</span></div>
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
      if (!p || !d) { showError('Please fill pickup/drop dates'); return false; }
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
      let rent = parseFloat(qs('#rent_24h')?.value || '0');
      
      // If rent_24h is empty or 0, try to get from vehicle data
      if (!(rent > 0) && state.data.selectedVehicleId) {
        // Try to load vehicle data synchronously or use cached data
        const vehicleCard = qs(`[data-vehicle-id="${state.data.selectedVehicleId}"]`);
        if (vehicleCard) {
          const priceText = vehicleCard.querySelector('.vehicle-price')?.textContent || '';
          const priceMatch = priceText.match(/₹([\d,]+)/);
          if (priceMatch) {
            rent = parseFloat(priceMatch[1].replace(/,/g, ''));
            // Auto-populate the field
            if (qs('#rent_24h')) {
              qs('#rent_24h').value = rent;
            }
          }
        }
      }
      
      if (!(rent > 0)) { 
        showError('Please enter a valid rent for 24 hrs'); 
        return false; 
      }
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
      
      // Validate Step 1 dates first (most critical validation)
      if (!validateStep(1)) { 
        // Scroll to Step 1 to show the error
        showStep(1);
        return; 
      }
      
      // Ensure rent_24h is populated before validation
      if (!qs('#rent_24h')?.value || parseFloat(qs('#rent_24h')?.value || 0) <= 0) {
        // Try to load vehicle data if not already loaded
        if (state.data.selectedVehicleId) {
          await loadVehicleDataForBilling();
          // Wait a bit for field to be populated
          await new Promise(resolve => setTimeout(resolve, 200));
        }
      }
      
      if (!validateStep(4)) { return; }
      
      // Set flag and disable button
      isBookingInProgress = true;
      confirmBtn.disabled = true;
      confirmBtn.style.pointerEvents = 'none';
      const originalText = confirmBtn.textContent;
      confirmBtn.textContent = 'Creating Booking...';
      
      // Collect all form data before sending
      const pickupDt = qs('#pickup_datetime')?.value || state.data.step_1?.pickup_datetime || '';
      const dropoffDt = qs('#dropoff_datetime')?.value || state.data.step_1?.dropoff_datetime || '';
      
      // Final date validation before sending
      if (pickupDt && dropoffDt) {
        const pickupDate = new Date(pickupDt);
        const dropoffDate = new Date(dropoffDt);
        if (isNaN(pickupDate.getTime()) || isNaN(dropoffDate.getTime())) {
          showError('Invalid date format. Please check your dates.');
          isBookingInProgress = false;
          confirmBtn.disabled = false;
          confirmBtn.style.pointerEvents = '';
          confirmBtn.textContent = originalText;
          return;
        }
        if (dropoffDate <= pickupDate) {
          showError('Drop-off date and time must be after pickup date and time.');
          isBookingInProgress = false;
          confirmBtn.disabled = false;
          confirmBtn.style.pointerEvents = '';
          confirmBtn.textContent = originalText;
          showStep(1); // Navigate back to Step 1
          return;
        }
      }
      
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
          
          // Show success message before redirect
          const successMessage = data.message || 'Booking created successfully!';
          
          // Success - redirect immediately with success parameter
          if (data.redirect_url) {
            // Add success message to URL as query parameter
            const url = new URL(data.redirect_url, window.location.origin);
            url.searchParams.set('booking_created', 'success');
            url.searchParams.set('message', encodeURIComponent(successMessage));
            window.location.href = url.toString();
          } else {
            const redirectUrl = window.bookingFlow?.successRedirect || '/business/bookings';
            const url = new URL(redirectUrl, window.location.origin);
            url.searchParams.set('booking_created', 'success');
            url.searchParams.set('message', encodeURIComponent(successMessage));
            window.location.href = url.toString();
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


