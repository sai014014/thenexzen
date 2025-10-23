/**
 * Toggle the individual/corporate form based on the selected customer type
 * @param {String} customerType - The type of customer (individual or corporate)
 * @param {String} customerId - The ID of the customer
 */
function toggleCustomerType() {
  const customerTypeElement = document.querySelector("#customerType");
  const customerIdElement = document.querySelector("#customerId");

  if (!customerTypeElement || !customerIdElement) return;

  const customerType = customerTypeElement.value;
  const customerId = customerIdElement.value;

  const individualFormDiv = document.querySelector("#individualFormDiv");
  const corporateFormDiv = document.querySelector("#corporateFormDiv");

  if (!individualFormDiv || !corporateFormDiv) return;

  const url =
    baseUrl +
    "customerManagement/" +
    (customerType === "individual"
      ? "load-individualForm/" + customerId
      : "load-contractForm/" + customerId);

  fetch(url)
    .then((response) => response.text())
    .then((html) => {
      /**
       * If the form is empty, redirect the user to the customer management page
       * @param {String} html - The HTML content of the form
       */
      if (html == false) {
        window.location.href = baseUrl + "customerManagement/";
        return;
      }

      /**
       * If the customer type is individual, load the individual form and set the
       * dob min and max dates. Otherwise, load the corporate form.
       * @param {String} html - The HTML content of the form
       */
      if (customerType === "individual") {
        individualFormDiv.innerHTML = html;
        corporateFormDiv.innerHTML = "";
        setDoBMaxDate();
        setDoBMinDate();
        toggleCurrentAddress();
      } else {
        corporateFormDiv.innerHTML = html;
        individualFormDiv.innerHTML = "";
      }

      /**
       * Toggle the visibility of the individual/corporate forms
       * @param {Boolean} displayIndividualForm - Whether to display the individual form
       * @param {Boolean} displayCorporateForm - Whether to display the corporate form
       */
      individualFormDiv.style.display =
        customerType === "individual" ? "block" : "none";
      corporateFormDiv.style.display =
        customerType === "corporate" ? "block" : "none";

      commonFormFunctions();
      clearErrors();
    });
}

window.addEventListener("load", toggleCustomerType);

/**
 * Toggles the current address field to match the permanent address
 * when the 'sameAsPermanent' checkbox is checked.
 */
function toggleCurrentAddress() {
  const checkbox = document.querySelector("#sameAsPermanent");
  const permanentAddress = document.querySelector("#permanentAddress");
  const currentAddress = document.querySelector("#currentAddress");

  if (!checkbox || !permanentAddress || !currentAddress) return;

  /**
   * If the checkbox is checked, make the current address match the permanent address
   * and set the current address field to read-only.
   * Also, update the current address as the permanent address changes.
   */
  if (checkbox.checked) {
    currentAddress.value = permanentAddress.value;
    currentAddress.readOnly = true;

    permanentAddress.addEventListener("input", () => {
      currentAddress.value = permanentAddress.value;
    });
  } else {
    /**
     * If the checkbox is unchecked, clear the current address field
     * and make it editable.
     */
    currentAddress.value = "";
    currentAddress.readOnly = false;
  }
}

/**
 * Clears all the error messages in the form.
 */
function clearErrors(parent_DOM = document) {
  const errorMessages = parent_DOM.querySelectorAll(".error-message");
  errorMessages.forEach((msg) => {
    if (msg) msg.innerText = "";
  });

  const globalError = parent_DOM.querySelector("#globalError");
  if (globalError) globalError.innerText = "";
}

/**
 * Validate the Government ID field based on the selected ID type.
 * @returns {Boolean} True if the ID number is valid, false otherwise.
 */
