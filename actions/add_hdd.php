<?php
include '../common/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hddName = $_POST['hddName'];

    $stmt = $conn->prepare("INSERT INTO hdd_resources (name) VALUES (?)");
    $stmt->execute([$hddName]);

    header("Location: ../hdd_list");
}