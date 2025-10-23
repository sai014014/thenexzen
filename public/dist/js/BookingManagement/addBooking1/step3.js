// Define a namespace for BookingManagement
window.BookingManagement = window.BookingManagement || {};

let customerId = 0;
let dropDownCustomerTypeSelected = "";

// Define the Step 3 elements and related functions
const step3Elements = {
  customerTypeRadio: document.querySelectorAll(
    'input[type="radio"][name="customerType"]'
  ),
  customerListElement: getElement("customerList"),
  customerDropdownContainer: getElement("customerDropdownContainer"),
  newCustomerForm: getElement("newCustomerForm"),
  addNewCustomerBtn: getElement("addNewCustomer"),
};

step3Elements.addNewCustomerBtn?.addEventListener("click", () => {
  if (step3Elements.customerListElement) {
    step3Elements.customerListElement.value = "addNew";
    step3Elements.customerListElement.dispatchEvent(
      new Event("change", { bubbles: true, cancelable: true })
    );
  }
});

// Fetch customers based on the selected customer type
const fetchCustomers = async () => {
  const customerType = document.querySelector(
    'input[name="customerType"]:checked'
  )?.value;

  // Determine if the same customer type is being re-selected
  const appendCustomerFlag =
    dropDownCustomerTypeSelected === customerType ? 1 : 0;
  dropDownCustomerTypeSelected = customerType;

  // Show dropdown for customers
  toggleDisplay(step3Elements.newCustomerForm, false);
  try {
    if (["individualCustomer", "corporateCustomer"].includes(customerType)) {
      const response = await fetch(
        `${baseUrl}/bookingManagement/getCustomers?customerType=${customerType}`,
        {
          method: "GET",
        }
      );
      const data = await response.json();

      if (data && data.length) {
        renderCustomers(data, customerType, appendCustomerFlag);
      } else {
        renderNoCustomersFound(customerType);
      }
    } else {
      // Show dropdown for customers
      toggleDisplay(step3Elements.newCustomerForm, false);
    }
  } catch (error) {
    renderNoCustomersFound(customerType);
    // Show dropdown for customers
    toggleDisplay(step3Elements.newCustomerForm, false);
  }
};
// Fetch customers based on the selected customer type
const fetchCustomerDetail = async () => {
  const customerType = document.querySelector(
    'input[name="customerType"]:checked'
  )?.value;

  let customerId = step3Elements.customerListElement.value;
  customerId = customerId == "addNew" ? 0 : customerId;
  try {
    const response = await fetch(
      `${baseUrl}/bookingManagement/getCustomerTypeForm?customerType=${customerType}&customerId=${customerId}`,
      {
        method: "GET",
      }
    );
    const data = await response.text();

    if (data && data.length) {
      renderCustomerDetails(data, customerType);
    } else {
      renderNoCustomersFound(customerType);
    }
    commonFormFunctions();
  } catch (error) {
    console.error("Error fetching customers:", error);
    renderNoCustomersFound(customerType);
  }
};

// Event listener to trigger fetchCustomers when customerType changes
step3Elements.customerTypeRadio.forEach((checkbox) => {
  checkbox.addEventListener("change", fetchCustomers);
});
step3Elements.customerListElement.addEventListener(
  "change",
  fetchCustomerDetail
);

// Render "No Customers Found" message in case of no data
const renderNoCustomersFound = (customerType) => {
  setInnerHTML(
    step3Elements.customerListElement,
    `
    <option value="">Select Customer</option>
    <option value="addNew" data-customer-type="${customerType}">Add New Customer</option>
  `
  );
};

// Render the list of customers based on the fetched data
const renderCustomers = (response, customerType, appendCustomerFlag) => {
  const options = response.map((customer) => {
    const displayName =
      customerType === "individualCustomer"
        ? customer.full_name
        : customer.company_name;
    return `<option value="${customer.id}" data-customer-type="${customerType}">${displayName}</option>`;
  });

  // Add the "Add New Customer" option at the end
  options.push(
    `<option value="addNew" data-customer-type="${customerType}">Add New Customer</option>`
  );

  const selectOptions = `
    <option value="">Select Customer</option>
    ${options.join("")}
  `;

  // Update the customer list
  if (step3Elements.customerListElement) {
    setInnerHTML(step3Elements.customerListElement, selectOptions);
  }

  if (appendCustomerFlag) {
    handleAppendCustomer(step3Elements.newCustomerForm);
  }
};

const renderCustomerDetails = (response, customerType) => {
  const tempDiv = document.createElement("div");
  setInnerHTML(tempDiv, response);
  setInnerHTML(step3Elements.newCustomerForm, "");
  step3Elements.newCustomerForm.appendChild(tempDiv.firstElementChild);

  toggleDisplay(step3Elements.newCustomerForm, true);

  // Load and evaluate <script> tags in the fetched HTML
  const scripts = tempDiv.querySelectorAll("script");
  loadScriptsFromHTML(scripts).then(() => {
    if (customerType === "individualCustomer") {
      getElement("individualFormSubmitButton").onclick = () =>
        individualFormSubmit(1);
    } else {
      getElement("corporateFormSubmitButton").onclick = () =>
        corporateFormSubmit(1);
    }
  });

  if (window.BookingManagement?.showHideCustomerSubmitButton) {
    window.BookingManagement.showHideCustomerSubmitButton(customerType);
  } else {
    console.error("showHideCustomerSubmitButton is not available.");
  }
};

