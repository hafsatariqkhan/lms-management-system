<?php
session_start();
include 'db_connect.php';



if(!isset($_SESSION['instructor_id'])){
    die("Invalid Instructor ID");
}

$instructor_id = $_SESSION['instructor_id'];


if(!isset($_GET['course_id'])){
    die("Course Not Found");
}

$course_id = $_GET['course_id'];


$inst = $conn->query("
SELECT * FROM instructor
WHERE Instructor_ID='$instructor_id'
")->fetch_assoc();


$course = $conn->query("
SELECT * FROM course
WHERE Course_ID='$course_id'
")->fetch_assoc();


$query = "
SELECT 
    s.Std_ID,
    s.Name,
    s.Email,
    e.Enrollment_Date
FROM enrollment e

JOIN student s
ON e.Std_ID = s.Std_ID

WHERE e.Course_ID='$course_id'
";

$result = $conn->query($query);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Course Students</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">


<style>

body{
    background:#f8fafc;
}

/* SIDEBAR */
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
    .instructor-badge-title {
        font-weight: 700; 
        letter-spacing: 0.5px; 
        color: white;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 15px;
    }


/* CARD */

.card-box{
    background:white;
    border-radius:14px;
    padding:20px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

</style>

</head>

<body>

<!-- SIDEBAR -->
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

        

<!-- MAIN -->

<div class="main-content">

    <!-- HEADER -->

    <div class="card-box mb-4">

        <h3>
            👨‍🎓 Students Enrolled
        </h3>

        <p class="text-muted mb-0">
            Course:
            <b>
                <?php echo $course['Course_Name']; ?>
            </b>
        </p>

    </div>

    <!-- STUDENTS TABLE -->

    <div class="card-box">

        <table class="table table-bordered">

            <thead class="table-dark">

                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Enrollment Date</th>
                </tr>

            </thead>

            <tbody>

            <?php if($result->num_rows > 0){ ?>

                <?php while($row = $result->fetch_assoc()){ ?>

                    <tr>

                        <td>
                            <?php echo $row['Std_ID']; ?>
                        </td>

                        <td>
                            <?php echo $row['Name']; ?>
                        </td>

                        <td>
                            <?php echo $row['Email']; ?>
                        </td>

                        <td>
                            <?php echo $row['Enrollment_Date']; ?>
                        </td>

                    </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>
                    <td colspan="4" class="text-center">
                        ❌ No Students Found
                    </td>
                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>