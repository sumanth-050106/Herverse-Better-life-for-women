<?php
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = array();

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    $response['success'] = true;
    $response['message'] = 'Logout successful';
} else {
    $response['success'] = false;
    $response['message'] = 'No active session found';
}

echo json_encode($response);
?>