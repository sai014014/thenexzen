if (typeof GLOBAL_ERROR_TIMEOUT == "undefined") {
  GLOBAL_ERROR_TIMEOUT = 3000;
}
window.onload = function () {
  const viewFullScreenButton = document.getElementById("viewFullScreen");

  function openFullscreen() {
    if (document.documentElement.requestFullscreen) {
      document.documentElement.requestFullscreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      // Firefox
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullscreen) {
      // Chrome, Safari and Opera
      document.documentElement.webkitRequestFullscreen();
    } else if (document.documentElement.msRequestFullscreen) {
      // IE/Edge
      document.documentElement.msRequestFullscreen();
    }
  }

  function closeFullscreen() {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.mozCancelFullScreen) {
      // Firefox
      document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
      // Chrome, Safari and Opera
      document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) {
      // IE/Edge
      document.msExitFullscreen();
    }
  }

  if (viewFullScreenButton) {
    viewFullScreenButton.addEventListener("click", () => {
      if (!document.fullscreenElement) {
        openFullscreen();
      } else {
        closeFullscreen();
      }
    });
  }
};
/**
 * Function to add event listeners to input fields with class inputFieldMobileNumberOnly
 * to only allow numeric characters.
 */
function commonFormFunctions() {
  const inputFieldMobileNumberOnly = document.getElementsByClassName(
    "inputFieldMobileNumberOnly"
  );

  /**
   * Loop through the elements and add an event listener to each one.
   */
  Array.prototype.forEach.call(inputFieldMobileNumberOnly, function (element) {
    element.addEventListener("input", function (e) {
      /**
       * Replace any non-numeric characters with an empty string
       */
      e.target.value = e.target.value.replace(/\D/g, "");
    });
  });
}

window.addEventListener("load", commonFormFunctions);

// Create a global variable to track active AJAX/fetch requests
let activeRequests = 0;

/**
 * Show the loader when an AJAX request is initiated
 */
function showLoader() {
  /**
   * Increment the active requests counter
   */
  activeRequests++;
  /**
   * Show the loader
   */
  const loader = document.getElementById("loader");
  if (loader) {
    loader.style.display = "flex";
  }
}

/**
 * Hide the loader when an AJAX request is completed
 */
function hideLoader() {
  /**
   * Decrement the active requests counter
   */
  activeRequests--;

  /**
   * Hide the loader if there are no active requests left
   */
  if (activeRequests <= 0) {
    const loader = document.getElementById("loader");
    if (loader) {
      loader.style.display = "none";
    }
  }
}

// Intercept AJAX calls and modify them to show/hide the loader
$(document).ready(function () {
  // Global event for starting an AJAX request
  $(document).ajaxStart(function () {
    showLoader(); // Call function to show loader
  });

  // Global event for completing an AJAX request
  $(document).ajaxStop(function () {
    hideLoader(); // Call function to hide loader
  });
});

// Intercept fetch calls and modify them to show/hide the loader
const originalFetch = window.fetch;
/**
 * Intercept fetch calls and modify them to show/hide the loader
 * @param {string|Request} input The request URL or a Request object
 * @param {RequestInit} [init] The request init object
 * @returns {Promise<Response>} The response promise
 * @description This function intercepts fetch calls and shows the loader when the request is
 *              being sent. When the request is completed, the loader is hidden.
 *              This makes it easier to handle loading states in the application.
 */
window.fetch = function (input, init = {}) {
  /**
   * Show the loader
   */
  showLoader();

  /**
   * Call the original fetch function with the given input and init object
   * @returns {Promise<Response>} The response promise
   */
  return (
    originalFetch(input, init)
      /**
       * When the request is completed, hide the loader
       * @param {Response} response The response object
       * @returns {Promise<Response>} The response promise
       */
      .then((response) => {
        hideLoader();
        return response;
      })
      /**
       * If there is an error, hide the loader and throw the error
       * @param {*} error The error object
       * @throws {*} The error object
       */
      .catch((error) => {
        hideLoader();
        throw error;
      })
  );
};

