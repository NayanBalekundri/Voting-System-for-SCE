const passwordInput = document.getElementById("password");
const togglePassword = document.getElementById("togglePassword");

togglePassword.addEventListener("click", function () {
  const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
  passwordInput.setAttribute("type", type);
  this.classList.toggle("fa-eye");
  this.classList.toggle("fa-eye-slash");
});

passwordInput.addEventListener("input", function () {
  if (this.value.length > 0) {
    togglePassword.style.display = "block";
  } else {
    togglePassword.style.display = "none";
    togglePassword.classList.remove("fa-eye-slash");
    togglePassword.classList.add("fa-eye");
    passwordInput.setAttribute("type", "password");
  }
});
