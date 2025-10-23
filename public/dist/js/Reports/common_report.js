$(document).ready(function () {
  // Function to convert DataTable to plain HTML table and send via AJAX
  function getPlainHtmlTable() {
    // Get all the records from the DataTable (beyond pagination)
    const tableApi = $("#reportTable").DataTable(); // DataTable API instance
    const allData = tableApi.rows({ search: "applied" }).nodes(); // Get all row nodes

    // Clone the original table structure
    const plainTable = $("#reportTable").clone();

    // Clear the table body and repopulate it with all rows
    const tableBody = plainTable.find("tbody");
    tableBody.empty(); // Clear current rows

    // Iterate over all rows and append them to the table body
    allData.each(function (rowNode) {
      const clonedRow = $(rowNode).clone();

      // Remove all unwanted attributes except rowspan and colspan
      clonedRow.find("*").each(function () {
        const attrs = this.attributes;
        for (let i = attrs.length - 1; i >= 0; i--) {
          const attrName = attrs[i].name;
          if (attrName !== "rowspan" && attrName !== "colspan") {
            this.removeAttribute(attrName);
          }
        }
      });

      // Append the cleaned row to the table body
      tableBody.append(clonedRow);
    });

    // Remove unwanted attributes and classes from the table itself
    plainTable
      .removeAttr("id")
      .removeAttr("class")
      .removeAttr("style")
      .find("*")
      .removeAttr("class")
      .removeAttr("style");

    // Get the clean HTML content of the table
    const tableHtml = plainTable[0].outerHTML;
    console.log(tableHtml);

    return tableHtml;
  }

  // Export to Excel function
  function exportToExcel(email = "") {
    const tableHtml = getPlainHtmlTable(); // Get plain HTML table

    // Encode the HTML content to handle special characters
    const encodedHtml = encodeURIComponent(tableHtml);

    // Encrypt the encoded HTML (Base64 encode the encoded string)
    const encryptedHtml = btoa(encodedHtml); // Base64 encode the URI-encoded HTML content

    // Send the encrypted HTML to backend via AJAX
    $.ajax({
      url: "generate-report", // CodeIgniter route for backend
      type: "POST",
      data: { encryptedHtml: encryptedHtml, format: "excel", email: email },
      success: function (response) {
        console.log(response);

        // Check if the response contains the expected data
        if (response.encryptedFile && response.fileName) {
          const fileName = response.fileName || "report";
          const fileContent = atob(response.encryptedFile); // Decrypt the file content (base64 decode)

          // Create a Blob object for the Excel file
          const blob = new Blob(
            [
              new Uint8Array(
                fileContent.split("").map(function (c) {
                  return c.charCodeAt(0);
                })
              ),
            ],
            {
              type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            }
          );

          // Create a temporary download link
          const link = document.createElement("a");
          link.href = URL.createObjectURL(blob);
          link.download = fileName + ".xlsx"; // Set the filename for download
          link.click(); // Trigger the download
        } else {
          if (email && response.message) {
            showToast({
              title: "Report",
              message: response.message,
              type: "success",
            });
          } else {
            showToast({
              title: "Report",
              message: "Error: File not found.",
              type: "error",
            });
          }
        }
      },
      error: function () {
        showToast({
          title: "Report",
          message: "Error generating report.",
          type: "error",
        });
      },
    });
  }

  // Export to PDF function
  function exportToPdf(email = "") {
    const tableHtml = getPlainHtmlTable(); // Get plain HTML table

    // Encode the HTML content to handle special characters
    const encodedHtml = encodeURIComponent(tableHtml);

    // Encrypt the encoded HTML (Base64 encode the encoded string)
    const encryptedHtml = btoa(encodedHtml); // Base64 encode the URI-encoded HTML content

    // Send the encrypted HTML to backend via AJAX
    $.ajax({
      url: "generate-report", // CodeIgniter route for backend
      type: "POST",
      data: { encryptedHtml: encryptedHtml, format: "pdf", email: email },
      success: function (response) {

        if (response.encryptedFile && response.fileName) {
          const fileName = response.fileName || "report";
          const fileContent = atob(response.encryptedFile); // Decrypt the file content (base64 decode)

          // Create a Blob object for the PDF file
          const blob = new Blob(
            [
              new Uint8Array(
                fileContent.split("").map(function (c) {
                  return c.charCodeAt(0);
                })
              ),
            ],
            { type: "application/pdf" } // Correct MIME type for PDF
          );

          // Create a temporary download link
          const link = document.createElement("a");
          link.href = URL.createObjectURL(blob);
          link.download = fileName + ".pdf"; // Set the filename for download
          link.click(); // Trigger the download
        } else {
          showToast({
            title: "Report",
            message: "Error: File not found.",
            type: "error",
          });
        }
      },
      error: function () {
        showToast({
          title: "Report",
          message: "Error generating report.",
          type: "error",
        });
      },
    });
  }

  // Export Button
  $("#exportExcelData").on("click", function () {
    exportToExcel();
  });
  $("#exportPdfData").on("click", function () {
    exportToPdf();
  });

  // Send Button in Modal
  $("#sendEmail").on("click", function () {
    const email = $("#emailInput").val();
    const format = $('input[name="exportFormat"]:checked').val(); // Get selected format

    // Validate Email
    if (!validateEmail(email)) {
      alert("Please enter a valid email address.");
      return;
    }

    // Close the modal
    var emailModal = bootstrap.Modal.getInstance(getElement("emailModal"));
    emailModal.hide();

    // Trigger the export function (mocked for demonstration)
    exportDataToEmail(email, format);
  });

  // Email Validation Function
  function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  // Mock Export Function
  function exportDataToEmail(email, format) {
    if (format === "excel") {
      exportToExcel(email);
    } else if (format === "pdf") {
      exportToPdf(email);
    } else {
      alert("Please select a format to export.");
    }
  }
});
