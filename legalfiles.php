<?php require 'auth_check.php'; ?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>ملفاتي القانونية</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 0;
    }
    .container {
      width: 450px;
      margin: 20px auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
    }
    .header {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
    }
    .header button {
      margin-left: 10px;
      cursor: pointer;
      background: none;
      border: none;
      font-size: 20px;
    }
    h2 {
      margin: 0;
      flex: 1;
      text-align: center;
    }
    .tabs {
      display: flex;
      justify-content: space-between;
      margin: 15px 0;
      border-bottom: 2px solid #ccc;
    }
    .tab {
      flex: 1;
      text-align: center;
      padding: 8px;
      cursor: pointer;
      border-bottom: 3px solid transparent;
      font-size: 14px;
    }
    .tab.active {
      border-bottom: 3px solid #4d6653;
      font-weight: bold;
      color: #4d6653;
    }
    .file {
      background: #a4b8aa;
      padding: 12px;
      border-radius: 8px;
      margin: 10px 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #fff;
      font-size: 14px;
      cursor: pointer;
    }
    .file button {
      background: none;
      border: none;
      font-size: 18px;
      cursor: pointer;
      color: #fff;
    }
    .buttons {
      margin-top: 20px;
      text-align: center;
    }
    .btn {
      padding: 10px 15px;
      margin: 5px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      background: #a4b8aa;
      color: white;
      font-size: 14px;
    }
    .btn:hover {
      background: #8c9f8f;
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 10px;
      width: 350px;
      text-align: center;
      animation: fadeIn 0.3s ease;
    }
    .modal-content h3{margin-top: 0;}
    .modal-content button {
      margin-top: 15px;
      padding: 8px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      background: #a4b8aa;
      color: white;
    }
    .close {
      background: #d9534f;
    }
    input[type="text"], input[type="date"] {
      padding: 5px;
      width: 80%;
      margin: 5px 0;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: scale(0.9);}
      to {opacity: 1; transform: scale(1);}
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <a href="main.php" class="back-btn" style="font-size:20px; text-decoration:none;">⬅</a>
      <h2>ملفاتي القانونية</h2>
    </div>

    <div class="tabs">
      <div class="tab active" onclick="openTab('all', this)">كل الملفات</div>
      <div class="tab" onclick="openTab('docs', this)">مستندات قانونية</div>
      <div class="tab" onclick="openTab('upcoming', this)">مواعيد قادمة</div>
      <div class="tab" onclick="openTab('finished', this)">منتهية</div>
    </div>

    <!-- محتوى التابات (راح يتعبّى من الباك إند) -->
    <div id="all" class="tab-content"></div>
    <div id="docs" class="tab-content" style="display:none"></div>
    <div id="upcoming" class="tab-content" style="display:none"></div>
    <div id="finished" class="tab-content" style="display:none"></div>

    <div class="buttons">
      <button class="btn" onclick="share()">مشاركة</button>
      <button class="btn" onclick="downloadPDF()">تحميل PDF</button>
      <button class="btn" onclick="printPage()">طباعة</button>
      <button class="btn" onclick="openAddModal()">إضافة ملف جديد</button>
    </div>
  </div>

  <!-- مودال عرض الملف -->
  <div id="fileModal" class="modal">
    <div class="modal-content">
      <h3 id="modalTitle">عنوان الملف</h3>
      <p id="modalDate">تاريخ آخر تحديث</p>
      <button onclick="downloadFile()">تحميل الملف</button>
      <button class="close" onclick="closeModal()">إغلاق</button>
    </div>
  </div>

  <!-- مودال إضافة ملف جديد -->
  <div id="addModal" class="modal">
    <div class="modal-content">
      <h3>إضافة ملف جديد</h3>
      <input type="text" id="newFileName" placeholder="اسم الملف">
      <input type="date" id="newFileDate">
      <br>
      <button onclick="addNewFile()">إضافة</button>
      <button class="close" onclick="closeAddModal()">إغلاق</button>
    </div>
  </div>

