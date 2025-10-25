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

    // Construct query parameters for data fetching
    const queryParams = new URLSearchParams({
      dateFilterSelected: selectedDateFilter,
      dateFilterSelectedDates: JSON.stringify(customDateRange),
    });

    // Fetch only filtered data (earnings chart and stats)
    await Promise.all([
      fetchSummaryCards(queryParams),
      fetchNetSummaryGraphData(queryParams),
      // Note: Vehicle status and ongoing bookings remain live (not filtered)
    ]);

    // Hide the date filter UI after applying filter
    document.querySelector("#globalRecordsCustomDateFilter").style.display =
      "none";
  } catch (error) {
    console.error("Error applying global filters:", error);
  }
}

// Fetch summary card data from the server and update the DOM
async function fetchSummaryCards(queryParams) {
  try {
    const response = await fetch(
      `dashboard/get-summary-card-data?${queryParams.toString()}`,
      {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      }
    );

    if (!response.ok) {
      throw new Error("Failed to fetch summary card data");
    }

    const data = await response.json();
    updateSummaryCards(data.data);
  } catch (error) {
    console.error("Error fetching summary card data:", error);
  }
}

// Update summary cards with fetched data
function updateSummaryCards(cardData) {
  const cardElements = [
    { key: "total_payments", selector: ".multicards:nth-child(1) .earn_grow" },
    { key: "total_bookings", selector: ".multicards:nth-child(2) .earn_grow" },
    { key: "total_vendors", selector: ".multicards:nth-child(3) .earn_grow" },
    { key: "total_vehicles", selector: ".multicards:nth-child(4) .earn_grow" },
    { key: "total_customers", selector: ".multicards:nth-child(5) .earn_grow" },
  ];

  const increaseIcon = `<svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                        <path d="M9.37289 3.60352L10.714 4.97886L7.85607 7.90972L5.51348 5.50737L1.17383 9.96372L1.99959 10.8105L5.51348 7.20703L7.85607 9.60938L11.5456 5.83169L12.8868 7.20703V3.60352H9.37289Z" fill="#1DAA7B" />
                      </svg>`;

  const decreaseIcon = `<svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                          <path d="M9.37289 10.8105L10.714 9.43521L7.85607 6.50435L5.51348 8.90669L1.17383 4.45034L1.99959 3.60352L5.51348 7.20703L7.85607 4.80469L11.5456 8.58237L12.8868 7.20703V10.8105H9.37289Z" fill="#F93C65" />
                        </svg>`;

  cardElements.forEach(({ key, selector }) => {
    const cardElement = document.querySelector(selector);
    if (!cardElement) return;

    let currentValue = cardData[0][key] || 0;
    let previousValue = cardData[1][key] || 0;

    // Ensure "total_payments" is always a float
    if (key === "total_payments") {
      currentValue = parseFloat(currentValue).toFixed(2);
      previousValue = parseFloat(previousValue).toFixed(2);
    }

    updateCardValue(
      cardElement,
      currentValue,
      previousValue,
      increaseIcon,
      decreaseIcon,
      key,
      cardData["previousFilterContent"]
    );
  });
}

// Helper function to update card value and display percentage changes
function updateCardValue(
  cardElement,
  currentValue,
  previousValue,
  increaseIcon,
  decreaseIcon,
  key,
  previousFilterContent
) {
  const h4Element = cardElement.querySelector("h4");
  if (h4Element) {
    h4Element.innerHTML =
      key === "total_payments"
        ? CURRENCY_SYMBOL + `${currentValue}`
        : currentValue;
  }

  const pElement = cardElement.querySelector("p");
  if (pElement) {
    const percentageChange = calculatePercentageChange(
      currentValue,
      previousValue
    );
    let changeText = "no change ";

    if (previousFilterContent) {
      if (percentageChange > 0) {
        changeText = `<span class="grow_per">${increaseIcon}${percentageChange}%</span> up from `;
      } else if (percentageChange < 0) {
        changeText = `<span class="grow_down">${decreaseIcon}${Math.abs(
          percentageChange
        )}%</span> down from `;
      }

      pElement.innerHTML =
        changeText + (previousFilterContent ? `${previousFilterContent}` : "");
    } else {
      pElement.innerHTML = "";
    }
  }
}

// Calculate the percentage change between two values
function calculatePercentageChange(current, previous) {
  try {
    current = parseFloat(current);
    previous = parseFloat(previous);

    // Handle edge cases where values are zero
    if (current === 0 && previous === 0) {
      return 0;
    }

    if (previous === 0) {
      return current > 0 ? 100 : 0;
    }

    const change = ((current - previous) * 100) / previous;
    
    // Round to whole numbers to avoid tiny decimals like 0.001
    const roundedChange = Math.round(change);
    
    // If the change is very small (less than 1%), return 0
    if (Math.abs(roundedChange) < 1) {
      return 0;
    }
    
    return isNaN(roundedChange) ? 0 : roundedChange;
  } catch (error) {
    console.error("Error calculating percentage change:", error);
    return 0;
  }
}

