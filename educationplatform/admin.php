<?php
session_start();
require 'db.php';
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$uploadMsg = "";
$uploadErr = "";

// Load courses from DB
$courses = $pdo->query("SELECT * FROM courses ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

// Handle video upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['video'])) {
    $course_id   = (int)$_POST['course_id'];
    $video_title = trim($_POST['video_title']);
    $file        = $_FILES['video'];

    $allowed = ['video/mp4','video/webm','video/ogg','video/avi','video/quicktime','video/x-msvideo'];
    $maxSize = 500 * 1024 * 1024;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadErr = "Upload failed. Please try again.";
    } elseif (!in_array($file['type'], $allowed)) {
        $uploadErr = "Only video files are allowed (mp4, webm, ogg, avi, mov).";
    } elseif ($file['size'] > $maxSize) {
        $uploadErr = "File too large. Max size is 500MB.";
    } else {
        $courseRow = $pdo->prepare("SELECT name FROM courses WHERE id = ?");
        $courseRow->execute([$course_id]);
        $courseName = $courseRow->fetchColumn();
        $course_key = preg_replace('/[^a-z0-9_]/', '_', strtolower($courseName));

        $dir = __DIR__ . "/uploads/$course_key/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = time() . "_" . preg_replace('/[^a-z0-9]/', '_', strtolower($video_title)) . ".$ext";
        $dest     = $dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $stmt = $pdo->prepare("INSERT INTO videos (course_id, title, filename) VALUES (?,?,?)");
            $stmt->execute([$course_id, $video_title, $filename]);
            $uploadMsg = "Video '$video_title' uploaded successfully to '$courseName'!";
        } else {
            $uploadErr = "Failed to move uploaded file. Check folder permissions.";
        }
    }
}

// Handle video delete
if (isset($_GET['delete']) && isset($_GET['vid'])) {
    $vid = (int)$_GET['vid'];
    $row = $pdo->prepare("SELECT v.filename, c.name FROM videos v JOIN courses c ON v.course_id=c.id WHERE v.id=?");
    $row->execute([$vid]);
    $data = $row->fetch(PDO::FETCH_ASSOC);
    if ($data) {
        $key = preg_replace('/[^a-z0-9_]/', '_', strtolower($data['name']));
        $fp  = __DIR__ . "/uploads/$key/" . $data['filename'];
        if (file_exists($fp)) unlink($fp);
        $pdo->prepare("DELETE FROM videos WHERE id = ?")->execute([$vid]);
    }
    header("Location: admin.php?tab=courses");
    exit();
}

