const globalFilterButton = document.getElementById("globalRecordsFilterButton");
console.log(globalFilterButton);

const globalFilterDropdown = document.getElementById("globalRecordsDropdown");
const globalFilterOptions = document.querySelectorAll(".globalFilterOption");
const globalCustomDateInputs = document.getElementById(
  "globalRecordsCustomDateFilter"
);
const globalCustomOption = document.querySelector(
  '.globalFilterOption[data-value="7"]'
); // Custom option is value 7
const globalApplyButton = document.getElementById("globalRecordsApplyButton");
const globalStartDate = document.getElementById("globalFromDate");
const globalEndDate = document.getElementById("globalToDate");

// Toggle dropdown
if (globalFilterButton) {
  globalFilterButton.addEventListener("click", () => {
    globalFilterDropdown.classList.toggle("active");
    globalFilterButton.classList.toggle("active");
  });
}

// Handle option selection
if (globalFilterOptions) {
  globalFilterOptions.forEach((option) => {
    option.addEventListener("click", () => {
      if (option.dataset.value === "7") {
        // Custom Range
        globalCustomDateInputs.style.display = "flex";
        return;
      }

      globalCustomDateInputs.style.display = "none";

      globalFilterOptions.forEach((opt) => opt.classList.remove("selected"));
      option.classList.add("selected");

      globalFilterButton.textContent = option.textContent;

      globalFilterDropdown.classList.remove("active");
      globalFilterButton.classList.remove("active");

      // Call triggerGlobalFilter immediately when non-custom option is selected
      triggerGlobalFilter();
    });
  });
}

// Handle apply button click (only for custom range)
if (globalApplyButton) {
  globalApplyButton.addEventListener("click", () => {
    const start = new Date(globalStartDate.value);
    const end = new Date(globalEndDate.value);

    if (start && end) {
      const formatDate = (date) => {
        return date.toLocaleDateString("en-US", {
          month: "short",
          day: "numeric",
          year: "numeric",
        });
      };

      globalFilterButton.textContent = `${formatDate(start)} - ${formatDate(
        end
      )}`;

      globalFilterOptions.forEach((opt) => opt.classList.remove("selected"));
      globalCustomOption.classList.add("selected");

      globalFilterDropdown.classList.remove("active");
      globalFilterButton.classList.remove("active");

      // Call triggerGlobalFilter after Apply is clicked for custom range
      triggerGlobalFilter();
    }
  });
}

// Close dropdown if click outside
document.addEventListener("click", (e) => {
  if (globalFilterButton) {
    if (
      !globalFilterButton.contains(e.target) &&
      !globalFilterDropdown.contains(e.target)
    ) {
      globalFilterDropdown.classList.remove("active");
      globalFilterButton.classList.remove("active");
    }
  }
});

// Helper function to fetch filter values
function getGlobalDateFilterValues() {
  const selectedFilter =
    document
      .querySelector("#globalRecordsDropdown .globalFilterOption.selected")
      ?.getAttribute("data-value") || "";
  const startDate = document.getElementById("globalFromDate")?.value || "";
  const endDate = document.getElementById("globalToDate")?.value || "";

  return {
    globalSelectedFilter: selectedFilter,
    globalStartDateSelected: startDate,
    globalEndDateSelected: endDate,
  };
}

// Attach this function to window so it's globally accessible
window.getGlobalDateFilterValues = getGlobalDateFilterValues;
