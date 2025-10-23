/**
 * Additional Branches Toggle
 */
$(document).ready(function () {
  // Toggle additional branches section based on the checkbox
  $("#additionalBranchesCheck").on("change", function () {
    if ($(this).is(":checked")) {
      $("#additionalBranchesContainer").show();
    } else {
      $("#additionalBranchesContainer").hide();
    }
  });

  /**
   * Add Additional Branch
   */
  // Initialize a counter for additional branches
  let branchCount = 0;

  // Show or hide additional branches section
  $("#additionalBranchesCheck").on("change", function () {
    if ($(this).is(":checked")) {
      branchCount++; // Increment branch count when checkbox is checked for the first time
      addBranchSection(branchCount); // Add the first branch section
      $("#additionalBranchesContainer").show(); // Show the container
    } else {
      $("#additionalBranchesContainer").hide(); // Hide the container
      $("#additionalBranchesContainerListDiv").empty(); // Reset the content
      branchCount = 0; // Reset the branch count
    }
  });

  // Add Additional Branch
  $("#addBranchBtn").on("click", function () {
    branchCount++; // Increment branch count for new branch
    addBranchSection(branchCount); // Add new branch section
  });

  // Function to add a new branch section
  function addBranchSection(count) {
    console.log("addBranchSection");

    const branchHTML = `
          <div class="additional-branch mb-3">
              <h4>Additional Branch ${count}</h4>
              <div class="row">
              <div class="form-group">
              <label for="additionalStreet" class="form-label">Street</label>
              <input type="text" class="form-control mb-2" name="additionalStreet[]" required>
              </div>
               <div class="form-group">
              <label for="additionalLocality" class="form-label">Locality</label>
              <input type="text" class="form-control mb-2" name="additionalLocality[]" required>
              </div>
               <div class="form-group">
              <label for="additionalCity" class="form-label">City</label>
              <input type="text" class="form-control mb-2" name="additionalCity[]" required>
              </div>
               <div class="form-group">
              <label for="additionalState" class="form-label">State</label>
              <input type="text" class="form-control mb-2" name="additionalState[]" required>
               </div>
               <div class="form-group">
              <label for="additionalPostalCode" class="form-label">Postal Code</label>
              <input type="text" class="form-control mb-2" name="additionalPostalCode[]" pattern="\\d{6}" required>
               </div>
               </div>
              <button type="button" class="btn btn-danger removeBranchBtn">Remove Branch</button>
          </div>`;

    $("#additionalBranchesContainerListDiv").append(branchHTML); // Append new branch section
  }

  // Remove Additional Branch
  $(document).on("click", ".removeBranchBtn", function () {
    $(this).closest(".additional-branch").remove(); // Remove the specific branch section
    updateBranchHeadings(); // Update headings after removal
  });

  /**
   * Function to update branch headings after a branch is removed
   */
  function updateBranchHeadings() {
    let index = 1; // Reset index for headings
    $(".additional-branch").each(function () {
      $(this).find("h5").text(`Additional Branch ${index}`); // Update heading
      index++; // Increment index
    });
    branchCount = index - 1; // Update the branch count
  }

  // Remove Additional Branch
  $(document).on("click", ".removeBranchBtn", function () {
    $(this).closest(".additional-branch").remove();
  });

  /*------------------- additional branch end -------------------- */

  // Payout Method Toggle
  $("#payoutMethod").on("change", function () {
    const method = $(this).val();
    $("#bankDetails, #upiDetails, #otherPayoutMethod").hide();

    // Remove required attributes from all sections initially
    $("#bankName, #accountHolder, #accountNumber, #ifscCode").removeAttr(
      "required"
    );
    $("#upiId").removeAttr("required");
    $("#otherMethod").removeAttr("required");

    // Show the corresponding section and set required attributes
    if (method === "Bank Transfer") {
      $("#bankDetails").show();
      $("#bankName, #accountHolder, #accountNumber, #ifscCode").attr(
        "required",
        true
      );
    } else if (method === "UPI Payment") {
      $("#upiDetails").show();
      $("#upiId").attr("required", true);
    } else if (method === "Other") {
      $("#otherPayoutMethod").show();
      $("#otherMethod").attr("required", true);
    }
  });

  // Payout Frequency Toggle
  $("#payoutFrequency").on("change", function () {
    const frequency = $(this).val();
    if (frequency === "Other") {
      $("#otherPayoutFrequency").show();
      $("#otherFrequencyText").attr("required", true);
    } else {
      $("#otherPayoutFrequency").hide();
      $("#otherFrequencyText").attr("required", false);
    }
  });

  /**
   * Add Additional Certificates
   */
  // Initialize a counter for the number of certificates
  let certificateCount = 0;

  // Add Additional Certificates
  $("#addCertificateBtn").on("click", function () {
    if (certificateCount < 6) {
      certificateCount++; // Increment certificate count

      const certificateHTML = `
                <div class="certificate mb-3">
                    <h5>Document ${certificateCount}</h5>
                    <div class="row">
                    <div class="form-group">
                    <label for="certificateName${certificateCount}" class="form-label">Certificate Name</label>
                    <input type="text" class="form-control mb-2" name="certificateName${certificateCount}" required>
                    </div>
                    <input type="hidden" class="form-control mb-2" name="certificatesCount[]" value="${certificateCount}" required>
                    <div class="form-group">
                     <label for="certificateFront${certificateCount}" class="form-label">Certificate Front Side</label>
                    <input type="file" class="form-control mb-2 fileInput" id="certificateFront${certificateCount}" name="certificateFront${certificateCount}" required accept="image/*,.pdf" capture="environment" onchange="handleFileUpload(this);">
                   
                    </div>
                    <div class="form-group">
                      <label for="certificateBack${certificateCount}" class="form-label">Certificate Back Side</label>
                    <input type="file" class="form-control mb-2 fileInput" id="certificateBack${certificateCount}" name="certificateBack${certificateCount}" required accept="image/*,.pdf" capture="environment" onchange="handleFileUpload(this);">
                    </div>
                    </div>
                    <div class="form-group">
                    <button type="button" class="btn btn-danger removeCertificateBtn">Remove Document</button>
                    </div>
                </div>`;

      $("#additionalCertificatesContainer").append(certificateHTML); // Append the new certificate section
    } else {
      showToast({
        title: "Vendor",
        message: "Maximum of 6 additional certificates allowed.",
        type: "info",
      });
    }
  });

  // Remove Additional Certificate
  $(document).on("click", ".removeCertificateBtn", function () {
    $(this).closest(".certificate").remove(); // Remove the corresponding certificate section
    certificateCount--; // Decrement the certificate count
    updateDocumentHeadings(); // Update document headings
  });

  /**
   * Function to update document headings after a document is removed
   */
  function updateDocumentHeadings() {
    $(".certificate").each(function (index) {
      $(this)
        .find("h5")
        .text(`Document ${index + 1}`); // Update heading dynamically
    });
  }

  // Form Validation
  $("#vendorForm").on("submit", function (event) {
    event.preventDefault();
    const form = this;
    const vendorId = getElement("vendorId").value.trim();
    console.log("form submit");

    if (form.checkValidity()) {
      const url = baseUrl + "vendorManagement/save-vendorForm";
      const submitButton = document.querySelector('button[type="submit"]');
      $.ajax({
        type: "POST",
        url: url, // Change this to your form submission URL
        data: new FormData(form),
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.success) {
            if (vendorId != 0) {
              // Redirect to home page on success
              window.location.href =
                baseUrl + "vendorManagement/view-vendorDetails/" + vendorId; // Change to your actual home URL
            } else {
              // Redirect to home page on success
              window.location.href = baseUrl + "vendorManagement"; // Change to your actual home URL
            }
          } else {
            // Show error messages returned from the server
            submitButton.insertAdjacentHTML(
              "beforebegin",
              '<div id="globalError" class="error-message">' +
                response.errors.join("<br>") +
                "</div>"
            );
          }
        },
        error: function () {
          showToast({
            title: "Vendor",
            message: "Form submission failed.",
            type: "error",
          });
        },
      });
    }
    form.classList.add("was-validated");
  });
  document.getElementById("postalCode").addEventListener("input", function (e) {
    let input = this.value.replace(/\D/g, ""); // Remove non-numeric characters
    this.value = input;

    if (input.length === 6) {
      fetch(`https://api.postalpincode.in/pincode/${input}`)
        .then((response) => response.json())
        .then((data) => {
          if (data[0]?.Status === "Success" && data[0].PostOffice.length > 0) {
            let firstPostOffice = data[0].PostOffice[0];
            let stateField = document.getElementById("state");
            let cityField = document.getElementById("city");

            stateField.value = firstPostOffice.State;
            cityField.value = firstPostOffice.District;

            stateField.readOnly = true;
            cityField.readOnly = true;
          } else {
            enableFields();
          }
        })
        .catch((error) => {
          enableFields();
        });
    }
  });

  function enableFields() {
    let stateField = document.getElementById("state");
    let cityField = document.getElementById("city");

    stateField.value = "";
    cityField.value = "";

    stateField.readOnly = false;
    cityField.readOnly = false;
  }
});
