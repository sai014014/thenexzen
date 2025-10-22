/**
 * Validate and submit the Individual Form.
 * @returns {boolean} False to prevent default form submission if validation fails.
 */
function individualFormSubmit() {
  const individualFormDom = document.querySelector("#individualForm");
  // Clear previous error messages
  clearErrors(individualFormDom);
  let isValid = true;
  const errors = [];
  let errorCount = 0;

  // If validation failed, show errors above the submit button
  const submitButton = individualFormDom.querySelector(
    "#individualFormSubmitButton"
  );

  // Check if #globalError already exists and remove it
  const existingGlobalError = individualFormDom.querySelector("#globalError");
  if (existingGlobalError) {
    existingGlobalError.remove();
  }

  // Retrieve the Customer ID from the form
  const customerId = individualFormDom
    .querySelector("#customerID")
    .value.trim();
  if (customerId != "" && customerId != 0) {
    if (
      $("#uploadLicenseFront").val() == "" &&
      $("#uploadLicenseBack").val() == ""
    ) {
      submitButton.insertAdjacentHTML(
        "beforebegin",
        '<div id="globalError" class="error-message">Your data is already updated.' +
          "</div>"
      );
      return false;
    }
  }

  // 1. Full Name
  const fullName = individualFormDom.querySelector("#fullName").value.trim();

  if (!fullName) {
    // If the Full Name is empty, add an error message
    errors.push("Full Name is required.");
    individualFormDom.querySelector("#fullNameError").innerText =
      "Full Name is required.";
    isValid = false;
    errorCount++;
    console.log(individualFormDom.querySelector("#fullNameError"));
  }

  // 2. Mobile Number
  const mobileNumber = individualFormDom
    .querySelector("#mobileNumber")
    .value.trim();
  if (!/^\d{10}$/.test(mobileNumber)) {
    // If the Mobile Number is not a 10-digit number, add an error message
    errors.push("Mobile Number must be a 10-digit number.");
    individualFormDom.querySelector("#mobileNumberError").innerText =
      "Mobile Number must be a 10-digit number.";
    isValid = false;
    errorCount++;
  }

  // 4. Date of Birth
  const dob = new Date(individualFormDom.querySelector("#dob").value);
  const today = new Date();
  const age = today.getFullYear() - dob.getFullYear();
  if (isNaN(age)) {
    // If the customer is less than 18 years old, add an error message
    errors.push("Date of birth is required.");
    individualFormDom.querySelector("#dobError").innerText =
      "Date of birth is required.";
    isValid = false;
    errorCount++;
  } else if (age < 18) {
    // If the customer is less than 18 years old, add an error message
    errors.push("You must be at least 18 years old.");
    individualFormDom.querySelector("#dobError").innerText =
      "You must be at least 18 years old.";
    isValid = false;
    errorCount++;
  }

  // 5. Permanent Address
  const permanentAddress = individualFormDom
    .querySelector("#permanentAddress")
    .value.trim();
  if (!permanentAddress) {
    // If the Permanent Address is empty, add an error message
    errors.push("Permanent Address is required.");
    individualFormDom.querySelector("#permanentAddressError").innerText =
      "Permanent Address is required.";
    isValid = false;
    errorCount++;
  }

  // 6. Government ID
  const idType = individualFormDom.querySelector("#idType").value.trim();
  if (!idType) {
    // If the Government ID type is not selected, add an error message
    const errorMessage = "Please select a Government ID type.";
    individualFormDom.querySelector("#idTypeError").innerText = errorMessage;
    isValid = false;
  }

  // 7. Government ID Number
  const idNumber = individualFormDom.querySelector("#idNumber").value.trim();
  if (!idNumber) {
    // If the Government ID Number is empty, add an error message
    errors.push("Government ID Number is required.");
    individualFormDom.querySelector("#idNumberError").innerText =
      "Government ID Number is required.";
    isValid = false;
    errorCount++;
  } else {
    // Validate the Government ID Number based on the selected type
    isValid = validateID(individualFormDom);
    !isValid ? errorCount++ : "";
  }

  // 8. Driving License Number
  const drivingLicense = individualFormDom
    .querySelector("#drivingLicense")
    .value.trim();
  if (!drivingLicense) {
    // If the Driving License Number is empty, add an error message
    errors.push("Driving License Number is required.");
    individualFormDom.querySelector("#drivingLicenseError").innerText =
      "Driving License Number is required.";
    isValid = false;
    errorCount++;
  } else {
    // Validate the Driving License Number
    isValid = validateDrivingLicense(individualFormDom);
    !isValid ? errorCount++ : "";
  }

  // If validation failed, show errors above the submit button
  if (errorCount > 0) {
    if (errors.length == 0 || errors.length > 1) {
      submitButton.insertAdjacentHTML(
        "beforebegin",
        '<div id="globalError" class="error-message">Please check the form for insufficient or incorrect data.' +
          "</div>"
      );
    } else {
      submitButton.insertAdjacentHTML(
        "beforebegin",
        '<div id="globalError" class="error-message">' + errors[0] + "</div>"
      );
    }

    return false; // Prevent form submission
  }

  // Prepare form data for submission
  const formData = new FormData(individualFormDom);

  // Submit the form using AJAX
  fetch(baseUrl + "customerManagement/save-individualForm", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Show error messages returned from the server
        submitButton.insertAdjacentHTML(
          "beforebegin",
          '<div id="globalError" class="text-success">Successfully saved.</div>'
        );
        setTimeout(() => {
          clearErrors(individualFormDom);
          postSaveCustomerDetails(data.insertId);
        }, GLOBAL_ERROR_TIMEOUT ?? 2000);
      } else {
        // Show error messages returned from the server
        submitButton.insertAdjacentHTML(
          "beforebegin",
          '<div id="globalError" class="error-message">' +
            data.errors.join("<br>") +
            "</div>"
        );
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      submitButton.insertAdjacentHTML(
        "beforebegin",
        '<div id="globalError" class="error-message">An error occurred. Please try again.</div>'
      );
    });

  return false; // Prevent default form submission
}
/**
 * Validate the Government ID field based on the selected ID type.
 * @returns {Boolean} True if the ID number is valid, false otherwise.
 */
