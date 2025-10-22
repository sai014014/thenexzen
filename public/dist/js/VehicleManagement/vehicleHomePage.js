var page = 1;
var limit = RECORDS_PER_PAGE;

// Function to trigger global filters and fetch necessary data
async function triggerGlobalFilter() {
  try {
    // Retrieve selected filter values
    const {
      globalSelectedFilter,
      globalStartDateSelected,
      globalEndDateSelected,
    } = getGlobalDateFilterValues();
    const selectedDateFilter = globalSelectedFilter;
    const customDateRange = {
      startDate: globalStartDateSelected,
      endDate: globalEndDateSelected,
    };

    const searchQuery = document
      .getElementById("searchBar")
      .value.toLowerCase();
    const vehicleType = getElement("vehicleType").value;
    const status = getElement("status").value;

    // Construct query parameters for data fetching
    const queryParams = new URLSearchParams({
      dateFilterSelected: selectedDateFilter,
      dateFilterSelectedDates: JSON.stringify(customDateRange),
      search: searchQuery,
      vehicleType: vehicleType,
      status: status,
      page: page, // Pass the current page
      limit: limit,
    });

    // Fetch vendors data
    await Promise.all([fetchVendorsList(queryParams)]);

    // Hide the date filter UI after applying filter
    document.querySelector("#globalRecordsCustomDateFilter").style.display =
      "none";
  } catch (error) {
    console.error("Error applying global filters:", error);
  }
}

// Fetch vendor data from the server and update the DOM
async function fetchVendorsList(queryParams) {
  try {
    const response = await fetch(
      `vehicleManagement/getVehicleData?${queryParams.toString()}`,
      {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      }
    );

    if (!response.ok) {
      throw new Error("Failed to fetch vendor data");
    }

    const data = await response.json();
    updateTableData(data);
  } catch (error) {
    console.error("Error fetching vendor data:", error);
  }
}

function updateTableData(response) {
  const table = $("#vehicleTable").DataTable();
  table.clear();

  // Check if data is present in the response
  if (response.data.length > 0) {
    response.data.forEach((vehicle) => {
      const priceInfoIcon = `
          <i class="fas fa-info-circle" 
              title="View Pricing Details"
              onclick="showPriceInfo(${vehicle.vehicle_id}, ${vehicle.rental_price_24h}, ${vehicle.kilometer_limit}, ${vehicle.extra_price_per_hour}, ${vehicle.extra_price_per_km})">
          </i>`;
      table.row.add([
        `<div class="vehicleModal">
          <div class="vehicleLogo">
              <img class="vechile-logo"
                  src="${baseUrl}${vehicle.brand_image}"
                  >
          </div>
          <div class="vehicleInfo">
              <span class="vehicleName">${vehicle.model_name}</span>
              <span class="vehicleNumber">${vehicle.registration_number}</span>
          </div>
      </div>`,
        `${vehicle.vehicle_type} - ${vehicle.transmission_type}`,
        vehicle.fuel_type,
        vehicle.seating_capacity,
        `${priceInfoIcon}`,
        `<span class="vehicleStatusSpan vehicleStatus-${vehicle.status}">${
          VEHICLE_STATUS_ARRAY[vehicle.status]
        }</span>`,
        `<div class="action-icons">
            <span class="VehicleViewAction" onclick="showVehicle(${vehicle.vehicle_id})">View</span>
            <span class="VehicleDeleteAction" onclick="deleteVehicle(${vehicle.vehicle_id})">Delete</span>
  
        </div>`,
      ]);
    });
    // Draw only if there is data
    table.draw();
  } else {
    // Explicitly clear and redraw the table even if no data is returned
    table.draw();
  }
  // Update pagination buttons dynamically
  updatePagination(response.total, response.perPage, response.currentPage);
}

// Function to update pagination buttons
function updatePagination(total, perPage, currentPage) {
  total = parseInt(total);
  perPage = parseInt(perPage);
  const totalPages = Math.ceil(total / perPage);
  const startRecord = (currentPage - 1) * perPage + 1;
  const endRecord = Math.min(currentPage * perPage, total);

  if (total) {
    // Generate "Rows per page" dropdown
    const rowsPerPageOptions = RECORDS_PER_PAGE_OPTIONS;
    const rowsPerPageDropdown = `
        <label class="d-flex align-items-center">
            Rows per page:
            <select id="rows-per-page" class="form-control form-control-sm rows-select ms-2">
                ${rowsPerPageOptions
                  .map(
                    (size) =>
                      `<option value="${size}" ${
                        size == perPage ? "selected" : ""
                      }>${size}</option>`
                  )
                  .join("")}
            </select>
        </label>
      `;

    // Pagination HTML with right-aligned text, no wrapping, and plain arrows
    const paginationHtml = `
        <div class="d-flex justify-content-end align-items-center text-nowrap w-100">
            <div class="me-3">${rowsPerPageDropdown}</div>
            <div class="me-3">${startRecord}-${endRecord} of ${total}</div>
            <div>
                <button class="border-0 bg-transparent p-1 prev-page" data-page="${
                  currentPage - 1
                }" ${currentPage === 1 ? "disabled" : ""}>
                    ‹
                </button>
                <button class="border-0 bg-transparent p-1 next-page" data-page="${
                  currentPage + 1
                }" ${currentPage === totalPages ? "disabled" : ""}>
                    ›
                </button>
            </div>
        </div>
      `;

    // Update the pagination container
    const paginationContainer = getElement(`pagination`);
    if (paginationContainer) {
      paginationContainer.innerHTML = paginationHtml;
    }
  }
}

