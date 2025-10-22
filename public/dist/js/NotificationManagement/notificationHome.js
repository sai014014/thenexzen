// DOM Elements
const notificationsContainer = getElement("notifications-container");
const notifyType = document.querySelectorAll(".notifyType");
const filterBtn = getElement("filter-btn");
const filterDropdownElement = getElement("filter-dropdown"); // Get the filter dropdown
let filterSelectedValue = 0;
let notifyTypeValue = "all";
// Array to store snooze data

const snoozeFilterBtn = getElement("snooze-filter-btn");
const snoozeFilterDropdownElement = getElement("snooze-filter-dropdown"); // Get the filter dropdown


// Tab functionality
notifyType.forEach((tab) => {
  tab.addEventListener("click", () => {
    notifyType.forEach((t) => t.classList.remove("active"));
    tab.classList.add("active");
    notifyTypeValue = tab.getAttribute("data-tab");
    triggerGlobalFilter();
  });
});

// Filter dropdown toggle functionality
filterBtn.addEventListener("click", showHideFilterDropDown);
snoozeFilterBtn.addEventListener("click", showHideSnoozeFilterDropDown);

function showHideFilterDropDown() {
  filterDropdownElement.style.display =
    filterDropdownElement.style.display === "block" ? "none" : "block";
}
function showHideSnoozeFilterDropDown() {
  snoozeFilterDropdownElement.style.display =
    snoozeFilterDropdownElement.style.display === "block" ? "none" : "block";
}

// Close the dropdown when an option is selected
if (filterDropdownElement) {
  filterDropdownElement.addEventListener("click", function (event) {
    const selectedOption = event.target.closest(".filter-option"); // Get the clicked menu item

    if (selectedOption) {
      // Remove 'active' class from all options
      filterDropdownElement
        .querySelectorAll(".filter-option")
        .forEach((option) => option.classList.remove("active"));

      // Add 'active' class to the selected option
      selectedOption.classList.add("active");

      // Update the filterSelectedValue with the selected option's data-value
      filterSelectedValue = selectedOption.getAttribute("data-value");

      showHideFilterDropDown();
      triggerGlobalFilter();
    }
  });
}
if (snoozeFilterDropdownElement) {
  snoozeFilterDropdownElement.addEventListener("click", function (event) {
    const selectedOption = event.target.closest(".filter-option"); // Get the clicked menu item

    if (selectedOption) {
      // Remove 'active' class from all options
      snoozeFilterDropdownElement
        .querySelectorAll(".filter-option")
        .forEach((option) => option.classList.remove("active"));

      // Add 'active' class to the selected option
      selectedOption.classList.add("active");

      // Update the filterSelectedValue with the selected option's data-value
      snoozeTimeValue = selectedOption.getAttribute("data-value");

      showHideSnoozeFilterDropDown();
    }
  });
}

// Optional: Close the dropdown when clicking outside
document.addEventListener("click", function (event) {
  if (
    !filterBtn.contains(event.target) &&
    !filterDropdownElement.contains(event.target)
  ) {
    filterDropdownElement.style.display = "none";
  }
  if (
    !snoozeFilterBtn.contains(event.target) &&
    !snoozeFilterDropdownElement.contains(event.target)
  ) {
    snoozeFilterDropdownElement.style.display = "none";
  }
});

function triggerGlobalFilter() {
  // Retrieve selected filter values
  const {
    globalSelectedFilter,
    globalStartDateSelected,
    globalEndDateSelected,
  } = getGlobalDateFilterValues();
  const dateFilterSelected = globalSelectedFilter;
  const dateFilterSelectedDates = {
    startDate: globalStartDateSelected,
    endDate: globalEndDateSelected,
  };

  // Construct the query string with URLSearchParams
  const params = new URLSearchParams({
    dateFilterSelected,
    dateFilterSelectedDates: JSON.stringify(dateFilterSelectedDates),
    filterSelectedValue,
    activeTabValue: notifyTypeValue,
  });

  fetch(`notificationManagement/get-notifications?${params.toString()}`, {
    method: "GET",
    headers: {
      "Content-Type": "text/html",
    },
  })
    .then((response) => response.text())
    .then((data) => {
      notificationsContainer.innerHTML = data;
      $("#globalRecordsCustomDateFilter").hide();
    })
    .catch((error) => {
      console.error("Error fetching notifications:", error);
    });
}

// Initialize the global filter trigger (Optional)
document.addEventListener("DOMContentLoaded", () => {
  triggerGlobalFilter();
});
