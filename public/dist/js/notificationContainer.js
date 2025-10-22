// Store notifications in localStorage to persist across page loads
let snoozeTimeValue = 10;
let snoozeDataArray = [];
let snoozeTimers = [];
let scheduledNext30Ids = new Set();

// Initialize lastNotificationIds from localStorage or create new
let lastNotificationIds = getLastNotificationIdsFromStorage();

// Request notification permission properly on page load
document.addEventListener("DOMContentLoaded", function () {
  if (
    Notification.permission !== "granted" &&
    Notification.permission !== "denied"
  ) {
    // Request permission when user interacts with the page
    document.addEventListener("click", requestNotificationPermission, {
      once: true,
    });
  }
});

// Separate function to request notification permission
function requestNotificationPermission() {
  if (Notification.permission === "granted") {
    return Promise.resolve();
  }
  if (Notification.permission === "denied") {
    return Promise.reject(new Error("Permission denied"));
  }
  return Notification.requestPermission().then((permission) => {
    if (permission !== "granted") {
      throw new Error("Permission not granted");
    }
  });
}

// Function to fetch notifications
function fetchNotifications() {
  hideLoader();
  $.ajax({
    url: baseUrl + "notificationManagement/get-dashboardNotifications",
    method: "GET",
    dataType: "json",
    success: function (response) {
      if (response) {
        showNewNotificationAlert(response.unread_notifications);
        updateNotificationList(response);
        if (response.next_30_min_notifications) {
          scheduleNext30MinNotifications(response.next_30_min_notifications);
        }
      }
    },
    error: function (xhr, status, error) {
      console.error("Error fetching notifications:", error);
    },
  });
}

// Function to update notification list
function updateNotificationList(data) {
  let allHtml = "",
    unreadHtml = "";
  let notifications = data.allNotifications || [];
  let unreadNotifications = data.unread_notifications || [];
  let totalUnreadCount = data.total_unread_count || 0;

  if (notifications.length === 0) {
    allHtml = `<p class="px-4">No notifications available.</p>`;
  } else {
    notifications.forEach((notification) => {
      let {
        notificationId,
        bookingId,
        vehicleId,
        notificationHeading,
        notificationDetail,
        lastSnoozeTime,
        isRead,
      } = notification;
      let title = notificationHeading || "No Title";
      let message = notificationDetail || "No Message";
      let timeAgo = lastSnoozeTime || "Just now";
      let status = parseInt(isRead) || 0;

      let notificationHtml = `
        <div class="notification-item">
          <div class="car-icon">🚗</div>
          <div class="notification-content">
            <div class="notification-title">${title}</div>
            <div class="notification-message">${message}</div>
            <div class="notification-actions">
              <input type="hidden" class="notificationDetails" value="${
                notificationId ?? 0
              }" data-booking="${bookingId ?? 0}" data-vehicle="${
        vehicleId ?? 0
      }">
              <button type="button" class="btn notification-btn ${
                status == 0 ? "view-btn" : "btn-secondary"
              }">View</button>
              <button type="button" class="btn snooze-btn">Snooze</button>
            </div>
            <div class="notification-time">${timeDifferenceAgo(timeAgo)}</div>
          </div>
        </div>`;

      allHtml += notificationHtml;
      if (status == 0) unreadHtml += notificationHtml;
    });
  }

  if (unreadNotifications.length === 0)
    unreadHtml = `<p class="px-4">No unread notifications.</p>`;

  if (unreadNotifications.length > 0) $(".notification-badge").show();

  $("#all .notification-list").html(allHtml);
  $("#unread .notification-list").html(unreadHtml);
  $(".notifications-count").text(`${totalUnreadCount} new notifications`);
}

// Fetch notifications initially & refresh every 30 mins
fetchNotifications();
setInterval(fetchNotifications, 1800000);

// Handle tab switching
$("#notificationsContainer")
  .find(".tab")
  .on("click", function () {
    $("#notificationsContainer").find(".tab").removeClass("active");
    $("#notificationsContainer").find(".tab-content").removeClass("active");
    $(this).addClass("active");
    $("#" + $(this).data("tab")).addClass("active");
  });

