let bookingData = {}; // Declare a local variable to hold the bookingData if needed
let vehiclesData = []; // Array to store the fetched vehicle JSON data

// Define the Step 2 elements and related functions
const step2Elements = {
  fuelTypeElement: getElement("fuelType"),
  transmissionElement: getElement("transmissionType"),
  seatingElement: getElement("seatingCapacity"),
  vehicleCards: getElement("vehicleCards"),
  paginationNav: getElement("paginationNav"),
  sortByElement: getElement("sortDropdown"),
};

step2Elements.sortByElement?.addEventListener("change", (e) => {
  fetchVehicles(1, bookingData);
});

let selectedVehicleId = null;
let selectedVehicleData = null;

// Populate vehicle cards via AJAX
const fetchVehicles = async (page = 1, bookingData) => {
  console.log("bookingData", bookingData);

  const filters = {
    fuelType: step2Elements.fuelTypeElement.value,
    transmission: step2Elements.transmissionElement.value,
    seating: step2Elements.seatingElement.value,
    sortBy: step2Elements.sortByElement.value,
  };
  selectedVehicleData = null;

  try {
    const response = await $.ajax({
      url: `${baseUrl}bookingManagement/getFilteredVehicles`,
      type: "GET",
      data: { ...filters, page, bookingData },
    });
    if (response?.vehicles?.length && response?.vehiclesHtml) {
      vehiclesData = response.vehicles; // Store the fetched JSON data
      renderVehicleCards(response);
    } else {
      vehiclesData = []; // Clear previous data if no vehicles are available
      renderNoVehiclesFound();
    }
  } catch (error) {
    vehiclesData = []; // Clear data in case of an error
    renderNoVehiclesFound();
  }
};

// Show message when no vehicles are available
const renderNoVehiclesFound = () => {
  setInnerHTML(
    step2Elements.vehicleCards,
    "<p>No vehicles available with the selected filters.</p>"
  );
};

// Render vehicle cards
const renderVehicleCards = (data) => {
  setInnerHTML(step2Elements.vehicleCards, "");
  toggleDisplay(step2Elements.paginationNav, false);

  if (data.vehicles.length > 0) {
    setInnerHTML(step2Elements.vehicleCards, data.vehiclesHtml);
    setupVehiclePagination(data.pagination);
    highlightSelectedVehicle();
    toggleDisplay(step2Elements.paginationNav, true);
  } else {
    setInnerHTML(
      step2Elements.vehicleCards,
      "<p>No vehicles available with the selected filters.</p>"
    );
  }
};

// Setup pagination
const setupVehiclePagination = (pagination) => {
  const paginationNavUl = step2Elements.paginationNav.querySelector("ul");
  setInnerHTML(paginationNavUl, ""); // Clear previous pagination

  for (let i = 1; i <= pagination.totalPages; i++) {
    paginationNavUl.innerHTML += `
        <li class="page-item ${pagination.currentPage === i ? "active" : ""}">
          <a class="page-link" href="#" data-page="${i}">${i}</a>
        </li>`;
  }

  // Handle page navigation
  paginationNavUl.addEventListener("click", (event) => {
    const pageLink = event.target.closest(".page-link");
    if (pageLink) {
      const page = parseInt(pageLink.dataset.page);
      fetchVehicles(page);
    }
  });
};

// Highlight the selected vehicle
const highlightSelectedVehicle = () => {
  step2Elements.vehicleCards
    .querySelectorAll(".vehicle-card")
    .forEach((card) => {
      card.addEventListener("click", () => handleVehicleSelection(card));
    });
};

// Handle vehicle selection
const handleVehicleSelection = (card) => {
  const vehicleCards =
    step2Elements.vehicleCards.querySelectorAll(".vehicle-card");
  vehicleCards.forEach((c) => c.classList.remove("selected"));
  card.classList.add("selected");
  selectedVehicleId = card.dataset.vehicleId;

  // Find the selected vehicle data and store it in bookingData.selectedVehicle
  selectedVehicleData = vehiclesData.find(
    (vehicle) => vehicle.vehicle_id === selectedVehicleId
  );
};

// Event: Filter change
[
  step2Elements.fuelTypeElement,
  step2Elements.transmissionElement,
  step2Elements.seatingElement,
].forEach(
  (element) =>
    element.addEventListener("change", () => fetchVehicles(1, bookingData)) // Wrap in an anonymous function
);

// Validate in Step 2
function validateStep2(bookingData) {
  let isValid = !!selectedVehicleData;

  if (!isValid) {
    const errorDiv = document.createElement("div");
    errorDiv.id = "globalError";
    errorDiv.classList.add("error-message");
    errorDiv.textContent = "Please select a vehicle to proceed.";
    getElement("step2").appendChild(errorDiv);
  } else {
    bookingData.selectedVehicle = selectedVehicleData;
  }

  return isValid;
}

export { validateStep2, fetchVehicles };
