(() => {
  "use strict";
  const form = document.querySelector("#businessForm");
  const newPassword = document.querySelector("#newPassword");
  const confirmPassword = document.querySelector("#confirmPassword");

  form.addEventListener("submit", async function (event) {
    event.preventDefault();
    event.stopPropagation();

    // Reset previous custom validation
    confirmPassword.setCustomValidity("");

    // Match check
    if (newPassword.value !== confirmPassword.value) {
      confirmPassword.setCustomValidity("Passwords do not match");
    }

    // Add Bootstrap validation class
    form.classList.add("was-validated");

    if (!form.checkValidity()) {
      return;
    }

    const formData = new FormData(form);

    try {
      const response = await fetch(form.action, {
        method: form.method || "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error("Network response was not ok");
      }

      const data = await response.json();

      showToast({
        title: "Account Password",
        message: data.message,
        type: data.success ? "success" : "error",
      });
    } catch (error) {
      console.error("Form submission failed:", error);
    }
  });
})();
document.querySelectorAll(".toggle-password").forEach(function (icon) {
  icon.addEventListener("click", function () {
    const targetId = icon.getAttribute("data-target");
    const targetInput = document.getElementById(targetId);
    const isPassword = targetInput.type === "password";
    targetInput.type = isPassword ? "text" : "password";
    icon.classList.toggle("bi-eye");
    icon.classList.toggle("bi-eye-slash");
  });
});
