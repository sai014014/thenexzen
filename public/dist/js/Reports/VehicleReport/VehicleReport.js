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

    const ownershipType = getElement("ownershipType").value;

    // Construct query parameters for data fetching
    const queryParams = new URLSearchParams({
      dateFilterSelected: selectedDateFilter,
      dateFilterSelectedDates: JSON.stringify(customDateRange),
      ownershipType: ownershipType,
      page: page, // Pass the current page
      limit: limit,
    });

    // Fetch vehicle data
    await Promise.all([fetchVehiclesList(queryParams)]);

    // Hide the date filter UI after applying filter
    document.querySelector("#globalRecordsCustomDateFilter").style.display =
      "none";
  } catch (error) {
    console.error("Error applying global filters:", error);
  }
}

// Fetch vehicle data from the server and update the DOM
async function fetchVehiclesList(queryParams) {
  try {
    const response = await fetch(
      `vehicle-reportData?${queryParams.toString()}`,
      {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      }
    );

    if (!response.ok) {
      throw new Error("Failed to fetch vehicle data");
    }

    const data = await response.json();
    updateTableData(data);
  } catch (error) {
    console.error("Error fetching vehicle data:", error);
  }
}

function updateTableData(response) {
  console.log("updateTableData", response);

  // Check if DataTable instance already exists, then destroy it
  if ($.fn.DataTable.isDataTable("#reportTable")) {
    $("#reportTable").DataTable().destroy();
  }

  const table = $("#reportTable").DataTable({
    searching: false,
    paging: false,
    ordering: true,
    info: false,
    responsive: true,
  });
  table.clear();

  // Check if data is present in the response
  if (response.data.length > 0) {
    let srlno = 1;
    response.data.forEach((vehicle) => {
      table.row.add([
        srlno,
        vehicle.vehicle_name,
        vehicle.vehicle_number,
        vehicle.ownership_type,
        vehicle.registered_on,
        vehicle.insurance_expiry,
        vehicle.total_rentals,
        vehicle.total_kilometers,
        convertCurrencyFormat(vehicle.net_revenue),
        vehicle.last_rental_date,
      ]);
      srlno++;
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
  $("#reportTable").DataTable({
    searching: false,
    paging: false,
    ordering: true,
    info: false,
    responsive: true,
  });
});

$("#ownershipType").on("change", triggerGlobalFilter);

triggerGlobalFilter();
