<?php
session_start();
require 'db.php';
if (isset($_SESSION['user'])) { header("Location: home.php"); exit(); }

$error   = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username  = trim($_POST['username']);
    $password  = trim($_POST['password']);
    $confirm   = trim($_POST['confirm']);
    $full_name = trim($_POST['full_name']);
    $state     = trim($_POST['state']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);

    if (strlen($username) < 3) {
        $error = "Username must be at least 3 characters.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            $error = "Username already taken. Please choose another.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                "INSERT INTO users (username, password, full_name, state, email, phone) VALUES (?,?,?,?,?,?)"
            );
            $stmt->execute([$username, $hashed, $full_name, $state, $email, $phone]);
            $success = "Account created successfully! Redirecting to login...";
            header("Refresh: 2; url=login.php");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account — EduPlatform</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    background-color: #f4f6f9;
    font-family: 'Segoe UI', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px 16px;
}

.register-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.10);
    width: 100%;
    max-width: 680px;
    overflow: hidden;
}

/* ── HEADER ── */
.card-top {
    background: #2c3e50;
    padding: 28px 36px;
    display: flex;
    align-items: center;
    gap: 16px;
}
.card-top-icon {
    width: 52px; height: 52px;
    background: rgba(255,255,255,0.12);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; color: #fff; flex-shrink: 0;
}
.card-top h2 {
    font-size: 20px; font-weight: 700; color: #fff; margin: 0 0 3px;
}
.card-top p { font-size: 13px; color: rgba(255,255,255,0.65); margin: 0; }

/* ── BODY ── */
.card-body { padding: 30px 36px; }

