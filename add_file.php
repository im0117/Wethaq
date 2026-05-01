<?php
// api/add_file.php
session_start();
require 'config.php'; // لأن config.php داخل نفس مجلد api

header('Content-Type: application/json; charset=utf-8');

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
  http_response_code(401);
  echo json_encode(["error" => "Not logged in"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$title  = trim($data['title'] ?? '');
$date   = trim($data['date'] ?? '');
$status = trim($data['status'] ?? 'doc');

if (!$title || !$date) {
  http_response_code(400);
  echo json_encode(["error" => "Missing title or date"]);
  exit;
}

// حفظ الملف في جدول legal_files
$stmt = $pdo->prepare("INSERT INTO legal_files (user_id, title, file_date, status) VALUES (?, ?, ?, ?)");
$stmt->execute([$user_id, $title, $date, $status]);

// 🔔 إضافة إشعار باستخدام جدول notifications
$message = "تم إضافة ملف جديد: " . $title;
$note = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
$note->execute([$user_id, $message]);

echo json_encode(["status" => "ok"]);