// Delegate Event Listeners
document.addEventListener("change", function (e) {
  if (e.target && e.target.id.endsWith("rows-per-page")) {
    const newPerPage = parseInt(e.target.value, 10);
    console.log("newPerPage", newPerPage);

    page = 1;
    limit = newPerPage;
    triggerGlobalFilter();
  }
});

document.addEventListener("click", function (e) {
  if (
    e.target.classList.contains("prev-page") ||
    e.target.classList.contains("next-page")
  ) {
    const newPage = parseInt(e.target.getAttribute("data-page"), 10);

    if (newPage > 0) {
      const perPage = parseInt(getElement(`rows-per-page`).value, 10);
      page = newPage;
      limit = perPage;
      triggerGlobalFilter();
    }
  }
});

document.addEventListener("DOMContentLoaded", function () {
  // Initialize the DataTable and disable the search option
  $("#vehicleTable").DataTable({
    searching: false,
    paging: false,
    ordering: true,
    info: false,
    responsive: true,
  });
});

// On document load, trigger the global filter to fetch data
document.addEventListener("DOMContentLoaded", triggerGlobalFilter);

// Function to delete customer (placeholder)
window.showVehicle = function (vehicleId) {
  // Redirect to the view vendor page
  window.location.href =
    baseUrl + "vehicleManagement/view-vehicleDetails/" + vehicleId;
};

// Function to delete customer (placeholder)
window.deleteVehicle = function (vehicleId) {
  Swal.fire({
    title: "🚗💨 Delete Vehicle?",
    html: "<b style='color: red;'>This action cannot be undone!</b><br>⚠️ Are you sure you want to <b>permanently remove</b> this vehicle?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#ff4d4d",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "🗑️ Yes, Delete!",
    cancelButtonText: "❌ Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      changeStatus(vehicleId, "deleted");
    }
  });
};
// Function to delete customer (placeholder)
window.validateSearch = function (input) {
  let searchValue = input.value.trim(); // Remove any leading or trailing whitespace

  // Trigger triggerGlobalFilter() only if 3 or more characters have been entered
  if (searchValue.length == 0 || searchValue.length >= 3) {
    triggerGlobalFilter();
  }
};
// Function to show pricing information in a modal
// Replace the showPriceInfo function with this new version
function showPriceInfo(vehicleId, basePrice, kmLimit, extraRate, extraPriceKm) {
  // Get the element that was clicked
  const infoIcon = event.currentTarget;

  // Remove any existing tooltips first
  const existingTooltips = document.querySelectorAll(".price-tooltip");
  existingTooltips.forEach((tooltip) => tooltip.remove());

  // Create tooltip element
  const tooltip = document.createElement("div");
  tooltip.className = "price-tooltip";
  tooltip.innerHTML = `
    <h4>Pricing Details</h4>
    <p class="tooltripText"><span class="tooltripTitle">Base Price:</span> <span class="tooltripValue">${CURRENCY_SYMBOL}${basePrice.toFixed(
    2
  )}</span></p>
    <p class="tooltripText"><span class="tooltripTitle">Kilometer Limit:</span> <span class="tooltripValue">${kmLimit}kms</span></p>
    <p class="tooltripText"><span class="tooltripTitle">Extra Price per Hour:</span> <span class="tooltripValue">${CURRENCY_SYMBOL}${extraRate.toFixed(
    2
  )}</span></p>
    <p class="tooltripText"><span class="tooltripTitle">Extra Price per Km:</span> <span class="tooltripValue">${CURRENCY_SYMBOL}${extraPriceKm.toFixed(
    2
  )}</span></p>
  `;

  // Position the tooltip
  document.body.appendChild(tooltip);

  // Get the position of the info icon
  const rect = infoIcon.getBoundingClientRect();
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

  // Position below the icon
  tooltip.style.top = rect.bottom + scrollTop + 5 + "px";
  tooltip.style.left = rect.left + scrollLeft - 100 + rect.width / 2 + "px";

  // Show the tooltip
  tooltip.style.display = "block";

  // Close tooltip when clicking anywhere else
  document.addEventListener("click", function closeTooltip(e) {
    if (e.target !== infoIcon && !tooltip.contains(e.target)) {
      tooltip.remove();
      document.removeEventListener("click", closeTooltip);
    }
  });

  // Prevent the event from bubbling up
  event.stopPropagation();
}
document
  .getElementById("vehicleType")
  .addEventListener("change", triggerGlobalFilter);
document
  .getElementById("status")
  .addEventListener("change", triggerGlobalFilter);
