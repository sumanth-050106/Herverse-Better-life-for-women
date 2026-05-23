<?php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start session
    session_start();

    $response = array();

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $response['success'] = false;
        $response['message'] = 'User not authenticated';
        echo json_encode($response);
        exit;
    }

    // Get user ID from session
    $userId = $_SESSION['user_id'];

    // Get location data
    $latitude = filter_input(INPUT_POST, 'latitude', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $longitude = filter_input(INPUT_POST, 'longitude', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    try {
        // Insert emergency alert
        $stmt = $pdo->prepare('INSERT INTO emergency_alerts (user_id, latitude, longitude) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $latitude, $longitude]);

        $response['success'] = true;
        $response['message'] = 'Emergency alert sent successfully';
        $response['alert_id'] = $pdo->lastInsertId();
    } catch(PDOException $e) {
        $response['success'] = false;
        $response['message'] = 'Failed to send emergency alert: ' . $e->getMessage();
    }

    echo json_encode($response);
}
?>