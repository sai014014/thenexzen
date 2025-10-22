document
  .getElementById("profileDropdown")
  .addEventListener("click", function () {
    const dropdown = document.getElementById("dropdownMenu");
    dropdown.style.display =
      dropdown.style.display === "block" ? "none" : "block";
  });

// Close dropdown if clicked outside
window.addEventListener("click", function (event) {
  if (!event.target.matches("#profileDropdown")) {
    document.getElementById("dropdownMenu").style.display = "none";
  }
});