// Handle dynamically added "View" & "Snooze" button clicks
document.addEventListener("click", function (event) {
  if (event.target.classList.contains("notification-btn")) {
    viewNotification(event.target);
  } else if (event.target.classList.contains("snooze-btn")) {
    snoozeNotification(event.target);
  }
});

// Function to handle "View" action
function viewNotification(element) {
  const notificationActions = element.closest(".notification-actions");
  const notificationDetails = notificationActions?.querySelector(
    ".notificationDetails"
  );

  if (!notificationDetails) return;

  const notificationId = notificationDetails.value;
  const bookingId = notificationDetails.dataset.booking;
  const vehicleId = notificationDetails.dataset.vehicle;

  fetch(baseUrl + "notificationManagement/view-notification", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ notificationId }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status) {
        if (parseInt(bookingId)) {
          window.open(
            baseUrl +
              `bookingManagement/view-BookingDetails?bookingId=${bookingId}`,
            "_blank"
          );
        } else if (vehicleId) {
          window.open(
            baseUrl + `vehicleManagement/view-vehicleDetails/${vehicleId}`,
            "_blank"
          );
        }
        typeof triggerGlobalFilter === "function" && triggerGlobalFilter();
        fetchNotifications();
      }
    })
    .catch((error) => console.error("Error:", error));
}

// Function to handle "Snooze" action
function snoozeNotification(element) {
  const notificationActions = element.closest(".notification-actions");
  const notificationDetails = notificationActions?.querySelector(
    ".notificationDetails"
  );

  if (!notificationDetails) return;

  const notificationId = notificationDetails.value;
  const bookingId = notificationDetails.dataset.booking;
  const vehicleId = notificationDetails.dataset.vehicle;
  const snoozeMinutes = snoozeTimeValue;

  if (!snoozeMinutes || isNaN(snoozeMinutes) || snoozeMinutes <= 0) {
    typeof showHideSnoozeFilterDropDown === "function" &&
      showHideSnoozeFilterDropDown();
    return;
  }

  fetch(baseUrl + "notificationManagement/snooze-notification", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      notificationId,
      bookingId,
      vehicleId,
      snoozeMinutes,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status) {
        // Add to snooze array
        snoozeDataArray.push({
          notificationId,
          snoozeEndTime: Date.now() + snoozeMinutes * 60000,
        });

        // Remove from lastNotificationIds when snoozed
        removeFromLastNotificationIds(notificationId);

        typeof triggerGlobalFilter === "function" && triggerGlobalFilter();
        fetchNotifications();

        setTimeout(() => {
          snoozeDataArray = snoozeDataArray.filter(
            (item) => item.notificationId !== notificationId
          );
          fetchNotifications();
        }, snoozeMinutes * 60000);
        scheduleSnoozeFetch(notificationId, Date.now() + snoozeMinutes * 60000);
      }
    })
    .catch((error) => console.error("Error snoozing notification:", error));
}

// Remove notification from lastNotificationIds
function removeFromLastNotificationIds(notificationId) {
  for (let key of lastNotificationIds) {
    if (key.startsWith(notificationId + "-")) {
      lastNotificationIds.delete(key);
    }
  }
  saveLastNotificationIds();
}

// Save lastNotificationIds to localStorage
function saveLastNotificationIds() {
  localStorage.setItem(
    "lastNotificationIds",
    JSON.stringify([...lastNotificationIds])
  );
  updateBadgeVisibility();
}

function updateBadgeVisibility() {
  const hasUnread = $(".notification-list .view-btn").length > 0;
  if (hasUnread && lastNotificationIds.size > 0) {
    $(".notification-badge").show();
  } else {
    $(".notification-badge").hide();
  }
}

