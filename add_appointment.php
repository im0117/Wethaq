<?php
session_start();
require '../config.php';

header('Content-Type: application/json; charset=utf-8');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
  echo json_encode(["error" => "Not logged in"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$title = trim($data['title'] ?? 'جلسة محكمة');
$date  = trim($data['date']  ?? '');

if (!$date) {
  echo json_encode(["error" => "Missing date"]);
  exit;
}

// 1) إضافة الموعد لجدول appointments
$stmt = $pdo->prepare("
  INSERT INTO appointments (user_id, title, session_date)
  VALUES (?, ?, ?)
");
$stmt->execute([$user_id, $title, $date]);

// 2) 🔔 إضافة إشعار “تم حجز موعد جديد”
$msg = "تم حجز موعد جديد بتاريخ: $date";
$note = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
$note->execute([$user_id, $msg]);

echo json_encode(["status" => "ok"]);
