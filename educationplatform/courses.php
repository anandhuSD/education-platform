<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$courses = [
    ["name"=>"Web Development",    "instructor"=>"John Smith", "duration"=>"12 Weeks","level"=>"Beginner",    "icon"=>"bi-code-slash",      "color"=>"#27ae60","bg"=>"#eafaf1","description"=>"Learn HTML, CSS, JavaScript, PHP and build real-world websites from scratch."],
    ["name"=>"Data Science",       "instructor"=>"Jane Doe",   "duration"=>"10 Weeks","level"=>"Intermediate","icon"=>"bi-bar-chart-fill",  "color"=>"#2980b9","bg"=>"#eaf4fb","description"=>"Explore data analysis, visualization, and machine learning with Python."],
    ["name"=>"Cyber Security",     "instructor"=>"Ali Hassan", "duration"=>"8 Weeks", "level"=>"Advanced",    "icon"=>"bi-shield-lock-fill","color"=>"#e74c3c","bg"=>"#fdecea","description"=>"Master ethical hacking, network security, and digital forensics techniques."],
    ["name"=>"Machine Learning",   "instructor"=>"Sara Khan",  "duration"=>"14 Weeks","level"=>"Advanced",    "icon"=>"bi-cpu-fill",        "color"=>"#8e44ad","bg"=>"#f5eef8","description"=>"Build AI models using supervised and unsupervised learning algorithms."],
    ["name"=>"UI/UX Design",       "instructor"=>"Mike Lee",   "duration"=>"6 Weeks", "level"=>"Beginner",    "icon"=>"bi-palette-fill",    "color"=>"#f39c12","bg"=>"#fef9e7","description"=>"Design beautiful, user-friendly interfaces using Figma and design principles."],
    ["name"=>"Mobile Development", "instructor"=>"Sara Khan",  "duration"=>"14 Weeks","level"=>"Intermediate","icon"=>"bi-phone-fill",      "color"=>"#16a085","bg"=>"#e8f8f5","description"=>"Build cross-platform mobile apps using Flutter and React Native."],
];

