<?php
require_once 'db.php';
require_once 'config.php';

header('Content-Type: application/json');

$plan_id = isset($_POST['plan_id']) ? $_POST['plan_id'] : null;

if (!$plan_id) {
    echo json_encode(['error' => 'Plan ID not provided']);
    exit();
}

try {
    $stmt = $conn->prepare("DELETE FROM purchases WHERE id = :plan_id");
    $stmt->bindParam(':plan_id', $plan_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Plan unsubscribed successfully']);
    } else {
        echo json_encode(['error' => 'Failed to unsubscribe from plan']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 