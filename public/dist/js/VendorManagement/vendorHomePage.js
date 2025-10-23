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
    const vendorType = getElement("vendorType").value;
    const status = getElement("status").value;

    // Construct query parameters for data fetching
    const queryParams = new URLSearchParams({
      dateFilterSelected: selectedDateFilter,
      dateFilterSelectedDates: JSON.stringify(customDateRange),
      search: searchQuery,
      vendorType: vendorType,
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
      `vendorManagement/getVendorData?${queryParams.toString()}`,
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
  const table = $("#vendorTable").DataTable();
  table.clear();

  // Check if data is present in the response
  if (response.data.length > 0) {
    let srlno = 1;
    response.data.forEach((vendor) => {
      table.row.add([
        srlno,
        vendor.vendor_name,
        vendor.mobile_number,
        vendor.vendor_type,
        vendor.id_number, // Assuming this is the column for ID/GSTIN
        vendor.inserted_on,
        vendor.status,
        `<div class="action-icons">
              <span class="VehicleViewAction" onclick="showVendor(${vendor.id})">View</span>
              <span class="VehicleDeleteAction" onclick="deleteVendor(${vendor.id})">Delete</span>
          </div>`,
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
  $("#vendorTable").DataTable({
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
window.showVendor = function (vendorId) {
  // Redirect to the view vendor page
  window.location.href =
    baseUrl + "vendorManagement/view-vendorDetails/" + vendorId;
};

// Function to delete customer (placeholder)
window.deleteVendor = function (vendorId) {
  Swal.fire({
    title: "🏢❌ Delete Vendor?",
    html: "<b style='color: red;'>This action is irreversible!</b><br>⚠️ Are you sure you want to <b>permanently remove this vendor</b>?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#ff4d4d",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "🗑️ Yes, Delete!",
    cancelButtonText: "❌ Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      changeStatus(vendorId, "deleted");
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
document
  .getElementById("vendorType")
  .addEventListener("change", triggerGlobalFilter);
document
  .getElementById("status")
  .addEventListener("change", triggerGlobalFilter);
