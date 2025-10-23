// Global variables
let currentStep = 1;
const totalSteps = 4;
let selectedVehicleId = null;
let bookingData = {};
let vehicles = [];
let dropDownCustomerTypeSelected = "";

// Define the default bookingData structure
const defaultBookingData = {
  startDateTime: "",
  endDateTime: "",
  selectedVehicle: null,
  customer: {
    customerId: 0,
    customerType: "individualCustomer",
  },
  orderBillDetails: {
    totalAmount: 0,
    discountAmount: 0,
    advanceAmount: 0,
    advancePaymentMethod: "Cash",
    advanceAmountAdditionalInfo: "",
  },
  additionalInfo: "",
};

// elements
const prevBtn = document.querySelector("#prevBtn");
const nextBtn = document.querySelector("#nextBtn");
const startDateTimeElement = document.querySelector("#startDateTime");
const endDateTimeElement = document.querySelector("#endDateTime");
const durationInfoStep1Element = document.querySelector("#durationInfoStep1");
const vehicleList = document.querySelector("#vehicleList");
const fuelTypeElement = document.querySelector("#fuelType");
const transmissionElement = document.querySelector("#transmission");
const seatingElement = document.querySelector("#seating");
const customerSelectElement = document.querySelector("#customerSelect");
const customerListElement = document.querySelector("#customerList");
const additionalInfoElement = document.querySelector("#additionalInfo");

// error elements
const startDateTimeError = document.querySelector("#startDateTimeError");
const endDateTimeError = document.querySelector("#endDateTimeError");
const summaryDateError = document.querySelector("#summaryDateError");
const vehicleSectionError = document.querySelector("#vehicleSectionError");
const customerSectionError = document.querySelector("#customerSectionError");

/**
 * Resets the bookingData object to its default values
 * @function
 */
const resetBookingData = () => {
  bookingData = structuredClone(defaultBookingData);
};

/**
 * Initializes the form and resets bookingData
 * @function
 * @listens DOMContentLoaded
 */
document.addEventListener("DOMContentLoaded", () => {
  resetBookingData();
  setupEventListeners();
  updateSteps();
  updateButtons();
});

/**
 * Updates the next and previous buttons based on the current step
 * @function
 */
const updateButtons = () => {
  prevBtn.style.display = currentStep === 1 ? "none" : "block";
  nextBtn.textContent =
    currentStep === totalSteps ? "Complete Booking" : "Next";
};

/**
 * Updates the visual state of the booking form steps
 * @function
 */
const updateSteps = () => {
  // Toggle active pane
  document
    .querySelectorAll(".tab-pane.active")
    .forEach((pane) => pane.classList.remove("active"));
  getElement(`step${currentStep}`)?.classList.add("active");
  console.log(getElement(`step${currentStep}`));

  // Update step indicators
  document.querySelectorAll(".step-item").forEach((step) => {
    const stepNum = parseInt(step.dataset.step);
    // Set 'active' class if it's the current step
    step.classList.toggle("active", stepNum == currentStep);
    // Set 'completed' class for all steps before the current one
    step.classList.toggle("completed", stepNum < currentStep);
  });

  // Special handling for step 2 (vehicle selection)
  if (currentStep === 2 && vehicleList && !vehicleList.children.length) {
    // If vehicleList exists and is empty, apply filters to load vehicles
    applyFilters();
  }

  // Update the order summary (assumed to be defined elsewhere)
  updateOrderSummary();
};

/**
 * Sets up event listeners for various elements in the booking form.
 * @function
 */
const setupEventListeners = () => {
  // Navigation buttons
  nextBtn.addEventListener("click", handleNext);
  prevBtn.addEventListener("click", handlePrev);

  // Date and time inputs
  [startDateTimeElement, endDateTimeElement].forEach((element) =>
    element.addEventListener("change", validateDates)
  );

  // Customer selection
  customerSelectElement.addEventListener("change", handleCustomerChange);
  customerListElement.addEventListener("change", handleCustomerListChange);

  // Vehicle filters
  [fuelTypeElement, transmissionElement, seatingElement].forEach((element) =>
    element.addEventListener("change", applyFilters)
  );
};

