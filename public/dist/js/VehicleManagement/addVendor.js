function initializeVendorFormScripts() {
  const payoutMethod = getElement("payoutMethod");
  const payoutFrequency = getElement("payoutFrequency");
  const saveVendorBtn = getElement("saveVendorBtn");

  if (!payoutMethod || !payoutFrequency || !saveVendorBtn) {
    console.warn("Vendor form elements not found. Initialization skipped.");
    return;
  }

  // Payout Method Toggle
  payoutMethod.addEventListener("change", handlePayoutMethodChange);
  payoutFrequency.addEventListener("change", handlePayoutFrequencyChange);
  saveVendorBtn.addEventListener("click", handleSaveVendor);
}

// Handle Payout Method Change
function handlePayoutMethodChange() {
  const method = this.value;

  const sections = {
    "Bank Transfer": "bankDetails",
    "UPI Payment": "upiDetails",
    Other: "otherPayoutMethod",
  };

  // Hide all sections
  ["bankDetails", "upiDetails", "otherPayoutMethod"].forEach((section) => {
    getElement(section).style.display = "none";
  });

  // Remove "required" from all fields
  const allFields = [
    "bankName",
    "accountHolder",
    "accountNumber",
    "ifscCode",
    "upiId",
    "otherMethod",
  ];
  allFields.forEach((id) => getElement(id).removeAttribute("required"));

  // Show selected section and add "required" to its fields
  if (sections[method]) {
    getElement(sections[method]).style.display = "block";

    if (method === "Bank Transfer") {
      ["bankName", "accountHolder", "accountNumber", "ifscCode"].forEach((id) =>
        getElement(id).setAttribute("required", "true")
      );
    } else if (method === "UPI Payment") {
      getElement("upiId").setAttribute("required", "true");
    } else if (method === "Other") {
      getElement("otherMethod").setAttribute("required", "true");
    }
  }
}

// Handle Payout Frequency Change
function handlePayoutFrequencyChange() {
  const otherFrequencySection = getElement("otherPayoutFrequency");
  const otherFrequencyInput = getElement("otherFrequencyText");

  if (this.value === "Other") {
    otherFrequencySection.style.display = "block";
    otherFrequencyInput.setAttribute("required", "true");
  } else {
    otherFrequencySection.style.display = "none";
    otherFrequencyInput.removeAttribute("required");
  }
}

// Handle Save Vendor Form Submission
function handleSaveVendor() {
  const form = getElement("vendorForm");
  $("#globalError").remove(); // Clear previous error message

  if (form.checkValidity()) {
    submitVendorForm(form);
  } else {
    form.classList.add("was-validated"); // Add Bootstrap styling if invalid
  }
}

// Form Submission via AJAX
function submitVendorForm(form) {
  const url = baseUrl + "vendorManagement/save-vendorForm";

  $.ajax({
    type: "POST",
    url: url,
    data: new FormData(form),
    contentType: false,
    processData: false,
    success: function (response) {
      if (response.success) {
        handleVendorSaveSuccess(response);
      } else {
        handleVendorSaveError(response.errors, form);
      }
    },
    error: function () {
      showToast({
        title: "Vendor",
        message: "Failed to save vendor. Please try again.",
        type: "error",
      });
    },
  });
}

// Success Handler
function handleVendorSaveSuccess(response) {
  // Hide the modal
  const modalEl = getElement("addVendorModal");
  const modal = bootstrap.Modal.getInstance(modalEl); // Important to use this to properly hide existing modal
  modal.hide();

  // Show success message in toast
  showToast({
    title: "Success",
    message: "Vendor added successfully!",
    type: "success",
  });
}

// Error Handler
function handleVendorSaveError(errors, form) {
  const errorHtml = `
      <div id="globalError" class="alert alert-danger mt-3">
          ${errors.join("<br>")}
      </div>`;
  form.insertAdjacentHTML("beforeend", errorHtml);
}
