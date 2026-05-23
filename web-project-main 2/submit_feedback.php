<?php
header('Content-Type: application/json');

// Include database configuration
require_once 'config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['rating']) || !isset($data['comment'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Rating and comment are required']);
    exit;
}

$rating = filter_var($data['rating'], FILTER_VALIDATE_INT);
$comment = trim($data['comment']);

// Validate rating range
if ($rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
    exit;
}

// Validate comment length
if (strlen($comment) < 1 || strlen($comment) > 1000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Comment must be between 1 and 1000 characters']);
    exit;
}

try {
    // Create feedback table if not exists
    $pdo->exec('CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        rating INT NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    // Insert feedback
    $stmt = $pdo->prepare('INSERT INTO feedback (rating, comment) VALUES (?, ?)');
    
    if ($stmt->execute([$rating, $comment])) {
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your feedback!'
        ]);
    } else {
        throw new Exception('Failed to submit feedback');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while submitting your feedback. Please try again later.'
    ]);
}