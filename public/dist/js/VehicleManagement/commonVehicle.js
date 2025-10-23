/**
 * Change the status of a vendor.
 * @param {number} vehicleId - The ID of the vendor to update.
 * @param {string} status - The new status of the vendor.
 */
function changeStatus(vehicleId, status) {
  let mainContent = document.querySelector(".main-content");
  if (vehicleId) {
    // Create FormData object and append the necessary data
    const formData = new FormData();
    formData.append("vehicleId", vehicleId);
    formData.append("status", status);
    // Submit the form using AJAX
    const url = baseUrl + "vehicleManagement/changeStatus-vehicleForm";
    fetch(url, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          if (mainContent) {
            let alertDiv = document.createElement("div");
            alertDiv.id = "globalError";
            alertDiv.className = "alert alert-success";
            alertDiv.textContent = "Status successfully changes";

            mainContent.prepend(alertDiv);
          }
        } else {
          if (mainContent) {
            let alertDiv = document.createElement("div");
            alertDiv.id = "globalError";
            alertDiv.className = "alert alert-danger";
            alertDiv.textContent = "Failed to change status.";

            mainContent.prepend(alertDiv);
          }
        }
        // Attempt to call triggerGlobalFilter() to refresh the page
        try {
          triggerGlobalFilter();
        } catch (error) {
          console.log("Error calling triggerGlobalFilter()", error);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  } else {
    if (mainContent) {
      let alertDiv = document.createElement("div");
      alertDiv.id = "globalError";
      alertDiv.className = "alert alert-danger";
      alertDiv.textContent = "Please provide valid inputs.";

      mainContent.prepend(alertDiv);
    }
  }
}
