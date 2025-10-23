document.addEventListener("DOMContentLoaded", function () {
  // Elements references
  const elements = {
    pickupTimeElement: getElement("pickupTime"),
    dropTimeElement: getElement("dropTime"),
    pickupPlaceElement: getElement("pickupPlace"),
    dropPlaceElement: getElement("dropPlace"),
    discountInputElement: getElement("discountInput"),
    rentalFeeElement: getElement("rentalFee"),
    amountToPayElement: getElement("amountToPay"),
    additionalInputElement: getElement("additionalCharges"),
    aCDescription: getElement("aCDescription"),
    pickupMeterElement: getElement("pickupMeter"),
    dropMeterElement: getElement("dropMeter"),
    pickupImageElement: getElement("pickupImage"),
    dropImageElement: getElement("dropImage"),
    remarksElement: getElement("remarks"),
    paymentMethodElement: getElement("paymentMethod"),
    otherPaymentInfoMainDivElement: getElement("otherPaymentInfoMainDiv"),
    paymentNotesElement: getElement("otherPaymentInfo"),
    startBookingBtnElement: getElement("startBookingBtn"),
    cancelBookingBtnElement: getElement("cancelBookingBtn"),
    dCurrencyButtonElement: getElement("dCurrencyButton"),
    dPercentButtonElement: getElement("dPercentButton"),
    aCCurrencyButtonElement: getElement("aCCurrencyButton"),
    aCPercentButtonElement: getElement("aCPercentButton"),
  };

  // Event listeners for time changes
  [elements.pickupTimeElement, elements.dropTimeElement].forEach((element) => {
    element.addEventListener("change", pickUpDropOffTime);
    // Trigger the change event manually
  });

  // === Button toggle logic ===
  const setupToggleButton = (button1, button2, targetType = 1, value) => {
    button1?.addEventListener("click", () => {
      if (targetType == 2) {
        bookingData.discountType = value;
      } else {
        bookingData.additionalChargeType = value;
      }

      button1.setAttribute("data-active", "true");
      button2?.removeAttribute("data-active");
      assignBookingPrice(bookingData);
    });
  };

  // Additional Charge Type (1: ₹, 2: %)
  setupToggleButton(
    elements.aCCurrencyButtonElement,
    elements.aCPercentButtonElement,
    1,
    1
  );
  setupToggleButton(
    elements.aCPercentButtonElement,
    elements.aCCurrencyButtonElement,
    1,
    2
  );

  // Discount Type (1: ₹, 2: %)
  setupToggleButton(
    elements.dCurrencyButtonElement,
    elements.dPercentButtonElement,
    2,
    1
  );
  setupToggleButton(
    elements.dPercentButtonElement,
    elements.dCurrencyButtonElement,
    2,
    2
  );

  elements.paymentMethodElement?.addEventListener("change", () => {
    bookingData.paymentMethod = elements.paymentMethodElement.value.trim();

    toggleDisplay(
      elements.otherPaymentInfoMainDivElement,
      elements.paymentMethodElement.value != "Cash"
    );
  });

  elements.paymentNotesElement?.addEventListener("input", () => {
    bookingData.paymentRemarks = elements.paymentNotesElement.value.trim();
  });
  elements.discountInputElement?.addEventListener("input", () => {
    bookingData.discountAmount = elements.discountInputElement.value;
    assignBookingPrice(bookingData);
  });
  elements.discountInputElement?.addEventListener("focusout", () => {
    if (elements.discountInputElement.value === "") {
      elements.discountInputElement.value = 0;
      bookingData.discountAmount = elements.discountInputElement.value;
      assignBookingPrice(bookingData);
    }
  });

  elements.additionalInputElement?.addEventListener("input", () => {
    bookingData.additionalChargeAmount = elements.additionalInputElement.value;
    assignBookingPrice(bookingData);
  });
  elements.additionalInputElement?.addEventListener("focusout", () => {
    if (elements.additionalInputElement.value === "") {
      elements.additionalInputElement.value = 0;
      bookingData.additionalChargeAmount =
        elements.additionalInputElement.value;
      assignBookingPrice(bookingData);
    }
  });

  elements.aCDescription?.addEventListener("input", () => {
    const additionalChargeName = elements.aCDescription.value;
    bookingData.additionalChargeName = additionalChargeName;
    assignBookingPrice(bookingData);
  });

  // Function to handle pickup and drop-off time logic
  async function pickUpDropOffTime() {
    const { pickupTimeElement, dropTimeElement } = elements;

    if (pickupTimeElement.value && dropTimeElement.value) {
      bookingData.startDateTime = pickupTimeElement.value;
      bookingData.endDateTime = dropTimeElement.value;
      if (checkVehicleAvailability(bookingData)) {
        assignBookingPrice(bookingData);
      }
    }
  }

  function assignBookingPrice(bookingData) {
    bookingData.subTotalAmount = calculateSubTotalAmount(bookingData);

    setTextContent(
      elements.rentalFeeElement,
      convertCurrencyFormat(bookingData.subTotalAmount, 1)
    );
    bookingData.totalAmount = calculateTotalAmount(bookingData);
    setInnerHTML(
      elements.amountToPayElement,
      CURRENCY_SYMBOL + bookingData.totalAmount
    );

    calculateToFloat(bookingData);
  }

  function prepareBookingData() {
    bookingData.startDateTime = elements.pickupTimeElement.value;
    bookingData.endDateTime = elements.dropTimeElement.value;
    bookingData.pickUpLocation = elements.pickupPlaceElement.value;
    bookingData.dropOffLocation = elements.dropPlaceElement.value;
    bookingData.pickupMeterReading = elements.pickupMeterElement.value;
    bookingData.dropMeterReading = elements.dropMeterElement.value;
    bookingData.remarks = elements.remarksElement.value;
    bookingData.paymentMethod = elements.paymentMethodElement.value;
  }

  function calculateToFloat(bookingData) {
    // Define the properties you want to convert to float
    let propertiesToConvert = [
      "subTotalAmount",
      "discountAmount",
      "advanceAmount",
      "additionalChargeAmount",
      "totalAmount",
    ];

    // Convert specific properties to float with 2 decimals
    propertiesToConvert.forEach((property) => {
      if (
        bookingData[property] !== undefined &&
        typeof bookingData[property] === "number"
      ) {
        // Round to two decimals
        bookingData[property] = Math.round(bookingData[property] * 100) / 100;
      } else if (
        bookingData[property] !== undefined &&
        typeof bookingData[property] === "string" &&
        !isNaN(bookingData[property]) // Check if string is a valid number
      ) {
        // Convert string values to float and round to two decimals
        bookingData[property] =
          Math.round(parseFloat(bookingData[property]) * 100) / 100;
      }
    });
  }

  // Function to check vehicle availability
  function checkVehicleAvailability(bookingData) {
    return new Promise((resolve, reject) => {
      $.ajax({
        url: `${baseUrl}bookingManagement/checkVehicleAvailabilityForBooking`,
        type: "POST",
        data: { bookingData: JSON.stringify(bookingData) },
        success: (response) => {
          if (response.vehicleAvailability) {
            resolve(true); // Vehicle available
          } else {
            showToast({
              title: "Booking",
              message: "Sorry, this vehicle has been booked for those dates.",
              type: "error",
            });
            bookingData.startDateTime = bookingData.startDateTime_old;
            bookingData.endDateTime = bookingData.endDateTime_old;

            // Format and assign to input fields
            getElement("pickupTime").value = bookingData.startDateTime;
            getElement("dropTime").value = bookingData.endDateTime;
            resolve(false); // Vehicle not available
          }
        },
        error: (xhr, status, error) => {
          console.error("Error checking vehicle availability:", error);
          alert("An error occurred while checking vehicle availability.");
          reject(error); // Error handling
        },
      });
    });
  }
  assignBookingPrice(bookingData);

  elements.startBookingBtnElement?.addEventListener("click", () => {
    bookingData.status = elements.startBookingBtnElement.value;
    if (bookingData.status == 4) {
      const newStart = new Date(bookingData.startDateTime);
      const today = new Date();

      // Check if today is before the original scheduled start time
      const isBefore = today < newStart;

      if (isBefore) {
        Swal.fire({
          title: "Booking will start now, are you sure?",
          html: "<b style='color: red;'>This action cannot be undone!</b><br>Are you sure you want to <b>start this booking now</b>?",
          icon: "warning",
          showCancelButton: true,
          cancelButtonColor: "#d33",
          confirmButtonColor: "#5d5fef",
          confirmButtonText: "Yes, start!",
          cancelButtonText: "No, Keep It",
        }).then(async (result) => {
          if (result.isConfirmed) {
            // 🕒 Get current datetime in 'YYYY-MM-DDTHH:mm' format
            const now = new Date();
            const formattedNow = now.toISOString().slice(0, 16); // Removes seconds and Z

            // ✅ Update bookingData and input element
            bookingData.startDateTime = formattedNow;
            elements.pickupTimeElement.value = formattedNow;

            const isAvailable = await checkVehicleAvailability(bookingData);
            if (isAvailable) {
              assignBookingPrice(bookingData);
              showToast({
                title: "Booking",
                message:
                  "Billing details have been updated. Click again to start booking.",
                type: "warning",
              });
            }
          }
        });
      } else {
        updateBookingFormData();
      }
    } else {
      updateBookingFormData();
    }
  });

  elements.cancelBookingBtnElement?.addEventListener("click", () => {
    Swal.fire({
      title: "❌ Cancel Booking?",
      html: "<b style='color: red;'>This action cannot be undone!</b><br>Are you sure you want to <b>cancel this booking</b>?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "🚫 Yes, Cancel!",
      cancelButtonText: "❌ No, Keep It",
    }).then((result) => {
      if (result.isConfirmed) {
        changeStatus(bookingData.bookingId, 0, "cancel");
      }
    });
  });

  function updateBookingFormData() {
    prepareBookingData();
    const pickupImage = elements.pickupImageElement.files[0]; // Get the first file
    const dropOffImage = elements.dropImageElement.files[0]; // Get the first file

    // Create a FormData object
    const formData = new FormData();

    // Append the JSON object data as a string (you can use JSON.stringify)
    formData.append("bookingData", JSON.stringify(bookingData));

    // Append the file
    if (pickupImage) {
      formData.append("pickupImage", pickupImage);
    }
    // Append the file
    if (dropOffImage) {
      formData.append("dropOffImage", dropOffImage);
    }

    // Send the FormData via AJAX
    $.ajax({
      url: `${baseUrl}bookingManagement/update-bookingFormData`, // Replace with your backend URL
      type: "POST",
      data: formData,
      processData: false, // Prevent jQuery from processing the data
      contentType: false, // Let the browser set the correct content type
      success: function (response) {
        if (response.success) {
          showToast({
            title: "Booking",
            message: response.message,
            type: "success",
          });
          setTimeout(() => {
            window.location.href = `${baseUrl}bookingManagement`;
          }, 5000);
        } else {
          showToast({
            title: "Booking",
            message: response.message,
            type: "error",
          });
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  }
});