// Function to handle global error messages
function handleErrorByHiding() {
  const errorMessage = document.getElementById("errorMessage");

  if (errorMessage && errorMessage.innerHTML.trim() !== "") {
    const errorMessageTimeout = GLOBAL_ERROR_TIMEOUT
      ? GLOBAL_ERROR_TIMEOUT
      : 3000;

    if (!errorMessage.dataset.timestamp) {
      // Add a timestamp if not already present
      errorMessage.dataset.timestamp = Date.now().toString();
    }

    // Calculate the elapsed time
    const elapsedTime =
      Date.now() - parseInt(errorMessage.dataset.timestamp, 10);

    // Remove the errorMessage element if the elapsed time exceeds the timeout
    if (elapsedTime >= errorMessageTimeout) {
      errorMessage.innerHTML = "";
      errorMessage.style.display = "none";
      delete errorMessage.dataset.timestamp; // Reset the timestamp
    } else {
      // Keep displaying the error message
      errorMessage.style.display = "block";
    }
  }
}

// Function to handle global error messages
function handleGlobalError() {
  const globalError = document.getElementById("globalError");

  if (globalError && globalError.innerHTML.trim() !== "") {
    const globalErrorTimeout = GLOBAL_ERROR_TIMEOUT ?? 3000;

    if (!globalError.dataset.timestamp) {
      // Add a timestamp if not already present
      globalError.dataset.timestamp = Date.now().toString();
    } else {
      // Calculate the elapsed time
      const elapsedTime =
        Date.now() - parseInt(globalError.dataset.timestamp, 10);

      // Remove the globalError element if the elapsed time exceeds the timeout
      if (elapsedTime >= globalErrorTimeout) {
        globalError.remove();
      }
    }
  }
}

// Function to handle error messages
function handleErrorMessages() {
  const errorMessages = document.querySelectorAll(".error-message");

  const globalErrorTimeout = GLOBAL_ERROR_TIMEOUT ?? 3000;

  errorMessages.forEach((errorMessage) => {
    // Check if the errorMessage has content to display
    if (errorMessage.innerHTML.trim() !== "") {
      if (!errorMessage.dataset.timestamp) {
        // Add a timestamp if not already present
        errorMessage.dataset.timestamp = Date.now().toString();
      } else {
        // Calculate the elapsed time
        const elapsedTime =
          Date.now() - parseInt(errorMessage.dataset.timestamp, 10);

        // Clear innerHTML only if the elapsed time exceeds GLOBAL_ERROR_TIMEOUT
        if (elapsedTime >= globalErrorTimeout) {
          errorMessage.innerHTML = "";
          errorMessage.removeAttribute("data-timestamp"); // Reset timestamp for reuse
        }
      }
    }
  });
}

// Function to handle global error messages
function handleTemporaryError() {
  const tempErrorMessageElements = document.querySelectorAll(
    ".temporary-error-message"
  );
  const globalErrorTimeout = GLOBAL_ERROR_TIMEOUT ?? 3000;

  tempErrorMessageElements.forEach((errorMessageElement) => {
    if (errorMessageElement.textContent.trim() !== "") {
      if (!errorMessageElement.dataset.timestamp) {
        // Add a timestamp if not already present
        errorMessageElement.dataset.timestamp = Date.now().toString();
      } else {
        // Calculate the elapsed time
        const elapsedTime =
          Date.now() - parseInt(errorMessageElement.dataset.timestamp, 10);

        // Clear innerHTML only if the elapsed time exceeds GLOBAL_ERROR_TIMEOUT
        if (elapsedTime >= globalErrorTimeout) {
          errorMessageElement.remove();
        }
      }
    } else {
      errorMessageElement.remove();
    }
  });
}
// Function to handle global error messages
function handleTemporarySuccess() {
  const tempErrorMessageElements = document.querySelectorAll(
    ".temporary-success-message"
  );
  const globalErrorTimeout = GLOBAL_ERROR_TIMEOUT ?? 3000;

  tempErrorMessageElements.forEach((errorMessageElement) => {
    if (errorMessageElement.textContent.trim() !== "") {
      if (!errorMessageElement.dataset.timestamp) {
        // Add a timestamp if not already present
        errorMessageElement.dataset.timestamp = Date.now().toString();
      } else {
        // Calculate the elapsed time
        const elapsedTime =
          Date.now() - parseInt(errorMessageElement.dataset.timestamp, 10);

        // Clear innerHTML only if the elapsed time exceeds GLOBAL_ERROR_TIMEOUT
        if (elapsedTime >= globalErrorTimeout) {
          errorMessageElement.remove();
        }
      }
    } else {
      errorMessageElement.remove();
    }
  });
}
function createTemporaryError(element, label) {
  // Add error div after the parent of the selector
  const parentElement = element.parentElement;
  const errorDiv = document.createElement("div");
  errorDiv.className = "temporary-error-message";
  errorDiv.textContent = label;
  parentElement.insertAdjacentElement("afterend", errorDiv);
  element.focus();
}
// Set up intervals to run the functions every 1 second
setInterval(handleErrorByHiding, 1000);
setInterval(handleGlobalError, 1000);
setInterval(handleErrorMessages, 1000);
setInterval(handleTemporaryError, 1000);