// Fetch graph data and update the chart
async function fetchNetSummaryGraphData(queryParams) {
  try {
    const response = await fetch(
      `dashboard/get-graph-data?${queryParams.toString()}`,
      {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      }
    );

    const data = await response.json();
    updateChart(data.data);
  } catch (error) {
    console.error("Error fetching chart data:", error);
  }
}

// Update the chart with new data
function updateChart(chartData) {
  try {
    if (!chartData || chartData.length === 0) return;

    data.labels = chartData.map((item) => item.time_group);
    data.datasets[0].data = chartData.map((item) =>
      Number(item.total_amount.replace(/,/g, ""))
    );

    // Adjust the y-axis minimum value dynamically
    const minValue = Math.min(...data.datasets[0].data);
    chart.options.scales.y.min = minValue < 0 ? minValue : 0;

    chart.update();
  } catch (error) {
    console.error("Error updating chart:", error);
  }
}

const tooltip = getElement("customTooltip");
const tooltipValue = tooltip?.querySelector(".tooltip-value");
const tooltipTitle = tooltip?.querySelector(".tooltip-title");
const tooltipDate = tooltip?.querySelector(".tooltip-date");

// Tooltip position logic for better visibility
function positionTooltip(x, y) {
  try {
    const margin = 15;
    const viewportWidth = window.innerWidth;
    const tooltipWidth = tooltip.offsetWidth;

    tooltip.style.left =
      x + tooltipWidth + margin > viewportWidth
        ? `${x - tooltipWidth - margin}px`
        : `${x + margin}px`;
    tooltip.style.top = `${y}px`;
  } catch (error) {
    console.error("Error positioning tooltip:", error);
  }
}

// Event listener for mouse leave to hide tooltip
const options = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    intersect: false,
    mode: "index",
  },
  plugins: {
    tooltip: {
      enabled: false,
      external: function (context) {
        const earningsValue = getElement("earningsValue");
        if (!tooltip || !tooltipTitle || !tooltipValue || !earningsValue)
          return;

        const { chart, tooltip: chartTooltip } = context;

        if (chartTooltip.opacity === 0) {
          earningsValue.classList.remove("visible");
          tooltip.classList.remove("visible");
          return;
        }

        const dataPoint = chartTooltip.dataPoints[0];
        const value = dataPoint.raw;
        const label = dataPoint.label;

        tooltipTitle.textContent = `${label} Earnings`;
        tooltipValue.textContent = `₹${Number(value).toLocaleString("en-IN")}`;
        earningsValue.textContent = `₹${Number(value).toLocaleString("en-IN")}`;
        earningsValue.classList.add("visible");

        // Position tooltip next to cursor
        positionTooltip(
          chartTooltip.caretX + chart.canvas.getBoundingClientRect().left,
          chartTooltip.caretY + chart.canvas.getBoundingClientRect().top
        );

        tooltip.classList.add("visible");

        // Draw glowing dot
        const ctx = chart.ctx;
        ctx.save();
        ctx.beginPath();
        ctx.arc(chartTooltip.caretX, chartTooltip.caretY, 8, 0, 2 * Math.PI);
        ctx.fillStyle = "white";
        ctx.shadowColor = "rgba(255, 255, 255, 0.8)";
        ctx.shadowBlur = 15;
        ctx.fill();
        ctx.restore();
      },
    },
    legend: { display: false },
  },
  scales: {
    x: {
      grid: { display: false },
      ticks: {
        color: "rgba(255, 255, 255, 0.8)",
        font: { size: 12 },
      },
      border: { display: false },
    },
    y: {
      grid: {
        color: "rgba(255, 255, 255, 0.1)",
        drawBorder: false,
        lineWidth: 1,
      },
      ticks: {
        color: "rgba(255, 255, 255, 0.8)",
        font: { size: 12 },
        callback: function (value) {
          if (value >= 10000000) return (value / 10000000).toFixed(2) + " Cr";
          if (value >= 100000) return (value / 100000).toFixed(2) + " L";
          if (value >= 1000) return (value / 1000).toFixed(2) + "K";
          return value.toLocaleString("en-IN");
        },
      },

      min: 0,
    },
  },
};

// Define initial data
const data = {
  labels: [],
  datasets: [
    {
      data: [],
      fill: { target: "origin", above: "rgba(255, 255, 255, 0.1)" },
      borderColor: "white",
      borderWidth: 3,
      tension: 0.4,
      pointStyle: "circle",
      pointRadius: 0,
      pointHoverRadius: 8,
      pointHoverBackgroundColor: "white",
      pointHoverBorderColor: "white",
      pointHoverBorderWidth: 3,
    },
  ],
};

// Create chart
const ctx = getElement("earningsChart").getContext("2d");

