<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    require_once 'config.php';
    require_once 'input_validation.php';

    $name = InputValidator::sanitize($_POST['name'] ?? '');
    $email = InputValidator::sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Name, email and password are required']);
        exit;
    }

    if (!InputValidator::validateName($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid name format. Only letters, spaces, hyphens, and apostrophes are allowed']);
        exit;
    }

    if (!InputValidator::validateEmail($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit;
    }

    if (!InputValidator::validatePassword($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $hashedPassword]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 