<?php
require 'auth_check.php';
require 'config.php';

// جلب بيانات المستخدم من قاعدة البيانات
$stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user   = $stmt->fetch(PDO::FETCH_ASSOC);

$name   = $user['name']  ?? 'مستخدم';
$email  = $user['email'] ?? 'غير معروف';
$userId = $user['id']    ?? '—';

// الحرف الأول للأفاتار
if (function_exists('mb_substr')) {
  $initial = mb_substr($name, 0, 1, 'UTF-8');
} else {
  $initial = substr($name, 0, 1);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WETHAQ - My Account</title>
  <style>
    :root {
      --accent:#2d6a4f;
      --light:#e6f0ea;
      --bg:#f7f9f7;
      --text:#1f2a24;
      --muted:#7b8b82;
    }
    body{
      font-family:"Segoe UI",Arial,sans-serif;
      background:var(--bg);
      margin:0;
      padding:20px;
      display:flex;
      justify-content:center;
      color:var(--text);
    }
    .card{
      width:360px;
      background:#fff;
      border-radius:16px;
      box-shadow:0 8px 25px rgba(0,0,0,.08);
      padding:20px;
      text-align:left;
      direction:ltr; /* عشان النص الإنجليزي يطلع مضبوط */
    }
    h2{
      margin:6px 0 16px;
      text-align:center;
      color:var(--accent);
    }
    .avatar{
      width:90px;
      height:90px;
      border-radius:50%;
      background:var(--light);
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:34px;
      color:var(--accent);
      margin:0 auto 10px;
      font-weight:700;
    }
    .row{
      margin:10px 0;
      padding:10px;
      background:var(--light);
      border-radius:10px;
    }
    .row label{
      display:block;
      font-size:12px;
      color:var(--muted);
      margin-bottom:4px;
    }
    .row span{
      font-weight:600;
      font-size:14px;
    }
    .btn{
      width:100%;
      margin-top:14px;
      background:var(--accent);
      color:#fff;
      font-weight:700;
      padding:12px;
      border:none;
      border-radius:10px;
      cursor:pointer;
      font-size:14px;
    }
    .btn:hover{opacity:.92}
    .links{
      display:flex;
      gap:10px;
      margin-top:10px;
    }
    .btn.ghost{
      background:transparent;
      color:var(--accent);
      border:1px solid #dcefe0;
      flex:1;
      font-weight:600;
    }
    .btn.ghost:hover{
      background:#f3f7f4;
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="avatar"><?= htmlspecialchars($initial, ENT_QUOTES, 'UTF-8') ?></div>
    <h2>حسابي</h2>

    <div class="row">
      <label>Full Name</label>
      <span><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="row">
      <label>Email</label>
      <span><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="row">
      <label>User ID</label>
      <span><?= htmlspecialchars($userId, ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="row">
      <label>Joined</label>
      <span>—</span> <!-- ما عندنا joined في الجدول الآن، نخليها فاضية مؤقتًا -->
    </div>

    <!-- زر يودّي للصفحة الرئيسية بنفس تصميمك -->
    <button class="btn" type="button" onclick="window.location.href='/wethaq/main.php'">
      الصفحة الرئيسية
    </button>

    <div class="links">
      <!-- تسجيل الخروج من السيرفر -->
      <form method="post" action="logout.php" style="flex:1; margin:0;">
        <button class="btn ghost" type="submit">تسجيل الخروج</button>
      </form>

      <!-- زر حذف الحساب (واجهة فقط الآن) -->
      <button class="btn ghost" type="button" onclick="alert('حذف الحساب غير مفعّل حالياً من جهة الخادم')">
        حذف الحساب
      </button>
    </div>
  </div>
</body>
</html>
