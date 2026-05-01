<?php
require 'auth_check.php';   // يتأكد إن المستخدم مسجّل دخول
require 'config.php';       // يجيب اتصال $pdo

// جلب اسم المستخدم من قاعدة البيانات
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$displayName = $user && !empty($user['name']) ? $user['name'] : "مستخدم";
if (function_exists('mb_substr')) {
  $initial = mb_substr($displayName, 0, 1, "UTF-8");
} else {
  $initial = substr($displayName, 0, 1);
}

$user_id = $_SESSION['user_id'];

$unreadStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND seen = 0");
$unreadStmt->execute([$user_id]);
$unread_count = (int)$unreadStmt->fetchColumn();

// 🔔 إشعارات الجلسات القريبة (جلسات تاريخها غداً ولم يُرسل لها إشعار بعد)
// ملاحظة: هذا يعتمد إن عندك جدول appointments مثل ما اتفقنا
$tomorrow = date('Y-m-d', strtotime('+1 day'));

try {
  $q = $pdo->prepare("
    SELECT id, title, session_date
    FROM appointments
    WHERE user_id = ?
      AND session_date = ?
      AND notified_upcoming = 0
  ");
  $q->execute([$user_id, $tomorrow]);
  $upcoming = $q->fetchAll(PDO::FETCH_ASSOC);

  foreach ($upcoming as $app) {
    $msg = "لديك جلسة قريبة بتاريخ: " . $app['session_date'];
    $note = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $note->execute([$user_id, $msg]);

    // علّم أنه أُرسل إشعار لهذه الجلسة
    $pdo->prepare("UPDATE appointments SET notified_upcoming = 1 WHERE id = ?")
        ->execute([$app['id']]);
  }
} catch (Exception $e) {
  // لو جدول appointments مو موجود، نتجاهل الخطأ عشان الصفحة ما توقف
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>WETHAQ — المنصة القانونية</title>
  <style>
    :root{
      --bg:#f7f9f7;
      --panel:#e6f0ea;
      --accent:#2d6a4f;
      --accent-2:#55a06b;
      --muted:#7b8b82;
      --text:#1f2a24;
      --white:#ffffff;
      --radius:14px;
      --shadow:0 8px 30px rgba(0,0,0,0.08);
      --border:#eef3ee;
      font-family:'Segoe UI',Roboto,Arial,sans-serif;
    }

    *{box-sizing:border-box}
    body{
      background:var(--bg);
      margin:0;
      padding:20px;
      display:flex;
      justify-content:center;
      color:var(--text)
    }
    .phone{
      width:360px;
      max-width:100%;
      background:var(--white);
      border-radius:18px;
      box-shadow:var(--shadow);
      overflow:hidden
    }

    header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:14px 16px;
      border-bottom:1px solid var(--border)
    }
    .logo{display:flex;
      align-items:center;
      gap:8px}

    .logo .mark{width:34px;
      height:28px;
      background:linear-gradient(135deg,var(--accent),var(--accent-2));
      border-radius:6px}

    .logo-img{
      width:39px;
      height:39px;
      object-fit:contain;
      border-radius:6px;
    }

    .brand{
      font-weight:700;
      color:var(--accent)
    }

    .profile{
      display:flex; 
      align-items:center;
      gap:8px;color:var(--muted)
    }

    .profile .who{
      line-height:1.1
    } 

    .avatar{
      width:36px; 
      height:36px;
      border-radius:50%;
      background:linear-gradient(180deg,#e9f4ee,#d7efe1);
      display:flex;align-items:center;justify-content:center;
      font-weight:600;color:var(--accent)
    }

    main.content{padding:20px}
    .greeting{font-size:20px;color:var(--accent);margin-bottom:18px;display:flex;align-items:center;gap:10px}
    .greeting .wave{font-size:20px}

    .card{
      width:100%;text-align:start;background:var(--panel);
      padding:14px 12px;border-radius:12px;
      display:flex;align-items:center;gap:12px;margin-bottom:12px;cursor:pointer;
      border:0;transition:transform .12s ease,box-shadow .12s ease,background .12s ease
    }
    .card:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(45,106,79,0.08)}
    .card:focus-visible{outline:2px solid var(--accent);outline-offset:2px}
    .card .icon{
      width:44px;height:44px;border-radius:10px;
      background:linear-gradient(180deg,#ffffff66,#ffffff22);
      display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:20px
    }
    .titles{display:flex;flex-direction:column}
    .title{font-weight:700;color:var(--accent)}
    .subtitle{color:var(--muted);font-size:13px}
    .spacer{height:18px}

    nav.bottom-nav{
      display:flex;gap:8px;padding:12px 16px;border-top:1px solid var(--border)
    }
    .nav-item{
      flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;
      font-size:12px;color:var(--muted);cursor:pointer;user-select:none
    }
    .nav-item .nav-icon{
      width:28px;
      height:28px;
      margin-bottom:6px;
      display:flex;
      align-items:center;
      justify-content:center;
      position:relative; /* عشان النقطة الحمراء تتثبت على الأيقونة */
    }
    .nav-item:focus-visible{
      outline:2px 
      solid var(--accent);
      border-radius:10px;
      outline-offset:3px
    }
    /* الدائرة الحمراء للإشعارات الجديدة */
    .notif-dot{
      position:absolute;
      top:-2px;
      right:-2px;
      width:10px;
      height:10px;
      border-radius:50%;
      background:#e03131;
      border:2px solid #ffffff;
    }

    @media (max-width:380px){.phone{width:92vw}}
  </style>
</head>
<body>
  <div class="phone">
    <header>
      <div class="logo">
        <img src="wethaq logo.png" class="logo-img" alt="Wethaq Logo">
        <div class="brand">WETHAQ</div>
      </div>
      <div class="profile" aria-label="الملف الشخصي">
        <div class="who">
          <div style="font-size:13px;color:var(--muted)">أهلاً</div>
          <div style="font-weight:700;color:var(--accent)">
            <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?>
          </div>
        </div>
        <div class="avatar" aria-hidden="true">
          <?= htmlspecialchars($initial, ENT_QUOTES, 'UTF-8') ?>
        </div>
      </div>
    </header>

    <main class="content">
      <div class="greeting">
        <span class="wave">👋</span>
        <div>مرحباً بك في وثاق</div>
      </div>

      <!-- كروت الميزات -->
      <button class="card" data-link="assistant.html">
        <div class="icon" aria-hidden="true">🤖</div>
        <div class="titles">
          <div class="title">مساعد الذكاء الاصطناعي القانوني</div>
          <div class="subtitle">استشارات فورية باستخدام الذكاء الاصطناعي</div>
        </div>
      </button>

      <button class="card" data-link="appointment.html">
        <div class="icon" aria-hidden="true">📅</div>
        <div class="titles">
          <div class="title">احجز موعد مع محامي</div>
          <div class="subtitle">حدد موعدك مع محامي معتمد</div>
        </div>
      </button>

      <button class="card" data-link="legalfiles.php">
        <div class="icon" aria-hidden="true">📁</div>
        <div class="titles">
          <div class="title">ملفاتي القانونية</div>
          <div class="subtitle">إدارة ورفع المستندات القانونية</div>
        </div>
      </button>

      <div class="spacer"></div>
    </main>

    <!-- تنقّل سفلي مباشر -->
    <nav class="bottom-nav" role="navigation" aria-label="القائمة السفلية">
      <button class="nav-item" id="nav-home" type="button" aria-label="الرئيسية">
        <div class="nav-icon" aria-hidden="true">🏠</div>
        <div>الرئيسية</div>
      </button>
      <button class="nav-item" id="nav-appointments" type="button" aria-label="مواعيدي">
        <div class="nav-icon" aria-hidden="true">🗓</div>
        <div>مواعيدي</div>
      </button>
      <button class="nav-item" id="nav-notifs" type="button" aria-label="الإشعارات">
        <div class="nav-icon" aria-hidden="true">
          🔔
          <?php if ($unread_count > 0): ?>
            <span class="notif-dot"></span>
          <?php endif; ?>
        </div>
        <div>الإشعارات</div>
      </button>
      <button class="nav-item" id="nav-account" type="button" aria-label="My Account">
        <div class="nav-icon" aria-hidden="true">👤</div>
        <div>حسابي</div>
      </button>
    </nav>
  </div>

  <script>
    // الكروت تنتقل مباشرة
    document.querySelectorAll('.card').forEach(btn=>{
      btn.addEventListener('click',()=>{
        window.location.href = btn.dataset.link;
      });
    });

    // الرئيسية
    document.getElementById('nav-home').addEventListener('click',()=>{
      window.location.href = '/wethaq/main.php';
    });

    // مواعيدي
    document.getElementById('nav-appointments').addEventListener('click',()=>{
      window.location.href = '/wethaq/appointment.html';
    });

    // الإشعارات
    document.getElementById('nav-notifs').addEventListener('click',()=>{
      window.location.href = '/wethaq/notifications.php';
    });

    // حسابي
    document.getElementById('nav-account').addEventListener('click',()=>{
      window.location.href = '/wethaq/account.php';
    });

    // دعم لوحة المفاتيح
    document.querySelectorAll('button,.card').forEach(el=>{
      el.addEventListener('keyup',e=>{
        if(e.key==='Enter'||e.key===' '){el.click();}
      });
    });
  </script>
</body>
</html>
