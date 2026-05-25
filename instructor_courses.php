<?php
session_start();
include 'db_connect.php';


if(!isset($_SESSION['instructor_id'])){
    header("Location: Login.php");
    exit();
}

$instructor_id = $_SESSION['instructor_id'];


$inst = $conn->query("
SELECT * FROM instructor
WHERE Instructor_ID='$instructor_id'
")->fetch_assoc();


$instructor_id = $_SESSION['instructor_id'];

$query = "
SELECT 
    c.Course_ID,
    c.Course_Name,
    COUNT(e.Std_ID) as total_students
FROM course c
LEFT JOIN enrollment e 
    ON c.Course_ID = e.Course_ID

LEFT JOIN teaches t 
    ON t.Course_ID = c.Course_ID 
    AND t.Instructor_ID = '$instructor_id'

LEFT JOIN course_allocation ca 
    ON ca.Course_ID = c.Course_ID 
    AND ca.Instructor_ID = '$instructor_id'

WHERE t.Course_ID IS NOT NULL 
   OR ca.Course_ID IS NOT NULL

GROUP BY c.Course_ID, c.Course_Name
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses | NUML LMS</title>
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


    /* MASTER INSTRUCTOR MAIN CONTENT LAYOUT */
    .main-content { 
        margin-left: 240px; 
        padding: 25px; 
        background: #f8fafc;
        min-height: 100vh;
        width: calc(100% - 240px);
        box-sizing: border-box;
    }

    .instructor-badge-title {
        font-weight: 700; 
        letter-spacing: 0.5px; 
        color: white;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 15px;
    }

    /* COURSE CARD */
    .course-card {
        background: white;
        border-radius: 14px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: none;
        transition: transform 0.2s ease;
    }
    
    .course-card:hover {
        transform: translateY(-2px);
    }

    .course-badge {
        background: #dcfce7;
        color: #166534;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        display: inline-block;
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
        
        <a class="nav-link text-warning mb-3" href="logout.php" style=" border-bottom: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">🚪 Logout</a>
    </nav>

    <div class="main-content">

        <div class="bg-white p-4 rounded-4 shadow-sm mb-4">
            <h3 style="font-weight: 700; color: #0f172a;" class="mb-1">📚 My Courses</h3>
            <p class="text-muted mb-0">
                Welcome, <b class="text-dark"><?php echo $inst['Name']; ?></b>
            </p>
        </div>

        <?php if($result && $result->num_rows > 0){ ?>
            <div class="row">
                <?php while($row = $result->fetch_assoc()){ ?>
                    <div class="col-md-6">
                        <div class="course-card">
                            <h4 style="font-weight: 700; color: #0f172a;" class="mb-2">
                                <?php echo $row['Course_Name']; ?>
                            </h4>
                            <p class="text-muted mb-3">
                                Course ID: <b class="text-secondary"><?php echo $row['Course_ID']; ?></b>
                            </p>
                            <p class="mb-4 text-secondary" style="font-weight: 500;">
                                👨‍🎓 Total Students: 
                                <span class="course-badge ms-1">
                                    <?php echo $row['total_students']; ?> Students
                                </span>
                            </p>
                            <div>
                                <a href="instructor_students.php?course_id=<?php echo $row['Course_ID']; ?>" class="btn btn-success px-4 rounded-pill" style="font-weight: 600; font-size: 14px;">
                                    View Students
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning rounded-3 border-0 shadow-sm">
                ❌ No Courses Assigned to you yet.
            </div>
        <?php } ?>

    </div> 
</div> 

<script src="js/bootstrap.bundle.min.js"></script>
</body> 
</html>