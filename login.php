<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = trim($_POST['password'] ?? '');

  if ($email && $pass) {

    $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($pass, $user['password_hash'])) {
      $_SESSION['user_id'] = $user['id'];
      header('Location: main.php'); 
      exit;
    } else {
      $error = "بيانات الدخول غير صحيحة";
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
  <title>Login & Reset Password</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      background: #fff;
      margin: 0;
      padding-left:20px;
    }
    .container {
      max-width: 300px;
      margin: 60px auto;
      padding: 20px;
    }
    .icon {
      display: block;
      margin: 0 auto 20px auto;
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
    }
    h2 {
      margin-bottom: 5px;
      color: #476a58;
      text-align: left;
    }
    p {
      font-size: 14px;
      color: gray;
      margin-bottom: 20px;
      text-align: left;
    }
    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 8px;
      border: none;
      background-color: #dce4df;
      font-size: 14px;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #476a58;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }
    a {
      color: #476a58;
      font-size: 13px;
      text-decoration: none;
      text-align: left;
    }
    .password-wrapper {
      position: relative;
    }
    .toggle-password {
      position: absolute;
      right: 10px;
      top: 35%;
      cursor: pointer;
      font-size: 14px;
      color: #555;
    }
    #login-msg {
      margin:10px 0;
      font-size:13px;
    }
  </style>
</head>
<body>

  <!-- صفحة تسجيل الدخول -->
  <div class="container" id="loginPage">
    <img src="login.png" alt="User Icon" class="icon">
    <h2>Welcome back !</h2>
    <p>Let's login for explore continues</p>

    <?php if ($error): ?>
      <p id="login-msg" style="color:#b23b3b;"><?= htmlspecialchars($error) ?></p>
    <?php else: ?>
      <p id="login-msg"></p>
    <?php endif; ?>

    <!-- هذي هي الفورم الحقيقية اللي تروح للـ PHP -->
    <form method="POST">
      <!-- نخلي الحقل هذا للإيميل أو اليوزر لكن اسمه email عشان PHP -->
      <input type="email" name="email" id="userid" placeholder="Enter your email..." required>

      <div class="password-wrapper">
        <input type="password" name="password" id="password" placeholder="********" required>
        <span class="toggle-password" onclick="togglePassword('password')">👁</span>
      </div>

      <a href="#" onclick="showForgotPage(); return false;">forgot password?</a><br><br>

      <!-- مهم: الزر يكون type="submit" عشان يرسل الـ POST -->
      <button type="submit">Sign in</button>
    </form>

    <p>Don't have an account ? <a href="signup.php">Sign Up here</a></p>
  </div>

  <!-- صفحة استعادة كلمة المرور (فقط شكل، بدون باك إند حاليًا) -->
  <div class="container" id="forgotPage" style="display:none;">
    <img src="login.png" alt="User Icon" class="icon">
    <h2>forgot password ..</h2>
    <p>If you forget a password create a new password</p>

    <div class="password-wrapper">
      <input type="password" placeholder="password" id="newpass">
      <span class="toggle-password" onclick="togglePassword('newpass')">👁</span>
    </div>

    <div class="password-wrapper">
      <input type="password" placeholder="confirm password" id="confirmpass">
      <span class="toggle-password" onclick="togglePassword('confirmpass')">👁</span>
    </div>

    <button type="button" onclick="alert('Reset not implemented yet')">Done</button><br><br>

    <a href="#" onclick="showLoginPage(); return false;">Back to login</a>
  </div>

<script>
function togglePassword(id){
  const input = document.getElementById(id);
  if (!input) return;
  input.type = (input.type === 'password') ? 'text' : 'password';
}

function showForgotPage(){
  document.getElementById('loginPage').style.display = 'none';
  document.getElementById('forgotPage').style.display = 'block';
}
function showLoginPage(){
  document.getElementById('forgotPage').style.display = 'none';
  document.getElementById('loginPage').style.display = 'block';
}
</script>

</body>
</html>
