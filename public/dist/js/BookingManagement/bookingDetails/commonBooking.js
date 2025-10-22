/**
 * Changes the status of a booking.
 * @param {number} bookingId - The ID of the booking to update.
 * @param {string} status - The new status of the booking.
 * @param {function} callback - Optional callback function to execute after status change.
 */
function changeStatus(bookingId, status, callback) {
  const errorDiv = document.createElement("div");
  errorDiv.id = "globalError";

  // Step 1: Use separate class names instead of one with spaces
  errorDiv.classList.add("alert"); // First class

  // Step 2: Select the parent element where the new element will be added
  const parentElement = getElement("bookingTabsContent");

  if (!bookingId) {
    errorDiv.textContent = "Invalid booking details. Please try again.";
    errorDiv.classList.add("alert-danger");
    errorDiv.classList.add("temporary-error-message"); // Second class
    parentElement.insertBefore(errorDiv, parentElement.firstChild);
    return;
  }

  const url = `${baseUrl}bookingManagement/changeStatus-bookingForm`;

  fetch(url, {
    method: "POST",
    body: JSON.stringify({ bookingId, status }),
    headers: { "Content-Type": "application/json" },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        errorDiv.textContent = `Booking status successfully updated to ${BOOKING_STATUS_ARRAY[
          status
        ].toLowerCase()}.`;
        errorDiv.classList.add("alert-success");
        errorDiv.classList.add("temporary-success-message"); // Second class
        parentElement.insertBefore(errorDiv, parentElement.firstChild);
        if (typeof callback === "function") {
          callback(); // Execute callback if provided
        } else {
          window.location.href = `${baseUrl}bookingManagement`;
        }
      } else {
        errorDiv.textContent = "Invalid booking details. Please try again.";
        errorDiv.classList.add("alert-danger");
        errorDiv.classList.add("temporary-error-message"); // Second class
        parentElement.insertBefore(errorDiv, parentElement.firstChild);
      }
    })
    .catch((error) => {
      errorDiv.textContent =
        "An error occurred while updating the booking status.";
      errorDiv.classList.add("alert-danger");
      errorDiv.classList.add("temporary-error-message"); // Second class
      parentElement.insertBefore(errorDiv, parentElement.firstChild);
    });
}
/**
 * Calculates the total duration between start and end times.
 */
function calculateTotalDuration(bookingData) {
  if (bookingData.startDateTime && bookingData.endDateTime) {
    const start = new Date(bookingData.startDateTime);
    const end = new Date(bookingData.endDateTime);
    const diffTime = Math.abs(end - start);

    return {
      days: Math.floor(diffTime / (1000 * 60 * 60 * 24)),
      hours: Math.floor((diffTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
      minutes: Math.floor((diffTime % (1000 * 60 * 60)) / (1000 * 60)),
    };
  }
  return { days: 0, hours: 0, minutes: 0 };
}

function calculateSubTotalAmount(bookingData) {
  const { days, hours, minutes } = calculateTotalDuration(bookingData);
  const totalDays = days + (hours > 0 || minutes > 0 ? 1 : 0);

  let subTotalAmount = totalDays * bookingData.selectedVehicle.rental_price_24h;

  let additionalAmount = parseFloat(bookingData.additionalChargeAmount) || 0;
  if (
    bookingData.additionalChargeType &&
    bookingData.additionalChargeType == 2
  ) {
    additionalAmount = subTotalAmount * (additionalAmount / 100);
  }
  subTotalAmount = subTotalAmount + additionalAmount;

  bookingData.subTotalAmount = numToFloat(subTotalAmount);
  return bookingData.subTotalAmount;
}

const calculateTotalAmount = (bookingData) => {
  const subTotalAmount = calculateSubTotalAmount(bookingData);
  let discountAmount = parseFloat(bookingData.discountAmount) || 0;
  const advanceAmount = parseFloat(bookingData.advanceAmount) || 0;
  const remainingAmount = parseFloat(bookingData.remainingAmount) || 0;

  const summaryGrandTotalAmount = subTotalAmount;

  if (bookingData.discountType && bookingData.discountType == 2) {
    discountAmount = summaryGrandTotalAmount * (discountAmount / 100);
  }
  let totalAmount =
    summaryGrandTotalAmount - advanceAmount - remainingAmount - discountAmount;

  totalAmount = numToFloat(totalAmount);

  bookingData.totalAmount = totalAmount;
  return totalAmount;
};

const elements = {
  licenseView: getElement("licenseView"),
};
// License modal logic
if (elements.licenseView) {
  elements.licenseView.addEventListener("click", () => {
    const frontImageSrc =
      elements.licenseView.getAttribute("data-licenseFront");
    const backImageSrc = elements.licenseView.getAttribute("data-licenseBack");

    const [frontImage, backImage] = document.querySelectorAll(
      "#licenseModal .modal-body img"
    );

    // Hide images initially
    [frontImage, backImage].forEach((image) => (image.style.display = "none"));

    // Show images if available
    if (frontImageSrc) {
      frontImage.src = frontImageSrc;
      frontImage.style.display = "block";
    }
    if (backImageSrc) {
      backImage.src = backImageSrc;
      backImage.style.display = "block";
    }

    // Show the modal
    $("#licenseModal").modal("show");
  });
}
