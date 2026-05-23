<?php
header('Content-Type: application/json');

try {
    // Include database configuration
    require_once 'config.php';

    // Initialize response array
    $response = array('success' => false, 'message' => '');

    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate and sanitize input
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

    // Validate required fields
    if (!$rating || !$category || !$comment) {
        throw new Exception('All fields are required');
    }

    // Validate rating range
    if ($rating < 1 || $rating > 5) {
        throw new Exception('Invalid rating value');
    }

    // Check if user is logged in
    session_start();
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User must be logged in to submit feedback');
    }

    // Create feedback table if not exists
    $createTable = "CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        rating INT NOT NULL,
        category VARCHAR(50) NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($createTable);

    // Prepare and execute the insert statement
    $stmt = $pdo->prepare("INSERT INTO feedback (user_id, rating, category, comment) VALUES (:user_id, :rating, :category, :comment)");
    
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':rating' => $rating,
        ':category' => $category,
        ':comment' => $comment
    ]);
    
    $response['success'] = true;
    $response['message'] = 'Feedback submitted successfully';

} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;