<?php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

// Validate input
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
    exit;
}

if (empty($subject)) {
    echo json_encode(['success' => false, 'message' => 'Please select a subject']);
    exit;
}

if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please enter your message']);
    exit;
}

try {
    // Prepare and execute the SQL statement
    $stmt = $pdo->prepare('INSERT INTO support_tickets (email, subject, message) VALUES (?, ?, ?)');
    
    if ($stmt->execute([$email, $subject, $message])) {
        echo json_encode([
            'success' => true,
            'message' => 'Your support message has been sent successfully! We will get back to you soon.'
        ]);
    } else {
        throw new Exception('Failed to submit support ticket');
    }
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while submitting your ticket. Please try again later.'
    ]);
}