function scrollToErrorElement(errorElement, offset = 0) {
  if (errorElement) {
    // Ensure the element is visible before scrolling
    errorElement.style.display = "block";

    const headerHeight = document.querySelector(".header")?.offsetHeight || 0;
    const totalOffset = headerHeight + offset;

    // Calculate the position to scroll to (taking sticky header into account)
    const elementTop =
      errorElement.getBoundingClientRect().top + window.scrollY - totalOffset;

    window.scrollTo({
      top: elementTop,
      behavior: "smooth",
    });

    // Optionally hide the element after a delay
    setTimeout(
      () => {
        errorElement.style.display = "none";
      },
      typeof GLOBAL_ERROR_TIMEOUT !== "undefined" ? GLOBAL_ERROR_TIMEOUT : 3000
    );
  }
}

const setInnerHTML = (element, content) => {
  if (element) element.innerHTML = content || "";
};
const setValue = (element, content) => {
  if (element) element.value = content || "";
};

const setTextContent = (element, content) => {
  if (element) element.textContent = content || "";
};

const toggleDisplay = (element, shouldDisplay) => {
  if (element) element.style.display = shouldDisplay ? "block" : "none";
};
const toggleDisplayFlex = (element, shouldDisplay) => {
  if (element) element.style.display = shouldDisplay ? "flex" : "none";
};

const fetchJsonData = async (url) => {
  try {
    const response = await fetch(url);
    return response.ok ? response.json() : Promise.reject("Fetch failed");
  } catch (error) {
    console.error("Error fetching data:", error);
    return null;
  }
};
const fetchTextData = async (url) => {
  try {
    const response = await fetch(url);
    return response.ok ? response.text() : Promise.reject("Fetch failed");
  } catch (error) {
    console.error("Error fetching data:", error);
    return null;
  }
};
// Helper function to safely get elements
const getElement = (id) => document.getElementById(id);
const getElements = (selector) =>
  Array.from(document.querySelectorAll(selector));

/**
 * Loads and executes scripts from a set of HTML script tags
 * @function
 * @param {NodeListOf<HTMLScriptElement>} scripts - Scripts to load
 * @returns {Promise<void[]>} A promise resolving when all scripts are loaded
 */
const loadScriptsFromHTML = (scripts) =>
  Promise.all(
    Array.from(scripts).map((script) =>
      script.src
        ? new Promise((resolve, reject) => {
            const newScript = document.createElement("script");
            newScript.src = script.src;
            newScript.onload = resolve;
            newScript.onerror = reject;
            document.head.appendChild(newScript);
          })
        : Promise.resolve().then(() => eval(script.textContent))
    )
  );

const numToFloat = (num) => parseFloat(num).toFixed(2);

const convertCurrencyFormat = (data, currencyFlag = 0) => {
  let number = 0;
  if (currencyFlag) {
    number = CURRENCY_SYMBOL + numToFloat(data ?? 0);
  } else {
    number = numToFloat(data ?? 0);
  }

  return number;
};
/**
 * Copies the given text to the clipboard.
 * @param {string} text - The text to copy.
 */
