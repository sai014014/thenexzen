// DOM Elements
const activityLogsContainer = getElement("activityLogs-container");

function triggerGlobalFilter() {
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

  const activityType = getElement("activityType").value;

  // Construct the query string with URLSearchParams
  const params = new URLSearchParams({
    dateFilterSelected,
    dateFilterSelectedDates: JSON.stringify(dateFilterSelectedDates),
    activityType: activityType,
  });

  fetch(`activityLogManagement/get-activityLogs?${params.toString()}`, {
    method: "GET",
    headers: {
      "Content-Type": "text/html",
    },
  })
    .then((response) => response.text())
    .then((data) => {
      console.log(data);
      activityLogsContainer.innerHTML = data;
      $("#globalRecordsCustomDateFilter").hide();
    })
    .catch((error) => {
      console.error("Error fetching activityLogs:", error);
    });
}

// On document load, trigger the global filter to fetch data
document.addEventListener("DOMContentLoaded", triggerGlobalFilter);

document
  .getElementById("activityType")
  .addEventListener("change", triggerGlobalFilter);
