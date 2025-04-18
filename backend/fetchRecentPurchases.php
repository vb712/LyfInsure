<?php
// Ensure no output before JSON
ob_start();

require_once 'db.php';
require_once 'config.php';

// Get user email from request
$user_email = isset($_GET['email']) ? $_GET['email'] : null;

if (!$user_email) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['error' => 'User email not provided']);
    exit();
}

try {
    $stmt = $conn->prepare("
        SELECT * FROM purchases 
        WHERE user_email = :email 
        ORDER BY purchase_date DESC 
        LIMIT 5
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->errorInfo()[2]);
    }
    
    $stmt->bindParam(':email', $user_email);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->errorInfo()[2]);
    }
    
    $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $formattedPurchases = [];
    foreach ($purchases as $row) {
        $formattedPurchases[] = [
            'id' => $row['id'],
            'plan_name' => $row['plan_name'],
            'premium' => $row['premium'],
            'base_coverage' => $row['base_coverage'],
            'extra_coverage' => $row['extra_coverage'],
            'duration' => $row['duration'],
            'purchase_date' => $row['purchase_date']
        ];
    }
    
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'purchases' => $formattedPurchases]);
} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

// No need to close PDO connection explicitly
?> 