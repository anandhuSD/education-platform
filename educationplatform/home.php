<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['user'];
$userId   = $_SESSION['user_id'] ?? 0;

// Get total courses and videos from DB
$totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$totalVideos  = $pdo->query("SELECT COUNT(*) FROM videos")->fetchColumn();

// Get all courses with video counts from DB
$courses = $pdo->query("
    SELECT c.*, COUNT(v.id) as video_count
    FROM courses c
    LEFT JOIN videos v ON v.course_id = c.id
    GROUP BY c.id ORDER BY c.id
")->fetchAll(PDO::FETCH_ASSOC);

$courseExtra = [
    'Web Development'    => ['instructor' => 'John Smith',  'duration' => '12 Weeks', 'icon' => 'bi-code-slash',       'color' => 'text-success'],
    'Data Science'       => ['instructor' => 'Jane Doe',    'duration' => '10 Weeks', 'icon' => 'bi-bar-chart-fill',   'color' => 'text-primary'],
    'Cyber Security'     => ['instructor' => 'Ali Hassan',  'duration' => '8 Weeks',  'icon' => 'bi-shield-lock-fill', 'color' => 'text-danger'],
    'Machine Learning'   => ['instructor' => 'Sara Khan',   'duration' => '14 Weeks', 'icon' => 'bi-cpu-fill',         'color' => 'text-info'],
    'UI/UX Design'       => ['instructor' => 'Mike Lee',    'duration' => '6 Weeks',  'icon' => 'bi-palette-fill',     'color' => 'text-warning'],
    'Mobile Development' => ['instructor' => 'Sara Khan',   'duration' => '14 Weeks', 'icon' => 'bi-phone-fill',       'color' => 'text-danger'],
];
foreach ($courses as &$c) $c = array_merge($c, $courseExtra[$c['name']] ?? []);
unset($c);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home — EduPlatform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 230px;
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: #2c3e50;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }
        .sidebar .brand {
            font-size: 17px; font-weight: 700; color: white;
            text-align: center; padding: 20px;
            border-bottom: 1px solid #34495e;
        }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); border-radius: 5px; }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active { background: #34495e; color: white; }

        /* ── MAIN ── */
        .main { margin-left: 230px; padding: 20px; }

        /* ── TOPBAR ── */
        .topbar {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            padding: 14px 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ── WELCOME BANNER ── */
        .welcome-banner {
            background: #2c3e50;
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        .welcome-banner::after {
            content: '';
            position: absolute;
            right: -30px; top: -30px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }

        /* ── STAT CARDS ── */
        .stat-card {
            border: none;
            border-left: 4px solid #27ae60;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .stat-card.blue  { border-left-color: #2980b9; }
        .stat-card.red   { border-left-color: #e74c3c; }

        /* ── COURSE TABLE ── */
        .courses-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            overflow: hidden;
            margin-bottom: 20px;
        }
        .courses-card .card-header {
            background: #2c3e50;
            color: white;
            font-weight: 600;
            padding: 14px 20px;
        }
        .table th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #6c757d;
        }
        .table td { vertical-align: middle; font-size: 14px; }

        /* ── BUTTONS ── */
        .btn-join {
            background-color: #27ae60;
            border-color: #27ae60;
            color: white;
            font-size: 12px;
            padding: 5px 14px;
            border-radius: 6px;
        }
        .btn-join:hover {
            background-color: #219a52;
            border-color: #219a52;
            color: white;
        }
        .btn-join-disabled {
            background-color: #e9ecef;
            border-color: #dee2e6;
            color: #adb5bd;
            font-size: 12px;
            padding: 5px 14px;
            border-radius: 6px;
            pointer-events: none;
        }

        /* ── VIDEO BADGE ── */
        .vid-badge {
            font-size: 11px;
            background: #eafaf1;
            color: #27ae60;
            border: 1px solid #a9dfbf;
            border-radius: 99px;
            padding: 2px 8px;
            font-weight: 600;
        }
        .vid-badge-empty {
            font-size: 11px;
            background: #f8f9fa;
            color: #adb5bd;
            border: 1px solid #dee2e6;
            border-radius: 99px;
            padding: 2px 8px;
        }

        /* ── LEVEL BADGE ── */
        .level-Beginner     { background:#eafaf1; color:#27ae60; }
        .level-Intermediate { background:#eaf4fb; color:#2980b9; }
        .level-Advanced     { background:#fdecea; color:#e74c3c; }
        .level-badge {
            font-size: 10px; font-weight: 600;
            padding: 3px 8px; border-radius: 99px;
        }

        .course-name-cell { font-weight: 600; color: #2c3e50; }
    </style>
</head>
<body>

<!-- ── SIDEBAR ── -->
<div class="sidebar">
    <div class="brand">
        <i class="bi bi-mortarboard-fill me-2"></i>EduPlatform
    </div>
    <nav class="nav flex-column px-3 py-3 gap-1">
        <a href="home.php"    class="nav-link active"><i class="bi bi-house-door-fill me-2"></i>Home</a>
        <a href="courses.php" class="nav-link"><i class="bi bi-book-fill me-2"></i>Courses</a>
        <a href="about.php"   class="nav-link"><i class="bi bi-info-circle-fill me-2"></i>About Us</a>
        <a href="contact.php" class="nav-link"><i class="bi bi-telephone-fill me-2"></i>Contact</a>
    </nav>
    <div class="px-3 mt-auto pb-4">
        <a href="logout.php" class="btn btn-danger w-100">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a>
    </div>
</div>

<!-- ── MAIN ── -->
<div class="main">

    <!-- Topbar -->
    <div class="topbar">
        <h5 class="mb-0 fw-bold text-dark">Dashboard</h5>
        <span class="badge rounded-pill text-white px-3 py-2"
              style="background:#2c3e50; font-size:13px;">
            <i class="bi bi-person-fill me-1"></i>
            <?php echo htmlspecialchars($username); ?>
        </span>
    </div>

    <!-- Welcome Banner -->
    <div class="welcome-banner mb-4">
        <h4 class="fw-bold text-white mb-1">
            Welcome back, <?php echo htmlspecialchars($username); ?>! 👋
        </h4>
        <p class="text-white opacity-75 small mb-3">
            You have access to <?php echo $totalCourses; ?> courses
            with <?php echo $totalVideos; ?> video lessons available.
        </p>
        <a href="courses.php" class="btn btn-sm"
           style="background:#27ae60;color:white;border-radius:8px;font-size:13px;font-weight:600;">
            <i class="bi bi-play-circle-fill me-1"></i>Browse Courses
        </a>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#eafaf1;border-radius:10px;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-book-fill text-success fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">TOTAL COURSES</p>
                        <h3 class="fw-bold mb-0" style="color:#2c3e50;">
                            <?php echo $totalCourses; ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card blue shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#eaf4fb;border-radius:10px;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-camera-video-fill text-primary fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">VIDEO LESSONS</p>
                        <h3 class="fw-bold mb-0" style="color:#2c3e50;">
                            <?php echo $totalVideos; ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card red shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;background:#fdecea;border-radius:10px;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-award-fill text-danger fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">CERTIFICATES</p>
                        <h3 class="fw-bold mb-0" style="color:#2c3e50;">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="card courses-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-table me-2"></i>Available Courses</span>
            <a href="courses.php" class="text-white small opacity-75 text-decoration-none">
                View all <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="padding:12px 16px">#</th>
                        <th>Course Name</th>
                        <th>Instructor</th>
                        <th>Duration</th>
                        <th>Videos</th>
                        <th>Level</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $levelMap = [
                    'Web Development'    => 'Beginner',
                    'Data Science'       => 'Intermediate',
                    'Cyber Security'     => 'Advanced',
                    'Machine Learning'   => 'Advanced',
                    'UI/UX Design'       => 'Beginner',
                    'Mobile Development' => 'Intermediate',
                ];
                foreach ($courses as $i => $c):
                    $vc    = $c['video_count'];
                    $level = $levelMap[$c['name']] ?? 'Beginner';
                ?>
                <tr>
                    <td style="padding:12px 16px;color:#adb5bd"><?php echo $i + 1; ?></td>
                    <td>
                        <i class="bi <?php echo $c['icon']; ?> <?php echo $c['color']; ?> me-2"></i>
                        <span class="course-name-cell"><?php echo htmlspecialchars($c['name']); ?></span>
                    </td>
                    <td class="text-muted"><?php echo htmlspecialchars($c['instructor']); ?></td>
                    <td class="text-muted"><?php echo htmlspecialchars($c['duration']); ?></td>
                    <td>
                        <?php if ($vc > 0): ?>
                        <span class="vid-badge">
                            <i class="bi bi-play-circle-fill me-1"></i><?php echo $vc; ?> video<?php echo $vc > 1 ? 's' : ''; ?>
                        </span>
                        <?php else: ?>
                        <span class="vid-badge-empty">No videos yet</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="level-badge level-<?php echo $level; ?>">
                            <?php echo $level; ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($vc > 0): ?>
                        <a href="courses.php?course_id=<?php echo $c['id']; ?>"
                           class="btn btn-join btn-sm">
                            <i class="bi bi-play-circle me-1"></i>Watch
                        </a>
                        <?php else: ?>
                        <button class="btn btn-join-disabled btn-sm" disabled>
                            <i class="bi bi-clock me-1"></i>Soon
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>