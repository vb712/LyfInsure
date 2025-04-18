<?php
$conn = mysqli_connect("localhost", "root", "", "life_insurance");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
