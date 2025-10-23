function previewDocument(fileUrl, fileType, title) {
  const modal = new bootstrap.Modal(getElement("documentPreviewModal"));
  const container = getElement("documentPreviewContainer");
  const modalTitle = getElement("documentPreviewModalLabel");
  const downloadBtn = getElement("downloadDocument");

  // Set modal title
  modalTitle.textContent = title;

  // Set download link
  downloadBtn.href = fileUrl;

  // Show loading spinner
  container.innerHTML = '<div class="loading-spinner"></div>';

  // Show modal
  modal.show();

  if (fileType === "image") {
    const img = new Image();
    img.onload = function () {
      container.innerHTML = "";
      container.appendChild(img);
    };
    img.onerror = function () {
      container.innerHTML =
        '<div class="alert alert-danger">Error loading image</div>';
    };
    img.src = fileUrl;
    img.style.maxWidth = "100%";
    img.style.height = "auto";
  } else if (fileType === "pdf") {
    // For PDF files, use an iframe with PDF viewer
    const iframe = document.createElement("iframe");
    iframe.src = fileUrl;
    container.innerHTML = "";
    container.appendChild(iframe);
  }
}

// Add event listener to close modal when escape key is pressed
document.addEventListener("keydown", function (event) {
  if (event.key === "Escape") {
    const modal = bootstrap.Modal.getInstance(
      getElement("documentPreviewModal")
    );
    if (modal) {
      modal.hide();
    }
  }
});

// Clear preview when modal is hidden
document
  .getElementById("documentPreviewModal")
  .addEventListener("hidden.bs.modal", function () {
    getElement("documentPreviewContainer").innerHTML = "";
  });

// Add this to your JavaScript file
document.addEventListener("DOMContentLoaded", function () {
  const carouselElement = getElement("vehicleImageCarousel");
  if (carouselElement) {
    // Initialize the carousel
    const carousel = new bootstrap.Carousel(carouselElement, {
      interval: 5000, // 5 seconds between slides
      wrap: true,
    });

    // Update active thumbnail when carousel slides
    carouselElement.addEventListener("slide.bs.carousel", function (event) {
      updateActiveThumbnail(event.to);
    });
  }

  // Initialize the first thumbnail as active
  updateActiveThumbnail(0);
});

// Function to switch carousel image when thumbnail is clicked
function switchCarouselImage(index) {
  const carousel = bootstrap.Carousel.getInstance(
    getElement("vehicleImageCarousel")
  );
  carousel.to(index);
  updateActiveThumbnail(index);
}

// Function to update active thumbnail
function updateActiveThumbnail(activeIndex) {
  // Remove active class from all thumbnails
  document.querySelectorAll(".thumbnail-item").forEach((thumb) => {
    thumb.classList.remove("active");
  });

  // Add active class to current thumbnail
  const activeThumbnail = document.querySelector(
    `.thumbnail-item[data-index="${activeIndex}"]`
  );
  if (activeThumbnail) {
    activeThumbnail.classList.add("active");
  }
}

// Optional: Add keyboard navigation
document.addEventListener("keydown", function (event) {
  const carousel = bootstrap.Carousel.getInstance(
    getElement("vehicleImageCarousel")
  );

  if (event.key === "ArrowLeft") {
    carousel.prev();
  } else if (event.key === "ArrowRight") {
    carousel.next();
  }
});