// Function to show new notification alerts
function showNewNotificationAlert(unreadNotifications) {
  if (!unreadNotifications || !Array.isArray(unreadNotifications)) return;

  lastNotificationIds = getLastNotificationIdsFromStorage();

  const newNotifications = unreadNotifications.filter((notification) => {
    const notifId = notification.notificationId;
    const notifTime = notification.lastSnoozeTime || "";
    const combinedKey = `${notifId}-${notifTime}`;

    const isNotSnoozed = !snoozeDataArray.some(
      (item) => item.notificationId === notifId
    );
    const isNew = !lastNotificationIds.has(combinedKey);
    return isNew && isNotSnoozed;
  });

  const notificationsToShow = newNotifications
    .sort((a, b) => new Date(b.lastSnoozeTime) - new Date(a.lastSnoozeTime))
    .slice(0, 5);

  notificationsToShow.forEach((notification) => {
    const combinedKey = `${notification.notificationId}-${notification.lastSnoozeTime}`;
    lastNotificationIds.add(combinedKey);

    showDesktopNotification(notification);
    showToast({
      title: notification.notificationHeading || "Notification",
      message: notification.notificationDetail || "",
      type: "info",
      time: timeDifferenceAgo(notification.lastSnoozeTime),
    });
  });

  saveLastNotificationIds();
}

// Function to show desktop notification
function showDesktopNotification(notification) {
  if (!("Notification" in window)) return;

  if (Notification.permission === "granted") {
    try {
      const options = {
        body: notification.notificationDetail || "",
        icon: baseUrl + "public/images/notificationCar1.svg",
        tag: `notification-${notification.notificationId}`,
      };

      setTimeout(() => {
        const desktopNotification = new Notification(
          notification.notificationHeading || "Notification",
          options
        );

        desktopNotification.onclick = () => {
          window.focus();
          desktopNotification.close();
        };
      }, 100);
    } catch (error) {
      console.error("Error showing desktop notification:", error);
    }
  }
}

// Check for snoozed notifications that have expired
setInterval(() => {
  const now = Date.now();
  const expiredSnoozes = snoozeDataArray.filter(
    (item) => item.snoozeEndTime <= now
  );

  if (expiredSnoozes.length > 0) {
    snoozeDataArray = snoozeDataArray.filter(
      (item) => item.snoozeEndTime > now
    );
    fetchNotifications();
  }
}, 30000);

function getLastNotificationIdsFromStorage() {
  try {
    return new Set(
      JSON.parse(localStorage.getItem("lastNotificationIds")) || []
    );
  } catch (e) {
    return new Set();
  }
}

window.addEventListener("storage", (event) => {
  if (event.key === "lastNotificationIds") {
    lastNotificationIds = new Set(JSON.parse(event.newValue) || []);
  }
});

function scheduleSnoozeFetch(notificationId, endTime) {
  snoozeTimers.push({ notificationId, endTime });
  processSnoozeTimers();
}

function processSnoozeTimers() {
  if (snoozeTimers.length === 0) return;

  snoozeTimers.sort((a, b) => b.endTime - a.endTime); // Keep latest at top
  const latestSnooze = snoozeTimers[0];

  // Clear all existing timeouts before rescheduling
  snoozeTimers.forEach((item) => clearTimeout(item.timer));
  snoozeTimers = [];

  const timeUntilFetch = latestSnooze.endTime - Date.now();
  if (timeUntilFetch > 0) {
    const timer = setTimeout(() => {
      fetchNotifications();
    }, timeUntilFetch);

    snoozeTimers.push({
      notificationId: latestSnooze.notificationId,
      endTime: latestSnooze.endTime,
      timer,
    });
  }
}

function scheduleNext30MinNotifications(notifications = []) {
  notifications.forEach((notification) => {
    const id = notification.notificationId;
    if (scheduledNext30Ids.has(id)) return;

    const showAt = new Date(notification.lastSnoozeTime).getTime();
    const delay = showAt - Date.now();

    if (delay > 0) {
      scheduledNext30Ids.add(id);
      setTimeout(() => {
        showDesktopNotification(notification);
        showToast({
          title:
            notification.notificationHeading ||
            "Upcoming notification reminder",
          message: notification.notificationDetail || "",
          type: "info",
          time: "Now",
        });
        scheduledNext30Ids.delete(id); // Clean up after showing
      }, delay);
    }
  });
}