/**
 * Handles the click event of the next button.
 * @function
 */
const handleNext = () => {
  // Check if the current step is valid
  if (validateCurrentStep()) {
    // If the current step is valid, check if it is the last step
    if (currentStep < totalSteps) {
      // If it is not the last step, increment the current step
      currentStep++;
      // Update the steps and buttons
      updateSteps();
      updateButtons();
    } else {
      // If it is the last step, handle the form submission
      handleFormSubmission();
    }
  }
};
/**
 * Handles the click event of the previous button
 * @function
 */
const handlePrev = () => {
  if (currentStep > 1) {
    currentStep--;
    updateSteps();
    updateButtons();
  }
};
/**
 * Validates the current step
 * @function
 * @returns {boolean} Whether the step is valid
 */
function validateCurrentStep() {
  console.log("validateCurrentStep - " + currentStep);
  switch (currentStep) {
    case 1:
      return validateDates();
    case 2:
      if (!selectedVehicleId) {
        setInnerHTML(vehicleSectionError, "Please select a vehicle.");
        scrollToErrorElement(vehicleSectionError);
        return false;
      }
      selectVehicle(selectedVehicleId);
      return !!bookingData.selectedVehicle;
    case 3:
      return validateCustomerDetails();
    case 4:
      saveBookingFormData();
      return true;
    default:
      return false;
  }
}
/**
 * Resets the form to its initial state
 * @function
 */
function resetForm() {
  currentStep = 1;
  resetBookingData();
  getElement("bookingForm").reset();
  updateSteps();
  updateButtons();
}
/**
 * Loads and executes scripts from a set of HTML script tags
 * @function
 * @param {NodeListOf<HTMLScriptElement>} scripts - Scripts to load
 * @returns {Promise<void[]>} A promise resolving when all scripts are loaded
 */
const loadScriptsFromHTML = (scripts) =>
  Promise.all(
    Array.from(scripts).map((script) =>
      script.src
        ? new Promise((resolve, reject) => {
            const newScript = document.createElement("script");
            newScript.src = script.src;
            newScript.onload = resolve;
            newScript.onerror = reject;
            document.head.appendChild(newScript);
          })
        : Promise.resolve().then(() => eval(script.textContent))
    )
  );

/* ---------------------------- customer section start ------------------------------- */
function validateCustomerDetails() {
  if (!customerSelectElement.value) {
    setInnerHTML(customerSectionError, "Please select customer type.");
    scrollToErrorElement(customerSectionError);
    return false;
  }
  const customerID = customerListElement.value;
  if (!customerID) {
    setInnerHTML(customerSectionError, "Please select a customer.");
    scrollToErrorElement(customerSectionError);
    return false;
  }

  if (customerID == "addNew") {
    setInnerHTML(
      customerSectionError,
      "Please add a new customer before proceeding."
    );
    scrollToErrorElement(customerSectionError);
    return false;
  }

  updateOrderSummary();
  return true;
}

// Customer Type Change Handler
function handleCustomerChange(e) {
  const customerType = e.target.value;

  // Determine if the same customer type is being re-selected
  const appendCustomerFlag =
    dropDownCustomerTypeSelected === customerType ? 1 : 0;
  dropDownCustomerTypeSelected = customerType;

  const existingCustomerDropdown = getElement("existingCustomerDropdown");
  const newCustomerForm = getElement("newCustomerForm");

  if (["individualCustomer", "enterpriseCustomer"].includes(customerType)) {
    // Show dropdown for existing customers
    toggleDisplay(existingCustomerDropdown, true);
    toggleDisplay(newCustomerForm, false);

    // Fetch customers via AJAX
    fetchJsonData(
      `${baseUrl}bookingManagement/getCustomers?customerType=${customerType}`
    )
      .then((data) => {
        // Populate the dropdown
        customerListElement.innerHTML = `<option value="">Select Customer</option>`;
        data.forEach((customer) => {
          const displayName =
            customerType === "individualCustomer"
              ? customer.full_name
              : customer.company_name;
          customerListElement.innerHTML += `<option value="${customer.id}" data-customer-type="${customerType}">${displayName}</option>`;
        });
        customerListElement.innerHTML += `<option value="addNew" data-customer-type="${customerType}">Add New Customer</option>`;

        if (appendCustomerFlag) {
          handleAppendCustomer(newCustomerForm);
        }
      })
      .catch((error) => console.error("Error fetching customers:", error));
  } else {
    // Hide dropdown and form if no valid option is selected
    toggleDisplay(existingCustomerDropdown, false);
    toggleDisplay(newCustomerForm, false);
  }
}

