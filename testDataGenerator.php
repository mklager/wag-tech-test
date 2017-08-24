<?php
require_once('./config.php');

try {
    $pdo = new PDO('mysql:host=localhost;dbname=' . DATABASE, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $err) {
    die($err->getMessage());
}
$stmt = $pdo->prepare("INSERT INTO users(user_id, user_score, posted) VALUES (?, ?, ?)");
$users = range(1, 100000);
$week_days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

foreach (['last week', 'this week'] as $week) {
    foreach ($week_days as $day) {
        if ('this week' == $week && $week_days[(date("w", strtotime("tomorrow")) - 1)] == $day) {
            break 2;
        }
        foreach ($users as $user_id) {
            $user_score = mt_rand(0, (pow(10, 5) - 1));
            $posted = date("Y-m-d", strtotime($day, strtotime($week)));
            $stmt->execute(array($user_id, $user_score, $posted));
        }
    }
}