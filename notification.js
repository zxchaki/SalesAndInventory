$(document).ready(function() {
    // Click event for the notification bell
    $('#notificationBell').click(function() {
      // Show the low stock modal when the bell is clicked
      $('#lowStockModal').modal('show');
    });
  });
  