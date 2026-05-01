<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name  = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass  = trim($_POST['password'] ?? '');
  $confirm = trim($_POST['password_confirm'] ?? '');

  if ($name && $email && $pass && $confirm) {

    if ($pass !== $confirm) {
      $error = "كلمتا المرور غير متطابقتين";
    } else {
      // هل الإيميل مستخدم من قبل؟
      $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
      $check->execute([$email]);
      if ($check->fetch()) {
        $error = "هذا الإيميل مسجل من قبل";
      } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hash]);

        // نخزن user_id في السيشن مباشرةً
        $_SESSION['user_id'] = $pdo->lastInsertId();

        header('Location: main.php');
        exit;
      }
    }
  } else {
    $error = "يرجى تعبئة جميع الحقول";
  }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WETHAQ - Sign Up</title>
  <style>
    body {
      font-family: system-ui, Arial, "Noto Naskh Arabic", sans-serif;
      background: #fff;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      min-height: 100vh;
      color: #2f4b3f;
    }

    .container {
      max-width: 380px;
      width: 90%;
      margin-top: 40px;
      text-align: center;
    }

    .avatar {
      width: 100px;
      margin: 0 auto 15px;
    }

    h2 {
      margin: 8px 0;
      font-size: 22px;
      font-weight: 700;
    }
    p {
      font-size: 14px;
      color: #999;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 12px;
      position: relative;
    }

    input {
      width: 100%;
      padding: 12px 40px 12px 12px;
      border: none;
      border-radius: 25px;
      background: #e6f0eb;
      font-size: 14px;
      outline: none;
      text-align: left;
      direction: ltr;
    }

    .form-group img {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      width: 18px;
      opacity: 0.7;
    }

    .btn {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 25px;
      background: #2f4b3f;
      color: #fff;
      font-size: 15px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s;
      margin-top: 10px;
    }

    .btn:hover {
      background: #3b5e4d;
    }

    .divider {
      margin: 18px 0 10px;
      font-size: 13px;
      color: #666;
    }

    .signin-link {
      margin-top: 12px;
      font-size: 13px;
      color: #555;
    }

    .signin-link a {
      color: #2f4b3f;
      text-decoration: none;
      font-weight: bold;
    }

    .google{
      width:100%;
      display:flex;
      align-items:center;
      justify-content:center;
      gap:10px;
      border:2px solid #dadce0;
      background:#fff;
      color:#3c4043;
      border-radius:12px;
      padding:12px;
      font-weight:600;
      cursor:pointer;
      font-size:14px;
      transition:0.2s;
    }
    .google:hover{
      background:#f7f8f8;
    }
    .google img{
      display:block;
    }

    .error-msg{
      color:#b23b3b;
      font-size:13px;
      margin-bottom:10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- صورة الأفاتار -->
    <img src="avatar.png" alt="User Icon" class="avatar">

    <h2>Let’s Get Started</h2>
    <p>create an account to get all features...</p>

    <?php if ($error): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- هنا الفورم الحقيقي اللي يرسل للـ PHP -->
    <form method="POST" action="signup.php">
      <div class="form-group">
        <input type="text" name="name" placeholder="Enter Your Full Name..." required>
      </div>
      <div class="form-group">
        <input type="text" placeholder="Enter Your ID...">
      </div>
      <div class="form-group">
        <input type="email" name="email" placeholder="Enter Your Email..." required>
      </div>
      <div class="form-group">
        <input type="password" name="password" id="password" placeholder="Password..." required>
      </div>
      <div class="form-group">
        <input type="password" name="password_confirm" id="confirm" placeholder="Confirm Password..." required>
      </div>
      <button type="submit" class="btn">Sign Up</button>
    </form>

    <p class="signin-link">
      Already have account?
      <a href="login.php">Sign in</a>
    </p>

    <div class="divider">you can connect with</div>

    <!-- زر التسجيل بجوجل (واجهة فقط) -->
    <button class="google" type="button" onclick="alert('Google Sign-In (واجهة فقط)')">
      <img src="google.png" alt="Google" width="20" height="20">
      <span>Sign Up with Google</span>
    </button>

  </div>
</body>
</html>
