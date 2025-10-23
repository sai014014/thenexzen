$(document).ready(function () {
  const vehicleRegistrationId =
    getElement("vehicleRegistrationId").value.trim() ?? 0;
  $("#vehicleYear").datepicker({
    format: "yyyy",
    viewMode: "years",
    minViewMode: "years",
    endDate: new Date(), // Restrict to the current year as the maximum
    autoclose: true,
  });

  $("#vehicleType").change(function () {
    var vehicleTypeId = $(this).val();

    if (vehicleTypeId) {
      $.ajax({
        url: baseUrl + "vehicleManagement/getVehicleBrand/" + vehicleTypeId, // Update with your route
        type: "GET",
        data: {},
        success: function (brands) {
          // Clear previous options in the vehicle make field
          $("#vehicleBrand").empty();
          $("#vehicleModel").empty();

          // Append new options
          $.each(brands, function (index, brand) {
            $("#vehicleBrand").append(
              $("<option>", {
                value: brand.brand_id,
                text: brand.name,
              })
            );
          });
          $("#vehicleBrand").trigger("change");
          updateFieldVisibility();
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error: ", status, error);
        },
      });
    }
  });
  $("#vehicleBrand").change(function () {
    var vehicleBrandId = $(this).val();
    if (vehicleBrandId) {
      $.ajax({
        url: baseUrl + "vehicleManagement/getVehicleModel/" + vehicleBrandId, // Update with your route
        type: "GET",
        data: {},
        success: function (models) {
          // Clear previous options in the vehicle make field
          $("#vehicleModel").empty();

          // Append new options
          $.each(models, function (index, model) {
            $("#vehicleModel").append(
              $("<option>", {
                value: model.model_id,
                text: model.model_name,
              })
            );
          });
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error: ", status, error);
        },
      });
    }
  });
  if (vehicleRegistrationId == 0) {
    $("#vehicleType").trigger("change");
  }

  function updateFieldVisibility() {
    const transmissionSelect = getElement("transmissionType");
    const vehicleType = (selectedVehicleType = getElement("vehicleType").value);

    getElement("transmissionType").removeAttribute("required");
    // Show only options relevant to the selected vehicle type
    Array.from(transmissionSelect.options).forEach((option) => {
      option.style.display = "none"; // Hide all options initially
    });

    if (selectedVehicleType === "2") {
      // Bike / Scooter
      Array.from(transmissionSelect.options).forEach((option) => {
        if (option.getAttribute("data-type") === "bike") {
          option.style.display = "block";
        }
      });
      getElement("transmissionType").removeAttribute("required");
    } else {
      // Car or Heavy Vehicle
      Array.from(transmissionSelect.options).forEach((option) => {
        if (option.getAttribute("data-type") === "car-heavy") {
          option.style.display = "block";
        }
      });
      document
        .getElementById("transmissionType")
        .setAttribute("required", "required");
    }

    // Reset the selection to the first visible option
    transmissionSelect.selectedIndex = Array.from(
      transmissionSelect.options
    ).findIndex((option) => option.style.display === "block");

    // Get the fields and toggle display based on vehicle type
    const seatingCapacityGroup = getElement("seatingCapacityGroup");
    const seatingCapacityDropdown = getElement("seatingCapacity");
    const engineCapacityGroup = getElement("engineCapacityGroup");
    const payloadCapacityGroup = getElement("payloadCapacityGroup");

    // Hide all category-specific fields initially and remove required attributes
    seatingCapacityGroup.style.display = "none";
    engineCapacityGroup.style.display = "none";
    payloadCapacityGroup.style.display = "none";

    seatingCapacityDropdown.removeAttribute("required");
    seatingCapacityDropdown.innerHTML = ""; // Clear previous options
    engineCapacityGroup.querySelector("input").removeAttribute("required");
    payloadCapacityGroup.querySelector("input").removeAttribute("required");

    // Function to populate seating capacity dropdown with a range of options
    function populateSeatingCapacityOptions(min, max) {
      for (let i = min; i <= max; i++) {
        const option = document.createElement("option");
        option.value = i;
        option.textContent = i;
        seatingCapacityDropdown.appendChild(option);
      }
    }

    // Show relevant fields and add required attributes based on selected vehicle type
    if (vehicleType == "1" || vehicleType == 1) {
      // Car
      seatingCapacityGroup.style.display = "block";
      seatingCapacityDropdown.setAttribute("required", "required");
      populateSeatingCapacityOptions(2, 15);
    } else if (vehicleType == "2" || vehicleType == 2) {
      // Bike / Scooter
      engineCapacityGroup.style.display = "block";
      engineCapacityGroup
        .querySelector("input")
        .setAttribute("required", "required");
    } else if (vehicleType == "3" || vehicleType == 3) {
      // Heavy Vehicle
      payloadCapacityGroup.style.display = "block";
      payloadCapacityGroup
        .querySelector("input")
        .setAttribute("required", "required");
      seatingCapacityGroup.style.display = "block";
      seatingCapacityDropdown.setAttribute("required", "required");
      populateSeatingCapacityOptions(2, 50);
    }
  }

  updateFieldVisibility(); // Trigger on page load to set initial state

  const vehicleFormSubmitButton = getElement("vehicleFormSubmitButton");
  vehicleFormSubmitButton.addEventListener("click", handleSaveVehicle);

  // Handle Save Vehicle Form Submission
  function handleSaveVehicle() {
    const form = getElement("vehicle-registration-form");
    const vehicleError = getElement("vehicleError");
    $("#globalError").remove(); // Clear previous global error message
    vehicleError.textContent = ""; // Clear vehicle image error
    console.log("vehicleRegistrationId", vehicleRegistrationId);
    console.log("filesData", filesData);
    console.log(filesData["vehicleDocument"]);
    console.log(filesData["vehicleDocument"].length);

    if (
      vehicleRegistrationId == 0 &&
      (!filesData["vehicleDocument"] ||
        filesData["vehicleDocument"].length == 0)
    ) {
      vehicleError.textContent = "Please upload at least one vehicle image.";
      scrollToErrorElement(vehicleError, 250);
      return; // Stop if validation fails
    }

    // If form is valid, submit via AJAX
    if (form.checkValidity()) {
      // Validate vehicle document files if vehicle is being added (not edited)
      submitVehicleForm(form);
    } else {
      form.classList.add("was-validated"); // Add Bootstrap styling if invalid
    }
  }

  // Form Submission via AJAX
  function submitVehicleForm(form) {
    const url = baseUrl + "vehicleManagement/save-VehicleForm";
    const formData = buildVehicleFormData(form);

    $.ajax({
      type: "POST",
      url: url,
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        if (response.success) {
          handleVehicleSaveSuccess(response);
        } else {
          handleVehicleSaveError(response.errors, form);
        }
      },
      error: function () {
        showToast({
          title: "Vehicle",
          message: "Form submission failed. Please try again.",
          type: "error",
        });
      },
    });
  }

  // Build FormData with Files
  function buildVehicleFormData(form) {
    const formData = new FormData(form);

    ["insuranceDocument", "rcDocument", "vehicleDocument"].forEach((type) => {
      (filesData[type] || []).forEach((file) => {
        formData.append(type + "[]", file);
      });
      (filesDataExisting[type] || []).forEach((file) => {
        formData.append(type + "Existing[]", file);
      });
    });

    return formData;
  }

  // Success Handler
  function handleVehicleSaveSuccess(response) {
    const redirectUrl =
      vehicleRegistrationId !== 0
        ? `${baseUrl}vehicleManagement/view-vehicleDetails/${vehicleRegistrationId}`
        : `${baseUrl}vehicleManagement`;

    window.location.href = redirectUrl;
  }

  // Error Handler
  function handleVehicleSaveError(errors, form) {
    const errorHtml = `
      <div id="globalError" class="alert alert-danger mt-3">
          ${errors.join("<br>")}
      </div>`;
    form.insertAdjacentHTML("beforeend", errorHtml);
  }
});