// Get videos for a course
function getCourseVideos($courseName) {
    $key  = preg_replace('/[^a-z0-9_]/', '_', strtolower($courseName));
    $meta = __DIR__ . "/uploads/$key/videos.txt";
    $videos = [];
    if (file_exists($meta)) {
        foreach (file($meta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $parts = explode('|', trim($line));
            if (count($parts) == 2) $videos[] = $parts;
        }
    }
    return $videos;
}

$selectedCourse = null;
if (isset($_GET['course'])) {
    foreach ($courses as $c) {
        if ($c['name'] === $_GET['course']) {
            $selectedCourse = $c;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Courses</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar {
            width: 230px; min-height: 100vh;
            position: fixed; top: 0; left: 0;
            background: #2c3e50; z-index: 100;
        }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); border-radius: 5px; }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active { background: #34495e; color: white; }
        .sidebar .brand {
            font-size: 17px; font-weight: 700; color: white;
            text-align: center; padding: 20px;
            border-bottom: 1px solid #34495e;
        }
        .main { margin-left: 230px; padding: 20px; }
        .course-card { transition: transform 0.2s, box-shadow 0.2s; border: none; border-radius: 10px; }
        .course-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important; }
        .btn-join { background-color: #27ae60; border-color: #27ae60; color: white; font-size: 13px; }
        .btn-join:hover { background-color: #219a52; border-color: #219a52; color: white; }
        .video-item { background: #f8f9fa; border-radius: 8px; padding: 14px; margin-bottom: 10px; }
        video { border-radius: 8px; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column">
    <div class="brand"><i class="bi bi-mortarboard-fill me-2"></i>EduPlatform</div>
    <nav class="nav flex-column px-3 py-3 gap-1">
        <a href="home.php" class="nav-link"><i class="bi bi-house-door-fill me-2"></i>Home</a>
        <a href="about.php" class="nav-link"><i class="bi bi-info-circle-fill me-2"></i>About Us</a>
        <a href="courses.php" class="nav-link active"><i class="bi bi-book-fill me-2"></i>Courses</a>
        <a href="contact.php" class="nav-link"><i class="bi bi-telephone-fill me-2"></i>Contact</a>
    </nav>
    <div class="px-3 mt-auto pb-4">
        <a href="logout.php" class="btn btn-danger w-100">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a>
    </div>
</div>

<div class="main">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center bg-white
                rounded shadow-sm px-4 py-3 mb-4">
        <h5 class="mb-0 fw-bold text-dark">
            <?php echo $selectedCourse ? htmlspecialchars($selectedCourse['name']) : 'Courses'; ?>
        </h5>
        <span class="badge rounded-pill text-white px-3 py-2"
              style="background:#2c3e50; font-size:13px;">
            <i class="bi bi-person-fill me-1"></i>
            <?php echo htmlspecialchars($_SESSION['user']); ?>
        </span>
    </div>

    <?php if ($selectedCourse):
        $videos = getCourseVideos($selectedCourse['name']);
        $key    = preg_replace('/[^a-z0-9_]/', '_', strtolower($selectedCourse['name']));
    ?>

    <!-- Back Button -->
    <a href="courses.php" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left me-1"></i>Back to Courses
    </a>

    <!-- Course Header -->
    <div class="rounded-3 text-white p-4 mb-4"
         style="background:<?php echo $selectedCourse['color']; ?>;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width:60px;height:60px;background:rgba(255,255,255,0.2);flex-shrink:0;">
                <i class="bi <?php echo $selectedCourse['icon']; ?> text-white fs-4"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($selectedCourse['name']); ?></h4>
                <p class="mb-0 opacity-90 small">
                    <i class="bi bi-person-fill me-1"></i><?php echo $selectedCourse['instructor']; ?> &nbsp;|&nbsp;
                    <i class="bi bi-clock me-1"></i><?php echo $selectedCourse['duration']; ?> &nbsp;|&nbsp;
                    <i class="bi bi-bar-chart me-1"></i><?php echo $selectedCourse['level']; ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Videos -->
    <div class="card shadow-sm">
        <div class="card-header text-white fw-semibold" style="background:#2c3e50;">
            <i class="bi bi-camera-video-fill me-2"></i>
            Course Videos
            <span class="badge bg-success ms-2"><?php echo count($videos); ?> Video(s)</span>
        </div>
        <div class="card-body">
            <?php if (empty($videos)): ?>
            <div class="text-center py-5">
                <i class="bi bi-camera-video-off fs-1 text-muted"></i>
                <p class="text-muted mt-3">No videos available yet. Check back soon!</p>
            </div>
            <?php else: ?>
            <?php foreach ($videos as $idx => $v):
                $videoPath = "uploads/$key/" . $v[1];
            ?>
            <div class="video-item">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill text-white"
                          style="background:<?php echo $selectedCourse['color']; ?>;">
                        <?php echo $idx + 1; ?>
                    </span>
                    <h6 class="fw-bold mb-0" style="color:#2c3e50;">
                        <?php echo htmlspecialchars($v[0]); ?>
                    </h6>
                </div>
                <video width="100%" controls style="max-height:400px;">
                    <source src="<?php echo $videoPath; ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>

    <!-- Course List View -->
    <div class="rounded-3 text-white p-4 mb-4" style="background:#2c3e50;">
        <h4 class="fw-bold mb-1">Available Courses 📚</h4>
        <p class="mb-0 opacity-75 small">
            Browse our <?php echo count($courses); ?> courses and start learning today.
        </p>
    </div>

    <!-- Search & Filter -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body py-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0"
                               id="searchInput" placeholder="Search courses...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="levelFilter">
                        <option value="">All Levels</option>
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Cards -->
    <div class="row g-4" id="courseList">
        <?php foreach ($courses as $course):
            $videos    = getCourseVideos($course['name']);
            $vidCount  = count($videos);
            $levelClass = match($course['level']) {
                'Beginner'     => 'bg-success',
                'Intermediate' => 'bg-primary',
                'Advanced'     => 'bg-danger',
                default        => 'bg-secondary'
            };
        ?>
        <div class="col-md-4 course-item"
             data-name="<?php echo strtolower($course['name']); ?>"
             data-level="<?php echo $course['level']; ?>">
            <div class="card course-card shadow-sm h-100">
                <div class="rounded-top p-4 text-center"
                     style="background:<?php echo $course['bg']; ?>;">
                    <div class="rounded-circle d-inline-flex align-items-center
                                justify-content-center mb-2"
                         style="width:60px;height:60px;background:<?php echo $course['color']; ?>;">
                        <i class="bi <?php echo $course['icon']; ?> text-white fs-4"></i>
                    </div>
                    <h6 class="fw-bold mb-0" style="color:#2c3e50;">
                        <?php echo htmlspecialchars($course['name']); ?>
                    </h6>
                    <?php if ($vidCount > 0): ?>
                    <small class="text-muted">
                        <i class="bi bi-camera-video-fill me-1"></i><?php echo $vidCount; ?> video(s)
                    </small>
                    <?php endif; ?>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted small mb-3">
                        <?php echo htmlspecialchars($course['description']); ?>
                    </p>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">
                            <i class="bi bi-person-fill me-1"></i>
                            <?php echo htmlspecialchars($course['instructor']); ?>
                        </small>
                        <span class="badge <?php echo $levelClass; ?>" style="font-size:11px;">
                            <?php echo $course['level']; ?>
                        </span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-clock me-1 text-muted"></i>
                        <small class="text-muted"><?php echo $course['duration']; ?></small>
                    </div>
                    <div class="mt-auto d-grid">
                        <a href="courses.php?course=<?php echo urlencode($course['name']); ?>"
                           class="btn btn-join">
                            <i class="bi bi-play-circle me-1"></i>
                            <?php echo $vidCount > 0 ? "Watch Videos ($vidCount)" : "Join Now"; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div id="noResults" class="text-center py-5 d-none">
        <i class="bi bi-search fs-1 text-muted"></i>
        <p class="text-muted mt-2">No courses found.</p>
    </div>

    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const searchInput = document.getElementById('searchInput');
    const levelFilter = document.getElementById('levelFilter');
    const courseItems = document.querySelectorAll('.course-item');
    const noResults   = document.getElementById('noResults');

    function filterCourses() {
        const search = searchInput?.value.toLowerCase() ?? '';
        const level  = levelFilter?.value ?? '';
        let visible  = 0;
        courseItems.forEach(item => {
            const nm = item.dataset.name.includes(search);
            const lv = level === '' || item.dataset.level === level;
            item.style.display = nm && lv ? 'block' : 'none';
            if (nm && lv) visible++;
        });
        if (noResults) noResults.classList.toggle('d-none', visible > 0);
    }

    searchInput?.addEventListener('input',  filterCourses);
    levelFilter?.addEventListener('change', filterCourses);
</script>
</body>
</html>