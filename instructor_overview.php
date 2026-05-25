<?php
session_start();
include 'db_connect.php';

/* CHECK LOGIN */
if(!isset($_SESSION['instructor_id'])){
    header("Location: Login.php");
    exit();
}

$instructor_id = $_SESSION['instructor_id'];

/* INSTRUCTOR INFO */
$inst = $conn->query("
SELECT * FROM instructor
WHERE instructor_id='$instructor_id'
")->fetch_assoc();

/* TOTAL COURSES */
$total_courses = $conn->query("
SELECT COUNT(*) as total
FROM teaches
WHERE instructor_id='$instructor_id'
")->fetch_assoc()['total'];

/* TOTAL STUDENTS */
$total_students = $conn->query("
SELECT COUNT(DISTINCT e.Std_ID) as total
FROM teaches t
JOIN enrollment e ON t.Course_ID = e.Course_ID
WHERE t.instructor_id='$instructor_id'
")->fetch_assoc()['total'];

/* TOTAL ASSIGNMENTS */
$total_assignments = $conn->query("
SELECT COUNT(*) as total
FROM assignment a
JOIN teaches t ON a.Course_ID = t.Course_ID
WHERE t.instructor_id='$instructor_id'
")->fetch_assoc()['total'];

/* RECENT COURSES */
$courses = $conn->query("
SELECT c.Course_ID, c.Course_Name
FROM course c
JOIN teaches t ON c.Course_ID = t.Course_ID
WHERE t.instructor_id='$instructor_id'
LIMIT 3
");

/* RECENT SUBMISSIONS */
$submissions = $conn->query("
SELECT s.File_Name, s.Submitted_Date, a.Assignment_Title, e.Std_ID
FROM submission s
JOIN assignment a ON s.Assignment_ID = a.Assignment_ID
JOIN teaches t ON a.Course_ID = t.Course_ID
JOIN enrollment e ON a.Course_ID = e.Course_ID
WHERE t.instructor_id='$instructor_id'
ORDER BY s.Submitted_Date DESC
LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Overview | NUML LMS</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">

<style>
    body {
        background: #f8fafc;
    }

   
    .inst-sidebar { 
        min-height: 100vh; 
        background: #064e3b; 
        color: white; 
        position: fixed; 
        width: 240px; 
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        z-index: 1000;
    }
    
    .nav-links-wrapper {
        display: flex;
        flex-direction: column;
        flex-grow: 2;
    }

    .nav-link { 
        color: #d1fae5; 
        margin: 15px 0; 
        padding: 15px 20px; 
        text-decoration: none; 
        display: block; 
        font-size: 17px;
        font-weight: 500;
        transition: all 0.3s ease;
        white-space: nowrap;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08); 
    }
    
    .nav-link:hover { 
        background: rgba(209, 250, 229, 0.1); 
        color: white; 
        border-radius: 8px;
        padding-left: 25px;
    }

   
    .main-content { 
        margin-left: 240px; 
        padding: 25px; 
        background: #f8fafc; 
        min-height: 100vh; 
        width: calc(100% - 240px);
        box-sizing: border-box;
    }

    .stat-card {
        border-radius: 14px;
        padding: 25px;
        color: white;
        margin-bottom: 20px;
        border: none;
    }

    .card-box {
        background: white;
        padding: 25px;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .instructor-badge-title {
        font-weight: 700; 
        letter-spacing: 0.5px; 
        color: white;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 15px;
    }
</style>
</head>

<body>

<div class="d-flex">
    <nav class="inst-sidebar p-3">
        <div>
            <h4 class="text-center mt-2 mb-4 instructor-badge-title">Instructor Portal</h4>
            <div class="nav-links-wrapper">
                <a class="nav-link" href="instructor_overview.php">📊 Overview</a>
                <a class="nav-link" href="instructor_courses.php">📚 My Courses</a>
                <a class="nav-link" href="instructor_assignments_main.php">📝 Assignments</a>
                <a class="nav-link" href="instructor_materials.php">📂 Materials</a>
                <a class="nav-link" href="instructor_profile.php">👤 Profile</a>
            </div>
        </div>
        
        <a class="nav-link text-warning mb-3" href="logout.php" style="padding-top: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);">🚪 Logout</a>
    </nav>

    <div class="main-content">

        <div class="card-box mb-4">
            <h4 style="font-weight: 700; color: #0f172a;">Welcome, <?php echo $inst['Name']; ?> 👋</h4>
            <p class="text-muted mb-0">
                <strong>Department:</strong> <span class="text-dark"><?php echo $inst['Department']; ?></span> | 
                <strong>Joined:</strong> <span class="text-dark"><?php echo date('d-M-Y', strtotime($inst['Joining_Date'])); ?></span>
            </p>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="stat-card bg-success shadow-sm">
                    <h6>Total Courses</h6>
                    <h2><?php echo $total_courses; ?></h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card bg-primary shadow-sm">
                    <h6>Total Students</h6>
                    <h2><?php echo $total_students; ?></h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card bg-dark shadow-sm" style="background: #1e293b !important;">
                    <h6>Total Assignments</h6>
                    <h2><?php echo $total_assignments; ?></h2>
                </div>
            </div>
        </div>

        <div class="card-box mt-2">
            <h5 style="font-weight: 600; color: #0f172a;" class="mb-3">📚 Recent Assigned Courses</h5>
            <ul class="list-group list-group-flush">
                <?php while($c = $courses->fetch_assoc()){ ?>
                    <li class="list-group-flush py-2 text-secondary" style="font-size: 15px; font-weight: 500;">
                        📘 <strong class="text-dark"><?php echo $c['Course_ID']; ?></strong> - <?php echo $c['Course_Name']; ?>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <div class="card-box mt-4">
            <h5 style="font-weight: 600; color: #0f172a;" class="mb-3">📂 Recent Student Submissions</h5>
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student ID</th>
                        <th>Assignment</th>
                        <th>Date / Time</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($submissions && $submissions->num_rows > 0) { ?>
                        <?php while($s = $submissions->fetch_assoc()){ ?>
                        <tr>
                            <td><span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded" style="font-weight: 600; font-size: 13px;"><?php echo $s['Std_ID']; ?></span></td>
                            <td><b class="text-dark"><?php echo $s['Assignment_Title']; ?></b></td>
                            <td class="text-muted" style="font-size: 14px;">📅 <?php echo date('d-M-Y, h:i A', strtotime($s['Submitted_Date'])); ?></td>
                            <td>
                                <a href="uploads/<?php echo $s['File_Name']; ?>" target="_blank" class="btn btn-sm btn-outline-success px-3 rounded-pill" style="font-weight:600;">
                                    👁️ View File
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No recent submissions found.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>