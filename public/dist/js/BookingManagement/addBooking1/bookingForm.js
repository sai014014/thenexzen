// Define a namespace for BookingManagement
window.BookingManagement = window.BookingManagement || {};

import { validateStep1, attachStep1EventListeners } from "./step1.js";
import { validateStep2, fetchVehicles } from "./step2.js";
import { validateStep3, fetchCustomers } from "./step3.js";
import {
  validateStep4,
  step4PreFunctions,
  addEventListenerStep4,
} from "./step4.js";
import { validateStep5, step5PreFunctions } from "./step5.js";

// Define the default bookingData structure
const defaultBookingData = {
  startDateTime: "",
  endDateTime: "",
  pickUpLocation: "",
  dropOffLocation: "",
  pickupLocationName: "",
  dropLocationName: "",
  selectedVehicle: null,
  customer: {
    customerId: 0,
    customerType: "individualCustomer",
    customerCorporateDriver: 0,
  },
  subTotalAmount: 0,
  additionalChargeAmount: "0.00",
  additionalChargeType: 1, // 1 for Fixed, 2 for Percentage
  additionalChargeName: "Additional Charges",
  discountAmount: "0.00",
  discountType: 1, // 1 for Fixed, 2 for Percentage
  discountName: "Discount",
  advanceAmount: "0.00",
  totalAmount: 0,
  advancePaymentMethod: "Cash",
  paymentRemarks: "",
  additionalInfo: "",
};
let bookingData = {};

/**
 * Resets the bookingData object to its default values
 * @function
 */
const resetBookingData = () => {
  bookingData = structuredClone(defaultBookingData);
};

