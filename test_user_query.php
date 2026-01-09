<?php
// Test trực tiếp query User
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/models/Model.php';
require_once __DIR__ . '/app/models/User.php';

echo "<h2>Testing User Model</h2>";

$userModel = new User();

echo "<h3>1. Testing getUsers()</h3>";
$users = $userModel->getUsers(null, null, null, 10, 0);
echo "Users count: " . count($users) . "<br>";
echo "<pre>";
print_r($users);
echo "</pre>";

echo "<h3>2. Testing countUsers()</h3>";
$count = $userModel->countUsers(null, null, null);
echo "Total users: " . $count . "<br>";

echo "<h3>3. Testing getUserStats()</h3>";
$stats = $userModel->getUserStats();
echo "<pre>";
print_r($stats);
echo "</pre>";

echo "<h3>4. Direct Query Test</h3>";
$db = Database::getInstance();
$result = $db->query("SELECT * FROM tai_khoan LIMIT 5")->fetchAll();
echo "Direct query count: " . count($result) . "<br>";
echo "<pre>";
print_r($result);
echo "</pre>";
