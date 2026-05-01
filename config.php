<?php
$host = "localhost";
$dbname = "wethaq_db";

$user = "root";      // اسم المستخدم
$pass = "mysql";     // كلمة المرور الافتراضية في AMPPS تكون mysql

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Database connection failed: " . $e->getMessage());
}