function validateID(individualFormDom) {
  const idType = individualFormDom.querySelector("#idType").value.trim();
  const idNumber = individualFormDom.querySelector("#idNumber").value.trim();
  let regex;
  let errorMessage = "";
  let isValid = true;

  // Check if an ID type is selected
  if (!idType) {
    errorMessage = "Please select a Government ID type.";
    individualFormDom.querySelector("#idTypeError").innerText = errorMessage;
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
  if (errorMessage.length > 0) {
    individualFormDom.querySelector("#idNumberError").innerText = errorMessage;
    isValid = false;
  } else {
    // Clear the error message if the ID number is valid
    individualFormDom.querySelector("#idNumberError").innerText = "";
  }

  return isValid;
}
/**
 * Validate the driving license number
 * @returns {boolean} True if the driving license number is valid, false otherwise
 */
function validateDrivingLicense(individualFormDom) {
  const drivingLicense =
    individualFormDom.querySelector("#drivingLicense").value;

  // Pattern: 2 alphabets, 2 digits, 1-11 digits
  // Example: "DL01234567890"
  const regex = /^[A-Z]{2}[0-9]{2}[0-9]{1,11}$/;
  let errorMessage = "";

  if (!regex.test(drivingLicense)) {
    errorMessage =
      'Invalid Driving License format. It should be like "DL01234567890".';
    individualFormDom.querySelector("#drivingLicenseError").innerText =
      errorMessage;
    return false;
  }

  // Clear error message if valid
  individualFormDom.querySelector("#drivingLicenseError").innerText = "";
  return true;
}

/**
 * Clears all the error messages in the form.
 */
function clearErrors(individualFormDom) {
  /**
   * QuerySelectorAll returns a NodeList, which is an array-like object.
   * We use the spread operator to convert it to a regular array and then
   * iterate over it using the forEach method.
   */
  const errorMessages = [
    ...individualFormDom.querySelectorAll(".error-message"),
  ];
  errorMessages.forEach((msg) => (msg.innerText = ""));
  const globalError = individualFormDom.querySelector("#globalError");
  globalError ? (globalError.innerText = "") : "";
}

// corporate
/**
 * Validate and submit the Corporate Form.
 * @returns {boolean} False to prevent default form submission if validation fails.
 */
function corporateFormSubmit() {
  const corporateFormDOM = document.querySelector("#corporateForm");
  // Clear previous error messages
  clearErrors(corporateFormDOM);
  let isValid = true;
  const errors = [];
  let errorCount = 0;

  const submitButton = corporateFormDOM.querySelector(
    "#corporateFormSubmitButton"
  );
  const existingGlobalError = individualFormDom.querySelector("#globalError");
  if (existingGlobalError) {
    existingGlobalError.remove();
  }

  // Retrieve and validate form fields
  const customerId = corporateFormDOM.getElementById("customerId").value.trim();

  // 1. Company Name
  const companyName = corporateFormDOM
    .getElementById("companyName")
    .value.trim();
  if (!companyName) {
    errors.push("Company Name is required.");
    corporateFormDOM.getElementById("companyNameError").innerText =
      "Company Name is required.";
    isValid = false;
    errorCount++;
  }

  // 2. Company Type
  const companyType = corporateFormDOM
    .getElementById("companyType")
    .value.trim();
  if (!companyType) {
    errors.push("Company Type is required.");
    corporateFormDOM.getElementById("companyTypeError").innerText =
      "Company Type is required.";
    isValid = false;
    errorCount++;
  }

  // 3. GSTIN (Optional, only validate if filled)
  const gstin = corporateFormDOM.getElementById("gstin").value.trim();
  if (
    gstin &&
    !/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}$/.test(
      gstin
    )
  ) {
    errors.push("Invalid GSTIN format.");
    corporateFormDOM.getElementById("gstinError").innerText =
      "Invalid GSTIN format.";
    isValid = false;
    errorCount++;
  }

  // 4. Company Address
  const companyAddress = corporateFormDOM
    .getElementById("companyAddress")
    .value.trim();
  if (!companyAddress) {
    errors.push("Company Address is required.");
    corporateFormDOM.getElementById("companyAddressError").innerText =
      "Company Address is required.";
    isValid = false;
    errorCount++;
  }

  // 5. PAN Number (Optional, only validate if filled)
  const panNumber = corporateFormDOM.getElementById("panNumber").value.trim();
  if (panNumber && !/^[A-Z]{5}\d{4}[A-Z]{1}$/.test(panNumber)) {
    errors.push("Invalid PAN Number format.");
    corporateFormDOM.getElementById("panNumberError").innerText =
      "Invalid PAN Number format.";
    isValid = false;
    errorCount++;
  }

  // 6. Contact Person Name
  const contactPersonName = corporateFormDOM
    .getElementById("contactPersonName")
    .value.trim();
  if (!contactPersonName) {
    errors.push("Contact Person Name is required.");
    corporateFormDOM.getElementById("contactPersonNameError").innerText =
      "Contact Person Name is required.";
    isValid = false;
    errorCount++;
  }

  // 7. Designation
  const designation = corporateFormDOM
    .getElementById("designation")
    .value.trim();
  if (!designation) {
    errors.push("Designation is required.");
    corporateFormDOM.getElementById("designationError").innerText =
      "Designation is required.";
    isValid = false;
    errorCount++;
  }

  // 8. Official Email
  const officialEmail = corporateFormDOM
    .getElementById("officialEmail")
    .value.trim();
  if (!officialEmail || !/^\S+@\S+\.\S+$/.test(officialEmail)) {
    errors.push("A valid Official Email Address is required.");
    corporateFormDOM.getElementById("officialEmailError").innerText =
      "A valid Official Email Address is required.";
    isValid = false;
    errorCount++;
  }

  // 9. Mobile Number
  const contactMobile = corporateFormDOM
    .getElementById("contactMobile")
    .value.trim();
  if (!/^\d{10}$/.test(contactMobile)) {
    errors.push("Mobile Number must be a 10-digit number.");
    corporateFormDOM.getElementById("contactMobileError").innerText =
      "Mobile Number must be a 10-digit number.";
    isValid = false;
    errorCount++;
  }

  // 10. Driver(s) Validation (Optional, only validate if added)
  const drivers = corporateFormDOM.querySelectorAll(".driver-info");
  if (customerId != "" && customerId != 0) {
    const hasEditableFields = Array.from(drivers).some((driver) => {
      const driverIndex = driver.querySelector('[name="driverIndex[]"]').value;
      return !driver.querySelector(`#driverName${driverIndex}`).readOnly;
    });
    if (!hasEditableFields) {
      return false; // Exit early if all fields are readonly
    }
  }
  console.log(drivers);

  drivers.forEach((driver, index) => {
    const driverIndex = driver.querySelector('[name="driverIndex[]"]').value;
    const driverName = driver
      .querySelector(`#driverName${driverIndex}`)
      .value.trim();
    const driverLicense = driver
      .querySelector(`#driverLicense${driverIndex}`)
      .value.trim();
    const driverLicenseExpiry = driver.querySelector(
      `#driverLicenseExpiry${driverIndex}`
    ).value;
    console.log(driver.querySelector(`#driverLicenseExpiry${driverIndex}`));
    console.log(driverLicenseExpiry);

    // Check if Driver's Name is provided
    if (!driverName) {
      errors.push(`Driver ${driverIndex + 1}: Name is required.`);
      corporateFormDOM.getElementById(
        `driverName${driverIndex}Error`
      ).innerText = "Name is required.";
      isValid = false;
      errorCount++;
    }

    // Check if Driving License Number is provided and valid
    if (!driverLicense) {
      errors.push(
        `Driver ${driverIndex + 1}: Driving License Number is required.`
      );
      corporateFormDOM.getElementById(
        `driverLicense${driverIndex}Error`
      ).innerText = "Driving License Number is required.";
      isValid = false;
      errorCount++;
    } else if (!/^[A-Z]{2}[0-9]{2}[0-9]{1,11}$/.test(driverLicense)) {
      errors.push(
        `Driver ${
          driverIndex + 1
        }: Invalid Driving License format. It should be like "DL01234567890".`
      );
      corporateFormDOM.getElementById(
        `driverLicense${driverIndex}Error`
      ).innerText = "Invalid Driving License format.";
      isValid = false;
      errorCount++;
    }

    // Check if License Expiry Date is provided
    if (!driverLicenseExpiry) {
      errors.push(
        `Driver ${driverIndex + 1}: License Expiry Date is required.`
      );
      corporateFormDOM.getElementById(
        `driverLicenseExpiry${driverIndex}Error`
      ).innerText = "License Expiry Date is required.";
      isValid = false;
      errorCount++;
    }
  });

  // Invoicing and Payment Preferences Section

  // 11. Billing Name
  const billingName = corporateFormDOM
    .getElementById("billingName")
    .value.trim();
  if (!billingName) {
    errors.push("Billing Name is required.");
    corporateFormDOM.getElementById("billingNameError").innerText =
      "Billing Name is required.";
    isValid = false;
    errorCount++;
  }

  // 12. Billing Email
  const billingEmail = corporateFormDOM
    .getElementById("billingEmail")
    .value.trim();
  if (!billingEmail || !/^\S+@\S+\.\S+$/.test(billingEmail)) {
    errors.push("A valid Billing Email Address is required.");
    corporateFormDOM.getElementById("billingEmailError").innerText =
      "A valid Billing Email Address is required.";
    isValid = false;
    errorCount++;
  }

  // 13. Billing Address
  const billingAddress = corporateFormDOM
    .getElementById("billingAddress")
    .value.trim();
  if (!billingAddress) {
    errors.push("Billing Address is required.");
    corporateFormDOM.getElementById("billingAddressError").innerText =
      "Billing Address is required.";
    isValid = false;
    errorCount++;
  }

  // 14. Payment Method
  const paymentMethod = corporateFormDOM
    .getElementById("paymentMethod")
    .value.trim();
  if (!paymentMethod) {
    errors.push("Preferred Payment Method is required.");
    corporateFormDOM.getElementById("paymentMethodError").innerText =
      "Preferred Payment Method is required.";
    isValid = false;
    errorCount++;
  }

  // 15. Invoice Frequency
  const invoiceFrequency = corporateFormDOM
    .getElementById("invoiceFrequency")
    .value.trim();
  if (!invoiceFrequency) {
    errors.push("Invoice Frequency is required.");
    corporateFormDOM.getElementById("invoiceFrequencyError").innerText =
      "Invoice Frequency is required.";
    isValid = false;
    errorCount++;
  }

  // Show errors if validation failed
  if (errorCount > 0) {
    const errorMessage =
      errors.length > 1
        ? "Please check the form for insufficient or incorrect data."
        : errors[0];
    submitButton.insertAdjacentHTML(
      "beforebegin",
      `<div id="globalError" class="error-message">${errorMessage}</div>`
    );
    return false; // Prevent form submission
  }

  // Prepare form data for submission
  const formData = new FormData(corporateFormDOM);

  // Submit the form using AJAX
  fetch(baseUrl + "customerManagement/save-corporateForm", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        submitButton.insertAdjacentHTML(
          "beforebegin",
          `<div id="globalError" class="text-success">Successfully updated</div>`
        );
        setTimeout(() => {
          clearErrors(corporateFormDOM);
          postSaveCustomerDetails(data.insertId);
        }, GLOBAL_ERROR_TIMEOUT ?? 2000);
      } else {
        // Show error messages returned from the server
        submitButton.insertAdjacentHTML(
          "beforebegin",
          `<div id="globalError" class="error-message">${data.errors.join(
            "<br>"
          )}</div>`
        );
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      submitButton.insertAdjacentHTML(
        "beforebegin",
        '<div id="globalError" class="error-message">An error occurred. Please try again.</div>'
      );
    });

  return false; // Prevent default form submission
}
/**
 * Adds a new driver information section to the form
 */
