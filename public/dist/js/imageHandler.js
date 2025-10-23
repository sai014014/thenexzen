function handleFileUpload(input) {
  console.log("File input triggered:", input.files);

  const allowedTypes = [
    "image/jpeg",
    "image/jpg",
    "image/png",
    "image/webp",
    "application/pdf",
  ];
  const file = input.files[0];

  if (!file) return;

  console.log(
    `Original File - Name: ${file.name}, Type: ${
      file.type
    }, Size: ${formatBytes(file.size)}`
  );

  if (!allowedTypes.includes(file.type)) {
    showBootstrapAlert(
      "Only image (JPEG, JPG, PNG) and PDF files are allowed.",
      "Alert"
    );
    input.value = "";
    return;
  }

  if (file.size > 20 * 1024 * 1024) {
    // Reject files larger than 20MB
    showBootstrapAlert("File size should not exceed 20MB.", "Alert");
    input.value = "";
    return;
  }

  if (file.type === "application/pdf" && file.size > 2 * 1024 * 1024) {
    showBootstrapAlert("PDF file should be below 2MB.", "Alert");
    input.value = "";
    return;
  }

  // Handle image compression based on file size
  if (file.type.startsWith("image/")) {
    let quality = 0.9; // Default quality

    if (file.size > 5 * 1024 * 1024) quality = 0.6; // 5MB - 10MB (60% quality)
    if (file.size > 10 * 1024 * 1024) quality = 0.4; // 10MB - 20MB (40% quality)

    console.log(
      `Compressing image (original size: ${formatBytes(
        file.size
      )}) with quality: ${quality}`
    );

    compressImage(file, quality, (compressedBlob) => {
      console.log(`Compressed File Size: ${formatBytes(compressedBlob.size)}`);

      if (compressedBlob.size > 2 * 1024 * 1024) {
        console.warn("Image is still too large after compression.");
        showBootstrapAlert(
          "Image is too large even after compression.",
          "Alert"
        );
        input.value = "";
      } else {
        console.log(
          `Image successfully compressed. New size: ${formatBytes(
            compressedBlob.size
          )}`
        );
        const fileList = new DataTransfer();
        fileList.items.add(
          new File([compressedBlob], file.name, { type: file.type })
        );
        input.files = fileList.files;
      }
    });
  }
}

function compressImage(file, quality, callback) {
  const reader = new FileReader();
  reader.readAsDataURL(file);
  reader.onload = function (event) {
    const img = new Image();
    img.src = event.target.result;

    img.onload = function () {
      const maxWidth = 1920;
      const maxHeight = 1080;
      let width = img.width;
      let height = img.height;

      if (width > maxWidth || height > maxHeight) {
        const aspectRatio = width / height;
        if (width > height) {
          width = maxWidth;
          height = Math.round(maxWidth / aspectRatio);
        } else {
          height = maxHeight;
          width = Math.round(maxHeight * aspectRatio);
        }
      }

      const canvas = document.createElement("canvas");
      const ctx = canvas.getContext("2d");
      canvas.width = width;
      canvas.height = height;
      ctx.drawImage(img, 0, 0, width, height);

      canvas.toBlob(
        (blob) => {
          if (blob) {
            callback(blob);
          } else {
            console.error("Image compression failed.");
            showBootstrapAlert(
              "Error compressing image. Try a smaller file.",
              "Compression Failed"
            );
          }
        },
        "image/jpeg",
        quality
      );
    };

    img.onerror = function () {
      console.error("Error loading image file.");
      showBootstrapAlert("Invalid image format.", "Error");
    };
  };

  reader.onerror = function () {
    console.error("Error reading image file.");
    showBootstrapAlert("Failed to read file.", "Error");
  };
}

function formatBytes(bytes, decimals = 2) {
  if (bytes === 0) return "0 Bytes";
  const k = 1024;
  const dm = decimals < 0 ? 0 : decimals;
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + " " + sizes[i];
}

function showBootstrapAlert(message, title = "Alert") {
  let modalContainer = document.getElementById("dynamicBootstrapModal");

  if (!modalContainer) {
    const modalHTML = `
          <div class="modal fade" id="dynamicBootstrapModal" tabindex="-1" aria-labelledby="modalTitle">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title" id="modalTitle">${title}</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">${message}</div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div>
                  </div>
              </div>
          </div>
      `;
    document.body.insertAdjacentHTML("beforeend", modalHTML);
    modalContainer = document.getElementById("dynamicBootstrapModal");

    modalContainer.addEventListener("shown.bs.modal", function () {
      modalContainer.removeAttribute("aria-hidden");
      modalContainer.querySelector(".btn-close").focus();
    });

    modalContainer.addEventListener("hidden.bs.modal", function () {
      modalContainer.setAttribute("aria-hidden", "true");
      document.activeElement.blur();
    });
  } else {
    modalContainer.querySelector(".modal-title").innerText = title;
    modalContainer.querySelector(".modal-body").innerText = message;
  }

  let modalInstance = new bootstrap.Modal(modalContainer);
  modalInstance.show();
}
