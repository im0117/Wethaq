<?php
require 'auth_check.php';
require 'config.php';

$user_id = $_SESSION['user_id'];

// جلب الإشعارات الخاصة بهذا المستخدم
$stmt = $pdo->prepare("SELECT message, created_at, seen FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// نعلّم كل الإشعارات إنها مقروءة
$pdo->prepare("UPDATE notifications SET seen = 1 WHERE user_id = ?")->execute([$user_id]);
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>الإشعارات - WETHAQ</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    :root{
      --bg:#f7f9f7;
      --panel:#e6f0ea;
      --accent:#2d6a4f;
      --accent-2:#55a06b;
      --muted:#7b8b82;
      --text:#1f2a24;
      --white:#ffffff;
      --border:#eef3ee;
      --shadow:0 8px 30px rgba(0,0,0,0.08);
      font-family:'Segoe UI',Roboto,Arial,sans-serif;
    }
    *{box-sizing:border-box}
    body{
      background:var(--bg);
      margin:0;
      padding:20px;
      display:flex;
      justify-content:center;
      color:var(--text);
    }
    .phone{
      width:360px;
      max-width:100%;
      background:var(--white);
      border-radius:18px;
      box-shadow:var(--shadow);
      overflow:hidden;
    }
    header{
      display:flex;
      align-items:center;
      gap:8px;
      padding:14px 16px;
      border-bottom:1px solid var(--border);
    }
    .back-btn{
      font-size:20px;
      text-decoration:none;
      color:var(--accent);
      padding:4px 6px;
    }
    .title-wrap{
      flex:1;
      text-align:center;
    }
    h2{
      margin:0;
      font-size:18px;
      color:var(--accent);
    }
    main{
      padding:14px 16px 10px;
    }
    .section-label{
      font-size:13px;
      color:var(--muted);
      margin-bottom:6px;
    }
    .notif-list{
      display:flex;
      flex-direction:column;
      gap:8px;
    }
    .notif{
      background:var(--panel);
      border-radius:12px;
      padding:10px 12px;
      display:flex;
      gap:10px;
      align-items:flex-start;
    }
    .notif-icon{
      width:32px;
      height:32px;
      border-radius:50%;
      background:linear-gradient(135deg,var(--accent),var(--accent-2));
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:16px;
      color:#fff;
      flex-shrink:0;
    }
    .notif-body{flex:1;}
    .notif-title{
      font-size:14px;
      font-weight:600;
      margin:0 0 3px;
      color:var(--accent);
    }
    .notif-text{
      font-size:12px;
      margin:0;
      color:var(--text);
    }
    .notif-meta{
      margin-top:6px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      font-size:11px;
      color:var(--muted);
    }
    .badge{
      padding:2px 8px;
      border-radius:999px;
      font-size:11px;
    }
    .badge-new{
      background:#2d6a4f;
      color:#fff;
    }
    .badge-read{
      background:#dce7e0;
      color:#2d6a4f;
    }
    .empty{
      text-align:center;
      padding:40px 10px 30px;
      font-size:14px;
      color:var(--muted);
    }
    .empty span{
      display:block;
      font-size:26px;
      margin-bottom:10px;
    }
    @media (max-width:380px){
      .phone{width:92vw;}
    }
  </style>
</head>
<body>
  <div class="phone">
    <header>
      <a href="/wethaq/main.php" class="back-btn">⬅</a>
      <div class="title-wrap">
        <h2>الإشعارات</h2>
      </div>
      <div style="width:24px"></div>
    </header>

    <main>
      <?php if (empty($notes)): ?>
        <div class="empty">
          <span>🔔</span>
          لا توجد إشعارات حالياً، سنخبرك عند حدوث أي جديد في ملفاتك أو مواعيدك.
        </div>
      <?php else: ?>
        <div class="section-label">كل الإشعارات</div>
        <div class="notif-list">
          <?php foreach ($notes as $n): ?>
            <div class="notif">
              <div class="notif-icon">📄</div>
              <div class="notif-body">
                <p class="notif-title">
                  إشعار جديد
                </p>
                <p class="notif-text">
                  <?= htmlspecialchars($n['message'], ENT_QUOTES, 'UTF-8') ?>
                </p>
                <div class="notif-meta">
                  <span>
                    <?= htmlspecialchars(date('Y-m-d H:i', strtotime($n['created_at']))) ?>
                  </span>
                  <?php if ($n['seen']): ?>
                    <span class="badge badge-read">مقروء</span>
                  <?php else: ?>
                    <span class="badge badge-new">جديد</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
