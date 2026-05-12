<!-- <!DOCTYPE html>
<html>
<head>
    <title>contact Us</title>
</head>
<body>

<h1>contact us</h1>

<a href="home.php">Home </a>|
<a href="about.php">About Us</a> |
<a href="courses.php">Courses</a> ||
<a href="logout.php">Logout</a>

<hr>

<p>get in touch</p>
<p>hello helooo heloooo</P>

</body>
</html> -->

<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact Us</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar {
            width: 230px;
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: #2c3e50;
            z-index: 100;
        }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); border-radius: 5px; }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active { background: #34495e; color: white; }
        .sidebar .brand {
            font-size: 17px;
            font-weight: 700;
            color: white;
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid #34495e;
        }
        .main { margin-left: 230px; padding: 20px; }
        .btn-send {
            background-color: #27ae60;
            border-color: #27ae60;
            color: white;
        }
        .btn-send:hover {
            background-color: #219a52;
            border-color: #219a52;
            color: white;
        }
        .contact-info-card {
            border-left: 4px solid #27ae60;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column">
    <div class="brand">
        <i class="bi bi-mortarboard-fill me-2"></i>EduPlatform
    </div>
    <nav class="nav flex-column px-3 py-3 gap-1">
        <a href="home.php" class="nav-link">
            <i class="bi bi-house-door-fill me-2"></i>Home
        </a>
        <a href="about.php" class="nav-link">
            <i class="bi bi-info-circle-fill me-2"></i>About Us
        </a>
        <a href="courses.php" class="nav-link">
            <i class="bi bi-book-fill me-2"></i>Courses
        </a>
        <a href="contact.php" class="nav-link active">
            <i class="bi bi-telephone-fill me-2"></i>Contact
        </a>
    </nav>
    <div class="px-3 mt-auto pb-4">
        <a href="logout.php" class="btn btn-danger w-100">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="main">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded shadow-sm px-4 py-3 mb-4">
        <h5 class="mb-0 fw-bold text-dark">Contact Us</h5>
        <span class="badge rounded-pill text-white px-3 py-2" style="background:#2c3e50; font-size:13px;">
            <i class="bi bi-person-fill me-1"></i>
            <?php echo htmlspecialchars($_SESSION['user']); ?>
        </span>
    </div>

    <!-- Hero Banner -->
    <div class="rounded-3 text-white p-4 mb-4" style="background:#2c3e50;">
        <h4 class="fw-bold mb-1">Get In Touch 📬</h4>
        <p class="mb-0 opacity-75 small">
            We'd love to hear from you. Send us a message and we'll respond as soon as possible.
        </p>
    </div>

    <div class="row g-4">

        <!-- Contact Form -->
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header text-white fw-semibold" style="background:#2c3e50;">
                    <i class="bi bi-envelope-fill me-2"></i>Send Us a Message
                </div>
                <div class="card-body p-4">

                    <?php
                    $success = "";
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $name    = trim($_POST['name'] ?? '');
                        $email   = trim($_POST['email'] ?? '');
                        $subject = trim($_POST['subject'] ?? '');
                        $message = trim($_POST['message'] ?? '');
                        if ($name && $email && $subject && $message) {
                            $success = "Thank you, $name! Your message has been sent successfully.";
                        }
                    }
                    ?>

                    <?php if ($success): ?>
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small" style="color:#2c3e50;">
                                Full Name
                            </label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="Enter your full name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small" style="color:#2c3e50;">
                                Email Address
                            </label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small" style="color:#2c3e50;">
                                Subject
                            </label>
                            <input type="text" name="subject" class="form-control"
                                   placeholder="Enter subject" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small" style="color:#2c3e50;">
                                Message
                            </label>
                            <textarea name="message" class="form-control" rows="5"
                                      placeholder="Write your message here..." required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-send">
                                <i class="bi bi-send-fill me-2"></i>Send Message
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="col-md-5">
            <div class="card shadow-sm mb-3 contact-info-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:45px;height:45px;background:#eafaf1;">
                        <i class="bi bi-geo-alt-fill text-success fs-5"></i>
                    </div>
                    <div>
                        <p class="fw-semibold mb-0 small" style="color:#2c3e50;">Address</p>
                        <p class="text-muted small mb-0">123 Education Street, Learning City</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3 contact-info-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:45px;height:45px;background:#eaf4fb;">
                        <i class="bi bi-telephone-fill text-primary fs-5"></i>
                    </div>
                    <div>
                        <p class="fw-semibold mb-0 small" style="color:#2c3e50;">Phone</p>
                        <p class="text-muted small mb-0">+1 (234) 567-8900</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3 contact-info-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:45px;height:45px;background:#fef9e7;">
                        <i class="bi bi-envelope-fill text-warning fs-5"></i>
                    </div>
                    <div>
                        <p class="fw-semibold mb-0 small" style="color:#2c3e50;">Email</p>
                        <p class="text-muted small mb-0">support@eduplatform.com</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm contact-info-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:45px;height:45px;background:#fdecea;">
                        <i class="bi bi-clock-fill text-danger fs-5"></i>
                    </div>
                    <div>
                        <p class="fw-semibold mb-0 small" style="color:#2c3e50;">Office Hours</p>
                        <p class="text-muted small mb-0">Mon – Fri: 9:00 AM – 6:00 PM</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>