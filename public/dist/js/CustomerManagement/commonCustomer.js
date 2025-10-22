/**
 * Changes the status of a customer.
 * @param {number} customerId - The ID of the customer to update.
 * @param {string} status - The new status of the customer.
 * @param {string} customerType - The type of customer (Individual or Corporate).
 */
function changeStatus(customerId, status, customerType) {
  let mainContent = document.querySelector(".main-content");
  if (customerId && customerType) {
    // Create FormData object and append the necessary data
    let formData = new FormData();
    formData.append("customerId", customerId);
    formData.append("status", status);
    // Submit the form using AJAX
    let url = "";
    if (customerType == "Individual") {
      url = baseUrl + "customerManagement/changeStatus-individualForm";
    } else {
      url = baseUrl + "customerManagement/changeStatus-corporateForm";
    }
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
        try {
          // Try to call triggerGlobalFilter() to refresh the page
          triggerGlobalFilter();
        } catch (e) {
          // Ignore any errors
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
