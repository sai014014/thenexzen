/**
 * Validate and submit the Individual Form.
 * @returns {boolean} False to prevent default form submission if validation fails.
 */
function individualFormSubmit(bookingManagementFlag = 0) {
  const individualFormDom = document.querySelector("#individualForm");
  const parent_DOM = bookingManagementFlag == 1 ? individualFormDom : document;

  // Clear previous error messages
  clearErrors(parent_DOM);
  let isValid = true;
  const errors = [];
  let errorCount = 0;

  // If validation failed, show errors above the submit button
  const submitButton = parent_DOM.querySelector("#individualFormSubmitButton");

  // Check if #globalError already exists and remove it
  const existingGlobalError = parent_DOM.querySelector("#globalError");
  if (existingGlobalError) {
    existingGlobalError.remove();
  }

  // Helper function to set error messages safely
  const setError = (selector, message) => {
    const errorElement = parent_DOM.querySelector(selector);
    if (errorElement) {
      errorElement.innerText = message;
    }
  };

  // Retrieve the Customer ID from the form
  const customerId =
    parent_DOM.querySelector("#customerId")?.value.trim() || "0";

  // 1. Full Name
  const fullName = parent_DOM.querySelector("#fullName")?.value.trim() || "";
  if (!fullName) {
    // If the Full Name is empty, add an error message
    errors.push("Full Name is required.");
    setError("#fullNameError", "Full Name is required.");
    isValid = false;
    errorCount++;
  }

  // 2. Mobile Number
  const mobileNumber =
    parent_DOM.querySelector("#mobileNumber")?.value.trim() || "";
  if (!/^\d{10}$/.test(mobileNumber)) {
    // If the Mobile Number is not a 10-digit number, add an error message
    errors.push("Mobile Number must be a 10-digit number.");
    setError("#mobileNumberError", "Mobile Number must be a 10-digit number.");
    isValid = false;
    errorCount++;
  }

  // 3. Email
  const email = parent_DOM.querySelector("#email")?.value.trim() || "";
  if (email.length > 0 && !/^\S+@\S+\.\S+$/.test(email)) {
    errors.push("A valid Email Address is required.");
    setError("#emailError", "A valid Email Address is required.");
    isValid = false;
    errorCount++;
  }

  // 4. Date of Birth
  const dob = parent_DOM.querySelector("#dob")?.value;
  const age = dob
    ? new Date().getFullYear() - new Date(dob).getFullYear()
    : NaN;
  if (isNaN(age)) {
    // If the customer is less than 18 years old, add an error message
    errors.push("Date of birth is required.");
    setError("#dobError", "Date of birth is required.");
    isValid = false;
    errorCount++;
  } else if (age < 18) {
    // If the customer is less than 18 years old, add an error message
    errors.push("You must be at least 18 years old.");
    setError("#dobError", "You must be at least 18 years old.");
    isValid = false;
    errorCount++;
  }

  // 5. Permanent Address
  const permanentAddress =
    parent_DOM.querySelector("#permanentAddress")?.value.trim() || "";
  if (!permanentAddress) {
    // If the Permanent Address is empty, add an error message
    errors.push("Permanent Address is required.");
    setError("#permanentAddressError", "Permanent Address is required.");
    isValid = false;
    errorCount++;
  }

  // 6. Government ID
  const idType = parent_DOM.querySelector("#idType")?.value.trim() || "";
  if (!idType) {
    errors.push("Please select a Government ID type.");
    setError("#idTypeError", "Please select a Government ID type.");
    isValid = false;
    errorCount++;
  }

  // 7. Government ID Number
  const idNumber = parent_DOM.querySelector("#idNumber")?.value.trim() || "";
  if (!idNumber) {
    // If the Government ID Number is empty, add an error message
    errors.push("Government ID Number is required.");
    setError("#idNumberError", "Government ID Number is required.");
    isValid = false;
    errorCount++;
  } else {
    // Validate the Government ID Number based on the selected type
    isValid = validateID(parent_DOM) && isValid;
    !isValid ? errorCount++ : "";
  }

  // 8. Driving License Number
  const drivingLicense =
    parent_DOM.querySelector("#drivingLicense")?.value.trim() || "";
  if (!drivingLicense) {
    // If the Driving License Number is empty, add an error message
    errors.push("Driving License Number is required.");
    setError("#drivingLicenseError", "Driving License Number is required.");
    isValid = false;
    errorCount++;
  } else {
    // Validate the Driving License Number
    isValid = validateDrivingLicense(parent_DOM) && isValid;
    !isValid ? errorCount++ : "";
  }
  // If validation failed, show errors above the submit button
  if (!isValid && submitButton) {
    const errorMessage =
      errors.length == 0 || errors.length > 1
        ? "Please check the form for insufficient or incorrect data."
        : errors[0];
    submitButton.insertAdjacentHTML(
      "beforebegin",
      `<div id="globalError" class="error-message">${errorMessage}</div>`
    );

    return false; // Prevent form submission
  }

  // Prepare form data for submission
  const formData = individualFormDom ? new FormData(individualFormDom) : null;

  if (formData) {
    // Submit the form using AJAX
    fetch(baseUrl + "customerManagement/save-individualForm", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          if (bookingManagementFlag == 1) {
            // Show error messages returned from the server
            submitButton?.insertAdjacentHTML(
              "beforebegin",
              '<div id="globalError" class="text-success">Successfully saved.</div>'
            );
            setTimeout(() => {
              clearErrors(parent_DOM);
              if (window.BookingManagement?.postSaveCustomerDetails) {
                window.BookingManagement.postSaveCustomerDetails(data.insertId);
              } else {
                console.error("postSaveCustomerDetails is not available.");
              }
            }, GLOBAL_ERROR_TIMEOUT ?? 3000);
          } else {
            window.location.href =
              customerId != 0
                ? `${baseUrl}customerManagement/view-individualCustomerDetails/${customerId}`
                : `${baseUrl}customerManagement`;
          }
        } else {
          submitButton?.insertAdjacentHTML(
            "beforebegin",
            `<div id="globalError" class="error-message">${data.errors.join(
              "<br>"
            )}</div>`
          );
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        submitButton?.insertAdjacentHTML(
          "beforebegin",
          '<div id="globalError" class="error-message">An error occurred. Please try again.</div>'
        );
      });
  }

  return false; // Prevent default form submission
}
