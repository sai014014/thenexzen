/**
 * Validate and submit the Corporate Form.
 * @returns {boolean} False to prevent default form submission if validation fails.
 */
function corporateFormSubmit(bookingManagementFlag = 0) {
  const corporateFormDom = document.querySelector("#corporateForm");
  if (!corporateFormDom) return false;

  const parent_DOM = bookingManagementFlag === 1 ? corporateFormDom : document;

  // Clear previous error messages
  clearErrors(parent_DOM);

  let isValid = true;
  const errors = [];
  let errorCount = 0;

  // Submit button
  const submitButton = parent_DOM.querySelector("#corporateFormSubmitButton");

  // Remove any existing global error
  const existingGlobalError = parent_DOM.querySelector("#globalError");
  if (existingGlobalError) existingGlobalError.remove();

  // Retrieve and validate form fields
  const getValue = (selector) => {
    const element = parent_DOM.querySelector(selector);
    return element ? element.value.trim() : "";
  };

  const setError = (selector, message) => {
    const errorElement = parent_DOM.querySelector(selector);
    if (errorElement) errorElement.innerText = message;
  };

  // Validation logic
  const validateField = (value, errorSelector, errorMessage) => {
    if (!value) {
      setError(errorSelector, errorMessage);
      errors.push(errorMessage);
      isValid = false;
      errorCount++;
    }
  };

  const customerId = getValue("#customerId");
  // 1. Company Name
  validateField(
    getValue("#companyName"),
    "#companyNameError",
    "Company Name is required."
  );
  // 2. Company Type
  validateField(
    getValue("#companyType"),
    "#companyTypeError",
    "Company Type is required."
  );

  // GSTIN validation
  const gstin = getValue("#gstin");
  if (
    gstin &&
    !/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}$/.test(
      gstin
    )
  ) {
    setError("#gstinError", "Invalid GSTIN format.");
    errors.push("Invalid GSTIN format.");
    isValid = false;
    errorCount++;
  }

  // 4. Company Address
  validateField(
    getValue("#companyAddress"),
    "#companyAddressError",
    "Company Address is required."
  );

  // PAN Number validation
  const panNumber = getValue("#panNumber");
  if (panNumber && !/^[A-Z]{5}\d{4}[A-Z]{1}$/.test(panNumber)) {
    setError("#panNumberError", "Invalid PAN Number format.");
    errors.push("Invalid PAN Number format.");
    isValid = false;
    errorCount++;
  }

  // 6. Contact Person Name
  validateField(
    getValue("#contactPersonName"),
    "#contactPersonNameError",
    "Contact Person Name is required."
  );

  // 7. Designation
  validateField(
    getValue("#designation"),
    "#designationError",
    "Designation is required."
  );

  // 8. Official Email
  const officialEmail = getValue("#officialEmail");
  if (officialEmail && !/^\S+@\S+\.\S+$/.test(officialEmail)) {
    setError(
      "#officialEmailError",
      "A valid Official Email Address is required."
    );
    errors.push("A valid Official Email Address is required.");
    isValid = false;
    errorCount++;
  }
  // 9. Mobile Number
  const contactMobile = getValue("#contactMobile");
  if (!/^\d{10}$/.test(contactMobile)) {
    setError("#contactMobileError", "Mobile Number must be a 10-digit number.");
    errors.push("Mobile Number must be a 10-digit number.");
    isValid = false;
    errorCount++;
  }

  // Drivers validation
  const drivers = parent_DOM.querySelectorAll(".driver-info");
  console.log(drivers);

  drivers.forEach((driver, index) => {
    const getDriverValue = (name) => {
      const element = driver.querySelector(name);
      if (!element) {
        console.log(`Element not found: ${name}`);
        return ""; // Return an empty string if the element is not found
      }
      console.log(element);
      console.log(element.value.trim());

      return element.value.trim();
    };

    const driverName = getDriverValue(`#driverName${index}`);
    const driverLicense = getDriverValue(`#driverLicense${index}`);
    const driverLicenseExpiry = getDriverValue(`#driverLicenseExpiry${index}`);

    // Check if Driver's Name is provided
    if (!driverName) {
      setError(`#driverName${index}Error`, "Name is required.");
      errors.push(`Driver ${index + 1}: Name is required.`);
      isValid = false;
      errorCount++;
    }
    // Check if Driving License Number is provided and valid
    if (!driverLicense) {
      setError(`#driverLicense${index}Error`, "Driving License is required.");
      errors.push(`Driver ${index + 1}: Driving License Number is required.`);
      isValid = false;
      errorCount++;
    } else if (!/^[A-Z]{2}[0-9]{2}[0-9]{1,11}$/.test(driverLicense)) {
      setError(
        `#driverLicense${index}Error`,
        "Invalid Driving License format."
      );
      errors.push(
        `Driver ${
          index + 1
        }: Invalid Driving License format. It should be like "DL01234567890".`
      );
      isValid = false;
      errorCount++;
    }
    console.log(
      "driverLicenseExpiry",
      driverLicenseExpiry,
      typeof driverLicenseExpiry
    );

    if (!driverLicenseExpiry || driverLicenseExpiry.length == 0) {
      setError(
        `#driverLicenseExpiry${index}Error`,
        "License Expiry Date is required."
      );
      errors.push(`Driver ${index + 1}: License Expiry Date is required.`);
      isValid = false;
      errorCount++;
    }
  });

  // Invoicing and Payment Preferences Section

  // 11. Billing Name
  validateField(
    getValue("#billingName"),
    "#billingNameError",
    "Billing Name is required."
  );
  // 12. Billing Email
  const billingEmail = getValue(`#billingEmail`);
  if (!billingEmail || !/^\S+@\S+\.\S+$/.test(billingEmail)) {
    setError(
      `#billingEmailError`,
      "A valid Billing Email Address is required."
    );
    errors.push(`A valid Billing Email Address is required.`);
    isValid = false;
    errorCount++;
  }

  // 13. Billing Address
  validateField(
    getValue("#billingAddress"),
    "#billingAddressError",
    "Billing Address is required."
  );

  // 14. Payment Method
  validateField(
    getValue("#paymentMethod"),
    "#paymentMethodError",
    "Preferred Payment Method is required."
  );

  // 15. Invoice Frequency
  validateField(
    getValue("#invoiceFrequency"),
    "#invoiceFrequencyError",
    "Invoice Frequency is required."
  );

  // Show errors if any
  if (errorCount > 0) {
    const errorMessage =
      errors.length > 1
        ? "Please check the form for insufficient or incorrect data."
        : errors[0];
    if (submitButton) {
      submitButton.insertAdjacentHTML(
        "beforebegin",
        `<div id="globalError" class="error-message">${errorMessage}</div>`
      );
    }
    return false;
  }

  // Form submission using AJAX
  const formData = new FormData(corporateFormDom);
  fetch(baseUrl + "customerManagement/save-corporateForm", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        if (bookingManagementFlag == 1 && submitButton) {
          submitButton.insertAdjacentHTML(
            "beforebegin",
            `<div id="globalError" class="text-success">Successfully updated</div>`
          );
          setTimeout(() => {
            clearErrors(parent_DOM);
            if (window.BookingManagement?.postSaveCustomerDetails) {
              window.BookingManagement.postSaveCustomerDetails(data.insertID);
            }
          }, GLOBAL_ERROR_TIMEOUT ?? 2000);
        } else {
          // Redirect to the appropriate page based on customer ID
          const redirectUrl =
            customerId != 0
              ? baseUrl +
                "customerManagement/view-corporateCustomerDetails/" +
                customerId
              : baseUrl + "customerManagement";
          window.location.href = redirectUrl;
        }
      } else {
        // Show error messages returned from the server
        if (submitButton) {
          submitButton.insertAdjacentHTML(
            "beforebegin",
            `<div id="globalError" class="error-message">${data.errors.join(
              "<br>"
            )}</div>`
          );
        }
      }
    })
    .catch(() => {
      if (submitButton) {
        submitButton.insertAdjacentHTML(
          "beforebegin",
          '<div id="globalError" class="error-message">An error occurred. Please try again.</div>'
        );
      }
    });

  return false;
}

