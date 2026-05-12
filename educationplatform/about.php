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
    <title>About Us</title>
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
        .team-card:hover { transform: translateY(-4px); transition: 0.3s; }
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
        <a href="about.php" class="nav-link active">
            <i class="bi bi-info-circle-fill me-2"></i>About Us
        </a>
        <a href="courses.php" class="nav-link">
            <i class="bi bi-book-fill me-2"></i>Courses
        </a>
        <a href="contact.php" class="nav-link">
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
        <h5 class="mb-0 fw-bold text-dark">About Us</h5>
        <span class="badge rounded-pill text-white px-3 py-2" style="background:#2c3e50; font-size:13px;">
            <i class="bi bi-person-fill me-1"></i>
            <?php echo htmlspecialchars($_SESSION['user']); ?>
        </span>
    </div>

    <!-- Hero Banner -->
    <div class="rounded-3 text-white p-4 mb-4" style="background:#2c3e50;">
        <h4 class="fw-bold mb-1">About EduPlatform 🎓</h4>
        <p class="mb-0 opacity-75 small">
            Empowering learners around the world with quality education and expert instructors.
        </p>
    </div>

    <!-- Mission & Vision -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width:45px;height:45px;background:#eafaf1;">
                            <i class="bi bi-bullseye text-success fs-5"></i>
                        </div>
                        <h5 class="fw-bold mb-0" style="color:#2c3e50;">Our Mission</h5>
                    </div>
                    <p class="text-muted small mb-0">
                        To provide accessible, high-quality education to everyone regardless of
                        their background. We believe learning should be engaging, practical,
                        and affordable for all.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width:45px;height:45px;background:#eaf4fb;">
                            <i class="bi bi-eye-fill text-primary fs-5"></i>
                        </div>
                        <h5 class="fw-bold mb-0" style="color:#2c3e50;">Our Vision</h5>
                    </div>
                    <p class="text-muted small mb-0">
                        To become the world's leading education platform, connecting millions
                        of students with expert instructors and transforming the future of
                        online learning.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm text-center border-0" style="border-left:4px solid #27ae60 !important;">
                <div class="card-body py-3">
                    <h3 class="fw-bold mb-0" style="color:#27ae60;">12+</h3>
                    <p class="text-muted small mb-0">Courses</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center border-0">
                <div class="card-body py-3">
                    <h3 class="fw-bold mb-0" style="color:#2980b9;">248+</h3>
                    <p class="text-muted small mb-0">Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center border-0">
                <div class="card-body py-3">
                    <h3 class="fw-bold mb-0" style="color:#f39c12;">10+</h3>
                    <p class="text-muted small mb-0">Instructors</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center border-0">
                <div class="card-body py-3">
                    <h3 class="fw-bold mb-0" style="color:#e74c3c;">5+</h3>
                    <p class="text-muted small mb-0">Years Experience</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Meet the Team -->
    <div class="card shadow-sm">
        <div class="card-header text-white fw-semibold" style="background:#2c3e50;">
            <i class="bi bi-people-fill me-2"></i>Meet the Team
        </div>
        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-3">
                    <div class="card border-0 bg-light text-center p-3 team-card">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                             style="width:60px;height:60px;background:#2c3e50;">
                            <span class="text-white fw-bold">JS</span>
                        </div>
                        <h6 class="fw-bold mb-0" style="color:#2c3e50;">John Smith</h6>
                        <small class="text-muted">Web Development</small>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 bg-light text-center p-3 team-card">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                             style="width:60px;height:60px;background:#27ae60;">
                            <span class="text-white fw-bold">JD</span>
                        </div>
                        <h6 class="fw-bold mb-0" style="color:#2c3e50;">Jane Doe</h6>
                        <small class="text-muted">Data Science</small>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 bg-light text-center p-3 team-card">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                             style="width:60px;height:60px;background:#2980b9;">
                            <span class="text-white fw-bold">ML</span>
                        </div>
                        <h6 class="fw-bold mb-0" style="color:#2c3e50;">Mike Lee</h6>
                        <small class="text-muted">UI/UX Design</small>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 bg-light text-center p-3 team-card">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                             style="width:60px;height:60px;background:#e74c3c;">
                            <span class="text-white fw-bold">SK</span>
                        </div>
                        <h6 class="fw-bold mb-0" style="color:#2c3e50;">Sara Khan</h6>
                        <small class="text-muted">Mobile Development</small>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>