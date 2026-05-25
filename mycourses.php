<?php
session_start();
include 'db_connect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$id = $_SESSION['user_id'];

$query = "
SELECT 
    c.Course_ID,
    c.Course_Name,
    e.Enrollment_Date,
    i.Name AS Teacher_Name
FROM enrollment e
JOIN course c ON e.Course_ID = c.Course_ID
LEFT JOIN teaches t ON c.Course_ID = t.Course_ID
LEFT JOIN instructor i ON t.Instructor_ID = i.Instructor_ID
WHERE e.Std_ID = '$id'
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Courses | NUML LMS</title>

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
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .nav-link:hover {
            color: #38bdf8;
            background: rgba(56, 189, 248, 0.1);
            border-radius: 8px;
            padding-left: 25px;
        }

        
        .main-content {
            margin-left: 240px; 
            padding: 25px;
            background: #f8fafc;
            min-height: 100vh;
            width: calc(100% - 240px);
        }

        .course-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: none;
        }

        .teacher-badge {
            display: inline-block;
            margin-top: 8px;
            padding: 5px 12px;
            background: #f1f5f9;
            color: #475569;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
        }
    </style>
</head>

<body style="background: #f8fafc;">

<div class="d-flex">
    <nav class="sidebar p-3">
        <div>
            <h4 class="text-center mt-3 mb-4" style="font-weight: 700; letter-spacing: 0.5px; color: white;">NUML LMS</h4>
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

        <a class="nav-link text-danger mb-3" href="logout.php" padding-top: 15px;">🚪 Logout</a>
    </nav>

    <div class="main-content">
        <h3 class="mb-4" style="font-weight: 700; color: #0f172a;">📚 My Enrolled Courses</h3>

        <?php if($result->num_rows == 0){ ?>
            <div class="alert alert-warning border-0 shadow-sm" style="border-radius: 10px;">
                ❌ No course enrolled yet
            </div>
        <?php } else { ?>

            <?php while($row = $result->fetch_assoc()) { ?>
                <div class="course-card shadow-sm">
                    <h5 style="font-weight: 600; color: #0f172a;">📘 <?php echo $row['Course_Name']; ?></h5>

                    <p class="mb-1 text-muted" style="font-size: 15px;">
                        <b>Course ID:</b> <span class="text-dark"><?php echo $row['Course_ID']; ?></span>
                    </p>

                    <p class="mb-1 text-muted" style="font-size: 15px;">
                        <b>Enrollment Date:</b> <span class="text-dark"><?php echo date('d-M-Y', strtotime($row['Enrollment_Date'])); ?></span>
                    </p>

                    <div class="teacher-badge">
                        👨‍🏫 Teacher: <?php echo $row['Teacher_Name'] ?? 'Not Assigned'; ?>
                    </div>
                </div>
            <?php } ?>

        <?php } ?>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>