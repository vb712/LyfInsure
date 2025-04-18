<?php
session_start();
header('Content-Type: application/json');

try {
    require_once 'config.php';
    require_once 'input_validation.php';

    // Sanitize all input
    $email = InputValidator::sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = InputValidator::validateBoolean($_POST['remember_me'] ?? false);

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
        exit;
    }

    // Validate email format
    if (!InputValidator::validateEmail($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit;
    }

    // Get user from database
    $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        // If remember me is checked, set cookie
        if ($remember_me) {
            $token = bin2hex(random_bytes(32));
            $expiry = time() + (30 * 24 * 60 * 60); // 30 days
            
            $stmt = $conn->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?");
            $stmt->execute([$token, date('Y-m-d H:i:s', $expiry), $user['id']]);
            
            setcookie('remember_token', $token, [
                'expires' => $expiry,
                'path' => '/',
                'domain' => '',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }

        echo json_encode([
            'status' => 'success',
            'name' => $user['name']
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