// Handle Append Customer Logic
function handleAppendCustomer(newCustomerForm) {
  const customerIDField = newCustomerForm.querySelector("#customerId");

  if (customerIDField) {
    const customerID = customerIDField.value?.trim() || "";
    if (customerID) {
      customerListElement.value = customerID;

      // Trigger change event to update UI
      const changeEvent = new Event("change", {
        bubbles: true,
        cancelable: true,
      });
      customerListElement.dispatchEvent(changeEvent);

      // Clear the customerId field
      customerIDField.value = "";
    } else {
      console.log("Customer ID is empty.");
    }
  }
}

// Handle Add New Customer Option
function handleCustomerListChange(e) {
  const selectedOption = e.target.options[e.target.selectedIndex];
  const customerType = selectedOption.getAttribute("data-customer-type");
  const selectedValue = e.target.value === "addNew" ? 0 : e.target.value;

  const newCustomerForm = getElement("newCustomerForm");

  toggleDisplay(newCustomerForm, false);
  setInnerHTML(newCustomerForm, "");
  fetchTextData(
    `${baseUrl}bookingManagement/getCustomerTypeForm?customerType=${customerType}&customerId=${selectedValue}`
  )
    .then((html) => {
      if (html) {
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = html;
        setInnerHTML(newCustomerForm, "");
        newCustomerForm.appendChild(tempDiv.firstElementChild);

        toggleDisplay(newCustomerForm, true);

        // Load and evaluate <script> tags in the fetched HTML
        const scripts = tempDiv.querySelectorAll("script");
        loadScriptsFromHTML(scripts).then(() => {
          if (customerType === "individualCustomer") {
            const individualSubmit = getElement("individualFormSubmitButton");
            if (individualSubmit) {
              individualSubmit.onclick = () => individualFormSubmit(1);
            }
          } else {
            const corporateSubmit = getElement("corporateFormSubmitButton");
            if (corporateSubmit) {
              corporateSubmit.onclick = () => corporateFormSubmit(1);
            }
          }
        });
        showHideCustomerSubmitButton(customerType);
      }
    })
    .catch((error) => console.error("Error fetching customers:", error));
}
function showHideCustomerSubmitButton(customerType) {
  const newCustomerFormElement = getElement("newCustomerForm");
  if (!newCustomerFormElement) return;

  const customerID = newCustomerFormElement.querySelector("#customerId");

  const isExistingCustomer = (customerID?.value.trim() || "0") !== "0";

  const formConfig = {
    individualCustomer: {
      formId: "individualForm",
      submitId: "individualFormSubmitButton",
    },
    enterpriseCustomer: {
      formId: "corporateForm",
      submitId: "corporateFormSubmitButton",
    },
  };

  const config = formConfig[customerType];
  if (!config) return;

  const submitButton = newCustomerFormElement.querySelector(
    `#${config.submitId}`
  );
  if (!submitButton) return;

  toggleDisplay(submitButton, false);

  if (isExistingCustomer) {
    const fileInputs = newCustomerFormElement.querySelectorAll(
      `#${config.formId} input[type=file]`
    );

    const handleFileChange = () => {
      const hasFiles = Array.from(fileInputs).some(
        (input) => input.files.length > 0
      );
      toggleDisplay(submitButton, hasFiles ? true : false);
    };

    fileInputs.forEach((input) =>
      input.addEventListener("change", handleFileChange)
    );

    if (customerType === "enterpriseCustomer") {
      const driverInputs =
        newCustomerFormElement.querySelectorAll(".driver-info");

      driverInputs.forEach((input) => {
        input.addEventListener("input", () => {
          toggleDisplay(submitButton, true);
        });

        input.addEventListener("change", () => {
          toggleDisplay(submitButton, true);
        });
      });
    }
  } else {
    toggleDisplay(submitButton, true);
  }
}

