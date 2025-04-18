<?php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "life_insurance";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $userId = $_POST['userId'] ?? '';
    $planName = $_POST['planName'] ?? '';
    $premium = $_POST['premium'] ?? '';
    $baseCoverage = $_POST['baseCoverage'] ?? '';
    $extraCoverage = $_POST['extraCoverage'] ?? '';
    $duration = $_POST['duration'] ?? '';

    error_log("Received purchase data: " . print_r($_POST, true));

    if (empty($userId)) {
        throw new Exception("User ID is required");
    }
    if (empty($planName)) {
        throw new Exception("Plan name is required");
    }
    if (empty($premium)) {
        throw new Exception("Premium amount is required");
    }

    $userId = filter_var($userId, FILTER_SANITIZE_EMAIL);
    if (!filter_var($userId, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    $stmt = $conn->prepare("INSERT INTO purchases (user_email, plan_name, premium, base_coverage, extra_coverage, duration, purchase_date) 
                           VALUES (:userId, :planName, :premium, :baseCoverage, :extraCoverage, :duration, NOW())");

    $stmt->bindParam(':userId', $userId);
    $stmt->bindParam(':planName', $planName);
    $stmt->bindParam(':premium', $premium);
    $stmt->bindParam(':baseCoverage', $baseCoverage);
    $stmt->bindParam(':extraCoverage', $extraCoverage);
    $stmt->bindParam(':duration', $duration);

    $stmt->execute();

    echo json_encode([
        'status' => 'success',
        'message' => 'Purchase saved successfully',
        'purchase_id' => $conn->lastInsertId()
    ]);

} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage(),
        'code' => $e->getCode()
    ]);
} catch(Exception $e) {
    error_log("General error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 