function validateID(parent_DOM = document) {
  const idTypeElement = parent_DOM.querySelector("#idType");
  const idNumberElement = parent_DOM.querySelector("#idNumber");
  const idTypeError = parent_DOM.querySelector("#idTypeError");
  const idNumberError = parent_DOM.querySelector("#idNumberError");

  if (!idTypeElement || !idNumberElement || !idTypeError || !idNumberError) {
    return true;
  }

  const idType = idTypeElement.value.trim();
  const idNumber = idNumberElement.value.trim();

  let regex;
  let errorMessage = "";
  let isValid = true;

  // Check if an ID type is selected
  if (!idType) {
    errorMessage = "Please select a Government ID type.";
    idTypeError.innerText = errorMessage;
    isValid = false;
  } else {
    // Validate the ID number based on the selected ID type
    switch (idType) {
      case "Aadhar":
        regex = /^[0-9]{12}$/;
        if (!regex.test(idNumber)) {
          errorMessage = "Aadhar number must be a 12-digit number.";
        }
        break;
      case "VoterID":
        regex = /^[A-Z]{3}[0-9]{7}$/;
        if (!regex.test(idNumber)) {
          errorMessage =
            "Voter ID must start with 3 letters and be followed by 7 digits.";
        }
        break;
      case "Passport":
        regex = /^[A-Z]{1,2}[0-9]{6}$/;
        if (!regex.test(idNumber)) {
          errorMessage =
            "Passport number must be 8 characters: 1 or 2 letters followed by 6 digits.";
        }
        break;
      case "PAN":
        regex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
        if (!regex.test(idNumber)) {
          errorMessage =
            "PAN number must be 10 characters: 5 letters, 4 digits, and 1 letter.";
        }
        break;
      default:
        errorMessage = "Please select a valid ID type.";
    }
  }
  // Display the error message if the ID number is invalid
  if (errorMessage) {
    idNumberError.innerText = errorMessage;
    isValid = false;
  } else {
    // Clear the error message if the ID number is valid
    idNumberError.innerText = "";
  }

  return isValid;
}

/**
 * Validate the driving license number
 * @returns {boolean} True if the driving license number is valid, false otherwise
 */
function validateDrivingLicense(parent_DOM = document) {
  const drivingLicenseElement = parent_DOM.querySelector("#drivingLicense");
  const drivingLicenseError = parent_DOM.querySelector("#drivingLicenseError");

  if (!drivingLicenseElement || !drivingLicenseError) return true;

  const drivingLicense = drivingLicenseElement.value;
  const regex = /^[A-Z]{2}[0-9]{2}[0-9]{1,11}$/;
  let errorMessage = "";

  if (!regex.test(drivingLicense)) {
    errorMessage =
      'Invalid Driving License format. It should be like "DL01234567890".';
    drivingLicenseError.innerText = errorMessage;
    return false;
  }

  drivingLicenseError.innerText = "";
  return true;
}
function validateGSTNumber(parent_DOM = document) {
  const gstinElement = parent_DOM.getElementById("gstin");
  const gstinErrorElement = parent_DOM.getElementById("gstinError");

  // Check if the GST input field and error message element exist
  if (!gstinElement || !gstinErrorElement) return true;

  const gstin = gstinElement.value.trim();

  /**
   * Pattern for a valid GST Number
   * 2 digits, 5 letters, 4 digits, 1 letter, 1 digit, 1 letter, 1 digit
   * Example: "12ABCD1234E1Z1"
   */
  const regex =
    /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}$/;
  let errorMessage = "";

  if (!regex.test(gstin)) {
    /**
     * Error message to be displayed if the GST Number is invalid
     */
    errorMessage = "Invalid GST Number.";
    gstinErrorElement.innerText = errorMessage;
    return false;
  }

  // Clear error message if valid
  gstinErrorElement.innerText = "";
  return true;
}

/**
 * Set the maximum date for the date of birth input field to 18 years ago
 */
function setDoBMaxDate(parent_DOM = document) {
  const dobElement = parent_DOM.querySelector("#dob");
  if (!dobElement) return;

  const today = new Date();
  const year = today.getFullYear() - 18;
  const month = String(today.getMonth() + 1).padStart(2, "0");
  const day = String(today.getDate()).padStart(2, "0");
  dobElement.setAttribute("max", `${year}-${month}-${day}`);
}

/**
 * Set the minimum date for the date of birth input field to 100 years ago
 */
function setDoBMinDate(parent_DOM = document) {
  const dobElement = parent_DOM.querySelector("#dob");
  if (!dobElement) return;

  const today = new Date();
  const year = today.getFullYear() - 100;
  const month = String(today.getMonth() + 1).padStart(2, "0");
  const day = String(today.getDate()).padStart(2, "0");
  dobElement.setAttribute("min", `${year}-${month}-${day}`);
}