/* ---------------------------- customer section end ------------------------------- */

/* ---------------------------- vehicles section start ------------------------------- */
// Filter functionality
const applyFilters = async (page = 1) => {
  const filters = {
    fuelType: fuelTypeElement.value,
    transmission: transmissionElement.value,
    seating: seatingElement.value,
  };

  try {
    const response = await $.ajax({
      url: `${baseUrl}/bookingManagement/getFilteredVehicles`,
      type: "GET",
      data: { ...filters, page, bookingData },
    });
    if (response?.vehicles?.length && response?.vehiclesHtml) {
      vehicles = response.vehicles;
      updateVehicleList(response.vehiclesHtml);
      updatePagination(response.pagination);
      highlightSelectedVehicle();
    } else {
      setInnerHTML(
        vehicleList,
        '<div class="alert alert-info">No vehicles match your filters</div>'
      );
    }
  } catch (error) {
    setInnerHTML(
      vehicleList,
      '<div class="alert alert-danger">Unable to fetch vehicles</div>'
    );
  }
};

// Highlight selected vehicle
const highlightSelectedVehicle = () => {
  if (selectedVehicleId) {
    const selectedCard = document.querySelector(
      `.vehicle-card[data-vehicle-id="${selectedVehicleId}"]`
    );
    selectedCard?.classList.add("selected");
  }
};

// Update vehicle list display
const updateVehicleList = (vehiclesHtml) => {
  setInnerHTML(vehicleList, vehiclesHtml);

  vehicleList.querySelectorAll(".vehicle-card").forEach((card) => {
    const isAvailable = card.dataset.vehicleStatus === "1";
    if (isAvailable) {
      card.addEventListener("click", () => handleVehicleSelection(card));
    }
  });
};

// Handle vehicle selection
const handleVehicleSelection = (card) => {
  const vehicleCards = vehicleList.querySelectorAll(".vehicle-card");
  vehicleCards.forEach((c) => c.classList.remove("selected"));
  card.classList.add("selected");
  selectedVehicleId = card.dataset.vehicleId;
};

// Update pagination controls
const updatePagination = (pagination) => {
  const paginationContainer = getElement("pagination");
  setInnerHTML(paginationContainer, "");

  for (let i = 1; i <= pagination.totalPages; i++) {
    const pageButton = document.createElement("button");
    pageButton.textContent = i;
    pageButton.className = `page-button ${
      i === pagination.currentPage ? "active" : ""
    }`;
    pageButton.addEventListener("click", (event) => {
      event.preventDefault(); // Prevent page reload
      applyFilters(i);
    });
    paginationContainer.appendChild(pageButton);
  }
};

const selectVehicle = (vehicleId) => {
  const vehicle = vehicles.find((v) => v.vehicle_id === String(vehicleId));
  if (vehicle && vehicle.status === "1") {
    bookingData.selectedVehicle = vehicle;
    bookingData.defaultSelectedVehicle = vehicle;
    updateOrderSummary();
  }
};

const postSaveCustomerDetails = (insertId) => {
  getElement("customerId").value = insertId;

  // Ensure the element exists before triggering
  if (customerSelectElement) {
    customerSelectElement.dispatchEvent(
      new Event("change", { bubbles: true, cancelable: true })
    );
  }
};
/* ---------------------------- vehicles section end ------------------------------- */

/**
 * Validates the start and end dates for a booking.
 *
 * This function checks if the provided start and end dates are valid,
 * ensuring they are in the correct format and the end date is after
 * the start date. It also updates the booking data and order summary
 * if the dates are valid.
 *
 * @returns {boolean} True if dates are valid, false otherwise.
 */