.back-link {
    display: inline-flex; align-items: center; gap: 6px;
    color: #6c757d; font-size: 13px; text-decoration: none;
    margin-bottom: 22px; transition: color 0.2s;
}
.back-link:hover { color: #27ae60; }

/* ── SECTION LABEL ── */
.section-label {
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 1px;
    color: #27ae60; margin-bottom: 14px;
    padding-bottom: 8px;
    border-bottom: 2px solid #eafaf1;
}

/* ── FORM ── */
.row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

.field-group { margin-bottom: 16px; }

.field-label {
    display: block; font-size: 12px; font-weight: 600;
    color: #2c3e50; text-transform: uppercase;
    letter-spacing: 0.7px; margin-bottom: 7px;
}
.field-required { color: #27ae60; margin-left: 2px; }

.field-input {
    width: 100%;
    background: #f8f9fa;
    border: 1.5px solid #e0e0e0;
    border-radius: 9px;
    padding: 10px 13px;
    color: #2c3e50;
    font-size: 14px;
    font-family: inherit;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
}
.field-input:focus {
    border-color: #27ae60;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(39,174,96,0.1);
}
.field-input::placeholder { color: #adb5bd; }

/* ── PASSWORD ── */
.pass-wrap { position: relative; }
.pass-toggle {
    position: absolute; right: 11px; top: 50%;
    transform: translateY(-50%);
    background: none; border: none; color: #adb5bd;
    cursor: pointer; font-size: 15px; padding: 2px;
}
.pass-toggle:hover { color: #2c3e50; }

/* ── STRENGTH BAR ── */
.strength-bar { margin-top: 6px; }
.strength-track {
    height: 4px; background: #e9ecef;
    border-radius: 99px; overflow: hidden;
}
.strength-fill {
    height: 100%; border-radius: 99px;
    transition: width 0.3s, background 0.3s; width: 0;
}
.strength-text { font-size: 11px; color: #adb5bd; margin-top: 3px; }

/* ── ALERTS ── */
.alert-box {
    border-radius: 10px; padding: 12px 14px;
    display: flex; align-items: center; gap: 10px;
    font-size: 13px; margin-bottom: 18px; font-weight: 500;
}
.alert-error {
    background: #fff5f5; border: 1px solid #ffcdd2; color: #c0392b;
}
.alert-success {
    background: #f0fdf4; border: 1px solid #a7f3d0; color: #1a7a4a;
}

/* ── SUBMIT ── */
.btn-register {
    width: 100%; padding: 13px;
    border: none; border-radius: 10px;
    background: linear-gradient(135deg, #27ae60, #219a52);
    color: #fff; font-size: 15px; font-weight: 700;
    cursor: pointer; transition: all 0.2s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    margin-top: 6px;
}
.btn-register:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(39,174,96,0.35);
}

/* ── FOOTER ── */
.card-footer-row {
    text-align: center; font-size: 13px; color: #6c757d;
    padding: 18px 36px;
    border-top: 1px solid #f0f0f0;
    background: #fafbfc;
}
.card-footer-row a {
    color: #27ae60; text-decoration: none; font-weight: 600;
}
.card-footer-row a:hover { text-decoration: underline; }

@media (max-width: 580px) {
    .card-top, .card-body { padding: 22px 18px; }
    .card-footer-row { padding: 16px 18px; }
    .row2 { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<div class="register-card">

    <!-- Header -->
    <div class="card-top">
        <div class="card-top-icon">
            <i class="bi bi-person-plus-fill"></i>
        </div>
        <div>
            <h2>Create Your Account</h2>
            <p>Join EduPlatform and start learning today</p>
        </div>
    </div>

    <!-- Body -->
    <div class="card-body">

        <a href="login.php" class="back-link">
            <i class="bi bi-arrow-left"></i> Back to Login
        </a>

        <?php if ($error): ?>
        <div class="alert-box alert-error">
            <i class="bi bi-exclamation-circle-fill"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert-box alert-success">
            <i class="bi bi-check-circle-fill"></i>
            <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">

            <!-- Account Credentials -->
            <div class="section-label">
                <i class="bi bi-lock-fill me-1"></i> Account Credentials
            </div>

            <div class="field-group">
                <label class="field-label">
                    Username <span class="field-required">*</span>
                </label>
                <input type="text" name="username" class="field-input"
                       placeholder="Choose a username (min. 3 characters)"
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                       required minlength="3">
            </div>

            <div class="row2">
                <div class="field-group">
                    <label class="field-label">
                        Password <span class="field-required">*</span>
                    </label>
                    <div class="pass-wrap">
                        <input type="password" name="password" class="field-input"
                               placeholder="Min. 6 characters" id="passMain"
                               required oninput="checkStrength(this.value)">
                        <button type="button" class="pass-toggle"
                                onclick="toggleVis('passMain','eye1')">
                            <i class="bi bi-eye" id="eye1"></i>
                        </button>
                    </div>
                    <div class="strength-bar">
                        <div class="strength-track">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <div class="strength-text" id="strengthText"></div>
                    </div>
                </div>
                <div class="field-group">
                    <label class="field-label">
                        Confirm Password <span class="field-required">*</span>
                    </label>
                    <div class="pass-wrap">
                        <input type="password" name="confirm" class="field-input"
                               placeholder="Repeat your password" id="passConf" required>
                        <button type="button" class="pass-toggle"
                                onclick="toggleVis('passConf','eye2')">
                            <i class="bi bi-eye" id="eye2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="section-label" style="margin-top: 6px;">
                <i class="bi bi-person-fill me-1"></i> Personal Information
            </div>

            <div class="row2">
                <div class="field-group">
                    <label class="field-label">
                        Full Name <span class="field-required">*</span>
                    </label>
                    <input type="text" name="full_name" class="field-input"
                           placeholder="Your full name"
                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                           required>
                </div>
                <div class="field-group">
                    <label class="field-label">State / Region</label>
                    <input type="text" name="state" class="field-input"
                           placeholder="e.g. Kerala"
                           value="<?php echo isset($_POST['state']) ? htmlspecialchars($_POST['state']) : ''; ?>">
                </div>
            </div>

            <div class="row2">
                <div class="field-group">
                    <label class="field-label">
                        Email Address <span class="field-required">*</span>
                    </label>
                    <input type="email" name="email" class="field-input"
                           placeholder="you@example.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           required>
                </div>
                <div class="field-group">
                    <label class="field-label">Phone Number</label>
                    <input type="tel" name="phone" class="field-input"
                           placeholder="+91 xxxxxxxxxx"
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>
            </div>

            <button type="submit" class="btn-register">
                <i class="bi bi-person-check-fill"></i> Create Account
            </button>

        </form>
    </div>

    <!-- Footer -->
    <div class="card-footer-row">
        Already have an account?
        <a href="login.php">Sign in here</a>
    </div>

</div>

<script>
function toggleVis(inputId, iconId) {
    const inp = document.getElementById(inputId);
    const ico = document.getElementById(iconId);
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        ico.className = 'bi bi-eye';
    }
}

function checkStrength(val) {
    const fill = document.getElementById('strengthFill');
    const text = document.getElementById('strengthText');
    let score = 0;
    if (val.length >= 6)            score++;
    if (val.length >= 10)           score++;
    if (/[A-Z]/.test(val))          score++;
    if (/[0-9]/.test(val))          score++;
    if (/[^A-Za-z0-9]/.test(val))   score++;
    const levels = [
        { w:'0%',   c:'transparent', t:'' },
        { w:'20%',  c:'#e74c3c',     t:'Very weak' },
        { w:'40%',  c:'#e67e22',     t:'Weak' },
        { w:'60%',  c:'#f1c40f',     t:'Fair' },
        { w:'80%',  c:'#8bc34a',     t:'Strong' },
        { w:'100%', c:'#27ae60',     t:'Very strong' },
    ];
    const l = levels[score] || levels[0];
    fill.style.width      = l.w;
    fill.style.background = l.c;
    text.textContent      = l.t;
    text.style.color      = l.c;
}
</script>
</body>
</html>