function copyToClipboard(text, iconElement) {
  if (!text) {
    console.warn("No text provided to copy!");
    return;
  }

  // Create a temporary textarea element
  const textarea = document.createElement("textarea");
  textarea.value = text;
  document.body.appendChild(textarea);

  // Select and copy the text
  textarea.select();
  document.execCommand("copy");

  // Remove the textarea
  document.body.removeChild(textarea);

  // Change the SVG to a checkmark icon
  const originalSVG = iconElement.innerHTML; // Store original SVG
  iconElement.innerHTML = `
    <svg width="13" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M6 11.172L3.414 8.586L2 10L6 14L14 6L12.586 4.586L6 11.172Z" fill="#5D5FEF"/>
    </svg>`;

  // Restore the original SVG after 3 seconds
  setTimeout(() => {
    iconElement.innerHTML = originalSVG;
  }, 3000);

  // Optionally, provide feedback
  console.log("Copied to clipboard:", text);
}

/**
 * Opens an image or PDF file in a popup on the right half of the webpage.
 * @param {string} filePath - The path to the image or PDF.
 */
function openFilePopup(filePath, fileName, name) {
  const popup = document.querySelector("#filePopup");
  const popupContent = popup.querySelector("#popupContent");
  const downloadBtn = popup.querySelector("#fileDownloadBtn");
  const popupTitle = popup.querySelector(".popup-title");

  if (!popup || !popupContent || !downloadBtn) {
    console.error("Popup elements not found in the DOM.");
    return;
  }

  // Determine file type
  const fileExtension = filePath.split(".").pop().toLowerCase();
  let contentHtml = "";

  if (["jpg", "jpeg", "png", "gif", "webp"].includes(fileExtension)) {
    // Image File
    contentHtml = `<img src="${filePath}" alt="Preview" class="popup-image">`;
  } else if (fileExtension === "pdf") {
    // PDF File
    contentHtml = `<embed src="${filePath}" type="application/pdf" class="popup-embed">`;
  } else {
    contentHtml = `<p>Unsupported file format</p>`;
  }

  popupContent.innerHTML = contentHtml;
  popup.style.display = "block"; // Show the popup

  // Directly assign the file URL for download
  downloadBtn.href = filePath;
  downloadBtn.download = fileName;
  downloadBtn.style.display = "inline-block";

  popupTitle.innerHTML = name;
}

/**
 * Closes the popup.
 */
