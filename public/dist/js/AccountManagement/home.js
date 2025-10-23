document.getElementById("postcode").addEventListener("input", function (e) {
  let input = this.value.replace(/\D/g, ""); // Remove non-numeric characters
  this.value = input;

  if (input.length === 6) {
    fetch(`https://api.postalpincode.in/pincode/${input}`)
      .then((response) => response.json())
      .then((data) => {
        if (data[0]?.Status === "Success" && data[0].PostOffice.length > 0) {
          let firstPostOffice = data[0].PostOffice[0];
          let stateField = document.getElementById("state");
          let cityField = document.getElementById("city");

          stateField.value = firstPostOffice.State;
          cityField.value = firstPostOffice.District;

          stateField.readOnly = true;
          cityField.readOnly = true;
        } else {
          enableFields();
        }
      })
      .catch((error) => {
        enableFields();
      });
  }
});

function enableFields() {
  let stateField = document.getElementById("state");
  let cityField = document.getElementById("city");

  stateField.value = "";
  cityField.value = "";

  stateField.readOnly = false;
  cityField.readOnly = false;
}
function triggerFileInput(inputId) {
  getElement(inputId).click();
}

(() => {
  "use strict";
  const form = document.querySelector("#businessForm");

  form.addEventListener("submit", async function (event) {
    event.preventDefault(); // Always prevent default submission
    event.stopPropagation();

    // Add Bootstrap validation class
    form.classList.add("was-validated");

    if (!form.checkValidity()) {
      return; // If form is invalid, stop here
    }

    // Create FormData object
    const formData = new FormData(form);

    try {
      const response = await fetch(form.action, {
        method: form.method || "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error("Network response was not ok");
      }

      const data = await response.json(); // or .text() depending on response

      // Show success message in toast
      showToast({
        title: "Account Details",
        message: data.message,
        type: data.success ? "success" : "error",
      });
      updateBusinessLogo();
    } catch (error) {
      // Handle error
      console.error("Form submission failed:", error);
    }
  });
})();
function updateBusinessLogo() {
  const input = getElement("businessLogo");
  const file = input.files?.[0];
  const preview = document.getElementById("businessLogoMain");

  if (file && preview) {
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

// Set up drag and drop for all upload boxes
document.addEventListener("DOMContentLoaded", function () {
  // Set up file inputs
  setupFileInputs();

  // Set up drag and drop for all upload boxes
  setupDragAndDrop();

  // Load existing business logo if available
  loadExistingBusinessLogo();
});

function setupFileInputs() {
  const fileInputs = document.querySelectorAll(".fileInput");

  fileInputs.forEach((input) => {
    const type = input.id;
    input.addEventListener("change", function () {
      handleFiles(this.files, type);
    });
  });
}

function setupDragAndDrop() {
  const uploadBoxes = document.querySelectorAll(".upload-box");

  uploadBoxes.forEach((box) => {
    const inputId = box.getAttribute("onclick").match(/'([^']+)'/)[1];

    box.addEventListener("dragover", function (e) {
      e.preventDefault();
      e.stopPropagation();
      this.style.borderColor = "#4880FF";
    });

    box.addEventListener("dragleave", function (e) {
      e.preventDefault();
      e.stopPropagation();
      this.style.borderColor = "#ccc";
    });

    box.addEventListener("drop", function (e) {
      e.preventDefault();
      e.stopPropagation();
      this.style.borderColor = "#ccc";

      handleFiles(e.dataTransfer.files, inputId);
    });
  });
}
function loadExistingBusinessLogo() {
  // Check if there's an existing logo URL in a hidden field or data attribute
  const logoContainer = document.getElementById("businessLogoPreview");
  const existingLogoUrl =
    document.getElementById("existingLogoUrl")?.value || "";

  if (existingLogoUrl && existingLogoUrl !== "") {
    // Create HTML for preview
    const previewHtml = `
        <div class="preview-container d-flex align-items-center mt-3">
          <div class="preview-image me-3">
            <img src="${existingLogoUrl}" alt="Business Logo" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
          </div>
        </div>
      `;
    logoContainer.innerHTML = previewHtml;
    logoContainer.style.display = "block";
  }
}

function removeExistingLogo() {
  // Clear the preview
  document.getElementById("businessLogoPreview").innerHTML = "";
  document.getElementById("businessLogoPreview").style.display = "none";

  // Set a hidden input to indicate the logo should be removed
  const hiddenInput = document.createElement("input");
  hiddenInput.type = "hidden";
  hiddenInput.name = "remove_business_logo";
  hiddenInput.value = "1";
  document.getElementById("businessForm").appendChild(hiddenInput);
}

// Function to handle preview display after file upload (works with existing imageHandler.js)
function handleFiles(files, inputId) {
  const input = document.getElementById(inputId);
  if (files && files.length > 0) {
    input.files = files;

    // Trigger the existing handleFileUpload function from imageHandler.js
    handleFileUpload(input);

    // Add preview functionality
    displayPreview(files[0], inputId);
  }
}

function displayPreview(file, inputId) {
  const previewElement = document.getElementById(inputId + "Preview");

  if (!previewElement) return;

  // Clear previous preview
  previewElement.innerHTML = "";

  if (file.type.startsWith("image/")) {
    const reader = new FileReader();
    reader.onload = function (e) {
      const previewHtml = `
          <div class="preview-container d-flex align-items-center mt-3">
            <div class="preview-image me-3">
              <img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
            </div>
          </div>
        `;
      previewElement.innerHTML = previewHtml;
      previewElement.style.display = "block";
    };
    reader.readAsDataURL(file);
  }
}