document.addEventListener("DOMContentLoaded", function () {
  resetBookingData();

  // Get DOM elements
  const steps = getElements(".step-item");
  const stepContents = getElements(".tab-pane");
  const prevBtn = getElement("prevBtn");
  const nextBtn = getElement("nextBtn");
  const orderSummary = getElement("orderSummary");

  const element = {
    pickupElement: getElement("datesSectionOrderSummary").querySelector(
      ".Pickup"
    ),
    dropElement: getElement("datesSectionOrderSummary").querySelector(".Drop"),
    vehicleSectionOrderSummary: getElement("vehicleSectionOrderSummary"),
    calculatePriceButton: getElement("calculatePrice"),
    editPriceKilometerModal: getElement("editPriceKilometerModal"),
    bookingPaymentSectionSummary: getElement("bookingPaymentSectionSummary"),
    bookingAmountDueSectionSummary: getElement(
      "bookingAmountDueSectionSummary"
    ),
  };

  let currentStep = 0;

  // Function to show a specific step
  function showStep(step) {
    stepContents.forEach((content, index) => {
      toggleDisplay(content, index === step);
    });

    steps.forEach((stepItem, index) => {
      if (stepItem) stepItem.classList.toggle("active", index === step);
    });

    toggleDisplay(prevBtn, step > 0);
    if (nextBtn)
      setTextContent(
        nextBtn,
        step === steps.length - 1 ? "Confirm Booking" : "Next"
      );

    const showSummary = step >= 1 && step < 5;
    toggleDisplay(orderSummary, showSummary);
  }

  function validateStep(step) {
    // Reset visibility of order summary sections
    const summarySections = [
      element.datesSectionOrderSummary,
      element.vehicleSectionOrderSummary,
      element.bookingPaymentSectionSummary,
      element.bookingAmountDueSectionSummary,
    ];
    summarySections.forEach((section) => toggleDisplay(section, false));

    // Validation functions mapped to steps
    const validationFunctions = [
      () => validateStep1WithActions(),
      () => validateStep2WithActions(),
      () => validateStep3WithActions(),
      () => validateStep4WithActions(),
      () => validateStep5WithActions(),
    ];

    // Execute the validation function for the given step if it exists
    return validationFunctions[step]?.() ?? false;

    // Helper function for step 1 validation and actions
    function validateStep1WithActions() {
      if (validateStep1(bookingData)) {
        showSummaryDates();
        fetchVehicles(1, bookingData);
        console.log("validated step 1", bookingData);
        toggleDisplay(element.datesSectionOrderSummary, true);
        return true;
      }
      return false;
    }

    // Helper function for step 2 validation and actions
    function validateStep2WithActions() {
      toggleDisplay(element.datesSectionOrderSummary, true);
      if (validateStep2(bookingData)) {
        // showSummaryVehicles();
        window.BookingManagement.showSummaryVehicles();
        fetchCustomers();
        console.log("validated step 2", bookingData);
        toggleDisplayFlex(element.vehicleSectionOrderSummary, true);
        toggleDisplay(element.bookingPaymentSectionSummary, true);
        toggleDisplay(element.bookingAmountDueSectionSummary, true);
        return true;
      }
      return false;
    }

    // Helper function for step 3 validation and actions
    function validateStep3WithActions() {
      toggleDisplay(element.datesSectionOrderSummary, true);
      toggleDisplayFlex(element.vehicleSectionOrderSummary, true);
      toggleDisplay(element.bookingPaymentSectionSummary, true);
      toggleDisplay(element.bookingAmountDueSectionSummary, true);
      if (validateStep3(bookingData)) {
        console.log("validated step 3", bookingData);
        step4PreFunctions(bookingData);
        addEventListenerStep4(bookingData);
        return true;
      }
      return false;
    }
    // Helper function for step 4 validation and actions
    async function validateStep4WithActions() {
      toggleDisplay(element.datesSectionOrderSummary, true);
      toggleDisplayFlex(element.vehicleSectionOrderSummary, true);
      toggleDisplay(element.bookingPaymentSectionSummary, true);
      toggleDisplay(element.bookingAmountDueSectionSummary, true);

      try {
        const isStep4Valid = await validateStep4(bookingData); // Await the result of validateStep4
        console.log("isStep4Valid", isStep4Valid);
        step5PreFunctions(bookingData);

        if (isStep4Valid) {
          console.log("validated step 4", bookingData);
          return true;
        }

        currentStep = 2; // Redirect to Step 2
        showStep(currentStep);
        return false;
      } catch (error) {
        console.error("Error in validating step 4", error);
        return false;
      }
    }
  }
  function validateStep5WithActions() {
    if (validateStep5(bookingData)) {
      console.log("validated step 5", bookingData);
      return true;
    }
  }

  // Event listeners for navigation buttons
  nextBtn?.addEventListener("click", () => {
    if (validateStep(currentStep)) {
      showStep(++currentStep);
      const topFixedParent = orderSummary?.closest(".Top_Fixed");
      if (currentStep > 1) {
        if (topFixedParent) {
          topFixedParent.style.overflowY = "scroll";
        }
      } else {
        if (topFixedParent) {
          topFixedParent.style.overflowY = "hidden";
        }
      }
    }
  });

  // Handle Previous button click
  prevBtn?.addEventListener("click", () => showStep(--currentStep));

  // Initialize the first step
  showStep(currentStep);

  /**
   * Formats a date string into a readable format.
   */
  function formatDateOnlyOrderSummary(datetime, flag = 1) {
    const date = new Date(datetime);
    if (flag != 1) {
      const day = String(date.getDate()).padStart(2, "0");
      const month = String(date.getMonth() + 1).padStart(2, "0"); // Month is 0-based
      const year = date.getFullYear();
      return `${day}/${month}/${year}`;
    } else {
      return date.toLocaleDateString("en-US", {
        weekday: "long",
        month: "short",
        day: "numeric",
        year: "numeric",
      });
    }
  }

  function formatTimeOnlyOrderSummary(datetime) {
    return new Date(datetime).toLocaleTimeString("en-US", {
      hour: "2-digit",
      minute: "2-digit",
    });
  }

  /* ------------------------ step 1 START ----------------------- */
  attachStep1EventListeners();
  /* ------------------------ step 1 END ----------------------- */

  /* ------------------------ step 2 START ----------------------- */
  // Show the formatted dates in #summaryDates
  function clearAndAppendInfoForDateSummary(
    sectionElement,
    dateTime,
    location
  ) {
    // Keep only the <h4>
    Array.from(sectionElement.children).forEach((child) => {
      if (child.tagName !== "H4") {
        sectionElement.removeChild(child);
      }
    });

    // Create and append new <p> elements
    const dateP = document.createElement("p");
    dateP.textContent = formatDateOnlyOrderSummary(dateTime);

    const timeP = document.createElement("p");
    timeP.textContent = `Time: ${formatTimeOnlyOrderSummary(dateTime)}`;

    const locationP = document.createElement("p");
    locationP.textContent = `Location: ${location}`;

    sectionElement.appendChild(dateP);
    sectionElement.appendChild(timeP);
    sectionElement.appendChild(locationP);
  }

  const showSummaryDates = () => {
    if (bookingData.startDateTime && bookingData.endDateTime) {
      // Call it for pickup and drop sections
      clearAndAppendInfoForDateSummary(
        element.pickupElement,
        bookingData.startDateTime,
        bookingData.pickupLocationName
      );
      setInnerHTML(
        getElement("previewPickupLocation"),
        bookingData.pickupLocationName
      );
      setInnerHTML(
        getElement("previewPickupDate"),
        formatDateOnlyOrderSummary(bookingData.startDateTime, 2)
      );
      setInnerHTML(
        getElement("previewPickupTime"),
        formatTimeOnlyOrderSummary(bookingData.startDateTime)
      );

      clearAndAppendInfoForDateSummary(
        element.dropElement,
        bookingData.endDateTime,
        bookingData.dropLocationName
      );
      setInnerHTML(
        getElement("previewDropLocation"),
        bookingData.dropLocationName
      );
      setInnerHTML(
        getElement("previewDropDate"),
        formatDateOnlyOrderSummary(bookingData.endDateTime, 2)
      );
      setInnerHTML(
        getElement("previewDropTime"),
        formatTimeOnlyOrderSummary(bookingData.endDateTime)
      );
    }
  };
  // Event: Edit Step 1
  getElements(".EditstepI").forEach((element) => {
    element.addEventListener("click", () => {
      currentStep = 0; // Redirect to Step 1
      showStep(currentStep);
    });
  });
  /* ------------------------ step 2 END ----------------------- */

  /* ------------------------ step 3 START ----------------------- */
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
        hours: Math.floor(
          (diffTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
        ),
        minutes: Math.floor((diffTime % (1000 * 60 * 60)) / (1000 * 60)),
      };
    }
    return { days: 0, hours: 0, minutes: 0 };
  }

  function calculateVehicleRentalAmount(bookingData) {
    const { days, hours, minutes } = calculateTotalDuration(bookingData);
    const totalDays = days + (hours > 0 || minutes > 0 ? 1 : 0);
    const totalAmount =
      totalDays * bookingData.selectedVehicle.rental_price_24h;
    return numToFloat(totalAmount);
  }

  function calculateSubTotalAmount(bookingData) {
    const { days, hours, minutes } = calculateTotalDuration(bookingData);
    const totalDays = days + (hours > 0 || minutes > 0 ? 1 : 0);

    let subTotalAmount =
      totalDays * bookingData.selectedVehicle.rental_price_24h;

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

    const summaryGrandTotalAmount = subTotalAmount;

    if (bookingData.discountType && bookingData.discountType == 2) {
      discountAmount = summaryGrandTotalAmount * (discountAmount / 100);
    }

    let totalAmount = summaryGrandTotalAmount - advanceAmount - discountAmount;
    totalAmount = numToFloat(totalAmount);

    bookingData.totalAmount = totalAmount;
    return totalAmount;
  };

  window.BookingManagement.showSummaryVehicles = () => {
    const selectedVehicle = bookingData.selectedVehicle;
    if (!selectedVehicle) {
      return;
    }
    const { days, hours, minutes } = calculateTotalDuration(bookingData);
    const subTotalAmount = calculateSubTotalAmount(bookingData);
    const totalAmount = calculateTotalAmount(bookingData);
    const vehicleRentalTotalAmount = calculateVehicleRentalAmount(bookingData);

    const vehicleSummarySection = element.vehicleSectionOrderSummary;
    const bookingSummarySection = element.bookingPaymentSectionSummary;
    const bookingAmountDueSummarySection =
      element.bookingAmountDueSectionSummary;
    // Set model name
    setInnerHTML(
      vehicleSummarySection.querySelector("#vehicleModelName"),
      selectedVehicle.model_name || "N/A"
    );

    // Set duration (e.g., 2X Days)
    setInnerHTML(
      vehicleSummarySection.querySelector("#summaryVehicleDuration"),
      `${days}X Day${days > 1 ? "s" : ""}`
    );

    // Set vehicle price per day
    setInnerHTML(
      vehicleSummarySection.querySelector("#summaryVehiclePrice24h"),
      `${CURRENCY_SYMBOL}${selectedVehicle.rental_price_24h}/day`
    );

    // Set total rental amount
    setInnerHTML(
      vehicleSummarySection.querySelector("#summaryVehicleTotalAmount"),
      `${CURRENCY_SYMBOL}${vehicleRentalTotalAmount}`
    );

    // Set additional charge amount
    setInnerHTML(
      vehicleSummarySection.querySelector("#summaryAdditionalChargesAmount"),
      `${bookingData.additionalChargeType == 1 ? CURRENCY_SYMBOL : "%"}${
        bookingData.additionalChargeAmount
      }`
    );
    setInnerHTML(
      vehicleSummarySection.querySelector("#summaryAdditionalChargesName"),
      bookingData.additionalChargeName
    );

    // Set subtotal
    setInnerHTML(
      bookingSummarySection.querySelector("#summarySubTotal"),
      `${CURRENCY_SYMBOL}${numToFloat(subTotalAmount)}`
    );
    // Set discount
    setInnerHTML(
      bookingSummarySection.querySelector("#summaryDiscount"),
      `${bookingData.discountType == 1 ? CURRENCY_SYMBOL : "%"}${numToFloat(
        bookingData.discountAmount
      )}`
    );
    setInnerHTML(
      bookingPaymentSectionSummary.querySelector("#summaryDiscountName"),
      bookingData.discountName
    );
    // Set advance
    setInnerHTML(
      bookingSummarySection.querySelector("#summaryAdvancePayment"),
      `${CURRENCY_SYMBOL}${numToFloat(bookingData.advanceAmount)}`
    );

    // Set amount due
    setInnerHTML(
      bookingAmountDueSummarySection.querySelector("#summaryAmountDue"),
      `${CURRENCY_SYMBOL}${numToFloat(totalAmount)}`
    );
  };
  function calculatePrice() {
    const ratePer24h =
      element.vehicleSectionOrderSummary.querySelector(
        "#summaryVehiclePrice24h"
      ).value ?? bookingData.selectedVehicle.rental_price_24h;

    bookingData.selectedVehicle.rental_price_24h = ratePer24h;
    // showSummaryVehicles();
    window.BookingManagement.showSummaryVehicles();
  }
  if (getElement("editStep2")) {
    getElement("editStep2").addEventListener("click", () => {
      currentStep = 1; // Redirect to Step 2
      showStep(currentStep);
    });
  }
  /* ------------------------ step 3 END ----------------------- */
  /* ------------------------ step 4 START ----------------------- */

  /* ------------------------ step 4 END ----------------------- */
});