function validateDates() {
  const startDateTime = startDateTimeElement.value;
  const endDateTime = endDateTimeElement.value;

  // Clear any previous errors
  startDateTimeError.innerHTML = "";
  endDateTimeError.innerHTML = "";
  if (summaryDateError) summaryDateError.innerHTML = ""; // Clear summary error

  // Check if both dates are provided
  if (!startDateTime) {
    startDateTimeError.innerHTML = "Start date and time is required.";
    return false;
  }
  if (!endDateTime) {
    endDateTimeError.innerHTML = "End date and time is required.";
    return false;
  }

  // Try parsing dates and validate the format
  const start = new Date(startDateTime);
  const end = new Date(endDateTime);

  if (isNaN(start.getTime())) {
    startDateTimeError.innerHTML = "Invalid start date and time format.";
    return false;
  }
  if (isNaN(end.getTime())) {
    endDateTimeError.innerHTML = "Invalid end date and time format.";
    return false;
  }

  // Check that start date is before end date
  if (start >= end) {
    endDateTimeError.innerHTML = "End date must be after start date.";
    if (summaryDateError) {
      summaryDateError.innerHTML = "End date must be after start date.";
    }
    return false;
  }

  // If all validations pass, update booking data and order summary
  bookingData.startDateTime = startDateTime;
  bookingData.endDateTime = endDateTime;

  updateSummaryDateTimeDetailsSection();
  updateOrderSummary();
  return true;
}
function syncDatesFromSummary() {
  const summaryStartDateTime = getElement("summaryStartDateTime");
  const summaryEndDateTime = getElement("summaryEndDateTime");
  const startDateTimeInput = getElement("startDateTime");
  const endDateTimeInput = getElement("endDateTime");

  // Update Step 1 date fields with the new values from Order Summary
  startDateTimeInput.value = summaryStartDateTime.value;
  endDateTimeInput.value = summaryEndDateTime.value;

  // Trigger validation to ensure the new dates are correct
  validateDates();
}

function updateOrderSummary() {
  const orderSummarySection = getElement("orderSummary");
  const orderSummarySections = document.querySelectorAll(
    ".orderSummarySections"
  );

  const currentSummary = getElement("summaryContent");
  if (!currentSummary) {
    orderSummarySection.style.display = "none";
    orderSummarySections.forEach((section) => (section.style.display = "none"));
    return;
  }

  const duration = calculateTotalDuration();

  const summaryStartDateTime = getElement("summaryStartDateTime");
  const summaryEndDateTime = getElement("summaryEndDateTime");
  summaryStartDateTime.value = bookingData.startDateTime;
  summaryEndDateTime.value = bookingData.endDateTime;

  const summaryDuration = getElement("summaryDuration");
  summaryDuration.textContent = `${duration.days} day(s) ${duration.hours} hour(s) ${duration.minutes} minute(s)`;
  // orderSummarySections[0].style.display = "block";
  if (currentStep == 2 && bookingData.selectedVehicle) {
    const selectedVehicleData = bookingData.selectedVehicle;
    getElement("summaryVehicleModelName").textContent =
      selectedVehicleData.model_name;

    document.querySelector("#summaryVehicleRate").value =
      selectedVehicleData.rental_price_24h;
    document.querySelector("#summaryVehicleKilometer").value =
      selectedVehicleData.kilometer_limit;
    document.querySelector("#summaryVehicleRatePerHour").value =
      selectedVehicleData.extra_price_per_hour;
    document.querySelector("#summaryVehicleRateForKilometer").value =
      selectedVehicleData.extra_price_per_km;

    // for booking summary
    const bookingSummaryVehicleModelImageElement = getElement(
      "bookingSummaryVehicleModelImage"
    );
    bookingSummaryVehicleModelImageElement
      ? (bookingSummaryVehicleModelImageElement.src =
          baseUrl + selectedVehicleData.brand_image)
      : null;
    const bookingSummaryVehicleModelNameElement = getElement(
      "bookingSummaryVehicleModelName"
    );
    bookingSummaryVehicleModelNameElement
      ? (bookingSummaryVehicleModelNameElement.textContent =
          selectedVehicleData.model_name)
      : null;
    const bookingSummaryVehicleRegistrationNumberElement = getElement(
      "bookingSummaryVehicleRegistrationNumber"
    );
    bookingSummaryVehicleRegistrationNumberElement
      ? (bookingSummaryVehicleRegistrationNumberElement.textContent =
          selectedVehicleData.registration_number)
      : null;
  }

  if (currentStep == 3) {
    const selectedValue = customerListElement ? customerListElement.value : "";
    const selectedOption = customerListElement
      ? customerListElement.options[customerListElement.selectedIndex]
      : null;
    const customerType = selectedOption
      ? selectedOption.getAttribute("data-customer-type")
      : "";
    const bookingSummaryCustomerDetailsSection = document.querySelector(
      "#bookingSummaryCustomerDetailsSection"
    );

    if (
      selectedValue &&
      selectedValue != "addNew" &&
      customerType &&
      bookingSummaryCustomerDetailsSection
    ) {
      bookingData.customer.customerId = selectedValue;
      bookingData.customer.customerType = customerType;
      fetch(
        baseUrl +
          `bookingManagement/getCustomerTypeForm?customerType=${customerType}&customerId=${selectedValue}&orderSummaryFlag=true`
      )
        .then((response) => response.text())
        .then((html) => {
          bookingSummaryCustomerDetailsSection.innerHTML =
            "Customer Information";
          if (html == false) {
            bookingSummaryCustomerDetailsSection.innerHTML += "";
          } else {
            const tempDiv = document.createElement("div");
            tempDiv.innerHTML = html;
            bookingSummaryCustomerDetailsSection.innerHTML += "";
            bookingSummaryCustomerDetailsSection.appendChild(
              tempDiv.firstElementChild
            );
          }
        })
        .catch((error) => console.error("Error fetching customers:", error));
    }
  }

  if (currentStep === 4) {
    calculateOrderSummary();
  }

  // Reattach event listeners for the new date inputs in the Order Summary
  summaryStartDateTime.addEventListener("change", syncDatesFromSummary);
  summaryEndDateTime.addEventListener("change", syncDatesFromSummary);

  toggleDisplay(orderSummarySection, currentStep === 1 ? false : true);
  // currentStep > 1 ? (orderSummarySection.style.display = "block") : null;
  orderSummarySections.forEach((section, index) => {
    toggleDisplay(section, index <= currentStep - 2 ? true : false);
  });
}