function addDriver() {
  const driverInfoDiv = getElement("driverInfo");

  // Get the last child element inside the driverInfoDiv
  const lastDriverDiv = driverInfoDiv.lastElementChild;

  // Get the driverIndex value from the lastDriverDiv
  const driverIndex = lastDriverDiv
    ? parseInt(lastDriverDiv.querySelector('[name="driverIndex[]"]').value)
    : 0;

  // Get the number of driver-info divs (children) inside driverInfoDiv
  driverInfoDiv.insertAdjacentHTML(
    "beforeend",
    `
              <div class="driver-info">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Driver ${driverIndex + 1}:</h5>
                      <button type="button" class="btn btn-danger btn-sm" onclick="removeDriver(this)">
                          <i class="fas fa-trash-alt"></i> Delete
                      </button>
                  </div>
                  <input type="hidden" name="driverIndex[]" value="${driverIndex}">
                  <div class="mb-3">
                      <label for="driverName${driverIndex}">Driver's Name: <span class="mandatoryFieldSpan">* </span></label>
                      <input type="text" class="form-control" id="driverName${driverIndex}" name="driverName${driverIndex}">
                      <div class="error-message" id="driverName${driverIndex}Error"></div>
                  </div>
                  <div class="mb-3">
                      <label for="driverLicense${driverIndex}">Driving License Number: <span class="mandatoryFieldSpan">* </span></label>
                      <input type="text" class="form-control" id="driverLicense${driverIndex}" name="driverLicense${driverIndex}">
                      <div class="error-message" id="driverLicense${driverIndex}Error"></div>
                  </div>
                  <div class="mb-3">
                      <label for="driverLicenseExpiry${driverIndex}">License Expiry Date: <span class="mandatoryFieldSpan">* </span></label>
                      <input type="date" class="form-control" id="driverLicenseExpiry${driverIndex}" name="driverLicenseExpiry${driverIndex}" onkeydown="return false" min="${currentDate}">
                      <div class="error-message" id="driverLicenseExpiry${driverIndex}Error"></div>
                  </div>
                  <div class="mb-3">
                      <label for="driverLicenseExpiryUploadLicenseFront${driverIndex}">Upload Driving License (Front): </label>
                      <input type="file" class="form-control" id="driverLicenseExpiryUploadLicenseFront${driverIndex}" name="driverLicenseExpiryUploadLicenseFront${driverIndex}" accept="image/*;capture=camera">
                  </div>
                  <div class="mb-3">
                      <label for="driverLicenseExpiryUploadLicenseBack${driverIndex}">Upload Driving License (Back): </label>
                      <input type="file" class="form-control" id="driverLicenseExpiryUploadLicenseBack${driverIndex}" name="driverLicenseExpiryUploadLicenseBack${driverIndex}" accept="image/*;capture=camera">
                  </div>
              </div>
    `
  );
}

/**
 * Removes a driver information section from the form
 * @param {HTMLElement} button The button that was clicked to initiate the removal
 */
function removeDriver(button) {
  Swal.fire({
    title: "🚗❌ Remove Driver?",
    html: "<b style='color: red;'>This action cannot be undone!</b><br>Are you sure you want to <b>remove this driver</b>?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "🗑️ Yes, Remove!",
    cancelButtonText: "❌ Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      // Find the parent 'driver-info' div and remove it
      button.closest(".driver-info").remove();

      // Show success notification
      Swal.fire({
        title: "✅ Driver Removed!",
        html: "🚗 The driver has been <b>successfully removed</b>.",
        icon: "success",
        timer: 2000,
        showConfirmButton: false,
      });
    }
  });
}