/**
 * Adds a new driver information section to the form.
 */
function addDriver(parent_DOM = document, bookingManagementFlag = 0) {
  const driverInfoDiv = parent_DOM.querySelector("#driverInfo");
  if (!driverInfoDiv) return;

  // Determine the next driver index
  const lastDriverRow = driverInfoDiv.lastElementChild;

  let driverIndex = 0;

  if (lastDriverRow) {
    const driverIndexInput = lastDriverRow.querySelector(
      '[name="driverIndex[]"]'
    );

    driverIndex =
      driverIndexInput && !isNaN(parseInt(driverIndexInput.value))
        ? parseInt(driverIndexInput.value) + 1
        : driverInfoDiv.children.length; // Fallback: use number of rows
  }

  // Get the number of driver-info divs (children) inside driverInfoDiv
  driverInfoDiv.insertAdjacentHTML(
    "beforeend",
    `
            <div class="driver-info">
              <div class="d-flex justify-content-between align-items-center">
                  <h5>Driver ${driverIndex + 1}:</h5>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeDriver(this, ${bookingManagementFlag})">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
                <input type="hidden" name="driverIndex[]" value="${driverIndex}">
                <input type="hidden" name="driverId${driverIndex}" value="0">
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
                    <input type="file" class="form-control fileInput" id="driverLicenseExpiryUploadLicenseFront${driverIndex}" name="driverLicenseExpiryUploadLicenseFront${driverIndex}" accept="image/*,.pdf" capture="environment" onchange="handleFileUpload(this);">
                </div>
                <div class="mb-3">
                    <label for="driverLicenseExpiryUploadLicenseBack${driverIndex}">Upload Driving License (Back): </label>
                    <input type="file" class="form-control fileInput" id="driverLicenseExpiryUploadLicenseBack${driverIndex}" name="driverLicenseExpiryUploadLicenseBack${driverIndex}" accept="image/*,.pdf" capture="environment" onchange="handleFileUpload(this);">
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
    title: "🚗 Remove Driver?",
    html: "<b style='color: red;'>This action cannot be undone!</b><br>Are you sure you want to <b>remove this driver</b>?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "🗑️ Yes, Remove!",
    cancelButtonText: "❌ No, Keep It",
  }).then((result) => {
    if (result.isConfirmed) {
      // Find the parent 'driver-info' div and remove it
      button.closest(".driver-info").remove();

      // Show success message after removal
      Swal.fire({
        title: "✅ Driver Removed!",
        html: "🚘 The driver has been <b>successfully removed</b>.",
        icon: "success",
        timer: 2000,
        showConfirmButton: false,
      });
    }
  });
}

// for booking management
/**
 * Adds a new driver information row to the table.
 */
function addDriver1(parent_DOM = document, bookingManagementFlag = 0) {
  const driverInfoTable = parent_DOM.querySelector("#driverInfo table tbody");
  if (!driverInfoTable) return;

  // Determine the next driver index
  const lastDriverRow = driverInfoTable.lastElementChild;

  let driverIndex = 0;

  if (lastDriverRow) {
    const driverIndexInput = lastDriverRow.querySelector(
      '[name="driverIndex[]"]'
    );

    driverIndex =
      driverIndexInput && !isNaN(parseInt(driverIndexInput.value))
        ? parseInt(driverIndexInput.value) + 1
        : driverInfoTable.children.length; // Fallback: use number of rows
  }

  // Append a new row for the driver
  driverInfoTable.insertAdjacentHTML(
    "beforeend",
    `
    <tr class="driver-row">
        <td>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="selectDriver${driverIndex}" name="selectDriver[]" value="${driverIndex}">
            </div>
        </td>
        <td>
            <input type="hidden" name="driverIndex[]" value="${driverIndex}">
            <input type="hidden" class="driverId" name="driverId${driverIndex}" value="0">
            <input type="text" class="form-control" id="driverName${driverIndex}" name="driverName${driverIndex}">
            <div class="error-message text-danger" id="driverName${driverIndex}Error"></div>
        </td>
        <td>
            <input type="text" class="form-control" id="driverLicense${driverIndex}" name="driverLicense${driverIndex}">
            <div class="error-message text-danger" id="driverLicense${driverIndex}Error"></div>
        </td>
        <td>
            <input type="date" class="form-control" id="driverLicenseExpiry${driverIndex}" name="driverLicenseExpiry${driverIndex}" onkeydown="return false" min="${currentDate}">
            <div class="error-message text-danger" id="driverLicenseExpiry${driverIndex}Error"></div>
        </td>
        <td>
            <input type="file" class="form-control fileInput" id="driverLicenseExpiryUploadLicenseFront${driverIndex}" name="driverLicenseExpiryUploadLicenseFront${driverIndex}" accept="image/*,.pdf" capture="environment" onchange="handleFileUpload(this);">
        </td>
        <td>
            <input type="file" class="form-control fileInput" id="driverLicenseExpiryUploadLicenseBack${driverIndex}" name="driverLicenseExpiryUploadLicenseBack${driverIndex}" accept="image/*,.pdf" capture="environment" onchange="handleFileUpload(this);">
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeDriver1(this, ${bookingManagementFlag})">
                <i class="fas fa-trash-alt"></i> Delete
            </button>
        </td>
    </tr>
    `
  );

  if (bookingManagementFlag === 1) {
    if (window.BookingManagement?.showHideCustomerSubmitButton) {
      window.BookingManagement.showHideCustomerSubmitButton(
        "corporateCustomer"
      );
    }
  }
}

/**
 * Removes a driver information row from the table.
 * @param {HTMLElement} button The button that was clicked to initiate the removal.
 */
function removeDriver1(button, bookingManagementFlag = 0) {
  Swal.fire({
    title: "🚗 Remove Driver?",
    html: "<b style='color: red;'>This action cannot be undone!</b><br>Are you sure you want to <b>remove this driver</b>?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "🗑️ Yes, Remove!",
    cancelButtonText: "❌ No, Keep It",
  }).then((result) => {
    if (result.isConfirmed) {
      // Remove the parent 'tr' (table row)
      button.closest("tr").remove();

      // Show success message after removal
      Swal.fire({
        title: "✅ Driver Removed!",
        html: "🚘 The driver has been <b>successfully removed</b>.",
        icon: "success",
        timer: 2000,
        showConfirmButton: false,
      });

      // Handle booking management logic
      if (bookingManagementFlag === 1) {
        if (window.BookingManagement?.showHideCustomerSubmitButton) {
          window.BookingManagement.showHideCustomerSubmitButton(
            "corporateCustomer"
          );
        }
      }
    }
  });
}
