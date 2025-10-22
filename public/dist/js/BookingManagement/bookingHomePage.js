$(document).ready(function () {
  const globalErrorElement = document.querySelector("#globalError");

  const tables = {
    ongoing: null,
    upcoming: null,
    completed: null,
  };

  function initializeDataTable(bookingType) {
    tables[bookingType] = $(`#${bookingType}Table`).DataTable({
      searching: false,
      paging: false,
      ordering: true,
      info: false,
      responsive: true,
    });
  }

  ["ongoing", "upcoming", "completed"].forEach((type) =>
    initializeDataTable(type)
  );

  function fetchBookingData(bookingType, page = 1, perPage = RECORDS_PER_PAGE) {
    const offset = (page - 1) * perPage;
    const url = `${baseUrl}bookingManagement/get-BookingDetailsList?bookingType=${bookingType}&page=${page}&limit=${perPage}`;

    $.ajax({
      url,
      type: "GET",
      success: function (response) {
        const table = tables[bookingType];
        table.clear().draw();

        if (response.data && response.data.length > 0) {
          response.data.forEach((booking, index) => {
            table.row.add(
              formatTableRow(bookingType, booking, offset + index + 1)
            );
          });
          table.draw();
        }

        updatePagination(
          response.total,
          response.perPage,
          response.currentPage,
          bookingType
        );
      },
      error: function () {
        setInnerHTML(
          globalErrorElement,
          "Failed to fetch booking data. Please try again later."
        );
      },
    });
  }

  function formatTableRow(bookingType, booking, serialNumber) {
    const commonData = [
      booking.booking_id,
      booking.full_name || booking.company_name,
      `<img src="${baseUrl}${booking.brand_image}" alt="Manufacturer Logo" width="50" height="50"> - ${booking.model_name}`,
      booking.start_date,
    ];

    // const actions = `
    //   <div class="action-icons">
    //     <span class="VehicleViewAction" onclick="showBookingDetails('${booking.booking_id}')">View</span>
    //     <span class="VehicleDeleteAction" onclick="deleteBooking('${booking.booking_id}', '${bookingType}')">Delete</span>
    //   </div>
    // `;
    const actions = `
      <div class="action-icons">
        <span class="VehicleViewAction" onclick="showBookingDetails('${booking.booking_id}')">View</span>
      </div>
    `;

    if (bookingType === "ongoing") {
      return [
        ...commonData,
        booking.end_date,
        CURRENCY_SYMBOL + booking.balance_amount,
        actions,
      ];
    } else if (bookingType === "upcoming") {
      return [...commonData, actions];
    } else if (bookingType === "completed") {
      return [
        ...commonData,
        booking.end_date,
        BOOKING_STATUS_ARRAY[booking.status],
        actions,
      ];
    }

    return [];
  }

  function updatePagination(total, perPage, currentPage, bookingType) {
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
            <select id="${bookingType}-rows-per-page" class="form-control form-control-sm rows-select ms-2">
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
                }" data-bookingtype="${bookingType}" ${
        currentPage === 1 ? "disabled" : ""
      }>
                    ‹
                </button>
                <button class="border-0 bg-transparent p-1 next-page" data-page="${
                  currentPage + 1
                }" data-bookingtype="${bookingType}" ${
        currentPage === totalPages ? "disabled" : ""
      }>
                    ›
                </button>
            </div>
        </div>
    `;

      // Update the pagination container
      const paginationContainer = getElement(`${bookingType}-pagination`);
      if (paginationContainer) {
        paginationContainer.innerHTML = paginationHtml;
      }
    }
  }

  // Delegate Event Listeners
  document.addEventListener("change", function (e) {
    if (e.target && e.target.id.endsWith("-rows-per-page")) {
      // Extract bookingType from the dropdown's ID
      const bookingType = e.target.id.replace("-rows-per-page", "");
      const newPerPage = parseInt(e.target.value, 10);
      console.log("newPerPage", newPerPage);

      fetchBookingData(bookingType, 1, newPerPage);
    }
  });

  document.addEventListener("click", function (e) {
    if (
      e.target.classList.contains("prev-page") ||
      e.target.classList.contains("next-page")
    ) {
      const newPage = parseInt(e.target.getAttribute("data-page"), 10);
      const bookingType = e.target.getAttribute("data-bookingtype");

      if (newPage > 0) {
        const perPage = parseInt(
          getElement(`${bookingType}-rows-per-page`).value,
          10
        );
        fetchBookingData(bookingType, newPage, perPage);
      }
    }
  });

  window.showBookingDetails = function (bookingId) {
    window.location.href = `${baseUrl}bookingManagement/view-BookingDetails?bookingId=${bookingId}`;
  };

  window.deleteBooking = function (bookingId, bookingType) {
    Swal.fire({
      title: "🗓️❌ Cancel Booking?",
      html: "<b style='color: red;'>This action is irreversible!</b><br>⚠️ Are you sure you want to <b>delete this booking</b>?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#ff4d4d",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "🗑️ Yes, Delete!",
      cancelButtonText: "❌ Cancel",
    }).then((result) => {
      if (result.isConfirmed) {
        changeStatus(bookingId, 2, () => {
          fetchBookingData(bookingType); // Refresh data after deletion
        });
      }
    });
  };

  // Tab click handlers to load data using vanilla JavaScript
  const ongoingTab = getElement("ongoing-tab");
  const upcomingTab = getElement("upcoming-tab");
  const completedTab = getElement("completed-tab");
  if (ongoingTab) {
    ongoingTab.addEventListener("click", function () {
      fetchBookingData("ongoing");
    });
  }
  if (upcomingTab) {
    upcomingTab.addEventListener("click", function () {
      fetchBookingData("upcoming");
    });
  }
  if (completedTab) {
    completedTab.addEventListener("click", function () {
      fetchBookingData("completed");
    });
  }

  // Fetch data on initial load
  fetchBookingData("ongoing");
});