// vehicle status
document.addEventListener("DOMContentLoaded", function () {
  const deleteVehicleRecord = getElement("deleteVehicleRecord");
  deleteVehicleRecord.addEventListener("click", function () {
    Swal.fire({
      title: "🚗 Delete Vehicle?",
      html: "<b style='color: red;'>This action cannot be undone.</b><br>Are you sure you want to <b>delete this vehicle</b>?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#ff4d4d",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "🗑️ Yes, Delete!",
      cancelButtonText: "❌ Cancel",
    }).then((result) => {
      if (result.isConfirmed) {
        const data = {
          vehicleId: vehicleRecordId,
          status: 2,
        };
        updateVehicleStatus(data, 1);
      }
    });
  });

  const bookButton = getElement("bookButton");
  bookButton.addEventListener("click", function () {
    window.location.href = baseUrl + "bookingManagement/load-bookingForm";
  });

  // Function to send AJAX request to update vehicle status
  function updateVehicleStatus(data, redirectHomePage = 0) {
    // Convert the data object to a URLSearchParams string for form submission
    const formData = new FormData();
    formData.append("vehicleId", data.vehicleId);
    formData.append("status", data.status);
    formData.append("startDate", data.start_date ?? null);
    formData.append("endDate", data.end_date ?? null);

    fetch(baseUrl + "vehicleManagement/changeStatus-vehicleForm", {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
      body: formData, // Send the form data as a URLSearchParams string
    })
      .then((response) => response.json())
      .then((responseData) => {
        if (responseData.status == "success") {
          showToast({
            title: "Vehicle",
            message: "Vehicle status updated successfully!",
            type: "success",
          });
          if (redirectHomePage == 1) {
            // Redirect to home page on success
            window.location.href = baseUrl + "vendorManagement"; // Change to your actual home URL
          }
          //   location.reload();
        } else {
          showToast({
            title: "Vehicle",
            message: "Failed to update vehicle status. Please try again.",
            type: "error",
          });
        }
      })
      .catch((error) => {
        showToast({
          title: "Vehicle",
          message: "An error occurred while updating vehicle status.",
          type: "error",
        });
        console.error("Error:", error);
      });
  }

  /* ------------------------------- inactive vehicle until i turn off ---------------------------- */
  // ====================
  // Time Picker Functionality
  // ====================
  const timeInputs = document.querySelectorAll(".time-input");
  const dropdowns = document.querySelectorAll(".time-dropdown");

  // Generate time options dynamically (1:00 AM to 12:30 PM/AM with 30-minute intervals)
  function generateTimeOptions() {
    const times = [];
    for (let hour = 1; hour <= 12; hour++) {
      times.push(`${hour}:00 AM`, `${hour}:30 AM`);
    }
    for (let hour = 1; hour <= 12; hour++) {
      times.push(`${hour}:00 PM`, `${hour}:30 PM`);
    }
    return times;
  }

  // Render time options in the dropdown
  function renderTimeOptions(dropdown) {
    const times = generateTimeOptions();
    dropdown.innerHTML = times
      .map(
        (time) => `
                <div class="time-option" data-time="${time}">
                    ${time}
                </div>
            `
      )
      .join("");
  }

  // Initialize time pickers
  dropdowns.forEach((dropdown) => {
    renderTimeOptions(dropdown);

    // Handle time selection
    dropdown.querySelectorAll(".time-option").forEach((option) => {
      option.addEventListener("click", () => {
        const time = option.getAttribute("data-time");
        const correspondingInput =
          dropdown.previousElementSibling.querySelector("input");
        correspondingInput.value = time;

        dropdown.style.display = "none"; // Close dropdown
      });
    });
  });

  // Toggle dropdown visibility for each time picker
  timeInputs.forEach((timeInput, index) => {
    timeInput.addEventListener("click", (e) => {
      e.stopPropagation(); // Prevent event from bubbling up
      dropdowns[index].style.display =
        dropdowns[index].style.display === "block" ? "none" : "block";
    });
  });

  // Close dropdown when clicking outside
  document.addEventListener("click", () => {
    dropdowns.forEach((dropdown) => {
      dropdown.style.display = "none";
    });
  });

  // ====================
  // Date Picker Functionality
  // ====================
  const inputField = document.getElementById("calendar-input");
  const calendarContainer = document.getElementById("calendar-container");
  const prevMonthBtn = document.getElementById("prev-month");
  const nextMonthBtn = document.getElementById("next-month");
  const monthYear = document.getElementById("month-year");
  const calendarDates = document.querySelector(".calendar-dates");
  const weekdaysContainer = document.querySelector(".calendar-weekdays");
  const startDateInput = document.getElementById("start-date");
  const endDateInput = document.getElementById("end-date");
  const applyBtn = document.getElementById("apply-btn");
  const cancelBtn = document.getElementById("cancel-btn");
  const manualReactivationCheckbox = document.getElementById(
    "manualReactivationCheckbox"
  );
  const globalApplyBtn = document.getElementById("global-apply-btn");
  const errorContainer = document.createElement("div"); // Container for error messages
  errorContainer.className = "error-messages";

  // Insert the error container above the globalApplyBtn
  globalApplyBtn.parentNode.insertBefore(errorContainer, globalApplyBtn);

  const weekdays = ["S", "M", "T", "W", "T", "F", "S"];
  let currentDate = new Date();
  let startDate = null;
  let endDate = null;

  // Initialize Calendar
  weekdaysContainer.innerHTML = weekdays
    .map((day) => `<div>${day}</div>`)
    .join("");

  // Show calendar when input field is clicked
  inputField.addEventListener("click", () => {
    calendarContainer.style.display = "block";
    renderCalendar();
  });

  // Hide calendar when cancel button is clicked
  cancelBtn.addEventListener("click", () => {
    clearDates();
  });

  function clearDates() {
    calendarContainer.style.display = "none";
    startDateInput.value = "";
    endDateInput.value = "";
    startDate = null;
    endDate = null;
    renderCalendar();
  }

  // Apply selected date range (for calendar apply button)
  applyBtn.addEventListener("click", () => {
    if (startDate && endDate) {
      inputField.value = `${formatDateDisplay(startDate)} - ${formatDateDisplay(
        endDate
      )}`;
    } else if (startDate) {
      inputField.value = formatDateDisplay(startDate);
    } else {
      inputField.value = "";
    }
    calendarContainer.style.display = "none";
  });

  // Handle "Until I Switch it on" checkbox
  manualReactivationCheckbox.addEventListener("change", () => {
    if (manualReactivationCheckbox.checked) {
      // Update end date input
      endDateInput.value = "Until I turn off";
      endDateInput.readOnly = true;

      // Update calendar input
      inputField.value = `${formatDateDisplay(startDate)} - Until I turn off`;

      // Disable end time input
      const endTimeInput = document.querySelector(".endtime input");
      endTimeInput.value = "";
      endTimeInput.readOnly = true;
    } else {
      // Enable end date input
      endDateInput.value = "";
      endDateInput.readOnly = false;

      // Update calendar input
      if (startDate) {
        inputField.value = formatDateDisplay(startDate);
      } else {
        inputField.value = "";
      }

      // Enable end time input
      const endTimeInput = document.querySelector(".endtime input");
      endTimeInput.readOnly = false;
    }
  });

  // Global Apply Button
  globalApplyBtn.addEventListener("click", () => {
    // Clear previous errors
    errorContainer.innerHTML = "";

    // Validate From Date and Start Time
    if (!startDateInput.value) {
      showError("From Date is required.");
      return;
    }
    const startTime12h = document.querySelector(".starttime input").value;
    if (!startTime12h) {
      showError("Start Time is required.");
      return;
    }

    // Initialize endTime as null
    let endTime12h = null;

    // Validate To Date and End Time if checkbox is unchecked
    if (!manualReactivationCheckbox.checked) {
      if (!endDateInput.value) {
        showError("To Date is required.");
        return;
      }
      endTime12h = document.querySelector(".endtime input").value;
      if (!endTime12h) {
        showError("End Time is required.");
        return;
      }
    }

    // If validation passes, save the data
    const fromDate = startDateInput.value
      ? formatDateSave(startDateInput.value)
      : null;
    const toDate = endDateInput.value
      ? formatDateSave(endDateInput.value)
      : null;
    const toggleStatus = manualReactivationCheckbox.checked;

    // Convert 12-hour time to 24-hour format
    const startTime24h = convertTo24HourFormat(startTime12h);
    const endTime24h = convertTo24HourFormat(endTime12h);

    // Combine date and time for From Date
    const fromDateTime =
      fromDate && startTime24h ? `${fromDate} ${startTime24h}` : null;

    // Combine date and time for To Date
    const toDateTime = toDate && endTime24h ? `${toDate} ${endTime24h}` : null;

    const data = {
      vehicleId: vehicleRecordId,
      status: 0,
      start_date: fromDateTime,
      end_date: toggleStatus ? null : toDateTime,
    };

    console.log(data);
    updateVehicleStatus(data);
  });

  // Navigate to previous month
  prevMonthBtn.addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
  });

  // Navigate to next month
  nextMonthBtn.addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
  });

  // Render the calendar
  function renderCalendar() {
    calendarDates.innerHTML = "";
    const firstDayIndex = new Date(
      currentDate.getFullYear(),
      currentDate.getMonth(),
      1
    ).getDay();
    const lastDay = new Date(
      currentDate.getFullYear(),
      currentDate.getMonth() + 1,
      0
    ).getDate();

    // Update month and year display
    monthYear.textContent = currentDate.toLocaleString("default", {
      month: "long",
      year: "numeric",
    });

    // Fill in empty days for the first week
    for (let i = 0; i < firstDayIndex; i++) {
      calendarDates.innerHTML += `<div></div>`;
    }

    // Fill in the days of the month
    for (let i = 1; i <= lastDay; i++) {
      const day = document.createElement("div");
      day.textContent = i;
      day.classList.add("calendar-day");

      const dateObj = new Date(
        currentDate.getFullYear(),
        currentDate.getMonth(),
        i
      );
      const formattedDate = formatDate(dateObj);

      // Highlight selected dates and range
      if (startDate && formattedDate === startDate) {
        day.classList.add("selected");
      } else if (endDate && formattedDate === endDate) {
        day.classList.add("selected");
      } else if (
        startDate &&
        endDate &&
        dateObj > new Date(startDate) &&
        dateObj < new Date(endDate)
      ) {
        day.classList.add("range");
      }

      // Handle date selection
      day.addEventListener("click", () => handleDateClick(formattedDate));
      calendarDates.appendChild(day);
    }
  }

  // Handle date selection logic
  function handleDateClick(date) {
    if (!startDate || (startDate && endDate)) {
      startDate = date;
      endDate = null;
    } else if (new Date(date) >= new Date(startDate)) {
      endDate = date;
    } else {
      startDate = date;
    }

    // Update start and end date inputs
    startDateInput.value = formatDateDisplay(startDate) || "";
    endDateInput.value = formatDateDisplay(endDate) || "";
    renderCalendar();
  }

  // Format date as YYYY-MM-DD for saving
  function formatDateSave(date) {
    const [day, month, year] = date.split("-");
    return `${year}-${month}-${day}`;
  }

  // Format date as DD-MM-YYYY for display
  function formatDateDisplay(date) {
    if (!date) return "";
    const [year, month, day] = date.split("-");
    return `${day}-${month}-${year}`;
  }

  // Format date as YYYY-MM-DD for internal use
  function formatDate(date) {
    return date.toISOString().split("T")[0];
  }

  // Show error message
  function showError(message) {
    const errorMessage = document.createElement("div");
    errorMessage.className = "error-message";
    errorMessage.textContent = message;
    errorContainer.appendChild(errorMessage);

    // Remove error message after 3 seconds
    setTimeout(() => {
      errorMessage.remove();
    }, 3000);
  }

  // Initial render
  renderCalendar();

  function convertTo24HourFormat(time12h) {
    if (!time12h) return "";

    // Split time and AM/PM
    const [time, modifier] = time12h.split(" ");
    let [hours, minutes] = time.split(":");

    // Convert to 24-hour format
    if (modifier === "PM" && hours !== "12") {
      hours = parseInt(hours, 10) + 12;
    }
    if (modifier === "AM" && hours === "12") {
      hours = "00";
    }

    return `${hours}:${minutes}:00`; // Add seconds for H:i:s format
  }
  const vehicleStatusDropdown = getElement("vehicleStatusDropdown");
  const datepickerSection = getElement("inactiveDatepicker");
  // Handle dropdown change event
  vehicleStatusDropdown.addEventListener("change", function () {
    // Inactive selected: Show datepicker section
    datepickerSection.style.display = "none";
    if (this.value === "1") {
      // Clear any previous errors
      clearDates();
      // Active selected: Trigger AJAX directly
      const data = {
        vehicleId: vehicleRecordId,
        status: 1,
      };
      updateVehicleStatus(data);
    } else if (this.value === "0") {
      // Inactive selected: Show datepicker section
      // datepickerSection.style.display = "block"; //currently holding this as it need to be discussed further
      const data = {
        vehicleId: vehicleRecordId,
        status: 0,
        startDate: getCurrentDateTime(),
        endDate: null,
      };
      updateVehicleStatus(data);
    }
  });
});
// Add this code where you initialize your date picker functionality
// Right after you define your constants/variables