window.BookingManagement.showHideCustomerSubmitButton = (customerType) => {
  const newCustomerFormElement = getElement("newCustomerForm");
  if (!newCustomerFormElement) return;

  const customerID = newCustomerFormElement.querySelector("#customerId");

  const isExistingCustomer = (customerID?.value.trim() || "0") !== "0";

  const formConfig = {
    individualCustomer: {
      formId: "individualForm",
      submitId: "individualFormSubmitButton",
    },
    corporateCustomer: {
      formId: "corporateForm",
      submitId: "corporateFormSubmitButton",
    },
  };

  const config = formConfig[customerType];
  if (!config) return;

  const submitButton = newCustomerFormElement.querySelector(
    `#${config.submitId}`
  );
  console.log(submitButton);

  if (!submitButton) return;

  toggleDisplay(submitButton, false);

  if (isExistingCustomer) {
    const fileInputs = newCustomerFormElement.querySelectorAll(
      `#${config.formId} input[type=file]`
    );

    const handleFileChange = () => {
      const hasFiles = Array.from(fileInputs).some(
        (input) => input.files.length > 0
      );
      toggleDisplay(submitButton, hasFiles ? true : false);
    };

    fileInputs.forEach((input) =>
      input.addEventListener("change", handleFileChange)
    );

    if (customerType === "corporateCustomer") {
      const driverRows = newCustomerFormElement.querySelectorAll("tr");

      driverRows.forEach((row) => {
        // Exclude the rows that contain an input field with name="selectDriver[]"
        if (row.querySelector('[name="selectDriver[]"]')) {
          return; // Skip this iteration if the row has a selectDriver input
        }

        // Get all inputs in the row except checkboxes
        const inputs = row.querySelectorAll('input:not([type="checkbox"])');

        inputs.forEach((input) => {
          input.addEventListener("input", () => {
            console.log(input);
            toggleDisplay(submitButton, true);
          });

          input.addEventListener("change", () => {
            console.log(input);
            toggleDisplay(submitButton, true);
          });
        });
      });
    }
  } else {
    toggleDisplay(submitButton, true);
  }
};

window.BookingManagement.postSaveCustomerDetails = (insertId) => {
  const newCustomerFormElement = getElement("newCustomerForm");
  newCustomerFormElement.querySelector("#customerId").value = insertId;

  const checkedRadio = document.querySelector(
    'input[type="radio"][name="customerType"]:checked'
  );

  if (checkedRadio) {
    checkedRadio.dispatchEvent(
      new Event("change", { bubbles: true, cancelable: true })
    );
  }
};

// Handle Append Customer Logic
function handleAppendCustomer(newCustomerForm) {
  const customerIDField = newCustomerForm.querySelector("#customerId");
  if (customerIDField) {
    const customerID = customerIDField.value?.trim() || 0;
    if (customerID) {
      step3Elements.customerListElement.value = customerID;
      // Trigger change event to update UI
      const changeEvent = new Event("change", {
        bubbles: true,
        cancelable: true,
      });
      step3Elements.customerListElement.dispatchEvent(changeEvent);

      // Clear the customerId field
      customerIDField.value = 0;
    }
  }
}

// validateStep3 function
function validateStep3(bookingData) {
  const customerType = document.querySelector(
    'input[name="customerType"]:checked'
  )?.value;
  const customerId = step3Elements.customerListElement.value;

  if (getElement("globalError")) getElement("globalError").remove();

  const errorDiv = document.createElement("div");
  errorDiv.id = "globalError";
  errorDiv.classList.add("error-message");
  if (customerType === "individualCustomer" && !customerId) {
    errorDiv.textContent = "Please select an individual customer.";
    getElement("step3").appendChild(errorDiv);
    return false;
  } else if (customerType === "corporateCustomer" && !customerId) {
    errorDiv.textContent = "Please select a corporate customer.";
    getElement("step3").appendChild(errorDiv);
    return false;
  } else {
    bookingData.customer.customerId = customerId;
    bookingData.customer.customerType = customerType;
    bookingData.customer.customerCorporateDriver =
      customerType === "corporateCustomer" ? getSelectedDrivers() : [];
    return true;
  }
}

function getSelectedDrivers() {
  const driverInfo = step3Elements.newCustomerForm.querySelector("#driverInfo");
  // Get all checkboxes with name "selectDriver[]"
  const checkboxes = driverInfo.querySelectorAll(
    'input[name="selectDriver[]"]:checked'
  );

  // Array to store the selected driver IDs
  const selectedDrivers = [];

  // Iterate through checked checkboxes
  checkboxes.forEach((checkbox) => {
    // Find the closest input with name "driverId[]"
    const driverIdInput = checkbox.closest("tr").querySelector(".driverId");
    console.log(driverIdInput);

    if (driverIdInput) {
      // Add the driver ID to the array
      selectedDrivers.push(driverIdInput.value);
    }
  });

  // Log or return the selected driver IDs
  console.log("Selected Driver IDs:", selectedDrivers);
  return selectedDrivers;
}

export { validateStep3, fetchCustomers };
