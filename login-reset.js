function togglePassword(id) {
  const input = document.getElementById(id);
  input.type = input.type === "password" ? "text" : "password";
}

/*عشان ينقل المستخدم من صفحة التسجيل لصحفة اعادة تعين الباسورد بدون رفرش الصفحه*/
function showForgotPage() {
  document.getElementById("loginPage").style.display = "none";
  document.getElementById("forgotPage").style.display = "block";
}

/*عشان ينقل المستخدم من صفحة التسجيل لصحفة اعادة تعين الباسورد بدون رفرش الصفحه*/
function showLoginPage() {
  document.getElementById("forgotPage").style.display = "none";
  document.getElementById("loginPage").style.display = "block";
}

function login() {
  const user = document.getElementById("userid").value;
  const pass = document.getElementById("password").value;
  if(user && pass) {
    alert("✅ تم تسجيل الدخول بنجاح)");
  } else {
    alert("⚠️ الرجاء إدخال اسم المستخدم وكلمة المرور");
  }
}

function resetPassword() {
  const newpass = document.getElementById("newpass").value;
  const confirmpass = document.getElementById("confirmpass").value;
  if(newpass && newpass === confirmpass) {
    alert("✅ Password reset successful!");
    showLoginPage();
  } else {
    alert("⚠️ Passwords do not match!");
  }
}