// Stats from DB
$userCount   = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$videoCount  = $pdo->query("SELECT COUNT(*) FROM videos")->fetchColumn();
$courseCount = count($courses);
$activeTab   = $_GET['tab'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard — EduPlatform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 230px; min-height: 100vh;
            position: fixed; top: 0; left: 0;
            background: #1a252f; z-index: 100;
            display: flex; flex-direction: column;
        }
        .sidebar .brand {
            font-size: 17px; font-weight: 700; color: white;
            text-align: center; padding: 20px;
            border-bottom: 1px solid #2c3e50;
        }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); border-radius: 5px; }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active { background: #2c3e50; color: white; }

        /* ── MAIN ── */
        .main { margin-left: 230px; padding: 20px; }

        /* ── STAT CARDS ── */
        .stat-card { border-radius: 10px; border: none; border-left: 4px solid; }

        /* ── COURSE VIDEO CARDS ── */
        .course-video-card { border: none; border-radius: 10px; transition: transform 0.2s; }
        .course-video-card:hover { transform: translateY(-3px); }

        /* ── UPLOAD ZONE ── */
        .upload-zone {
            border: 2px dashed #2c3e50; border-radius: 10px;
            padding: 30px; text-align: center;
            background: #f8f9fa; cursor: pointer; transition: background 0.2s;
        }
        .upload-zone:hover { background: #eaf4fb; border-color: #27ae60; }

        /* ── VIDEO ITEM ── */
        .video-item {
            background: #f8f9fa; border-radius: 8px;
            padding: 12px 15px; margin-bottom: 8px;
            display: flex; align-items: center; justify-content: space-between;
        }

        /* ── TABLE ── */
        .table th { font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; color: #6c757d; }
        .table td { vertical-align: middle; font-size: 14px; }

        .progress { height: 6px; }
    </style>
</head>
<body>

<!-- ── SIDEBAR ── -->
<div class="sidebar">
    <div class="brand">
        <i class="bi bi-shield-lock-fill me-2"></i>Admin Panel
    </div>
    <nav class="nav flex-column px-3 py-3 gap-1">
        <a href="admin.php?tab=dashboard"
           class="nav-link <?php echo $activeTab=='dashboard'?'active':''; ?>">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
        <a href="admin.php?tab=users"
           class="nav-link <?php echo $activeTab=='users'?'active':''; ?>">
            <i class="bi bi-people-fill me-2"></i>Manage Users
        </a>
        <a href="admin.php?tab=courses"
           class="nav-link <?php echo $activeTab=='courses'?'active':''; ?>">
            <i class="bi bi-book-fill me-2"></i>Manage Courses
        </a>
        <a href="admin.php?tab=messages"
           class="nav-link <?php echo $activeTab=='messages'?'active':''; ?>">
            <i class="bi bi-envelope-fill me-2"></i>Messages
        </a>
        <a href="admin.php?tab=settings"
           class="nav-link <?php echo $activeTab=='settings'?'active':''; ?>">
            <i class="bi bi-gear-fill me-2"></i>Settings
        </a>
    </nav>
    <div class="px-3 mt-auto pb-4">
        <a href="logout.php" class="btn btn-danger w-100">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a>
    </div>
</div>

<!-- ── MAIN ── -->
<div class="main">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center bg-white
                rounded shadow-sm px-4 py-3 mb-4">
        <h5 class="mb-0 fw-bold text-dark">Admin Dashboard</h5>
        <span class="badge rounded-pill text-white px-3 py-2"
              style="background:#e74c3c; font-size:13px;">
            <i class="bi bi-shield-lock-fill me-1"></i>
            <?php echo htmlspecialchars($_SESSION['admin']); ?>
        </span>
    </div>

    <?php if ($uploadMsg): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($uploadMsg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($uploadErr): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($uploadErr); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- ══════════════════════════════════════════════════
         DASHBOARD TAB
    ══════════════════════════════════════════════════ -->
    <?php if ($activeTab == 'dashboard'): ?>

    <!-- Welcome Banner -->
    <div class="rounded-3 text-white p-4 mb-4" style="background:#1a252f;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width:60px;height:60px;background:#e74c3c;flex-shrink:0;">
                <i class="bi bi-shield-lock-fill text-white fs-4"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1">
                    Hello, Admin <?php echo htmlspecialchars($_SESSION['admin']); ?>! 👋
                </h4>
                <p class="mb-0 opacity-75 small">
                    Welcome to the admin dashboard. You have full control over the platform.
                </p>
            </div>
        </div>
    </div>

    <!-- Stat Cards — live from DB -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card shadow-sm" style="border-color:#27ae60;">
                <div class="card-body">
                    <p class="text-muted small mb-1">TOTAL USERS</p>
                    <h3 class="fw-bold mb-0" style="color:#2c3e50;">
                        <?php echo $userCount; ?>
                    </h3>
                    <i class="bi bi-people-fill text-success fs-4 float-end mt-n4"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm" style="border-color:#2980b9;">
                <div class="card-body">
                    <p class="text-muted small mb-1">TOTAL COURSES</p>
                    <h3 class="fw-bold mb-0" style="color:#2c3e50;">
                        <?php echo $courseCount; ?>
                    </h3>
                    <i class="bi bi-book-fill text-primary fs-4 float-end mt-n4"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm" style="border-color:#f39c12;">
                <div class="card-body">
                    <p class="text-muted small mb-1">TOTAL VIDEOS</p>
                    <h3 class="fw-bold mb-0" style="color:#2c3e50;">
                        <?php echo $videoCount; ?>
                    </h3>
                    <i class="bi bi-camera-video-fill text-warning fs-4 float-end mt-n4"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm" style="border-color:#e74c3c;">
                <div class="card-body">
                    <p class="text-muted small mb-1">ACTIVE SESSIONS</p>
                    <h3 class="fw-bold mb-0" style="color:#2c3e50;">1</h3>
                    <i class="bi bi-activity text-danger fs-4 float-end mt-n4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Registered Users Table — from DB -->
    <div class="card shadow-sm">
        <div class="card-header text-white fw-semibold d-flex justify-content-between align-items-center"
             style="background:#1a252f;">
            <span><i class="bi bi-people-fill me-2"></i>Registered Users</span>
            <a href="admin.php?tab=users" class="text-white small opacity-75 text-decoration-none">
                View all <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="padding:12px 16px">#</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Joined</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 10")
                             ->fetchAll(PDO::FETCH_ASSOC);
                if (empty($users)):
                ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">No users registered yet.</td>
                </tr>
                <?php else: foreach ($users as $i => $u): ?>
                <tr>
                    <td style="padding:12px 16px;color:#adb5bd"><?php echo $i+1; ?></td>
                    <td>
                        <i class="bi bi-person-circle me-2 text-muted"></i>
                        <strong><?php echo htmlspecialchars($u['username']); ?></strong>
                    </td>
                    <td class="text-muted"><?php echo htmlspecialchars($u['full_name'] ?: '—'); ?></td>
                    <td class="text-muted"><?php echo htmlspecialchars($u['email'] ?: '—'); ?></td>
                    <td class="text-muted" style="font-size:12px">
                        <?php echo date('d M Y', strtotime($u['created_at'])); ?>
                    </td>
                    <td><span class="badge bg-success">Active</span></td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         MANAGE COURSES TAB
    ══════════════════════════════════════════════════ -->
    <?php elseif ($activeTab == 'courses'): ?>

    <div class="rounded-3 text-white p-4 mb-4" style="background:#1a252f;">
        <h4 class="fw-bold mb-1">
            <i class="bi bi-camera-video-fill me-2"></i>Manage Course Videos
        </h4>
        <p class="mb-0 opacity-75 small">
            Upload videos for each course. Students will see them on the courses page.
        </p>
    </div>

    <!-- Upload Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white fw-semibold" style="background:#1a252f;">
            <i class="bi bi-cloud-upload-fill me-2"></i>Upload New Video
        </div>
        <div class="card-body p-4">
            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small" style="color:#2c3e50;">
                            Select Course
                        </label>
                        <select name="course_id" class="form-select" required>
                            <option value="">-- Choose Course --</option>
                            <?php foreach ($courses as $c): ?>
                            <option value="<?php echo $c['id']; ?>">
                                <?php echo htmlspecialchars($c['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small" style="color:#2c3e50;">
                            Video Title
                        </label>
                        <input type="text" name="video_title" class="form-control"
                               placeholder="e.g. Introduction to HTML" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small" style="color:#2c3e50;">
                            Video File
                        </label>
                        <input type="file" name="video" class="form-control"
                               accept="video/*" required id="videoFile">
                    </div>
                </div>

                <div class="upload-zone mt-3" id="uploadZone"
                     onclick="document.getElementById('videoFile').click()">
                    <i class="bi bi-cloud-arrow-up-fill fs-1 text-muted"></i>
                    <p class="text-muted mb-0 mt-2" id="uploadZoneText">
                        Click to select a video file or drag & drop here
                    </p>
                    <small class="text-muted">Supported: MP4, WebM, OGG, AVI, MOV — Max 500MB</small>
                </div>

                <div class="mt-3 d-none" id="progressWrap">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Uploading...</small>
                        <small class="text-muted" id="progressText">0%</small>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                             id="progressBar" style="width:0%"></div>
                    </div>
                </div>

                <div class="d-grid mt-3">
                    <button type="submit" class="btn text-white fw-semibold"
                            style="background:#27ae60;" id="uploadBtn">
                        <i class="bi bi-cloud-upload me-2"></i>Upload Video
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Course Video Cards — from DB -->
    <div class="row g-3">
        <?php foreach ($courses as $c):
            $vstmt = $pdo->prepare("SELECT * FROM videos WHERE course_id = ? ORDER BY uploaded_at DESC");
            $vstmt->execute([$c['id']]);
            $videos = $vstmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="col-md-6">
            <div class="card course-video-card shadow-sm h-100">
                <div class="card-header d-flex align-items-center gap-2 py-3"
                     style="background:<?php echo $c['bg']; ?>; border:none;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:38px;height:38px;background:<?php echo $c['color']; ?>;">
                        <i class="bi <?php echo $c['icon']; ?> text-white"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="color:#2c3e50;">
                            <?php echo htmlspecialchars($c['name']); ?>
                        </h6>
                        <small class="text-muted"><?php echo count($videos); ?> video(s)</small>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($videos)): ?>
                    <p class="text-muted small text-center py-2">
                        <i class="bi bi-camera-video-off me-1"></i>No videos uploaded yet.
                    </p>
                    <?php else: ?>
                    <?php foreach ($videos as $v): ?>
                    <div class="video-item">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:32px;height:32px;background:<?php echo $c['color']; ?>20;">
                                <i class="bi bi-play-circle-fill"
                                   style="color:<?php echo $c['color']; ?>;"></i>
                            </div>
                            <div>
                                <p class="mb-0 small fw-semibold" style="color:#2c3e50;">
                                    <?php echo htmlspecialchars($v['title']); ?>
                                </p>
                                <small class="text-muted"><?php echo htmlspecialchars($v['filename']); ?></small>
                            </div>
                        </div>
                        <a href="admin.php?tab=courses&delete=1&vid=<?php echo $v['id']; ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Delete this video?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ══════════════════════════════════════════════════
         MANAGE USERS TAB
    ══════════════════════════════════════════════════ -->
    <?php elseif ($activeTab == 'users'): ?>

    <div class="rounded-3 text-white p-4 mb-4" style="background:#1a252f;">
        <h4 class="fw-bold mb-1">
            <i class="bi bi-people-fill me-2"></i>Manage Users
        </h4>
        <p class="mb-0 opacity-75 small">View and manage all registered users.</p>
    </div>

    <div class="card shadow-sm">
        <div class="card-header text-white fw-semibold" style="background:#1a252f;">
            <i class="bi bi-people-fill me-2"></i>All Registered Users
            <span class="badge bg-success ms-2"><?php echo $userCount; ?> Total</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="padding:12px 16px">#</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>State</th>
                        <th>Joined</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $allUsers = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")
                                ->fetchAll(PDO::FETCH_ASSOC);
                if (empty($allUsers)):
                ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-3">No users registered yet.</td>
                </tr>
                <?php else: foreach ($allUsers as $i => $u): ?>
                <tr>
                    <td style="padding:12px 16px;color:#adb5bd"><?php echo $i+1; ?></td>
                    <td>
                        <i class="bi bi-person-circle me-2 text-muted"></i>
                        <strong><?php echo htmlspecialchars($u['username']); ?></strong>
                    </td>
                    <td class="text-muted"><?php echo htmlspecialchars($u['full_name'] ?: '—'); ?></td>
                    <td class="text-muted"><?php echo htmlspecialchars($u['email'] ?: '—'); ?></td>
                    <td class="text-muted"><?php echo htmlspecialchars($u['phone'] ?: '—'); ?></td>
                    <td class="text-muted"><?php echo htmlspecialchars($u['state'] ?: '—'); ?></td>
                    <td class="text-muted" style="font-size:12px">
                        <?php echo date('d M Y', strtotime($u['created_at'])); ?>
                    </td>
                    <td><span class="badge bg-success">Active</span></td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         OTHER TABS (Messages, Settings)
    ══════════════════════════════════════════════════ -->
    <?php else: ?>
    <div class="rounded-3 text-white p-4 mb-4" style="background:#1a252f;">
        <h4 class="fw-bold mb-1">
            <i class="bi bi-tools me-2"></i><?php echo ucfirst($activeTab); ?>
        </h4>
        <p class="mb-0 opacity-75 small">This section is coming soon.</p>
    </div>
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-wrench-adjustable fs-1 text-muted"></i>
            <p class="text-muted mt-3">This feature is under construction.</p>
        </div>
    </div>
    <?php endif; ?>

</div><!-- end .main -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('videoFile')?.addEventListener('change', function () {
        const name = this.files[0]?.name || 'No file selected';
        document.getElementById('uploadZoneText').textContent = '✅ ' + name;
    });

    document.getElementById('uploadForm')?.addEventListener('submit', function () {
        const wrap = document.getElementById('progressWrap');
        const bar  = document.getElementById('progressBar');
        const txt  = document.getElementById('progressText');
        const btn  = document.getElementById('uploadBtn');
        wrap.classList.remove('d-none');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Uploading...';
        let p = 0;
        const iv = setInterval(() => {
            p = Math.min(p + Math.random() * 15, 95);
            bar.style.width = p + '%';
            txt.textContent = Math.round(p) + '%';
        }, 300);
    });
</script>
</body>
</html>