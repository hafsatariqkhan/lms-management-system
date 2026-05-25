<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$student_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);


$attendance_query = "
    SELECT 
        c.Course_ID,
        c.Course_Name,
        COUNT(a.Attendance_ID) as Total_Classes,
        SUM(CASE WHEN a.Status = 'Present' THEN 1 ELSE 0 END) as Present_Count,
        SUM(CASE WHEN a.Status = 'Absent' THEN 1 ELSE 0 END) as Absent_Count
    FROM course c
    LEFT JOIN enrollment e ON c.Course_ID = e.Course_ID
    LEFT JOIN attendance a ON c.Course_ID = a.Course_ID AND a.Std_ID = '$student_id'
    WHERE e.Std_ID = '$student_id'
    GROUP BY c.Course_ID, c.Course_Name";

$attendance_result = $conn->query($attendance_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Attendance | NUML LMS</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; }
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
        .profile-container { margin-left: 250px; padding: 40px; }
      .card-custom { 
        background: white; 
        border-radius: 16px; 
        padding: 30px; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
    </style>
</head>
<body>

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

        <a class="nav-link text-danger mb-3" href="logout.php" style=" padding-top: 15px;">🚪 Logout</a>
    </nav>
    <div class="profile-container">
        <div class="card-custom">
            <h4 class="mb-4 fw-bold">📅 My Semester Attendance</h4>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Total</th>
                        <th>Presents</th>
                        <th>Absents</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($attendance_result && $attendance_result->num_rows > 0) {
                        while ($row = $attendance_result->fetch_assoc()) {
                            $total = $row['Total_Classes'];
                            $presents = $row['Present_Count'];
                            $percentage = ($total > 0) ? round(($presents / $total) * 100) : 0;
                    ?>
                        <tr>
                            <td><?php echo $row['Course_ID']; ?></td>
                            <td><strong><?php echo $row['Course_Name']; ?></strong></td>
                            <td><?php echo $total; ?></td>
                            <td class="text-success fw-bold"><?php echo $presents; ?></td>
                            <td class="text-danger fw-bold"><?php echo $row['Absent_Count']; ?></td>
                            <td class="fw-bold"><?php echo $percentage; ?>%</td>
                        </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No records found.</td></tr>";
                    }
                    ?>
                </tbody>
                <div class="alert alert-warning d-flex align-items-center mt-4 border-0 rounded-3 bg-warning bg-opacity-10" style="color: #664d03;" role="alert">
    <span class="me-2" style="font-size: 1.5rem;">⚠️</span>
    <div class="small fw-bold">
        <strong>Eligibility Criteria:</strong> Students must maintain a minimum of <strong>75% attendance</strong> in each course to be eligible to appear in the Final Examinations.
    </div>
</div>
            </table>
        </div>
    </div>
</div>
</body>
</html>