// Define the Step 4 elements and related functions
let step4Elements = {
  step4: getElement("step4"),
  step5: getElement("step5"),
  vehicleRent24HrsElement: getElement("vehicleRent24Hrs"),
  kmLimitPBookingElement: getElement("kmLimitPBooking"),
  extraPricePHElement: getElement("extraPricePH"),
  extraPricePKElement: getElement("extraPricePK"),
  additionalChargeElement: getElement("additionalCharge"),
  aCCurrencyButtonElement: getElement("aCCurrencyButton"),
  aCPercentButtonElement: getElement("aCPercentButton"),
  aCDescriptionElement: getElement("aCDescription"),

  discountInputElement: getElement("discountInput"),
  dCurrencyButtonElement: getElement("dCurrencyButton"),
  dPercentButtonElement: getElement("dPercentButton"),
  dDescriptionElement: getElement("dDescription"),

  advanceInputElement: getElement("advanceInput"),

  summaryTotalPriceElement: getElement("summaryTotalPrice"),
  additionalInfoElement: getElement("additional-info"),
  paymentMethodElement: getElement("paymentMethod"),
  paymentNotesElement: getElement("otherPaymentInfo"),
  otherPaymentInfoMainDivElement: getElement("otherPaymentInfoMainDiv"),
};

function addEventListenerStep4(bookingData) {
  if (!window.BookingManagement?.showSummaryVehicles) return;

  const getSanitizedValue = (el) => el?.value.trim() || "0";

  // === Input fields mapping ===
  const inputBindings = [
    {
      el: step4Elements.vehicleRent24HrsElement,
      target: bookingData.selectedVehicle,
      key: "rental_price_24h",
      triggerSummary: true,
    },
    {
      el: step4Elements.kmLimitPBookingElement,
      target: bookingData.selectedVehicle,
      key: "kilometer_limit",
    },
    {
      el: step4Elements.extraPricePHElement,
      target: bookingData.selectedVehicle,
      key: "extra_price_per_hour",
    },
    {
      el: step4Elements.extraPricePKElement,
      target: bookingData.selectedVehicle,
      key: "extra_price_per_km",
    },
    {
      el: step4Elements.additionalChargeElement,
      target: bookingData,
      key: "additionalChargeAmount",
      triggerSummary: true,
    },
    {
      el: step4Elements.discountInputElement,
      target: bookingData,
      key: "discountAmount",
      triggerSummary: true,
    },
    {
      el: step4Elements.advanceInputElement,
      target: bookingData,
      key: "advanceAmount",
      triggerSummary: true,
    },
  ];

  inputBindings.forEach(({ el, target, key, triggerSummary }) => {
    el?.addEventListener("input", () => {
      target[key] = getSanitizedValue(el);
      if (triggerSummary) window.BookingManagement.showSummaryVehicles();
    });
  });

  // === Description fields (string, no default 0) ===
  step4Elements.aCDescriptionElement?.addEventListener("input", () => {
    bookingData.additionalChargeName =
      step4Elements.aCDescriptionElement.value.trim();
    window.BookingManagement.showSummaryVehicles();
  });

  step4Elements.dDescriptionElement?.addEventListener("input", () => {
    bookingData.discountName = step4Elements.dDescriptionElement.value.trim();
    window.BookingManagement.showSummaryVehicles();
  });

  // === Button toggle logic ===
  const setupToggleButton = (button1, button2, targetType = 1, value) => {
    button1?.addEventListener("click", () => {
      if (targetType == 2) {
        bookingData.discountType = value;
        console.log("bookingData.discountType", bookingData.discountType);
      } else {
        bookingData.additionalChargeType = value;
        console.log(
          "bookingData.additionalChargeType",
          bookingData.additionalChargeType
        );
      }

      button1.setAttribute("data-active", "true");
      button2?.removeAttribute("data-active");
      window.BookingManagement.showSummaryVehicles();
    });
  };

  // Additional Charge Type (1: ₹, 2: %)
  setupToggleButton(
    step4Elements.aCCurrencyButtonElement,
    step4Elements.aCPercentButtonElement,
    1,
    1
  );
  setupToggleButton(
    step4Elements.aCPercentButtonElement,
    step4Elements.aCCurrencyButtonElement,
    1,
    2
  );

  // Discount Type (1: ₹, 2: %)
  setupToggleButton(
    step4Elements.dCurrencyButtonElement,
    step4Elements.dPercentButtonElement,
    2,
    1
  );
  setupToggleButton(
    step4Elements.dPercentButtonElement,
    step4Elements.dCurrencyButtonElement,
    2,
    2
  );
}

