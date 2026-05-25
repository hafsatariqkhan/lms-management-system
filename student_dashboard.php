<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$id = $_SESSION['user_id'];

/* STUDENT INFO */
$query = "SELECT * FROM student WHERE Std_ID='$id'";
$result = $conn->query($query);
$row = $result->fetch_assoc();

/* NOTIFICATIONS */
$noti_count_query = "SELECT COUNT(*) as unread_count FROM notifications 
                     WHERE Std_ID='$id' AND Status='unread'";

$unread_count = $conn->query($noti_count_query)->fetch_assoc()['unread_count'];

$notifications_list = $conn->query("
    SELECT * FROM notifications 
    WHERE Std_ID='$id' 
    ORDER BY Notification_ID DESC 
    LIMIT 5
");

/* COURSES */
$courses_query = "
SELECT c.Course_ID,
       c.Course_Name,
       e.Enrollment_Date
FROM enrollment e
JOIN course c ON e.Course_ID = c.Course_ID
WHERE e.Std_ID='$id'
";

$courses_result = $conn->query($courses_query);
$total_courses = $courses_result->num_rows;

/* TOTAL ASSIGNMENTS */
$total_query = "
SELECT COUNT(*) as total
FROM assignment a
JOIN enrollment e 
ON a.Course_ID = e.Course_ID
WHERE e.Std_ID='$id'
";

$total_assignment = $conn->query($total_query)->fetch_assoc()['total'];

/* SUBMITTED */
$submitted_query = "
SELECT COUNT(DISTINCT a.Assignment_ID) as submitted
FROM assignment a
JOIN submission s 
ON a.Assignment_ID = s.Assignment_ID
WHERE s.Std_ID='$id'
AND s.Status IN ('Submitted','Late')
";

$submitted = $conn->query($submitted_query)->fetch_assoc()['submitted'];

/* PENDING */
$pending_query = "
SELECT COUNT(*) as pending
FROM assignment a
JOIN enrollment e 
ON a.Course_ID = e.Course_ID
LEFT JOIN submission s 
ON a.Assignment_ID = s.Assignment_ID
AND s.Std_ID='$id'
WHERE e.Std_ID='$id'
AND (s.Status IS NULL OR s.Status='Pending')
";

$pending = $conn->query($pending_query)->fetch_assoc()['pending'];

/* ASSIGNMENTS LIST */
$assignment_query = "
SELECT 
    a.Assignment_Title,
    a.Due_Date,
    s.Status
FROM assignment a
INNER JOIN course c 
    ON a.Course_ID = c.Course_ID
INNER JOIN enrollment e 
    ON e.Course_ID = c.Course_ID
LEFT JOIN submission s 
    ON s.Assignment_ID = a.Assignment_ID 
    AND s.Std_ID = '$id'
WHERE e.Std_ID = '$id'
";

$assignment_result = $conn->query($assignment_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Dashboard | NUML LMS</title>

<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/responsive.css" rel="stylesheet">

<style>

.sidebar {
    min-height: 100vh;
    background: #0f172a;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: fixed;
    width: 240px;
    z-index: 1000;
}

.nav-links-wrapper {
    display: flex;
    flex-direction: column;
    flex-grow: 2;
}

.nav-link {
    color: #94a3b8;
    margin: 5px 0;
    padding: 15px 20px;
    text-decoration: none;
    display: block;
    font-size: 17px;
    font-weight: 500;
    transition: all 0.3s ease;
    white-space: nowrap;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

.nav-link:hover {
    color: #38bdf8;
    background: rgba(56,189,248,0.1);
    border-radius: 8px;
    padding-left: 25px;
}

/* MAIN CONTENT */
.main-content {
    margin-left: 240px;
    padding: 25px;
    background: #f8fafc;
    min-height: 100vh;
    width: calc(100% - 240px);
    box-sizing: border-box;
}

.stat-card {
    border-radius: 15px;
    padding: 25px;
    color: white;
    margin-bottom: 20px;
    text-align: center;
    border: none;
}

.bg-primary {
    background: #3b82f6;
}

.bg-info {
    background: #06b6d4;
}

.bg-success {
    background: #22c55e;
}

.bg-danger {
    background: #ef4444;
}

.top-header {
    background: white;
    border-radius: 14px;
    padding: 15px 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.noti-dropdown-menu {
    width: 320px;
    max-height: 400px;
    overflow-y: auto;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    padding: 0;
    border: none;
}

.noti-header {
    background: #0f172a;
    color: white;
    padding: 10px 15px;
    font-size: 14px;
    font-weight: 600;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

.noti-item {
    padding: 12px 15px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13px;
    white-space: normal;
    color: #334155;
    display: block;
    text-decoration: none;
}

.noti-item:hover {
    background: #f8fafc;
}

.noti-item.unread-bg {
    background: #f0fdf4;
    font-weight: 600;
}

.noti-time {
    font-size: 11px;
    color: #94a3b8;
    display: block;
    margin-top: 4px;
}

.bell-btn {
    position: relative;
    background: #f1f5f9;
    border: none;
    border-radius: 50%;
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.bell-btn:hover {
    background: #e2e8f0;
}

.badge-dot {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    padding: 3px 6px;
    border-radius: 50%;
    font-weight: bold;
    border: 2px solid white;
}

</style>
</head>

<body style="background:#f8fafc;">

<div class="d-flex">

    <!-- SIDEBAR -->
    <nav class="sidebar p-3">

        <div>

            <h4 class="text-center mt-3 mb-4"
                style="font-weight:700; letter-spacing:0.5px; color:white;">
                NUML LMS
            </h4>

            <div class="nav-links-wrapper">
                <a class="nav-link" href="student_dashboard.php">🏠 Dashboard</a>
                <a class="nav-link" href="mycourses.php">📚 My Courses</a>
                <a class="nav-link" href="myassignments.php">📝 Assignments</a>
                <a class="nav-link" href="mysubmissions.php">📂 Submissions</a>
                <a class="nav-link" href="mycoursematerials.php">📂 Materials</a>
                <a class="nav-link" href="student_attendance.php">📅 Attendance</a>
                <a class="nav-link" href="student_profile.php">👤 Profile</a>
            </div>

        </div>

        <a class="nav-link text-danger mb-3"
           href="logout.php"
           style="padding-top:15px;">
           🚪 Logout
        </a>

    </nav>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOP HEADER -->
        <div class="d-flex justify-content-between align-items-center top-header mb-4">

            <div>
                <h4 class="mb-0"
                    style="font-weight:700; color:#0f172a;">
                    Student Portal Dashboard
                </h4>
            </div>

            <!-- NOTIFICATIONS -->
            <div class="dropdown">

                <button class="bell-btn dropdown-toggle no-caret"
                        type="button"
                        id="bellBtn"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">

                    🔔

                    <?php if($unread_count > 0) { ?>
                        <span class="badge-dot" id="bellBadge">
                            <?php echo $unread_count; ?>
                        </span>
                    <?php } ?>

                </button>

                <div class="dropdown-menu dropdown-menu-end noti-dropdown-menu"
                     aria-labelledby="bellBtn">

                    <div class="noti-header d-flex justify-content-between align-items-center">

                        <span>Notifications</span>

                        <?php if($unread_count > 0) { ?>
                            <span class="badge bg-danger text-white px-2 py-1 rounded-pill"
                                  id="headerBadge"
                                  style="font-size:11px;">
                                <?php echo $unread_count; ?> New
                            </span>
                        <?php } ?>

                    </div>

                    <?php
                    if($notifications_list && $notifications_list->num_rows > 0) {

                        while($n = $notifications_list->fetch_assoc()) {

                            $is_unread = ($n['Status'] == 'unread') ? 'unread-bg' : '';

                            $target_page = "mycoursematerials.php";

                            if (
                                strpos($n['Message'], 'Assignment') !== false ||
                                strpos($n['Message'], 'graded') !== false
                            ) {
                                $target_page = "myassignments.php";
                            }
                    ?>

                    <a href="<?php echo $target_page; ?>"
                       class="noti-item <?php echo $is_unread; ?>">

                        <div><?php echo $n['Message']; ?></div>

                        <small class="noti-time">
                            🕒 <?php echo date('d M, h:i A', strtotime($n['Created_At'])); ?>
                        </small>

                    </a>

                    <?php
                        }

                    } else {
                    ?>

                    <div class="p-4 text-center text-muted small">
                        No notifications found.
                    </div>

                    <?php } ?>

                </div>

            </div>

        </div>

        <!-- WELCOME -->
        <div class="mb-4 ps-2">

            <h1 style="font-weight:700; color:#0f172a;">
                Welcome, <?php echo $row['Name']; ?> 👋
            </h1>

            <p class="text-muted mb-0">

                <strong>Department:</strong>
                <span class="text-dark">
                    <?php echo $row['Department']; ?>
                </span>

                |

                <strong>ID:</strong>
                <span class="text-dark">
                    <?php echo $row['Std_ID']; ?>
                </span>

                |

                <strong>Email:</strong>
                <span class="text-dark">
                    <?php echo $row['Email']; ?>
                </span>

            </p>

        </div>

        <!-- STATS -->
        <div class="row text-center">

            <div class="col-md-3">
                <div class="stat-card bg-primary shadow-sm">
                    <h6>Enrolled Courses</h6>
                    <h2><?php echo $total_courses; ?></h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card bg-info shadow-sm">
                    <h6>Total Assignments</h6>
                    <h2><?php echo $total_assignment; ?></h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card bg-success shadow-sm">
                    <h6>Submitted</h6>
                    <h2><?php echo $submitted; ?></h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card bg-danger shadow-sm">
                    <h6>Pending</h6>
                    <h2><?php echo $pending; ?></h2>
                </div>
            </div>

        </div>

        <!-- ASSIGNMENTS -->
        <div class="card p-3 mt-4 shadow-sm border-0"
             style="border-radius:14px;">

            <h4 style="font-weight:600; color:#0f172a;"
                class="mb-2">
                📚 Recent Assignments
            </h4>

            <table class="table table-hover mt-2 align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th>Assignment</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                <?php if($assignment_result && $assignment_result->num_rows > 0) { ?>

                    <?php while($a = $assignment_result->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <b class="text-dark">
                                <?php echo $a['Assignment_Title']; ?>
                            </b>
                        </td>

                        <td>
                            <span class="text-secondary">
                                📅 <?php echo date('d-M-Y', strtotime($a['Due_Date'])); ?>
                            </span>
                        </td>

                        <td>

                        <?php

                        if($a['Status'] == 'Submitted') {

                            echo "<span class='badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill' style='font-size:12px; font-weight:500;'>Submitted</span>";

                        } elseif($a['Status'] == 'Late') {

                            echo "<span class='badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill' style='font-size:12px; font-weight:500;'>Late</span>";

                        } else {

                            echo "<span class='badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill' style='font-size:12px; font-weight:500;'>Pending</span>";

                        }

                        ?>

                        </td>

                    </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="3"
                            class="text-center text-muted py-4">
                            No assignments assigned yet.
                        </td>
                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script src="js/bootstrap.bundle.min.js"></script>

<script>

document.getElementById('bellBtn').addEventListener('click', function () {

    var badge = document.getElementById('bellBadge');

    if (badge) {

        var xhr = new XMLHttpRequest();

        xhr.open('GET', 'mark_notifications_read.php', true);

        xhr.onload = function () {

            if (
                xhr.status === 200 &&
                xhr.responseText.trim() === 'success'
            ) {

                badge.style.display = 'none';

                if(document.getElementById('headerBadge')) {

                    document.getElementById('headerBadge').style.display = 'none';

                }
            }
        };

        xhr.send();
    }
});

</script>

</body>
</html>