// Custom plugin for line shadow
const lineShadowPlugin = {
  id: "lineShadow",
  beforeDraw: (chart) => {
    const ctx = chart.ctx;
    ctx.save();
    ctx.shadowColor = "rgba(255, 255, 255, 0.3)";
    ctx.shadowBlur = 15;
    ctx.shadowOffsetY = 5;
    ctx.restore();
  },
};

// Initialize chart
const chart = new Chart(ctx, {
  type: "line",
  data: data,
  options: options,
  plugins: [lineShadowPlugin],
});

// Handle tooltip hide on mouse leave
document
  .querySelector(".chart-container")
  ?.addEventListener("mouseleave", () => {
    tooltip?.classList.remove("visible");
    getElement("earningsValue")?.classList.remove("visible");
  });

// doughnut chart of vehicles (LIVE DATA - not filtered by date range)
function fetchVehiclesSummaryData() {
  // Always fetch live vehicle data without date filters
  fetch(`dashboard/get-vehicles-data`, {
    method: "GET",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success" && data.data.length > 0) {
        const d = data.data[0];
        const counts = {
          available: parseInt(d.available_count || 0),
          booked: parseInt(d.booked_count || 0),
          // maintenance: parseInt(d.maintenance_count || 0),
        };
        renderDoughnutChart(counts);
      }
    })
    .catch((error) => console.error("Error fetching chart data:", error));
}

function renderDoughnutChart(counts) {
  const ctx = document.getElementById("doughnutChart").getContext("2d");

  if (window.vehicleChart) {
    window.vehicleChart.destroy();
  }

  // Clear any existing custom legend
  const existingLegend = document.querySelector(
    "#chartContainer .custom-legend"
  );
  if (existingLegend) {
    existingLegend.remove();
  }

  const colors = ["#28a745", "#ffc107" /*, "#dc3545"*/];

  const centerTextPlugin = {
    id: "centerTextOnHover",
    afterDraw(chart) {
      const ctx = chart.ctx;
      const width = chart.width;
      const height = chart.height;
      const activeElement = chart.getActiveElements()[0];

      ctx.save();

      if (activeElement) {
        const dataset = chart.data.datasets[activeElement.datasetIndex];
        const value = dataset.data[activeElement.index];
        const color = dataset.backgroundColor[activeElement.index];

        ctx.font = "bold 26px Arial";
        ctx.fillStyle = color;
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        ctx.fillText(value, width / 2, height / 2);
      }

      ctx.restore();
    },
  };

  window.vehicleChart = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: ["AVAILABLE", "BOOKED" /*, "MAINTENANCE"*/],
      datasets: [
        {
          data: [counts.available, counts.booked /*, counts.maintenance || 0*/], // Include maintenance count if it exists
          backgroundColor: colors,
          hoverBackgroundColor: colors,
          borderWidth: 2,
        },
      ],
    },
    options: {
      responsive: true,
      cutout: "60%",
      plugins: {
        legend: {
          display: false, // Hide default legend since we'll create a custom one
        },
        tooltip: {
          enabled: true,
        },
      },
      onHover: function (event, elements) {
        event.chart.draw(); // trigger center update
      },
    },
    plugins: [centerTextPlugin],
  });

  // Create custom legend with doughnut charts
  const legendContainer = document.createElement("div");
  legendContainer.className = "custom-legend";
  document.getElementById("chartContainer").appendChild(legendContainer);

  const labels = ["AVAILABLE", "BOOKED"/*, "MAINTENANCE"*/];
  const colorsMap = {
    AVAILABLE: "#28a745",
    BOOKED: "#ffc107",
    // MAINTENANCE: "#dc3545",
  };

  labels.forEach((label) => {
    const legendItem = document.createElement("div");
    legendItem.className = "legend-item";

    const doughnutContainer = document.createElement("div");
    doughnutContainer.className = "legend-doughnut";

    const canvas = document.createElement("canvas");
    doughnutContainer.appendChild(canvas);

    const text = document.createElement("span");
    text.textContent = label;

    legendItem.appendChild(doughnutContainer);
    legendItem.appendChild(text);
    legendContainer.appendChild(legendItem);

    // Create mini doughnut chart for the legend
    new Chart(canvas, {
      type: "doughnut",
      data: {
        datasets: [
          {
            data: [1, 0], // 1 for the colored part, 0 for the rest
            backgroundColor: [colorsMap[label], "white"],
            borderColor: colorsMap[label],
            borderWidth: 2,
          },
        ],
      },
      options: {
        responsive: true,
        cutout: "50%",
        rotation: -90,
        circumference: 360,
        plugins: {
          legend: { display: false },
          tooltip: { enabled: false },
        },
        events: [], // Disable all events
      },
    });
  });
}

// Initialize with empty data
renderDoughnutChart({
  available: 0,
  booked: 0,
  // maintenance: 0,
});

// On document load, fetch live data and filtered data
document.addEventListener("DOMContentLoaded", function() {
  // Fetch live data (vehicle status and ongoing bookings are already loaded from PHP)
  fetchVehiclesSummaryData();
  
  // Fetch filtered data (earnings chart and stats)
  triggerGlobalFilter();
});