// Global variable to store all uploaded files
let filesData = {
  insuranceDocument: [],
  rcDocument: [],
  vehicleDocument: [],
};

// Set up drag and drop for all upload boxes
document.addEventListener("DOMContentLoaded", function () {
  // Set up file inputs
  setupFileInputs();

  // Set up drag and drop for all upload boxes
  setupDragAndDrop();
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

function triggerFileInput(inputId) {
  getElement(inputId).click();
}

function handleFiles(fileList, typeId) {
  const files = Array.from(fileList);
  const type = typeId;
  const filePreview = getElement(type.replace("Document", "Preview"));

  files.forEach((file) => {
    // Check if file type is allowed (PDF or image)
    if (file.type === "application/pdf" || file.type.startsWith("image/")) {
      // Check if file already exists by checking name
      if (!filesData[type].some((f) => f.name === file.name)) {
        filesData[type].push(file);
        displayFile(file, type, filePreview);
      }
    } else {
      showToast({
        title: "Vehicle",
        message: "Only PDF and image files are allowed.",
        type: "error",
      });
    }
  });

  // Reset file input value to allow selecting the same file again
  getElement(type).value = "";
}

function displayFile(file, type, filePreview) {
  const fileItem = document.createElement("div");
  fileItem.classList.add("file-item");
  fileItem.dataset.fileName = file.name;
  fileItem.dataset.fileType = type;

  // Create file preview
  if (file.type === "application/pdf") {
    fileItem.innerHTML = `
      <div class="pdf-icon">
          <img src="https://upload.wikimedia.org/wikipedia/commons/8/87/PDF_file_icon.svg" alt="PDF Icon" style="width: 100%; height: 100%;">
      </div>
    `;
  } else if (file.type.startsWith("image/")) {
    const fileReader = new FileReader();
    fileReader.onload = function (e) {
      const imgContainer = document.createElement("div");
      imgContainer.style.width = "100%";
      imgContainer.style.height = "100%";

      const img = document.createElement("img");
      img.src = e.target.result;
      img.alt = file.name;
      img.style.width = "100%";
      img.style.height = "100%";
      img.style.objectFit = "cover";

      imgContainer.appendChild(img);
      fileItem.appendChild(imgContainer);

      // Create and append actions after image is loaded
      appendActions(fileItem, file, type);
    };
    fileReader.readAsDataURL(file);
  } else {
    // For non-image files that aren't PDFs (fallback)
    fileItem.innerHTML = `
      <div class="pdf-icon">
          <div style="text-align: center; padding: 20px;">File: ${file.name}</div>
      </div>
    `;
  }

  // For PDFs, append actions immediately
  if (file.type === "application/pdf") {
    appendActions(fileItem, file, type);
  }

  // Add file item to preview container
  filePreview.appendChild(fileItem);
}

// Update the appendActions function to match the desired style
function appendActions(fileItem, file, type, isExisting = false) {
  const actionsDiv = document.createElement("div");
  actionsDiv.classList.add("file-actions");

  // Create remove button
  const removeBtn = document.createElement("button");
  removeBtn.type = "button";
  removeBtn.classList.add("close-button");
  removeBtn.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white" viewBox="0 0 16 16">
      <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
    </svg>
  `;
  removeBtn.title = "Remove";
  removeBtn.onclick = function (e) {
    e.stopPropagation(); // Prevent triggering container click events
    removeFile(file, type, fileItem, isExisting);
  };

  // Create preview button (centered on hover)
  const previewBtn = document.createElement("button");
  previewBtn.type = "button"; // Set the button type
  previewBtn.classList.add("preview-button-center");
  previewBtn.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 16 16">
      <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
      <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
    </svg>
  `;
  previewBtn.title = "Preview";
  previewBtn.onclick = function (e) {
    e.stopPropagation(); // Prevent triggering container click events
    openGallery(type, file.name);
  };

  // Add close button directly to file item (outside of actions div)
  fileItem.appendChild(removeBtn);

  // Add preview button to actions div
  actionsDiv.appendChild(previewBtn);

  // Add actions div to file item
  fileItem.appendChild(actionsDiv);

  // Remove click handler from file item to prevent modal opening on item click
  fileItem.style.cursor = "default";
}

function removeFile(file, type, fileItem, isExisting) {
  if (isExisting) {
    // Remove from existing files
    filesDataExisting[type] = filesDataExisting[type].filter((f) => f !== file);
  } else {
    // Remove from newly uploaded files
    filesData[type] = filesData[type].filter((f) => f.name !== file.name);
  }
  // Remove file item from preview
  fileItem.remove();

  // Clear error message if files are removed
  if (type === "vehicleDocument") {
    getElement("vehicleError").textContent = "";
  }
}

function openGallery(type, file, isExisting) {
  const modalEl = getElement("galleryModal");
  const galleryModal = new bootstrap.Modal(modalEl);
  const bigImage = getElement("big_image");
  const smallImages = getElement("small_images");

  // Clear previous images
  bigImage.innerHTML = "";
  smallImages.innerHTML = "";

  // Get files of the selected type from both sources (new & existing)
  let existingFiles = filesDataExisting[type] || [];
  let newFiles = filesData[type] || [];

  // Merge both lists into one while ensuring only images/PDFs
  let fileList = [
    ...existingFiles.filter(
      (f) =>
        f.endsWith(".jpg") ||
        f.endsWith(".jpeg") ||
        f.endsWith(".png") ||
        f.endsWith(".pdf")
    ),
    ...newFiles.filter(
      (f) => f.type.startsWith("image/") || f.type === "application/pdf"
    ),
  ];

  if (!fileList.length) {
    console.error("No files found for preview in type:", type);
    return;
  }

  // Find index of the selected file
  let currentIndex = isExisting
    ? existingFiles.indexOf(file)
    : newFiles.findIndex((f) => f.name === file.name);

  if (currentIndex === -1) {
    currentIndex = 0;
  }

  // Display thumbnails
  fileList.forEach((fileItem, index) => {
    const thumbnail = document.createElement("div");
    thumbnail.classList.add("thumbnail");
    if (index === currentIndex) {
      thumbnail.classList.add("active");
    }

    if (typeof fileItem === "string") {
      // Existing files (URLs)
      if (fileItem.endsWith(".pdf")) {
        thumbnail.innerHTML = `<div class="pdf-icon"><img src="${baseUrl}/uploads/pdf1.svg" alt="PDF Icon"></div>`;
      } else {
        thumbnail.innerHTML = `<img src="${fileItem}" alt="Vehicle Image">`;
      }
    } else {
      // New files (Blob/File objects)
      if (fileItem.type === "application/pdf") {
        thumbnail.innerHTML = `<div class="pdf-icon"><img src="https://upload.wikimedia.org/wikipedia/commons/8/87/PDF_file_icon.svg" alt="PDF Icon"></div>`;
      } else {
        const reader = new FileReader();
        reader.onload = function (e) {
          thumbnail.innerHTML = `<img src="${e.target.result}" alt="${fileItem.name}">`;
        };
        reader.readAsDataURL(fileItem);
      }
    }

    // On clicking a thumbnail, update the big image
    thumbnail.addEventListener("click", function () {
      // Update active thumbnail
      document.querySelectorAll(".thumbnail").forEach((thumb) => {
        thumb.classList.remove("active");
      });
      this.classList.add("active");

      // Update big image
      displayBigImage(type, fileList[index]);
    });

    smallImages.appendChild(thumbnail);
  });

  // Show the selected file in big preview
  displayBigImage(type, fileList[currentIndex]);

  // Show modal
  galleryModal.show();
}

function displayBigImage(type, fileItem) {
  const bigImage = getElement("big_image");

  // Clear previous content
  bigImage.innerHTML = "";

  if (typeof fileItem === "string") {
    // Existing file (URL)
    if (fileItem.endsWith(".pdf")) {
      bigImage.innerHTML = `<embed src="${fileItem}" type="application/pdf" width="100%" height="500px">`;
    } else {
      bigImage.innerHTML = `<img src="${fileItem}" alt="Preview Image" class="img-fluid">`;
    }
  } else if (fileItem instanceof File || fileItem instanceof Blob) {
    // New uploaded file (Blob/File object)
    if (fileItem.type === "application/pdf") {
      const objectURL = URL.createObjectURL(fileItem);
      bigImage.innerHTML = `<embed src="${objectURL}" type="application/pdf" width="100%" height="500px">`;
    } else {
      const reader = new FileReader();
      reader.onload = function (e) {
        bigImage.innerHTML = `<img src="${e.target.result}" alt="Preview Image" class="img-fluid">`;
      };
      reader.readAsDataURL(fileItem);
    }
  } else {
    console.error("Unsupported file format:", fileItem);
  }
}

// Function to display existing files on page load
function displayExistingFiles(files, filePreviewId, type) {
  if (!Array.isArray(files)) {
    console.error("Invalid files input", files);
    return;
  }

  const previewContainer = document.getElementById(filePreviewId);
  if (!previewContainer) {
    console.error("Invalid filePreview element ID:", filePreviewId);
    return;
  }

  files.forEach((filePath) => {
    if (typeof filePath !== "string" || !filePath.trim()) {
      console.error("Invalid filePath", filePath);
      return;
    }

    const fileItem = document.createElement("div");
    fileItem.classList.add("file-item");
    fileItem.dataset.fileName = filePath;
    fileItem.dataset.fileType = type;

    // Extract file extension safely
    const fileParts = filePath.split(".");
    const fileExtension =
      fileParts.length > 1 ? fileParts.pop().toLowerCase() : "";

    if (fileExtension === "pdf") {
      fileItem.innerHTML = `
        <div class="pdf-icon">
            <img src="${baseUrl}/uploads/pdf1.svg" alt="PDF Document" style="width: 100%; height: 100%;">
        </div>
      `;
      appendActions(fileItem, filePath, type, true);
    } else if (["jpg", "jpeg", "png"].includes(fileExtension)) {
      const imgContainer = document.createElement("div");
      imgContainer.style.width = "100%";
      imgContainer.style.height = "100%";

      const img = document.createElement("img");
      img.src = filePath;
      img.alt = "Vehicle Image";
      img.style.width = "100%";
      img.style.height = "100%";
      img.style.objectFit = "cover";

      imgContainer.appendChild(img);
      fileItem.appendChild(imgContainer);

      appendActions(fileItem, filePath, type, true);
    } else {
      fileItem.innerHTML = `
        <div class="pdf-icon">
            <div style="text-align: center; padding: 20px;">File: ${filePath}</div>
        </div>
      `;
    }

    // Add file item to preview container
    previewContainer.appendChild(fileItem);
  });
}

const vehicleRegistrationId =
  getElement("vehicleRegistrationId").value.trim() ?? 0;

document.addEventListener("DOMContentLoaded", function () {
  if (vehicleRegistrationId > 0) {
    prePopulate();
  }
});

/**
 * Capitalizes the first letter of a string.
 * @param {string} string - The string to capitalize.
 * @returns {string} The string with the first letter capitalized.
 */
function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}
$(".readonly_input").on("keydown mousedown", function (e) {
  e.preventDefault();
  e.stopPropagation();
});

// vendor
function initVendorDropdown() {
  const select = document.querySelector(".selectVendorDropdown .select");
  const caret = document.querySelector(".selectVendorDropdown .caret");
  const menu = document.querySelector("#vendorList");
  const options = menu.querySelectorAll("li[data-vendor-id]");
  const selectedVendorName = getElement("selectedVendorName");
  const selectedVendorId = getElement("selectedVendorId");
  const searchBar = getElement("vendorSearchBar");

  // Clear previous click event (optional if you dynamically change options like here)
  select.onclick = null;
  searchBar.oninput = null;
  document.removeEventListener("click", closeDropdownOnOutsideClick);

  select.addEventListener("click", () => {
    caret.classList.toggle("caret-rotate");
    menu.classList.toggle("menu-open");
  });

  options.forEach((option) => {
    option.onclick = function () {
      selectedVendorName.textContent = option.innerText;
      selectedVendorId.value = option.getAttribute("data-vendor-id");

      options.forEach((opt) => opt.classList.remove("active"));
      option.classList.add("active");

      caret.classList.remove("caret-rotate");
      menu.classList.remove("menu-open");
    };
  });

  searchBar.oninput = function () {
    const query = searchBar.value.toLowerCase();
    options.forEach((option) => {
      option.style.display = option.innerText.toLowerCase().includes(query)
        ? ""
        : "none";
    });
  };

  function closeDropdownOnOutsideClick(e) {
    if (!select.contains(e.target) && !menu.contains(e.target)) {
      caret.classList.remove("caret-rotate");
      menu.classList.remove("menu-open");
    }
  }

  document.addEventListener("click", closeDropdownOnOutsideClick);
}

document
  .getElementById("ownershipType")
  .addEventListener("change", function () {
    const ownershipType = this.value;
    console.log("Ownership Type Changed:", ownershipType);

    const vendorNameGroup = getElement("vendorNameGroup");

    if (ownershipType === "Vendor") {
      $.ajax({
        url: baseUrl + "vehicleManagement/getVendorDetails/", // Your actual endpoint
        type: "GET",
        success: function (vendors) {
          const vendorList = getElement("vendorList");

          // Clear previous vendors (preserve search bar and add new button)
          const searchBar = getElement("vendorSearchBar");
          const addNewVendorSection = getElement("loadVendorModal");

          // Remove old vendor <li> items
          vendorList.innerHTML = "";

          // Re-add search bar & "Add New Vendor" button
          vendorList.appendChild(searchBar.parentElement);
          vendorList.appendChild(addNewVendorSection);

          // Append new vendors
          vendors.forEach(function (vendor) {
            const li = document.createElement("li");
            li.textContent = vendor.vendor_name;
            li.setAttribute("data-vendor-id", vendor.id);
            vendorList.insertBefore(li, addNewVendorSection);
          });

          // Show vendor section
          vendorNameGroup.style.display = "block";

          // Reinitialize dropdown since the list changed
          initVendorDropdown();

          // Make vendor selection required
          document
            .getElementById("selectedVendorId")
            .setAttribute("required", "required");
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error: ", status, error);
        },
      });
    } else {
      vendorNameGroup.style.display = "none";
      getElement("selectedVendorId").removeAttribute("required");
    }
  });

// Optional: If the page loads directly with `rental` pre-selected, auto-init
document.addEventListener("DOMContentLoaded", function () {
  const ownershipType = getElement("ownershipType").value;
  if (ownershipType === "rental") {
    getElement("vendorNameGroup").style.display = "block";
    initVendorDropdown();
  }
});

// Function to load content into the modal
function loadContentIntoModal(url) {
  const modalBody = getElement("addVendorModalBody");

  // Show a loading spinner while fetching content
  modalBody.innerHTML = `
          <div class="text-center">
              <div class="spinner-border" role="status">
                  <span class="visually-hidden">Loading...</span>
              </div>
          </div>
      `;

  // Fetch the content from the external URL
  fetch(url)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.text();
    })
    .then((data) => {
      // Insert the fetched content into the modal body
      modalBody.innerHTML = data;

      initializeVendorFormScripts(); // Call the function to bind event listeners

      // Show the modal
      const modal = new bootstrap.Modal(getElement("addVendorModal"));
      modal.show();
    })
    .catch((error) => {
      // Handle errors
      modalBody.innerHTML = `<div class="alert alert-danger">Failed to load content: ${error.message}</div>`;
      console.error("Error loading content:", error);
    });
}

// Example: Trigger the modal with a button
const loadVendorModal = getElement("loadVendorModal");
if (loadVendorModal) {
  loadVendorModal.addEventListener("click", function () {
    const url = baseUrl + "vehicleManagement/show-addVendor"; // Replace with the URL of the page to load
    loadContentIntoModal(url);
  });
}
