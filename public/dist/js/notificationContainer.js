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
  const baseUrl = window.location.origin;
  const pathname = window.location.pathname;
  // Extract the application base path (e.g., /nexzen/public or /nexzen or empty for subdomain)
  // Find /business in the pathname and get everything before it
  const businessIndex = pathname.indexOf('/business');
  const appBasePath = businessIndex !== -1 ? pathname.substring(0, businessIndex) : '';
  const url = baseUrl + appBasePath + "/business/notificationManagement/get-dashboardNotifications";
  
  
  $.ajax({
    url: url,
    method: "GET",
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    dataType: "json",
    success: function (response) {
      if (response) {
        showNewNotificationAlert(response.unread_notifications || []);
        updateNotificationList(response);
        if (response.next_30_min_notifications) {
          scheduleNext30MinNotifications(response.next_30_min_notifications);
        }
      }
    },
    error: function (xhr, status, error) {
      // Silently fail
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
        snoozeUntil,
        isSnoozed,
      } = notification;
      let title = notificationHeading || "No Title";
      let message = notificationDetail || "No Message";
      let timeAgo = lastSnoozeTime || "Just now";
      let status = parseInt(isRead) || 0;
      let isCurrentlySnoozed = parseInt(isSnoozed) || 0;
      
      // Build snooze button or badge (only for unread notifications)
      let snoozeControl = '';
      if (status == 0) { // Only show for unread notifications
        if (isCurrentlySnoozed == 0) {
          snoozeControl = '<button type="button" class="btn snooze-btn" data-notification-id="' + notificationId + '">Snooze</button>';
        } else {
          snoozeControl = '<span class="snoozed-badge">Snoozed</span>';
        }
      }
      
      // Build mark as read button
      let markReadControl = status == 0 ? '<button type="button" class="btn mark-read-btn">Mark as Read</button>' : '';
      
      let notificationHtml = `
        <div class="notification-item" data-notification-id="${notificationId}" data-status="${status}">
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
              ${markReadControl}
              ${snoozeControl}
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

  // Show/hide green dot badge based on unread count
  if (unreadNotifications.length > 0) {
    $(".notification-badge").show();
  } else {
    $(".notification-badge").hide();
  }

  $("#all .notification-list").html(allHtml);
  $("#unread .notification-list").html(unreadHtml);
  $(".notifications-count").text(`${totalUnreadCount} new notification${totalUnreadCount !== 1 ? 's' : ''}`);
  
  // Add "View All Notifications" link with correct path
  if (!document.querySelector('.view-all-notifications-link')) {
    const pathname = window.location.pathname;
    const businessIndex = pathname.indexOf('/business');
    const appBasePath = businessIndex !== -1 ? pathname.substring(0, businessIndex) : '';
    const viewAllHref = appBasePath + '/business/notifications';
    const viewAllLink = `<div class="view-all-notifications-container"><a href="${viewAllHref}" class="view-all-notifications-link">View All Notifications</a></div>`;
    $(".notifications-container").append(viewAllLink);
  }
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

// Handle dynamically added "Mark as Read" & "Snooze" button clicks using jQuery delegation
$(document).on('click', '.mark-read-btn', function(event) {
  event.stopPropagation();
  markAsRead(this);
});

$(document).on('click', '.snooze-btn', function(event) {
  event.stopPropagation();
  showSnoozeOptions(this);
});

$(document).on('click', '.snooze-option-btn', function(event) {
  event.stopPropagation();
  applySnooze(this);
});

// Close dropdown when clicking outside
// Close dropdown when clicking outside (only if not clicking on snooze options)
$(document).on('click', function(event) {
  // Don't close if clicking inside the dropdown or on the snooze button
  if ($(event.target).closest('.snooze-dropdown').length || $(event.target).hasClass('snooze-btn')) {
    return;
  }
  
  // Close if clicking elsewhere
  if ($('.snooze-dropdown').length > 0) {
    $('.snooze-dropdown').remove();
  }
});

// Function to show snooze options
function showSnoozeOptions(element) {
  const notificationId = element.dataset.notificationId;
  
  // Remove any existing snooze dropdowns
  $('.snooze-dropdown').remove();
  
  // Create and show snooze dropdown - use proper interpolation
  const dropdown = $('<div>', {
    class: 'snooze-dropdown'
  });
  
  // Add period-based options matching notifications page
  dropdown.append($('<button>')
    .attr('type', 'button')
    .addClass('snooze-option-btn')
    .attr('data-notification-id', notificationId)
    .attr('data-period', '1_hour')
    .html('<i class="fas fa-clock me-1"></i> 1 Hour')
  );
  
  dropdown.append($('<button>')
    .attr('type', 'button')
    .addClass('snooze-option-btn')
    .attr('data-notification-id', notificationId)
    .attr('data-period', '1_day')
    .html('<i class="fas fa-calendar-day me-1"></i> 1 Day')
  );
  
  dropdown.append($('<button>')
    .attr('type', 'button')
    .addClass('snooze-option-btn')
    .attr('data-notification-id', notificationId)
    .attr('data-period', '1_week')
    .html('<i class="fas fa-calendar-week me-1"></i> 1 Week')
  );
  
  dropdown.append($('<button>')
    .attr('type', 'button')
    .addClass('snooze-option-btn custom-snooze-btn')
    .attr('data-notification-id', notificationId)
    .attr('data-period', 'custom')
    .html('<i class="fas fa-calendar-alt me-1"></i> Custom')
  );
  
  // Add custom date input
  const customDateDiv = $('<div>', {
    class: 'custom-date-container',
    style: 'display: none; padding: 10px; border-top: 1px solid #e5e7eb;'
  }).append($('<input>')
    .attr('type', 'datetime-local')
    .addClass('form-control custom-date-input')
    .attr('data-notification-id', notificationId)
  );
  
  dropdown.append(customDateDiv);
  
  $(element).after(dropdown);
  
  // Handle custom date button click
  $(document).off('click', '.custom-snooze-btn');
  $(document).on('click', '.custom-snooze-btn', function(e) {
    e.stopPropagation();
    $('.custom-date-container').toggle();
    if ($('.custom-date-container').is(':visible')) {
      $('.custom-date-input').focus();
    }
  });
  
  // Handle custom date input change - submit when date is selected
  $(document).off('change', '.custom-date-input');
  $(document).on('change', '.custom-date-input', function() {
    if ($(this).val()) {
      // Auto-submit the snooze when a date is selected
      const notificationId = $(this).data('notification-id');
      const customBtn = $('.custom-snooze-btn[data-notification-id="' + notificationId + '"]');
      
      // Create a fake element to trigger applySnooze with period='custom'
      const fakeElement = $('<div>', {
        'data-notification-id': notificationId,
        'data-period': 'custom'
      });
      
      applySnooze(fakeElement[0]);
    }
  });
}

// Function to apply snooze
function applySnooze(element) {
  const notificationId = $(element).attr('data-notification-id') || $(element).data('notification-id') || element.dataset.notificationId;
  const period = $(element).attr('data-period') || $(element).data('period') || element.dataset.period;
  
  // Check if custom date input is visible
  const customDateInput = $('.custom-date-input[data-notification-id="' + notificationId + '"]');
  
  if (period === 'custom' && customDateInput.length && !customDateInput.val()) {
    alert('Please select a custom date and time');
    return;
  }
  
  // Hide the dropdown
  $('.snooze-dropdown').remove();
  
  // Get the correct base path
  const pathname = window.location.pathname;
  const businessIndex = pathname.indexOf('/business');
  const appBasePath = businessIndex !== -1 ? pathname.substring(0, businessIndex) : '';
  const baseUrl = window.location.origin;
  
  let snoozeDate;
  let requestData = {
    snooze_period: period
  };
  
  // Calculate snooze date based on period
  if (period === 'custom' && customDateInput.val()) {
    requestData.custom_date = customDateInput.val();
    snoozeDate = new Date(customDateInput.val());
  } else {
    const now = new Date();
    switch(period) {
      case '1_hour':
        snoozeDate = new Date(now.getTime() + 60 * 60000);
        break;
      case '1_day':
        snoozeDate = new Date(now.getTime() + 24 * 60 * 60000);
        break;
      case '1_week':
        snoozeDate = new Date(now.getTime() + 7 * 24 * 60 * 60000);
        break;
      default:
        snoozeDate = new Date(now.getTime() + 60 * 60000);
    }
    requestData.custom_date = snoozeDate.toISOString();
  }
  
  const url = baseUrl + appBasePath + '/business/notifications/' + notificationId + '/snooze';

  $.ajax({
    url: url,
    method: "POST",
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      'Content-Type': 'application/json'
    },
    data: JSON.stringify(requestData),
    success: function(data) {
      if (data.success) {
        // Calculate snooze end time for client-side tracking
        const snoozeEndTime = snoozeDate.getTime();
        
        // Add to snooze array
        snoozeDataArray.push({
          notificationId,
          snoozeEndTime: snoozeEndTime,
        });

        // Remove from lastNotificationIds when snoozed
        removeFromLastNotificationIds(notificationId);

        typeof triggerGlobalFilter === "function" && triggerGlobalFilter();
        
        // Show success message
        const periodText = period === '1_hour' ? '1 hour' : 
                          period === '1_day' ? '1 day' : 
                          period === '1_week' ? '1 week' : 'custom period';
        const resumeTime = snoozeDate.toLocaleString();
        alert('Notification snoozed successfully!\n\nSnoozed for: ' + periodText + '\nWill reappear at: ' + resumeTime);
        
        // Update the UI - replace snooze button with snoozed badge
        // Find the notification item by data-notification-id
        const notificationItem = $(`.notification-item[data-notification-id="${notificationId}"]`);
        const button = notificationItem.find('.snooze-btn');
        if (button.length) {
          button.replaceWith('<span class="snoozed-badge">Snoozed</span>');
        }
        
        // Refresh the notification list to remove it from unread
        setTimeout(() => {
          fetchNotifications();
        }, 500);
      } else {
        alert('Error: ' + (data.message || 'Failed to snooze notification'));
      }
    },
    error: function(xhr, status, error) {
      alert('Error snoozing notification. Please try again.');
    }
  });
}

// Legacy function for backward compatibility (not used anymore)
function snoozeNotification(element) {
  const notificationActions = element.closest(".notification-actions");
  const notificationDetails = notificationActions?.querySelector(
    ".notificationDetails"
  );

  if (!notificationDetails) return;

  const notificationId = notificationDetails.value;
  const snoozeMinutes = snoozeTimeValue || 10; // Default to 10 minutes

  // Get the correct base path
  const pathname = window.location.pathname;
  const businessIndex = pathname.indexOf('/business');
  const appBasePath = businessIndex !== -1 ? pathname.substring(0, businessIndex) : '';
  const baseUrl = window.location.origin;

  $.ajax({
    url: baseUrl + appBasePath + `/business/notifications/${notificationId}/snooze`,
    method: "POST",
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      'Content-Type': 'application/json'
    },
    data: JSON.stringify({
      snooze_period: '1_hour', // Use 1 hour for now
    }),
    success: function(data) {
      if (data.success) {
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
    },
    error: function(xhr, status, error) {
      console.error("Error snoozing notification:", error);
      console.error("Response:", xhr.responseText);
    }
  });
}

// Function to handle "Mark as Read" action
function markAsRead(element) {
  const notificationActions = element.closest(".notification-actions");
  const notificationDetails = notificationActions?.querySelector(
    ".notificationDetails"
  );

  if (!notificationDetails) return;

  const notificationId = notificationDetails.value;

  // Get the correct base path
  const pathname = window.location.pathname;
  const businessIndex = pathname.indexOf('/business');
  const appBasePath = businessIndex !== -1 ? pathname.substring(0, businessIndex) : '';
  const baseUrl = window.location.origin;

  $.ajax({
    url: baseUrl + appBasePath + `/business/notifications/${notificationId}/complete`,
    method: "POST",
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      'Content-Type': 'application/json'
    },
    data: JSON.stringify({
      completion_notes: 'Marked as read from notification popup'
    }),
    success: function(data) {
      if (data.success) {
        // Hide the snooze button and mark as read button for this notification
        const notificationItem = $(element).closest('.notification-item');
        const notificationId = notificationItem.attr('data-notification-id');
        
        // Hide both buttons
        notificationItem.find('.mark-read-btn').hide();
        notificationItem.find('.snooze-btn').hide();
        notificationItem.attr('data-status', '1');
        
        // Remove from unread tab and update counts
        setTimeout(() => {
          fetchNotifications();
        }, 300);
      }
    },
    error: function(xhr, status, error) {
      // Silently fail
    }
  });
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