<script>
  let currentFile = "";

  function openTab(tabId, element) {
    document.querySelectorAll(".tab-content").forEach(content => content.style.display = "none");
    document.getElementById(tabId).style.display = "block";
    document.querySelectorAll(".tab").forEach(tab => tab.classList.remove("active"));
    element.classList.add("active");
  }

  function openModal(name, dateText) {
    currentFile = name;
    document.getElementById("modalTitle").innerText = name;
    document.getElementById("modalDate").innerText = "تاريخ آخر تحديث: " + dateText;
    document.getElementById("fileModal").style.display = "flex";
  }

  function closeModal() {
    document.getElementById("fileModal").style.display = "none";
  }

  function downloadFile() {
    alert("يتم الآن تحميل الملف: " + currentFile);
    closeModal();
  }

  function share() {
    alert("تمت المشاركة بنجاح ✅");
  }

  function downloadPDF() {
    alert("سيتم تنزيل الملفات بصيغة PDF...");
  }

  function printPage() {
    window.print();
  }

  function openAddModal() {
    document.getElementById("addModal").style.display = "flex";
  }

  function closeAddModal() {
    document.getElementById("addModal").style.display = "none";
  }

  // =========================
  // جلب الملفات من الباك إند
  // =========================
  async function loadUserFiles() {
    try {
      const response = await fetch('/wethaq/api/get_files.php'); // مسار مطلق
      if (!response.ok) {
        console.error("خطأ في الاستجابة", response.status);
        alert("خطأ في تحميل الملفات من الخادم");
        return;
      }
      const files = await response.json();
      renderFiles(files);
    } catch (e) {
      console.error("خطأ في الاتصال بالباك إند", e);
      alert("تعذر الاتصال بالسيرفر");
    }
  }

  function renderFiles(files) {
    const allTab = document.getElementById("all");
    const docsTab = document.getElementById("docs");
    const upcomingTab = document.getElementById("upcoming");
    const finishedTab = document.getElementById("finished");

    allTab.innerHTML = "";
    docsTab.innerHTML = "";
    upcomingTab.innerHTML = "";
    finishedTab.innerHTML = "";

    files.forEach(f => {
      const dateText = new Date(f.file_date).toLocaleDateString('ar-SA');

      const div = document.createElement("div");
      div.className = "file";
      div.onclick = function() { openModal(f.title, dateText); };
      div.innerHTML = `<span>${f.title} - ${dateText}</span><button>⬇</button>`;

      allTab.appendChild(div.cloneNode(true));

      if (f.status === 'doc') {
        docsTab.appendChild(div.cloneNode(true));
      } else if (f.status === 'upcoming') {
        upcomingTab.appendChild(div.cloneNode(true));
      } else if (f.status === 'finished') {
        finishedTab.appendChild(div.cloneNode(true));
      }
    });
  }

  // =========================
  // إضافة ملف جديد
  // =========================
  async function addNewFile() {
    const name = document.getElementById("newFileName").value.trim();
    const date = document.getElementById("newFileDate").value;

    if (!name || !date) {
      alert("يرجى إدخال اسم الملف والتاريخ!");
      return;
    }

    const status = 'doc'; // مبدئيًا نخليها مستند قانوني

    try {
      const response = await fetch('/wethaq/api/add_file.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ title: name, date: date, status: status })
      });

      const result = await response.json();
      if (result.status === "ok") {
        closeAddModal();
        document.getElementById("newFileName").value = "";
        document.getElementById("newFileDate").value = "";
        loadUserFiles();
      } else {
        console.error(result);
        alert("صار خطأ أثناء حفظ الملف");
      }
    } catch (e) {
      console.error("خطأ في الإرسال", e);
      alert("تعذر الاتصال بالسيرفر");
    }
  }

  document.addEventListener("DOMContentLoaded", loadUserFiles);
</script>

</body>
</html>
