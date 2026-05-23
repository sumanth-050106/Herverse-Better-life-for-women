<?php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start session
    session_start();
    
    $response = array();
    
    // Get form data
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($email) || empty($password)) {
        $response['success'] = false;
        $response['message'] = 'Email and password are required';
        echo json_encode($response);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['success'] = false;
        $response['message'] = 'Invalid email format';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Get user by email
        $stmt = $pdo->prepare('SELECT id, first_name, last_name, email, password FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !password_verify($password, $user['password'])) {
            $response['success'] = false;
            $response['message'] = 'Invalid email or password';
            echo json_encode($response);
            exit;
        }
        
        // Store user data in session
        $_SESSION['user_id'] = $user['id'];
        
        $response['success'] = true;
        $response['message'] = 'Login successful';
        $response['user'] = array(
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email']
        );
    } catch(PDOException $e) {
        $response['success'] = false;
        $response['message'] = 'Login failed: ' . $e->getMessage();
    }
    
    echo json_encode($response);
}
?>