function calculateTotalDuration() {
  if (bookingData.startDateTime && bookingData.endDateTime) {
    const start = new Date(bookingData.startDateTime);
    const end = new Date(bookingData.endDateTime);

    const diffTime = Math.abs(end - start); // Difference in milliseconds

    // Calculate days
    const days = Math.floor(diffTime / (1000 * 60 * 60 * 24)); // Full days

    // Calculate hours
    const hours = Math.floor(
      (diffTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
    ); // Remaining hours

    // Calculate minutes
    const minutes = Math.floor((diffTime % (1000 * 60 * 60)) / (1000 * 60)); // Remaining minutes

    return {
      days,
      hours,
      minutes,
    };
  }
  return {
    days: 0,
    hours: 0,
    minutes: 0,
  };
}

// Update vehicle rate in booking data and recalculate order summary
const updateVehicleRate = (selector, property) => {
  document.querySelector(selector).oninput = function () {
    bookingData.selectedVehicle[property] =
      parseFloat(this.value) || bookingData.selectedVehicle[property];
    calculateOrderSummary();
  };
};

// Apply the updateVehicleRate function to relevant fields
updateVehicleRate("#summaryVehicleRate", "rental_price_24h");
updateVehicleRate("#summaryVehicleKilometer", "kilometer_limit");
updateVehicleRate("#summaryVehicleRatePerHour", "extra_price_per_hour");
updateVehicleRate("#summaryVehicleRateForKilometer", "extra_price_per_km");

// Update order bill details and recalculate order summary
const updateOrderBillDetails = (selector, property, type) => {
  document.querySelector(selector).oninput = function () {
    bookingData.orderBillDetails[property] =
      type == "input" ? parseFloat(this.value) || 0 : this.textContent || "";
    calculateOrderSummary();
  };
};

// Apply the updateOrderBillDetails function to relevant fields
updateOrderBillDetails("#summaryAdvance", "advanceAmount", "input");
updateOrderBillDetails("#summaryDiscount", "discountAmount", "input");
updateOrderBillDetails(
  "#summaryAdvancePaymentMethodAdditionalInfo",
  "advanceAmountAdditionalInfo",
  "textarea"
);

function resetVehicleRatesOrderSummary(totalResetFlag = 0) {
  const bookingDataObject = totalResetFlag
    ? bookingData.selectedVehicle
    : bookingData.defaultSelectedVehicle;

  const shouldReset = (value) => {
    return (
      totalResetFlag === 1 ||
      value === null ||
      value === undefined ||
      isNaN(parseFloat(value)) ||
      parseFloat(value) <= 0
    );
  };

  const updateField = (selector, property, label) => {
    const element = document.querySelector(selector);
    if (element && shouldReset(element.value)) {
      element.value = bookingDataObject[property];
      createTemporaryError(element, "Please enter valid " + label + ".");
      bookingData.selectedVehicle[property] = bookingDataObject[property];
    }
  };

  updateField(
    "#summaryVehicleRate",
    "rental_price_24h",
    "rental per day price"
  );
  updateField("#summaryVehicleKilometer", "kilometer_limit", "kilometer limit");
  updateField(
    "#summaryVehicleRatePerHour",
    "extra_price_per_hour",
    "extra price per hour"
  );
  updateField(
    "#summaryVehicleRateForKilometer",
    "extra_price_per_km",
    "extra price per kilometer"
  );

  // Recalculate order summary after resetting values
  calculateOrderSummary();

  const summaryAdvancePaymentMethod = document.querySelector(
    "#summaryAdvancePaymentMethod"
  );
  if (summaryAdvancePaymentMethod) {
    summaryAdvancePaymentMethod.dispatchEvent(new Event("change"));
  }
}

function calculateOrderSummary() {
  // Check if bookingData exist
  if (!bookingData) {
    console.error("bookingData or orderBillDetails is not initialized");
    return;
  }

  // Initialize orderBillDetails if it doesn't exist
  if (!bookingData.orderBillDetails) {
    bookingData.orderBillDetails = {
      totalAmount: 0,
      discountAmount: 0,
      advanceAmount: 0,
    };
  }

  const totalAmount = calculateTotalAmount();
  bookingData.orderBillDetails.totalAmount = totalAmount;

  const discountAmount =
    parseFloat(bookingData.orderBillDetails.discountAmount) || 0;
  const advanceAmount =
    parseFloat(bookingData.orderBillDetails.advanceAmount) || 0;

  const summaryGrandTotalAmount = totalAmount - discountAmount;
  const summaryTotalDueAmount = summaryGrandTotalAmount - advanceAmount;

  // Safely update DOM elements
  const updateElementValue = (id, value) => {
    const element = getElement(id);
    if (element) {
      element.value = numToFloat(value);
    }
  };

  updateElementValue("summarySubtotal", totalAmount);
  updateElementValue("summaryTotalDue", summaryTotalDueAmount);
  updateElementValue("summaryGrandTotal", summaryGrandTotalAmount);
}

function calculateTotalAmount() {
  if (!bookingData.selectedVehicle) return 0;

  const { days, hours, minutes } = calculateTotalDuration();
  const { rental_price_24h, extra_price_per_hour } =
    bookingData.selectedVehicle;

  const totalAmount =
    days * rental_price_24h +
    hours * extra_price_per_hour +
    (minutes / 60) * extra_price_per_hour;
  console.log("calculated total amount", totalAmount);

  return numToFloat(totalAmount);
}

function handleFormSubmission() {
  resetVehicleRatesOrderSummary();
}

// Show the discount input when the "Add Discount" button is clicked
document
  .querySelector("#addDiscountButton")
  .addEventListener("click", function () {
    const discountInput = document.querySelector("#summaryDiscount");
    discountInput.classList.remove("d-none");
    discountInput.focus();
    this.classList.add("d-none"); // Hide the button
  });
document
  .querySelector("#addAdvanceButton")
  .addEventListener("click", function () {
    const discountInput = document.querySelector("#summaryAdvance");
    const summaryAdvancePaymentMethodDiv = document.querySelector(
      "#summaryAdvancePaymentMethodDiv"
    );

    // Show the input field and the payment method div
    discountInput.classList.remove("d-none");
    summaryAdvancePaymentMethodDiv.classList.remove("d-none");

    // Focus on the input field
    discountInput.focus();

    // Hide the "Add Advance" button
    this.classList.add("d-none");
  });

document
  .querySelector("#summaryAdvancePaymentMethod")
  .addEventListener("change", function () {
    const summaryAdvancePaymentMethodElement = document.querySelector(
      "#summaryAdvancePaymentMethod"
    );
    const summaryAdvancePaymentMethodDiv = document.querySelector(
      "#summaryAdvancePaymentMethodDiv"
    );
    if (
      !summaryAdvancePaymentMethodElement &&
      summaryAdvancePaymentMethodDiv &&
      !summaryAdvancePaymentMethodDiv.classList.contains("d-none")
    ) {
      const orderSummaryErrorElement =
        document.querySelector("#orderSummaryError");
      if (orderSummaryErrorElement) {
        orderSummaryErrorElement.textContent = "Please select payment method";
      }
    }
    bookingData.orderBillDetails.advancePaymentMethod =
      summaryAdvancePaymentMethodElement.value || "Cash";
  });

additionalInfoElement.addEventListener("input", function () {
  bookingData.additionalInfo = additionalInfoElement.value;
});

const saveBookingFormData = () => {
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
        console.log("currentStep - " + currentStep);

        updateSteps();
        updateButtons();
      } else {
        alert(response.message);
      }
      console.log("Booking data saved successfully:", response);
    },
  });
};
// Function to format date to dd-mm-yyyy
function formatDateForStartEnd(date) {
  const day = ("0" + date.getDate()).slice(-2); // Get day and add leading zero if necessary
  const month = ("0" + (date.getMonth() + 1)).slice(-2); // Get month and add leading zero
  const year = date.getFullYear();
  return `${day}-${month}-${year}`;
}

