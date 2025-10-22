// Define the Step 5 elements and related functions
let step5Elements = {
  step5: getElement("step5"),
};

function populateStep5(bookingData) {
  const { step5 } = step5Elements;
  const { selectedVehicle } = bookingData;
  // Helper function to set text content
  const setTextContent_step5 = (selector, value) => {
    step5.querySelector(selector).textContent = value;
  };
  // Vehicle Details
  step5.querySelector("#vehicle-image").src =
    baseUrl + selectedVehicle.brand_image;

  setTextContent_step5(
    "#vehicle-regNumber",
    "(" + selectedVehicle.registration_number + ")"
  );
  setTextContent_step5("#vehicle-name", selectedVehicle.model_name);
  setTextContent_step5("#vehicle-fuel", selectedVehicle.fuel_type);
  setTextContent_step5("#vehicle-seats", selectedVehicle.seating_capacity);
  setTextContent_step5(
    "#vehicle-transmission",
    selectedVehicle.transmission_type
  );
  setTextContent_step5(
    "#vehicle-rate",
    CURRENCY_SYMBOL + selectedVehicle.rental_price_24h
  );
  setTextContent_step5("#vehicle-km-limit", selectedVehicle.kilometer_limit);
}

function step5PreFunctions(bookingData) {
  populateStep5(bookingData);
}
// validateStep5 function
function validateStep5(bookingData) {
  saveBookingFormData(bookingData);
}

const saveBookingFormData = (bookingData) => {
  $.ajax({
    url: baseUrl + "bookingManagement/save-bookingFormData",
    type: "POST",
    data: JSON.stringify(bookingData),
    contentType: "application/json",
    success: function (response) {
      // Check if the response structure matches expectations
      if (response.success === false) {
        const type = response.type;
        if (type == "datetime") {
          currentStep = 1;
        } else if (type === "vehicle") {
          currentStep = 2;
        } else if (type === "customer") {
          currentStep = 3;
        } else if (type == "save") {
          currentStep = 4;
          alert(response.message);
        }
        showStep(currentStep);
      } else {
        // Show success message in toast
        showToast({
          title: "Booking",
          message: response.message,
          type: "success",
        });
        setTimeout(() => {
          window.location.href = `${baseUrl}bookingManagement`;
        }, 5000);
      }
      console.log("Booking data saved successfully:", response);
    },
  });
};

export { validateStep5, step5PreFunctions };
