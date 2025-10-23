// Define the Step 1 elements and related functions
const step1Elements = {
  startDateTime: getElement("startDateTime"),
  endDateTime: getElement("endDateTime"),
  startDateTimeError: getElement("startDateTimeError"),
  endDateTimeError: getElement("endDateTimeError"),
  pickupLocation: getElement("pickupLocation"),
  dropLocation: getElement("dropLocation"),
  pickupLocationError: getElement("pickupLocationError"),
  dropLocationError: getElement("dropLocationError"),
  addPickupLocationBtn: getElement("addPickupLocationBtn"),
  savePickupLocationBtn: getElement("savePickupLocationBtn"),
  newPickupLocation: getElement("newPickupLocation"),
  newPickupLocationError: getElement("newPickupLocationError"),
  step1ErrorDiv: getElement("step1ErrorDiv"),
};

// Function to validate dates
function validateDates() {
  const startDateTime = new Date(step1Elements.startDateTime.value);
  const endDateTime = new Date(step1Elements.endDateTime.value);
  let isValid = true;

  if (!step1Elements.startDateTime.value || isNaN(startDateTime.getTime())) {
    setTextContent(
      step1Elements.startDateTimeError,
      "Start Date and Time must be today or later."
    );
    isValid = false;
  }

  if (
    !step1Elements.endDateTime.value ||
    isNaN(endDateTime.getTime()) ||
    startDateTime >= endDateTime
  ) {
    setTextContent(
      step1Elements.endDateTimeError,
      "End Date and Time must be after Start Date."
    );
    isValid = false;
  }

  return isValid;
}

// Function to validate locations
function validateLocations() {
  let isValid = true;

  if (step1Elements.pickupLocation.value === "") {
    setTextContent(
      step1Elements.pickupLocationError,
      "Pickup Location is required."
    );
    isValid = false;
  }

  if (step1Elements.dropLocation.value === "") {
    setTextContent(
      step1Elements.dropLocationError,
      "Drop Location is required."
    );
    isValid = false;
  }

  return isValid;
}

// Function to handle saving a new location
function saveNewLocation() {
  const newLocation = step1Elements.newPickupLocation.value.trim();
  setTextContent(step1Elements.newPickupLocationError, "");

  if (!newLocation) {
    setTextContent(
      step1Elements.newPickupLocationError,
      "Location name is required."
    );
    return;
  }

  $.ajax({
    url: baseUrl + "vehicleLocationManagement/save-LocationForm",
    type: "POST",
    data: { locationName: newLocation },
    success: handleLocationSaveSuccess,
    error: () =>
      setTextContent(
        step1Elements.newPickupLocationError,
        "There was an error. Please try again."
      ),
  });
}

// Handle location save success
function handleLocationSaveSuccess(response) {
  if (Array.isArray(response) && response.length > 0) {
    updateLocationDropdowns(response);
    $("#addLocationModal").modal("hide");
    step1Elements.newPickupLocation.value = "";
  } else {
    setTextContent(
      step1Elements.newPickupLocationError,
      "Error saving location. Please try again."
    );
  }
}

// Update the location dropdowns after saving a new location
function updateLocationDropdowns(locations) {
  const defaultOption = '<option value="">Select Location</option>';
  step1Elements.pickupLocation.innerHTML = defaultOption;
  step1Elements.dropLocation.innerHTML = defaultOption;

  locations.forEach((location) => {
    const option = new Option(
      location.vehicle_location_name,
      location.vehicle_location_id
    );
    step1Elements.pickupLocation.add(option.cloneNode(true));
    step1Elements.dropLocation.add(option);
  });
}

// Validate in Step 1
function validateStep1(bookingData) {
  let isValid = validateDates() && validateLocations();

  if (!isValid) {
    const errorDiv = document.createElement("div");
    errorDiv.id = "globalError";
    errorDiv.classList.add("error-message");
    errorDiv.textContent = "Please fill the details proceeding.";
    getElement("step1").appendChild(errorDiv);
  } else {
    bookingData.startDateTime = step1Elements.startDateTime.value;
    bookingData.endDateTime = step1Elements.endDateTime.value;
    bookingData.pickUpLocation = step1Elements.pickupLocation.value;
    bookingData.dropOffLocation = step1Elements.dropLocation.value;

    bookingData.pickupLocationName = getLocationName(
      step1Elements.pickupLocation
    );
    bookingData.dropLocationName = getLocationName(step1Elements.dropLocation);
  }

  return isValid;
}

function getLocationName(selectElement) {
  return selectElement.options[selectElement.selectedIndex].text;
}

// Function to show the modal for adding a new location
function showAddLocationModal() {
  $("#addLocationModal").modal("show");
}

// Function to handle saving a new location
function attachStep1EventListeners() {
  step1Elements.addPickupLocationBtn.addEventListener(
    "click",
    showAddLocationModal
  );
  step1Elements.savePickupLocationBtn.addEventListener(
    "click",
    saveNewLocation
  );
}

export { validateStep1, attachStep1EventListeners };
