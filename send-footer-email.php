<?php

require_once __DIR__ . "/function/config.php";

require_valid_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php");
        exit;
    }

    $email = mysqli_real_escape_string($conn, $email);

    $sql = "INSERT INTO footer_emails (email) VALUES ('$email')";

    mysqli_query($conn, $sql);

    header("Location: index.php");
    exit;
}

header("Location: index.php");
exit;