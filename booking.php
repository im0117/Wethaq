<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>حجز موعد - Wethaq</title>
  <link rel="stylesheet" href="style-2.css">

  <style>
    /* مودال أنيق */
    .modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.35);display:none;align-items:center;justify-content:center;z-index:1000}
    .modal{background:#fff;width:min(92vw,420px);border-radius:14px;box-shadow:0 20px 60px rgba(0,0,0,.18);padding:18px}
    .modal h3{margin:0 0 8px 0;font-size:18px;color:#2d6a4f}
    .modal p{margin:0 0 12px 0;font-size:14px;color:#233}
    .modal .actions{display:flex;gap:8px;justify-content:flex-end;margin-top:14px}
    .btn{padding:10px 14px;border-radius:10px;border:1px solid #dfe7e2;background:#2d6a4f;color:#fff;font-weight:700;cursor:pointer}
    .btn.ghost{background:#fff;color:#2d6a4f}
  </style>
</head>
<body>
  <div class="app-container">
    <!-- الهيدر -->
    <header>
      <!-- سنبدّل سلوك زر الرجوع ليعرض مودال تأكيد الخروج بدلاً من history.back مباشرة -->
      <button type="button" id="backBtn" class="back-btn" aria-label="رجوع" onclick="window.location.href='appointment.html'">←</button>
      <div>
        <h1>وثاق Wethaq</h1>
        <p class="subtitle">منصتك القانونية الذكية</p>
      </div>
    </header>

    <main>
      <!-- معلومات المحامي -->
      <div class="lawyer-card">
        <h3 id="lawyerName">اسم المحامي</h3>
        <p id="lawyerSpecialty">التخصص</p>
        <span class="stars">⭐⭐⭐⭐☆</span>
      </div>

      <!-- اختيار التاريخ -->
      <section class="calendar">
        <h3>اختر التاريخ</h3>
        <input type="date" id="date">
      </section>

      <!-- اختيار الفترة -->
      <section class="time-slots">
        <h3>اختر الفترة</h3>
        <button type="button">8:00 - 11:30 صباحًا</button>
        <button type="button">4:00 - 10:30 مساءً</button>
      </section>

      <!-- نوع الجلسة -->
      <section class="session-type">
        <h3>نوع الجلسة</h3>
        <button type="button">مكالمة صوتية</button>
        <button type="button">مكالمة فيديو</button>
        <button type="button">حضوري</button>
      </section>

      <!-- ملخص -->
      <section class="summary">
        <h3>ملخص الحجز</h3>
        <p id="summaryText">اختر التفاصيل أعلاه</p>
      </section>

      <!-- زر التأكيد -->
      <button type="button" class="confirm" id="confirmBtn">تأكيد الحجز</button>

      <hr style="width:85%; margin: 16px auto; opacity:.3;">
      <p class="note">سيتم إرسال إشعار بالتأكيد إلى بريدك الإلكتروني.</p>
    </main>
  </div>

  <!-- مودال عام قابل لإعادة الاستخدام -->
  <div class="modal-backdrop" id="modal">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="m-title">
      <h3 id="m-title">تنبيه</h3>
      <p id="m-body">نص المودال</p>
      <div class="actions" id="m-actions">
        <!-- تُحقن الأزرار برمجيًا -->
      </div>
    </div>
  </div>

  <script>
    /* ====== أدوات مودال عامة ====== */
    const $ = (s,root=document)=>root.querySelector(s);
    const modal = $("#modal"), mt = $("#m-title"), mb = $("#m-body"), ma = $("#m-actions");

    function openModal({title, body, actions=[{label:'حسناً', kind:'primary', onClick:closeModal}]}) {
      mt.textContent = title || 'تنبيه';
      mb.innerHTML = body || '';
      ma.innerHTML = '';
      actions.forEach(a=>{
        const b = document.createElement('button');
        b.className = 'btn' + (a.kind === 'ghost' ? ' ghost' : '');
        b.type = 'button';
        b.textContent = a.label;
        b.onclick = ()=>{ (a.onClick||closeModal)(); };
        ma.appendChild(b);
      });
      modal.style.display = 'flex';
      // إغلاق عند الضغط خارج الصندوق
      const onBackdrop = (e)=>{ if(e.target===modal) closeModal(); };
      modal.addEventListener('click', onBackdrop, {once:true});
    }
    function closeModal(){ modal.style.display = 'none'; }

    /* ====== من سكربتك الحالي ====== */
    const urlParams = new URLSearchParams(window.location.search);
    const lawyer = urlParams.get("lawyer");
    const specialty = urlParams.get("specialty");

    const nameEl = document.getElementById("lawyerName");
    const specEl = document.getElementById("lawyerSpecialty");

    if (lawyer) nameEl.textContent = lawyer;
    if (specialty) specEl.textContent = specialty;

    const dateInput = document.getElementById("date");
    const timeButtons = document.querySelectorAll(".time-slots button");
    const sessionButtons = document.querySelectorAll(".session-type button");
    const summaryText = document.getElementById("summaryText");
    const confirmBtn = document.getElementById("confirmBtn");
    const backBtn = document.getElementById("backBtn");

    // منع تواريخ سابقة
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, "0");
    const dd = String(today.getDate()).padStart(2, "0");
    dateInput.min = `${yyyy}-${mm}-${dd}`;

    let chosenDate = "", chosenTime = "", chosenSession = "";
    let hasBooked = false; // لتتبّع إتمام الحجز

    const esc = (s) => String(s)
      .replace(/&/g, "&amp;").replace(/</g, "&lt;")
      .replace(/>/g, "&gt;").replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");

    function updateSummary() {
      const lines = [];
      if (lawyer) lines.push("👨‍⚖ المحامي: " + esc(lawyer));
      if (specialty) lines.push("📌 التخصص: " + esc(specialty));
      if (chosenDate) lines.push("📅 التاريخ: " + esc(chosenDate));
      if (chosenTime) lines.push("⏰ الفترة: " + esc(chosenTime));
      if (chosenSession) lines.push("💬 الجلسة: " + esc(chosenSession));
      summaryText.innerHTML = lines.length ? lines.join("<br>") : "اختر التفاصيل أعلاه";
    }

    dateInput.addEventListener("change", () => { chosenDate = dateInput.value; updateSummary(); });
    timeButtons.forEach(btn => {
      btn.addEventListener("click", () => {
        timeButtons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        chosenTime = btn.textContent.trim();
        updateSummary();
      });
    });
    sessionButtons.forEach(btn => {
      btn.addEventListener("click", () => {
        sessionButtons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        chosenSession = btn.textContent.trim();
        updateSummary();
      });
    });

    // استبدال alert عند تأكيد الحجز بمودال أنيق
   // استبدال alert عند تأكيد الحجز بمودال + ربط بالباك إند
confirmBtn.addEventListener("click", () => {
  if (!lawyer || !specialty || !chosenDate || !chosenTime || !chosenSession) {
    openModal({
      title: "أكمل الحقول",
      body: "رجاءً املئ: <b>المحامي، التخصص، التاريخ، الفترة، ونوع الجلسة</b>.",
      actions: [
        {label:"حسناً", kind:"primary", onClick: closeModal}
      ]
    });
    return;
  }

  // نص العنوان اللي بنخزّنه في قاعدة البيانات
  const fullTitle = `جلسة مع ${lawyer} - ${specialty} - ${chosenSession} - ${chosenTime}`;

  // إرسال الموعد للباك إند
  fetch('/wethaq/api/add_appointment.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      title: fullTitle,
      date: chosenDate   // هذا يروح لـ session_date في الجدول
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === "ok") {
      // تم الحجز وحُفظ في الداتابيس + انضاف إشعار
      hasBooked = true;
      openModal({
        title: "تم حجز الموعد ✅",
        body: "تم حفظ الموعد وسيظهر في إشعاراتك.",
        actions: [
          {label:"إغلاق", kind:"ghost", onClick: closeModal},
          {label:"الذهاب للرئيسية", kind:"primary", onClick: ()=>{ window.location.href = "main.php"; }}
        ]
      });
    } else {
      openModal({
        title: "خطأ في الحجز",
        body: "تعذر حفظ الموعد حالياً، حاول مرة أخرى.",
        actions: [
          {label:"حسناً", kind:"primary", onClick: closeModal}
        ]
      });
    }
  })
  .catch(err => {
    console.error(err);
    openModal({
      title: "خطأ في الاتصال",
      body: "تعذر الاتصال بالسيرفر، تأكدي أن الخادم يعمل.",
      actions: [
        {label:"حسناً", kind:"primary", onClick: closeModal}
      ]
    });
  });
});
    updateSummary();
  </script>
</body>
</html>
