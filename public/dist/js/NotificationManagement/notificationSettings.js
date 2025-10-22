document.addEventListener("DOMContentLoaded", () => {
  const notificationForm = getElement("notificationForm");
  const notificationTableBody = document.querySelector(
    "#notificationTable tbody"
  );

  // Function to populate the table with updated records
  async function fetchRecords() {
    try {
      notificationForm.style.display = "none";
      const response = await fetch("get-notification-settings-records");
      const records = await response.json();

      notificationTableBody.innerHTML = ""; // Clear the table
      records.forEach((record, index) => {
        const row = `
          <tr>
            <td>${index + 1}</td>
            <td>${NOTIFICATION_TYPES[record.notification_type]}</td>
            <td>${record.notification_title}</td>
            <td>${record.schedule_time}</td>
            <td>${record.weeks}</td>
            <td>${record.days}</td>
            <td>${record.hours}</td>
            <td>${record.minutes}</td>
            <td>${record.snooze_for}</td>
            <td>${record.status ? "Active" : "Inactive"}</td>
            <td>${record.inserted_on}</td>
            <td>
              <button class="btn btn-sm btn-primary edit-btn" data-id="${
                record.setting_id
              }" 
                      data-type="${record.notification_type}" 
                      data-title="${record.notification_title}" 
                      data-schedule="${record.schedule_time}"
                      data-weeks="${record.weeks}"
                      data-days="${record.days}" 
                      data-hours="${record.hours}" 
                      data-minutes="${record.minutes}"
                      data-snooze="${record.snooze_for}">
                Edit
              </button>
            </td>
          </tr>
        `;
        notificationTableBody.insertAdjacentHTML("beforeend", row);
      });

      // Add click event to all edit buttons
      document.querySelectorAll(".edit-btn").forEach((button) => {
        button.addEventListener("click", (e) => {
          const {
            id,
            type,
            title,
            schedule,
            weeks,
            days,
            hours,
            minutes,
            snooze,
          } = e.target.dataset;
          getElement("setting_id").value = id;
          getElement("notificationType").value = type;
          getElement("notificationTitle").value = title;
          getElement("scheduleType").value = schedule;
          getElement("scheduleWeeks").value = weeks;
          getElement("scheduleDays").value = days;
          getElement("scheduleHours").value = hours;
          getElement("scheduleMinutes").value = minutes;
          getElement("snoozeFor").value = snooze;
          notificationForm.style.display = "block";
        });
      });
    } catch (error) {
      console.error("Error fetching records:", error);
    }
  }

  // Initial fetch for table records
  fetchRecords();

  // Form submit handler
  notificationForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    if (!notificationForm.checkValidity()) {
      notificationForm.classList.add("was-validated");
      return;
    }

    const formData = new FormData(notificationForm);

    try {
      const response = await fetch("save-notification-settings", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.status === "success") {
        // Show success message in toast
        showToast({
          title: "Notification",
          message: result.message,
          type: "success",
        });
        notificationForm.reset(); // Clear the form
        getElement("setting_id").value = ""; // Clear hidden input
        fetchRecords(); // Refresh the table
        notificationForm.style.display = "none";
      } else {
        showToast({
          title: "Notification",
          message: "Failed to save notification. Please try again.",
          type: "error",
        });
      }
    } catch (error) {
      console.error("Error:", error);
    }
  });
});
