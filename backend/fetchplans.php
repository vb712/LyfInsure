<?php
include 'db.php';

$query = "SELECT * FROM insurance_plans";
$result = mysqli_query($conn, $query);

$plans = [];
while ($row = mysqli_fetch_assoc($result)) {
    $plans[] = $row;
}

echo json_encode($plans);
?>
