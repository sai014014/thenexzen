document.addEventListener("DOMContentLoaded", function () {
  let currentStep = 1;
  let userEmail = ""; // Renamed from 'email' for clarity

  // Show the active step
  function showStep(step) {
    // Hide all steps
    getElements(".step").forEach((el) => el.classList.remove("active"));

    const stepElement = getElement(`step${step}Form`);
    if (stepElement) {
      stepElement.classList.add("active");
    } else {
      console.error(`Step element with ID step${step}Form not found`);
    }
  }

  // Handle Next Button for Step 1
  const nextStep1Button = getElement("nextStep1");
  nextStep1Button?.addEventListener("click", async function () {
    const step1Form = getElement("step1Form");

    if (step1Form.checkValidity()) {
      const formData = new FormData(step1Form);
      formData.append("currentStep", 1); // Add step parameter
      userEmail = formData.get("email"); // Extract email

      try {
        const response = await fetch(baseUrl + "saveRegistration", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(formData), // Send the entire form data
        });

        const data = await response.json();
        if (data.success) {
          if (data.status == 3 || data.status == 4) {
            currentStep = data.status;
          } else {
            currentStep = 2;
          }
          showStep(currentStep);
        } else {
          console.error("OTP send failed:", data.error);
          getElement("errorMessage").textContent = data.message;
          getElement("errorMessage").style.display = "block";
        }
      } catch (error) {
        console.error("Error sending OTP:", error);
        getElement("errorMessage").textContent =
          "An error occurred, please try again later.";
        getElement("errorMessage").style.display = "block";
      }
      hideErrorMessage();
    } else {
      step1Form.classList.add("was-validated");
    }
  });

  // Handle OTP verification
  const verifyOtpButton = getElement("verifyOtp");
  verifyOtpButton?.addEventListener("click", async function () {
    const otpInput = getElement("otp");
    const otp = otpInput.value;

    if (!userEmail) {
      userEmail = getElement("#step1Form input[name='email']").value;
    }

    if (otp.length === 6) {
      try {
        const response = await fetch(baseUrl + "validateRegistrationEmailOtp", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({ email: userEmail, otp }),
        });

        const data = await response.json();
        if (data.success) {
          currentStep = 3;
          showStep(currentStep);
        } else {
          console.error("OTP verification failed:", data.error);
          getElement("otpError").textContent = data.error;
          getElement("otpError").style.display = "block";
        }
      } catch (error) {
        console.error("Error verifying OTP:", error);
      }
    } else {
      otpInput.setCustomValidity("Please enter a valid 6-digit OTP.");
      otpInput.reportValidity();
    }
  });

  // Handle Next Button for Step 3
  const nextStep2Button = getElement("nextStep2");
  nextStep2Button?.addEventListener("click", async function () {
    const step3Form = getElement("step3Form");

    if (step3Form.checkValidity()) {
      const formData = new FormData(step3Form);
      formData.append("currentStep", 3);

      if (!userEmail) {
        currentStep = 1;
        showStep(currentStep); // Move to Step 1
        return;
      }

      formData.append("email", userEmail);

      try {
        const response = await fetch(baseUrl + "saveRegistration", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(formData),
        });

        const data = await response.json();
        if (data.success) {
          currentStep = 4;
          showStep(currentStep);
        } else {
          console.error("Step 3 update failed:", data.error);
          getElement("errorMessage").textContent = data.error;
          getElement("errorMessage").style.display = "block";
          hideErrorMessage();
        }
      } catch (error) {
        console.error("Error updating Step 2 data:", error);
      }
    } else {
      step3Form.classList.add("was-validated");
    }
  });

  // Handle Previous Buttons
  getElement("prevStep2")?.addEventListener("click", () => showStep(1));
  getElement("prevStep3")?.addEventListener("click", () => showStep(2));

  // Password Validation for Step 3
  const passwordField = getElement("password");
  const confirmPasswordField = getElement("confirm_password");
  const submitButton = getElement("step4Form")?.querySelector(
    "button[type='submit']"
  );
  const confirmPasswordMatch = getElement("confirmPasswordMatch");

  // Regex patterns for password validation
  const passwordCriteria = {
    capitalRegex: /[A-Z]/,
    smallRegex: /[a-z]/,
    numberRegex: /[0-9]/,
    specialCharRegex: /[.,\-_@#$&*()]/,
    noSpaceRegex: /^\S+$/,
  };

  // Password validation logic
  passwordField.addEventListener("input", function () {
    validatePassword(passwordField.value);
    checkFormValidity();
  });

  confirmPasswordField.addEventListener("input", function () {
    if (confirmPasswordField.value === passwordField.value) {
      confirmPasswordMatch.textContent = "Passwords match!";
      confirmPasswordMatch.classList.remove("text-danger");
      confirmPasswordMatch.classList.add("text-success");
    } else {
      confirmPasswordMatch.textContent = "Passwords do not match";
      confirmPasswordMatch.classList.remove("text-success");
      confirmPasswordMatch.classList.add("text-danger");
    }
    confirmPasswordMatch.style.display = "block";
    checkFormValidity();
  });

  // Validate password against criteria
  function validatePassword(password) {
    Object.entries(passwordCriteria).forEach(([key, regex]) => {
      toggleValidation(
        getElement(key.replace("Regex", "Check")),
        regex.test(password)
      );
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

  // Final form submission (Step 4)
  const step4Form = getElement("step4Form");
  step4Form?.addEventListener("submit", async function (e) {
    e.preventDefault();
    const form = this;

    if (form.checkValidity()) {
      const formData = new FormData(form);
      formData.append("currentStep", 4); // Add step parameter

      if (!userEmail) {
        currentStep = 1;
        showStep(currentStep); // Move to Step 1
        return;
      }

      formData.append("email", userEmail);

      try {
        const response = await fetch(baseUrl + "saveRegistration", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams(formData),
        });

        const data = await response.json();
        if (data.success) {
          getElement("successMessage").textContent =
            "Account created successfully! Redirecting...";
          getElement("successMessage").style.display = "block";
          setTimeout(() => (window.location.href = baseUrl + "login"), 2000);
        } else {
          console.error("Registration failed:", data.error);
          getElement("errorMessage").textContent = data.error;
          getElement("errorMessage").style.display = "block";
        }
      } catch (error) {
        console.error("Error during registration:", error);
        getElement("errorMessage").textContent =
          "An error occurred, please try again later.";
        getElement("errorMessage").style.display = "block";
      }
      hideErrorMessage();
    } else {
      form.classList.add("was-validated"); // Apply validation styles
    }
  });

  function hideErrorMessage() {
    setTimeout(() => {
      getElement("errorMessage").style.display = "none";
    }, 3000);
  }

  document.getElementById("postcode").addEventListener("input", function (e) {
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
