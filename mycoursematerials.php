<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION ['user_id'])) {
    header("Location: Login.php");
    exit();
}
$id = $_SESSION['user_id'];


$materials_query = "
    SELECT m.Name as Material_Title, m.Description, m.File_Name, c.Course_Name, i.Name as Instructor_Name
    FROM module m
    JOIN course c ON m.Course_ID = c.Course_ID
    JOIN enrollment e ON c.Course_ID = e.Course_ID
    JOIN teaches t ON c.Course_ID = t.Course_ID
    JOIN instructor i ON t.Instructor_ID = i.Instructor_ID
    WHERE e.Std_ID = '$id'
    ORDER BY m.Module_ID DESC
";
$materials_result = $conn->query($materials_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Course Materials | NUML LMS</title>
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
        box-sizing: border-box;
    }

    .card-box {
        background: white; 
        border-radius: 14px;
        padding: 25px; 
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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

        <a class="nav-link text-danger mb-3" href="logout.php" style=" padding-top: 15px;">🚪 Logout</a>
    </nav>
    
    <div class="main-content">
        <div class="card-box mb-4">
            <h3 style="font-weight: 700; color: #0f172a;">📂 Materials</h3>
            <p class="text-muted mb-0">Download lectures, slides, and reference books uploaded by your instructors.</p>
        </div>

        <div class="card p-3 shadow-sm border-0" style="border-radius: 14px;">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Material Title / Description</th>
                        <th>Course</th>
                        <th>Uploaded By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if($materials_result && $materials_result->num_rows > 0) { ?>
                    <?php while($m = $materials_result->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <b class="text-dark"><?php echo $m['Material_Title']; ?></b><br>
                            <small class="text-muted"><?php echo $m['Description']; ?></small>
                        </td>
                        <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded" style="font-size: 13px; font-weight: 500;"><?php echo $m['Course_Name']; ?></span></td>
                        <td class="text-secondary" style="font-size: 14px; font-weight: 500;">👨‍🏫 <?php echo $m['Instructor_Name']; ?></td>
                        <td>
                            <a href="uploads/materials/<?php echo $m['File_Name']; ?>" download class="btn btn-outline-success btn-sm px-3 rounded-pill" style="font-weight: 600;">
                                📥 Download File
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5" style="font-size: 15px;">No course materials uploaded yet.</td>
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