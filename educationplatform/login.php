<?php
session_start();
require 'db.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = trim($_POST['role'] ?? 'user');

    // ── ADMIN LOGIN ──────────────────────────────────────
    if ($role === "admin") {
        if ($username === "admin" && $password === "admin123") {
            $_SESSION['admin'] = $username;
            header("Location: admin.php");
            exit();
        } else {
            $error = "Invalid admin username or password!";
        }

    // ── USER LOGIN ───────────────────────────────────────
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user']    = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            header("Location: home.php");
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    }
}

// Keep role state after failed login
$postedRole = $_POST['role'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login — EduPlatform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; }
        .login-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.12);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        .image-side {
            background: #2c3e50;
            position: relative;
            min-height: 520px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            padding: 30px;
        }
        .image-side img {
            position: absolute; top: 0; left: 0;
            width: 100%; height: 100%;
            object-fit: cover; opacity: 0.55;
        }
        .overlay-text {
            position: relative; z-index: 2;
            color: white; text-align: center;
        }
        .overlay-text h3 { font-size: 22px; font-weight: 700; }
        .overlay-text p  { font-size: 13px; opacity: 0.85; margin: 0; }
        .form-side {
            background: white; padding: 48px 40px;
            display: flex; flex-direction: column; justify-content: center;
        }
        .login-icon {
            width: 54px; height: 54px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px; transition: background 0.3s;
        }
        .role-toggle .btn { font-size: 13px; padding: 8px 20px; }
        .role-toggle .btn-check:checked + .btn-outline-dark {
            background: #2c3e50; border-color: #2c3e50; color: white;
        }
        .btn-login { background-color: #27ae60; border-color: #27ae60; }
        .btn-login:hover { background-color: #219a52; border-color: #219a52; color: white; }
        .btn-admin-login { background-color: #2c3e50; border-color: #2c3e50; }
        .btn-admin-login:hover { background-color: #1a252f; border-color: #1a252f; color: white; }
    </style>
</head>
<body>

<div class="min-vh-100 d-flex align-items-center justify-content-center p-3">
    <div class="login-card row g-0">

        <!-- Left: Image Side -->
        <div class="col-md-6 image-side">
            <img src="images/Unknown-3.jpg" alt="Login Visual">
            <div class="overlay-text">
                <h3>Welcome to Our Platform</h3>
                <p>Manage your work smarter, faster, and better.</p>
            </div>
        </div>

        <!-- Right: Form Side -->
        <div class="col-md-6 form-side">

            <!-- Role Toggle -->
            <div class="role-toggle d-flex justify-content-center mb-4">
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="roleToggle"
                           id="userToggle" autocomplete="off"
                           <?php echo $postedRole !== 'admin' ? 'checked' : ''; ?>>
                    <label class="btn btn-outline-dark" for="userToggle"
                           onclick="setRole('user')">
                        <i class="bi bi-person-fill me-1"></i>User
                    </label>

                    <input type="radio" class="btn-check" name="roleToggle"
                           id="adminToggle" autocomplete="off"
                           <?php echo $postedRole === 'admin' ? 'checked' : ''; ?>>
                    <label class="btn btn-outline-dark" for="adminToggle"
                           onclick="setRole('admin')">
                        <i class="bi bi-shield-lock-fill me-1"></i>Admin
                    </label>
                </div>
            </div>

            <!-- Icon + Title -->
            <div class="text-center mb-4">
                <div class="login-icon" id="loginIcon"
                     style="background:<?php echo $postedRole === 'admin' ? '#e74c3c' : '#2c3e50'; ?>;">
                    <i class="bi <?php echo $postedRole === 'admin' ? 'bi-shield-lock-fill' : 'bi-person-fill'; ?> text-white fs-5"
                       id="loginIconImg"></i>
                </div>
                <h4 class="fw-bold" style="color:#2c3e50;" id="loginTitle">
                    <?php echo $postedRole === 'admin' ? 'Admin Login' : 'Welcome Back'; ?>
                </h4>
                <p class="text-muted small" id="loginSubtitle">
                    <?php echo $postedRole === 'admin' ? 'Sign in to the admin dashboard' : 'Sign in to your account'; ?>
                </p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center small" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <!-- IMPORTANT: role hidden field -->
                <input type="hidden" name="role" id="roleField"
                       value="<?php echo htmlspecialchars($postedRole); ?>">

                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold small"
                           style="color:#2c3e50;">Username</label>
                    <input type="text" class="form-control" id="username"
                           name="username"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           placeholder="Enter your username" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold small"
                           style="color:#2c3e50;">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password"
                               name="password" placeholder="Enter your password" required>
                        <button class="btn btn-outline-secondary" type="button"
                                onclick="togglePass()">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3"
                     id="rememberRow"
                     style="display:<?php echo $postedRole === 'admin' ? 'none' : 'flex'; ?>!important">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               id="remember" name="remember">
                        <label class="form-check-label small text-muted"
                               for="remember">Remember me</label>
                    </div>
                    <a href="forgot_password.php" class="small"
                       style="color:#27ae60;">Forgot password?</a>
                </div>

                <div class="d-grid">
                    <button type="submit"
                            class="btn <?php echo $postedRole === 'admin' ? 'btn-admin-login' : 'btn-login'; ?> text-white"
                            id="loginBtn">
                        <?php if ($postedRole === 'admin'): ?>
                            <i class="bi bi-shield-check me-1"></i>Login as Admin
                        <?php else: ?>
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        <?php endif; ?>
                    </button>
                </div>
            </form>

            <p class="text-center text-muted small mt-4 mb-0" id="registerLink"
               style="display:<?php echo $postedRole === 'admin' ? 'none' : 'block'; ?>">
                Don't have an account?
                <a href="registration.php" style="color:#27ae60; font-weight:600;">
                    Create Account
                </a>
            </p>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function setRole(role) {
        const roleField    = document.getElementById('roleField');
        const loginIcon    = document.getElementById('loginIcon');
        const loginIconImg = document.getElementById('loginIconImg');
        const loginTitle   = document.getElementById('loginTitle');
        const loginSub     = document.getElementById('loginSubtitle');
        const loginBtn     = document.getElementById('loginBtn');
        const rememberRow  = document.getElementById('rememberRow');
        const registerLink = document.getElementById('registerLink');

        roleField.value = role;

        if (role === 'admin') {
            loginIcon.style.background = '#e74c3c';
            loginIconImg.className     = 'bi bi-shield-lock-fill text-white fs-5';
            loginTitle.textContent     = 'Admin Login';
            loginSub.textContent       = 'Sign in to the admin dashboard';
            loginBtn.className         = 'btn btn-admin-login text-white';
            loginBtn.innerHTML         = '<i class="bi bi-shield-check me-1"></i>Login as Admin';
            rememberRow.style.display  = 'none';
            registerLink.style.display = 'none';
        } else {
            loginIcon.style.background = '#2c3e50';
            loginIconImg.className     = 'bi bi-person-fill text-white fs-5';
            loginTitle.textContent     = 'Welcome Back';
            loginSub.textContent       = 'Sign in to your account';
            loginBtn.className         = 'btn btn-login text-white';
            loginBtn.innerHTML         = '<i class="bi bi-box-arrow-in-right me-1"></i>Login';
            rememberRow.style.display  = 'flex';
            registerLink.style.display = 'block';
        }
    }

    function togglePass() {
        const inp = document.getElementById('password');
        const ico = document.getElementById('eyeIcon');
        if (inp.type === 'password') {
            inp.type = 'text';
            ico.className = 'bi bi-eye-slash';
        } else {
            inp.type = 'password';
            ico.className = 'bi bi-eye';
        }
    }
</script>
</body>
</html>