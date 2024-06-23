<?php
function sanitizeFormInput($formData) {
    foreach ($formData as $key => $value) {
        // Check if the value is an array (e.g., checkboxes)
        if (is_array($value)) {
            $formData[$key] = sanitizeFormInput($value); // Recursively sanitize array values
        } else {
            // Sanitize individual form field value
            $formData[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
    }
    return $formData;
}

// Sanitize form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_POST = sanitizeFormInput($_POST);
    // Now you can safely use $_POST array for further processing
}
?>
