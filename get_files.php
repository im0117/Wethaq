<?php
// api/get_files.php
session_start();
require 'config.php';

header('Content-Type: application/json; charset=utf-8');

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
  http_response_code(401);
  echo json_encode(["error" => "Not logged in"]);
  exit;
}

$stmt = $pdo->prepare("SELECT id, title, file_date, status FROM legal_files WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($files);