// Function to format time to hh:mm
function formatTimeForStartEnd(date) {
  const hours = ("0" + date.getHours()).slice(-2); // Get hours and add leading zero
  const minutes = ("0" + date.getMinutes()).slice(-2); // Get minutes and add leading zero
  return `${hours}:${minutes}`;
}

// Function to update the date and time in the respective divs
function updateSummaryDateTimeDetailsSection() {
  const startDateTime = bookingData.startDateTime;
  const endDateTime = bookingData.endDateTime;

  const { days, hours, minutes } = calculateTotalDuration();
  durationInfoStep1Element.textContent = `${days} days, ${hours} hours, ${minutes} minutes`;

  // Parse the start and end DateTime strings into Date objects
  const startDate = new Date(startDateTime);
  const endDate = new Date(endDateTime);

  // Format the dates and times
  const pickupDate = formatDateForStartEnd(startDate);
  const pickupTime = formatTimeForStartEnd(startDate);
  const returnDate = formatDateForStartEnd(endDate);
  const returnTime = formatTimeForStartEnd(endDate);

  const bookingSummaryDateTimeDetailsSection = document.querySelector(
    "#bookingSummaryDateTimeDetailsSection"
  );

  // Update the respective divs with formatted values
  bookingSummaryDateTimeDetailsSection.querySelector(
    ".bookingSummaryDateTimeDetailsSectionDiv:nth-child(1) .details-value"
  ).textContent = pickupDate;
  bookingSummaryDateTimeDetailsSection.querySelector(
    ".bookingSummaryDateTimeDetailsSectionDiv:nth-child(2) .details-value"
  ).textContent = pickupTime;
  bookingSummaryDateTimeDetailsSection.querySelector(
    ".bookingSummaryDateTimeDetailsSectionDiv:nth-child(3) .details-value"
  ).textContent = returnDate;
  bookingSummaryDateTimeDetailsSection.querySelector(
    ".bookingSummaryDateTimeDetailsSectionDiv:nth-child(4) .details-value"
  ).textContent = returnTime;
}