// Function to populate booking summary
function populateStep4(bookingData) {
  const { selectedVehicle } = bookingData;

  setValue(
    step4Elements.vehicleRent24HrsElement,
    selectedVehicle.rental_price_24h
  );
  setValue(
    step4Elements.kmLimitPBookingElement,
    selectedVehicle.kilometer_limit
  );
  setValue(
    step4Elements.extraPricePHElement,
    selectedVehicle.extra_price_per_hour
  );
  setValue(
    step4Elements.extraPricePKElement,
    selectedVehicle.extra_price_per_km
  );

  setValue(
    step4Elements.additionalChargeElement,
    bookingData.additionalChargeAmount
  );
  setValue(
    step4Elements.aCDescriptionElement,
    bookingData.additionalChargeName
  );
  setValue(step4Elements.advanceInputElement, bookingData.advanceAmount);

  setValue(step4Elements.discountInputElement, bookingData.discountAmount);
  setValue(step4Elements.dDescriptionElement, bookingData.discountName);

  customerDetailsStep4(bookingData);
  appendGrandTotal(bookingData);
}
const customerDetailsStep4 = (bookingData) => {
  const { customer } = bookingData;
  // Customer Details
  const bookingSummaryCustomerDetailsSection =
    step4Elements.step5.querySelector("#customerDetails");

  const customerCorporateDriver = encodeURIComponent(
    JSON.stringify(customer.customerCorporateDriver)
  );
  fetch(
    `${baseUrl}bookingManagement/getCustomerTypeForm?customerType=${customer.customerType}&customerId=${customer.customerId}&customerCorporateDriver=${customerCorporateDriver}&orderSummaryFlag=true`
  )
    .then((response) => response.text())
    .then((html) => {
      if (html == false) {
        bookingSummaryCustomerDetailsSection.innerHTML = "";
      } else {
        bookingSummaryCustomerDetailsSection.innerHTML = html;
      }
    })
    .catch((error) => console.error("Error fetching customers:", error));
};

function appendGrandTotal(bookingData) {
  console.log("appendGrandTotal called with", bookingData);

  if (window.BookingManagement?.calculateTotalAmount) {
    const grandTotal =
      window.BookingManagement.calculateTotalAmount(bookingData);
    setValue(step4Elements.summaryTotalPriceElement, numToFloat(grandTotal));
  }
}

function step4PreFunctions(bookingData) {
  populateStep4(bookingData);

  step4Elements.additionalInfoElement?.addEventListener("input", () => {
    bookingData.additionalInfo =
      step4Elements.additionalInfoElement.value.trim();
  });

  step4Elements.paymentMethodElement?.addEventListener("change", () => {
    bookingData.advancePaymentMethod =
      step4Elements.paymentMethodElement.value.trim();
    toggleDisplay(
      step4Elements.otherPaymentInfoMainDivElement,
      bookingData.advancePaymentMethod != "Cash"
    );
  });
  step4Elements.paymentNotesElement?.addEventListener("input", () => {
    bookingData.paymentRemarks =
      step4Elements.paymentNotesElement.value.trim();
  });

  step4Elements.discountInputElement?.addEventListener("input", () => {
    const discountValue =
      parseFloat(step4Elements.discountInputElement.value) || 0;
    bookingData.discountAmount = discountValue > 0 ? discountValue : 0;
    appendGrandTotal(bookingData);
  });

  step4Elements.advanceInputElement?.addEventListener("input", () => {
    const advanceValue =
      parseFloat(step4Elements.advanceInputElement.value) || 0;
    bookingData.advanceAmount = advanceValue > 0 ? advanceValue : 0;
    appendGrandTotal(bookingData);
  });

  appendGrandTotal(bookingData); // Initial call
}
// validateStep4 function
function validateStep4(bookingData) {
  return new Promise((resolve, reject) => {
    // Make an AJAX GET request to check vehicle availability
    $.ajax({
      url: `${baseUrl}/bookingManagement/checkVehicleAvailabilityForBooking`,
      type: "POST",
      data: { bookingData: JSON.stringify(bookingData) }, // Ensure the data structure matches backend expectations
      success: function (response) {
        // Check if the vehicle is available
        if (response.vehicleAvailability) {
          resolve(true); // Resolve with true if the vehicle is available
        } else {
          alert(
            "Sorry, this vehicle has been booked. Please choose another vehicle."
          );
          resolve(false); // Resolve with false if the vehicle is not available
        }
      },
      error: function (xhr, status, error) {
        console.error("Error checking vehicle availability:", error);
        alert("An error occurred while checking vehicle availability.");
        reject(error); // Reject the promise if there’s an error
      },
    });
  });
}

export { validateStep4, step4PreFunctions, addEventListenerStep4 };