function closePopup() {
  document.getElementById("filePopup").style.display = "none";
}
function showToast({
  title = "Notification",
  message = "",
  type = "info",
  time = null,
}) {
  // Map types to Bootstrap colors and icons
  const typeConfig = {
    info: { bgClass: "primary", icon: "info-circle-fill" },
    success: { bgClass: "success", icon: "check-circle-fill" },
    warning: { bgClass: "warning", icon: "exclamation-triangle-fill" },
    error: { bgClass: "danger", icon: "exclamation-circle-fill" },
  };

  // Use the mapped config or default to info
  const config = typeConfig[type] || typeConfig.info;

  let toastContainer = document.getElementById("toastContainer");

  // Create toast container if it doesn't exist
  if (!toastContainer) {
    toastContainer = document.createElement("div");
    toastContainer.id = "toastContainer";
    toastContainer.style.position = "fixed";
    toastContainer.style.bottom = "20px";
    toastContainer.style.right = "20px";
    toastContainer.style.zIndex = "1050";
    document.body.appendChild(toastContainer);
  }

  // Create the toast element
  const toast = document.createElement("div");
  toast.className = `toast mb-3 overflow-hidden border-0 shadow-lg`;
  toast.style.minWidth = "400px"; // Increased fixed width
  toast.style.maxWidth = "450px"; // Maximum width
  toast.setAttribute("role", "alert");
  toast.setAttribute("aria-live", "assertive");
  toast.setAttribute("aria-atomic", "true");

  // Format time if provided
  let timeStr = "";
  if (time) {
    if (typeof time === "string") {
      timeStr = time;
    } else if (time instanceof Date) {
      timeStr = time.toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
      });
    }
  }

  // Toast header with properly positioned elements
  const header = document.createElement("div");
  header.className = `toast-header bg-${config.bgClass} text-white py-2 d-flex align-items-center`;

  // Icon
  const iconSpan = document.createElement("span");
  iconSpan.className = `me-2`;
  iconSpan.innerHTML = `<i class="bi bi-${config.icon}"></i>`;

  // Title
  const titleSpan = document.createElement("strong");
  titleSpan.className = "me-auto";
  titleSpan.textContent = title;

  // Time (with specific styling to prevent wrapping)
  const timeSpan = document.createElement("small");
  if (timeStr) {
    timeSpan.className = "mx-2 text-nowrap";
    timeSpan.textContent = timeStr;
  }

  // Close button
  const closeButton = document.createElement("button");
  closeButton.type = "button";
  closeButton.className = "btn-close btn-close-white";
  closeButton.setAttribute("data-bs-dismiss", "toast");
  closeButton.setAttribute("aria-label", "Close");

  // Assemble header
  header.appendChild(iconSpan);
  header.appendChild(titleSpan);
  if (timeStr) {
    header.appendChild(timeSpan);
  }
  header.appendChild(closeButton);

  // Toast body
  const body = document.createElement("div");
  body.className = "toast-body py-3";
  body.textContent = message;

  // Assemble toast
  toast.appendChild(header);
  toast.appendChild(body);

  // Add progress bar for auto-dismiss visualization
  const progressBar = document.createElement("div");
  progressBar.className = `progress rounded-0 bg-transparent`;
  progressBar.style.height = "4px";

  const progressBarInner = document.createElement("div");
  progressBarInner.className = `progress-bar bg-${config.bgClass}`;
  progressBarInner.style.width = "100%";
  progressBarInner.style.transition = "width 5s linear";

  progressBar.appendChild(progressBarInner);
  toast.appendChild(progressBar);

  // Append toast to container
  toastContainer.appendChild(toast);

  // Initialize and show using Bootstrap's API
  const toastInstance = new bootstrap.Toast(toast, {
    delay: 5000,
    autohide: true,
  });

  toastInstance.show();

  // Start progress bar animation
  setTimeout(() => {
    progressBarInner.style.width = "0%";
  }, 100);

  // Auto-remove when hidden
  toast.addEventListener("hidden.bs.toast", () => {
    toast.remove();
  });

  // Return the toast instance in case the caller wants to control it
  return toastInstance;
}
function timeDifferenceAgo(dateTime) {
  // Convert input date to a timestamp
  const timestamp = new Date(dateTime).getTime();
  const difference = Math.floor((Date.now() - timestamp) / 1000); // Difference in seconds

  // Determine time unit and prepare the result
  if (difference >= 31536000) {
    // More than or equal to 1 year
    const years = Math.floor(difference / 31536000);
    return `${years} year${years > 1 ? "s" : ""} ago`;
  } else if (difference >= 2592000) {
    // More than or equal to 1 month
    const months = Math.floor(difference / 2592000);
    return `${months} month${months > 1 ? "s" : ""} ago`;
  } else if (difference >= 604800) {
    // More than or equal to 1 week
    const weeks = Math.floor(difference / 604800);
    return `${weeks} week${weeks > 1 ? "s" : ""} ago`;
  } else if (difference >= 86400) {
    // More than or equal to 1 day
    const days = Math.floor(difference / 86400);
    return `${days} day${days > 1 ? "s" : ""} ago`;
  } else if (difference >= 3600) {
    // More than or equal to 1 hour
    const hours = Math.floor(difference / 3600);
    return `${hours} hour${hours > 1 ? "s" : ""} ago`;
  } else if (difference >= 60) {
    // More than or equal to 1 minute
    const minutes = Math.floor(difference / 60);
    return `${minutes} minute${minutes > 1 ? "s" : ""} ago`;
  } else {
    // Less than 1 minute
    return `${difference} second${difference !== 1 ? "s" : ""} ago`;
  }
}
