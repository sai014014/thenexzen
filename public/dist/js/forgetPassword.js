document.addEventListener("DOMContentLoaded", () => {
  const emailForm = document.getElementById("emailForm");
  const emailInput = document.getElementById("email");
  const errorMessage = document.getElementById("errorMessage");
  const step1 = document.getElementById("step1");
  const step2 = document.getElementById("step2");
  const step3 = document.getElementById("step3");
  const resendOTPButton = document.getElementById("resendOTP");
  const otpForm = document.getElementById("otpForm");
  const otpInput = document.getElementById("otp");
  const passwordForm = document.getElementById("passwordForm");
  const passwordField = document.getElementById("password");
  const confirmPasswordField = document.getElementById("confirm_password");
  const submitButton = passwordForm.querySelector("button[type='submit']");
  const passwordCriteria = {
    capitalRegex: /[A-Z]/,
    smallRegex: /[a-z]/,
    numberRegex: /[0-9]/,
    specialCharRegex: /[.,\-_@#$&*()]/,
    noSpaceRegex: /^\S+$/,
  };
  let email = "";

  emailForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    if (!emailForm.checkValidity()) {
      emailForm.classList.add("was-validated");
      return;
    }

    // Validate form input
    if (!emailInput.value.trim()) {
      errorMessage.textContent = "Please enter a valid email address.";
      errorMessage.style.display = "block";
      return;
    }

    // Clear previous error messages
    errorMessage.style.display = "none";

    // Collect form data
    email = emailInput.value.trim(); // Trim email for safety

    try {
      // Send POST request using Fetch API
      const response = await fetch(baseUrl + "forget-password-sendOtp", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          email: email,
        }), // URL-encoded format
      });

      const data = await response.json();
      if (data.success) {
        // Transition to the next step
        step1.classList.remove("active");
        step2.classList.add("active");
      } else {
        errorMessage.textContent =
          data.message || "An error occurred. Please try again.";
        errorMessage.style.display = "block";
        if (data.status == 3) {
          setTimeout(() => {
            window.location.href = `${baseUrl}register`;
          }, GLOBAL_ERROR_TIMEOUT);
        }
      }
    } catch (error) {
      console.error("Error sending OTP:", error);
      errorMessage.textContent =
        "Something went wrong. Please try again later.";
      errorMessage.style.display = "block";
    }
  });

  //   verify otp
  if (resendOTPButton) {
    resendOTPButton.addEventListener("click", (e) => {
      e.preventDefault();

      // Validate the email field before triggering the submit
      if (!emailInput.value.trim()) {
        errorMessage.textContent = "Please enter a valid email address.";
        errorMessage.style.display = "block";
        return;
      }

      // Clear the error message if validation passes
      errorMessage.style.display = "none";

      // Trigger the submit event programmatically
      const submitEvent = new Event("submit", {
        bubbles: true,
        cancelable: true,
      });
      emailForm.dispatchEvent(submitEvent); // Dispatch the submit event
    });
  }

  otpForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    // Validate form input
    if (!otpForm.checkValidity()) {
      otpForm.classList.add("was-validated");
      return;
    }

    // Clear previous error messages
    errorMessage.style.display = "none";

    try {
      // Send POST request using Fetch API
      const response = await fetch(baseUrl + "forget-password-validateOtp", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          email: email,
          otp: otpInput.value.trim(),
        }), // URL-encoded format
      });

      const data = await response.json();
      if (data.success) {
        // Transition to the next step
        step2.classList.remove("active");
        step3.classList.add("active");
      } else {
        errorMessage.textContent =
          data.error || "Invalid OTP. Please try again.";
        errorMessage.style.display = "block";
      }
    } catch (error) {
      console.error("Error verifying OTP:", error);
      errorMessage.textContent =
        "Something went wrong. Please try again later.";
      errorMessage.style.display = "block";
    }
  });

  //   password
  // Validate password on input
  passwordField.addEventListener("input", () => {
    validatePassword(passwordField.value);
    checkFormValidity();
  });

  confirmPasswordField.addEventListener("input", () => {
    const confirmFeedback = confirmPasswordField.nextElementSibling; // Assuming the feedback is right after the confirm input
    if (confirmPasswordField.value === passwordField.value) {
      confirmFeedback.textContent = "Passwords match!";
      confirmFeedback.classList.remove("invalid-feedback");
      confirmFeedback.classList.add("valid-feedback");
    } else {
      confirmFeedback.textContent = "Passwords do not match.";
      confirmFeedback.classList.remove("valid-feedback");
      confirmFeedback.classList.add("invalid-feedback");
    }
    confirmFeedback.style.display = "block";
    checkFormValidity();
  });

  // Password form submission
  passwordForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    // Add Bootstrap's validation class
    passwordForm.classList.add("was-validated");

    // Check if the form is valid according to Bootstrap validation
    if (!passwordForm.checkValidity()) {
      return; // Stop submission if the form is invalid
    }

    const password = passwordField.value.trim();
    const confirmPassword = confirmPasswordField.value.trim();

    // Validate password criteria
    const isValid = Object.values(passwordCriteria).every((regex) =>
      regex.test(password)
    );
    if (!isValid) {
      errorMessage.textContent = "Please meet all password requirements.";
      errorMessage.style.display = "block";
      return;
    }

    // Check if passwords match
    if (password !== confirmPassword) {
      confirmPasswordField.classList.add("is-invalid");
      return;
    }

    try {
      const response = await fetch(baseUrl + "forget-password-updatePassword", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          email: email, // Assume `email` is already defined in scope
          password: password,
        }),
      });

      const data = await response.json();
      if (data.success) {
        window.location.href = baseUrl + "login";
      } else {
        errorMessage.textContent = data.error || "An error occurred.";
        errorMessage.style.display = "block";
      }
    } catch (error) {
      console.error("Error resetting password:", error);
      errorMessage.textContent = "Something went wrong. Please try again.";
      errorMessage.style.display = "block";
    }
  });

  // Validate password against criteria
  function validatePassword(password) {
    Object.entries(passwordCriteria).forEach(([key, regex]) => {
      const checkElement = document.getElementById(
        key.replace("Regex", "Check")
      );
      toggleValidation(checkElement, regex.test(password));
    });
  }

  // Toggle validation styling
  function toggleValidation(element, isValid) {
    element.classList.toggle("valid", isValid);
    element.classList.toggle("invalid", !isValid);
  }

  // Enable or disable submit button based on form validity
  function checkFormValidity() {
    const isPasswordValid = Object.values(passwordCriteria).every((regex) =>
      regex.test(passwordField.value)
    );
    const isConfirmPasswordValid =
      confirmPasswordField.value === passwordField.value &&
      confirmPasswordField.value !== "";

    submitButton.disabled = !(isPasswordValid && isConfirmPasswordValid);
  }
});