// Function to populate inputs from existing dates
function populateInputsFromDates() {
  if (startDate) {
    // Parse the date and time from the Y-m-d H:i:s format
    const [datePart, timePart] = startDate.split(" ");
    const [year, month, day] = datePart.split("-");
    const [hours, minutes] = timePart.split(":");

    // Format for display (DD-MM-YYYY)
    const displayDate = `${day}-${month}-${year}`;
    startDateInput.value = displayDate;

    // Convert 24-hour time to 12-hour format for the time input
    const startTime12h = convertTo12HourFormat(timePart);
    document.querySelector(".starttime input").value = startTime12h;

    if (endDate) {
      // Parse end date
      const [endDatePart, endTimePart] = endDate.split(" ");
      const [endYear, endMonth, endDay] = endDatePart.split("-");
      const [endHours, endMinutes] = endTimePart.split(":");

      // Format for display (DD-MM-YYYY)
      const endDisplayDate = `${endDay}-${endMonth}-${endYear}`;
      endDateInput.value = endDisplayDate;

      // Convert 24-hour time to 12-hour format for the time input
      const endTime12h = convertTo12HourFormat(endTimePart);
      document.querySelector(".endtime input").value = endTime12h;

      // Set calendar input value
      inputField.value = `${displayDate} - ${endDisplayDate}`;

      // Set the dates in your calendar picker
      startDate = formatDate(new Date(`${year}-${month}-${day}`));
      endDate = formatDate(new Date(`${endYear}-${endMonth}-${endDay}`));

      // Uncheck the manual reactivation checkbox
      manualReactivationCheckbox.checked = false;
      endDateInput.readOnly = false;
      document.querySelector(".endtime input").readOnly = false;
    } else {
      // No end date means "Until I turn off" is checked
      manualReactivationCheckbox.checked = true;
      endDateInput.value = "Until I turn off";
      endDateInput.readOnly = true;
      document.querySelector(".endtime input").value = "";
      document.querySelector(".endtime input").readOnly = true;
      inputField.value = `${displayDate} - Until I turn off`;

      // Set only start date in calendar picker
      startDate = formatDate(new Date(`${year}-${month}-${day}`));
      endDate = null;
    }

    // Render calendar with the dates
    renderCalendar();
  }
}

// Helper function to convert 24-hour time to 12-hour format
function convertTo12HourFormat(time24h) {
  if (!time24h) return "";

  const [hours, minutes] = time24h.split(":");
  const hourNum = parseInt(hours, 10);

  let period = "AM";
  let displayHour = hourNum;

  if (hourNum >= 12) {
    period = "PM";
    displayHour = hourNum > 12 ? hourNum - 12 : hourNum;
  }

  if (hourNum === 0) {
    displayHour = 12;
  }

  return `${displayHour}:${minutes} ${period}`;
}
function getCurrentDateTime() {
  const now = new Date();

  // Get date components
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, "0"); // Months are 0-based
  const day = String(now.getDate()).padStart(2, "0");

  // Get time components
  const hours = String(now.getHours()).padStart(2, "0");
  const minutes = String(now.getMinutes()).padStart(2, "0");
  const seconds = String(now.getSeconds()).padStart(2, "0");

  // Combine into Y-m-d H:i:s format
  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}
