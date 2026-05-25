<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Login</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="css/responsive.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="login-container">
        <div class="login-box">
            <h2 class="text-primary">LMS Portal</h2>
            <p>Login to your account</p>
<?php
    if (isset($_SESSION['login_error'])) {
    echo "<div class='alert alert-danger py-2 text-center' style='font-size:14px;'>" . $_SESSION['login_error'] . "</div>";
    unset($_SESSION['login_error']);
}
?>
<?php
if (isset($_SESSION['login_error'])) {
    echo "<div class='alert alert-danger py-2 text-center' style='font-size: 14px; border-radius: 8px;'>";
    echo $_SESSION['login_error'];
    echo "</div>";
    unset($_SESSION['login_error']);
}
?>
            
            <form action="auth.php" method="POST">
                
                <div class="mb-3">
                    <label class="form-label">User ID</label>
                    <input type="text" name="userid" class="form-control" placeholder="Enter ID" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="passwordField" class="form-control" required placeholder="Enter password">
                        <span class="input-group-text" id="togglePassword" style="cursor: pointer; background: white;">
                            <i class="bi bi-eye-slash" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Login As</label>
                    <select name="role" class="form-select">
                        <option value="student">Student</option>
                        <option value="instructor">Instructor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#passwordField');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            if (passwordField.getAttribute('type') === 'password') {
                passwordField.setAttribute('type', 'text');
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            } else {
                passwordField.setAttribute('type', 'password');
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            }
        });
    </script>
        
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>