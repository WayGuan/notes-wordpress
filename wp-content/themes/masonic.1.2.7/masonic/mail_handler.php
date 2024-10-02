<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get the file name from the AJAX request
  $fileName = isset($_POST['fileName']) ? $_POST['fileName'] : '';

  // Your wp_mailer code here
  $to = 'bonnie.wang@vaughan.ca';
  $subject = 'New Booking';
  $message = 'Hello';
  $headers = array('Content-Type: text/html; charset=UTF-8');

  // Add the file as an attachment
  $attachment = WP_CONTENT_DIR . '/uploads/' . $fileName; // Adjust the path accordingly

  if (file_exists($attachment)) {
    wp_mail($to, $subject, $message, $headers, $attachment);
    echo 'Email sent successfully with attachment.';
  } else {
    echo 'Attachment not found.';
  }
}
?>