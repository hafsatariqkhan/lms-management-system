<?php
session_start();
include 'db_connect.php';


if(!isset($_SESSION['instructor_id'])){
    die("Invalid Instructor ID");
}

$instructor_id = $_SESSION['instructor_id'];


if(isset($_POST['add_assignment'])){

    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_date    = mysqli_real_escape_string($conn, $_POST['due_date']);
    $course_id   = mysqli_real_escape_string($conn, $_POST['course_id']);

    $insert = "INSERT INTO assignment (Assignment_Title, Description, Due_Date, Course_ID) 
               VALUES ('$title','$description','$due_date','$course_id')";

    if($conn->query($insert)){
        $success = "✅ Assignment Created Successfully";


        $course_info = $conn->query("SELECT Course_Name FROM course WHERE Course_ID = '$course_id'")->fetch_assoc();
        $course_name = $course_info['Course_Name'];
        
    
        $enrolled_students = $conn->query("SELECT Std_ID FROM enrollment WHERE Course_ID = '$course_id'");
        
        if($enrolled_students->num_rows > 0){
            $msg = "📝 New Assignment Alert: '$title' has been posted in $course_name. Due Date: " . date('d-M-Y', strtotime($due_date));
            
            // Sab students ko insert karein loop ke zariye
            while($student = $enrolled_students->fetch_assoc()){
                $s_id = $student['Std_ID'];
                $msg_escaped = mysqli_real_escape_string($conn, $msg);
                $conn->query("INSERT INTO notifications (Std_ID, Message, Status) VALUES ('$s_id', '$msg_escaped', 'unread')");
            }
        }
        
    } else {
        $error = "❌ Failed To Create Assignment: " . $conn->error;
    }
}


if(isset($_POST['give_grade'])){
    $submission_id = mysqli_real_escape_string($conn, $_POST['submission_id']);
    $grade         = mysqli_real_escape_string($conn, $_POST['grade']);
    $feedback      = mysqli_real_escape_string($conn, $_POST['feedback']);

    
    $sub_info_query = "
        SELECT s.Std_ID, a.Assignment_Title 
        FROM submission s 
        JOIN assignment a ON s.Assignment_ID = a.Assignment_ID 
        WHERE s.Submission_ID = '$submission_id'
    ";
    $sub_info_res = $conn->query($sub_info_query)->fetch_assoc();
    $target_student = $sub_info_res['Std_ID'];
    $assignment_title = $sub_info_res['Assignment_Title'];

    $update = "UPDATE submission SET Grade='$grade', Feedback='$feedback' WHERE Submission_ID='$submission_id'";

    if($conn->query($update)){
        $success = "✅ Grade Submitted Successfully";

        

        $clean_title = mysqli_real_escape_string($conn, $assignment_title);
        $msg = "🎯 Your assignment '$clean_title' has been graded! Grade: $grade.";
        
        $msg_escaped = mysqli_real_escape_string($conn, $msg);
        $conn->query("INSERT INTO notifications (Std_ID, Message, Status) VALUES ('$target_student', '$msg_escaped', 'unread')");
       

    } else {
        $error = "❌ Failed To Submit Grade: " . $conn->error;
    }
} 



$courses_res = $conn->query("SELECT c.Course_ID, c.Course_Name FROM course c JOIN teaches t ON c.Course_ID = t.Course_ID WHERE t.Instructor_ID='$instructor_id'");
$courses_list = [];
while($row = $courses_res->fetch_assoc()) {
    $courses_list[] = $row;
}


$assignments = $conn->query("SELECT a.*, c.Course_Name FROM assignment a JOIN course c ON a.Course_ID = c.Course_ID JOIN teaches t ON c.Course_ID = t.Course_ID WHERE t.Instructor_ID='$instructor_id' ORDER BY a.Assignment_ID DESC");


$submissions = $conn->query("
    SELECT
        s.Submission_ID,
        s.File_Name,
        s.Submitted_Date,
        s.Status,
        s.Grade,
        s.Feedback,
        st.Std_ID,
        st.Name AS Student_Name,
        a.Assignment_Title,
        c.Course_Name
    FROM submission s
    INNER JOIN assignment a ON s.Assignment_ID = a.Assignment_ID
    INNER JOIN course c ON a.Course_ID = c.Course_ID
    INNER JOIN student st ON s.Std_ID = st.Std_ID
    INNER JOIN teaches t ON c.Course_ID = t.Course_ID
    WHERE t.Instructor_ID = '$instructor_id'
    ORDER BY s.Submitted_Date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructor Assignments | NUML LMS</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
    <style>
        body{
             background:#f8fafc; 
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

        .card-box{ 
            background:white;
             border-radius:14px; 
             padding:20px; 
             margin-bottom:20px; 
             box-shadow:0 2px 10px rgba(0,0,0,0.08); 
            }
        .assignment-card{ 
        border-left:5px solid #198754; 
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
    <div class="card-box">
        <h3>📝 Instructor Assignments Panel</h3>
        <p class="text-muted">Create assignments, view submissions and give grades</p>
    </div>

    <?php
    if(isset($success)) echo "<div class='alert alert-success'>$success</div>";
    if(isset($error)) echo "<div class='alert alert-danger'>$error</div>";
    ?>

    <div class="row">
        <div class="col-md-5">
            <div class="card-box">
                <h5 class="mb-4" style="color: #064e3b; font-weight: 600;">➕ Create Assignment</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Select Course</label>
                        <select name="course_id" class="form-control" required>
                            <option value="">Select Course</option>
                            <?php foreach($courses_list as $c){ ?>
                                <option value="<?php echo $c['Course_ID']; ?>"><?php echo $c['Course_Name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assignment Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" required>
                    </div>
                    <button type="submit" name="add_assignment" class="btn text-white w-100" style="background: #064e3b;">Create Assignment</button>
                </form>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card-box">
                <h5 class="mb-4" style="color: #064e3b; font-weight: 600;">📚 Existing Assignments</h5>
                <div style="max-height: 420px; overflow-y: auto; padding-right: 5px;">
                <?php if($assignments && $assignments->num_rows > 0) { ?>
                    <?php while($a = $assignments->fetch_assoc()){ ?>
                        <div class="card assignment-card mb-3 shadow-sm">
                            <div class="card-body">
                                <h5 style="color: #064e3b; font-size:16px;"><?php echo $a['Assignment_Title']; ?></h5>
                                <p class="text-muted small mb-2"><?php echo $a['Description']; ?></p>
                                <small class="text-secondary d-block">📘 Course: <b><?php echo $a['Course_Name']; ?></b></small>
                                <small class="text-danger d-block">📅 Due Date: <b><?php echo date('d-M-Y', strtotime($a['Due_Date'])); ?></b></small>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { echo "<p class='text-muted text-center py-4'>No assignments created yet.</p>"; } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card-box mt-3">
        <h5 class="mb-4" style="color: #064e3b; font-weight: 600;">📂 Student Submissions & Grading</h5>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle" style="font-size: 14px;">
                <thead class="text-white" style="background: #064e3b;">
                    <tr>
                        <th>Student Details</th>
                        <th>Course</th>
                        <th>Assignment</th>
                        <th>Submission Date / Status</th>
                        <th>File Link</th>
                        <th>Current Grade</th>
                        <th>Current Feedback</th>
                        <th width="20%">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if($submissions && $submissions->num_rows > 0) { ?>
                    <?php while($s = $submissions->fetch_assoc()){ ?>
                        <tr>
                            <td>
                                <b><?php echo $s['Student_Name']; ?></b><br>
                                <small class="text-muted">ID: <?php echo $s['Std_ID']; ?></small>
                            </td>
                            <td><?php echo $s['Course_Name']; ?></td>
                            <td><?php echo $s['Assignment_Title']; ?></td>
                            <td>
                                <span class="d-block small text-secondary">📅 <?php echo date('d-M-Y h:i A', strtotime($s['Submitted_Date'])); ?></span>
                                <?php 
                                if(strcasecmp($s['Status'], 'Late') == 0 || $s['Status'] == 'Late'){ 
                                    echo "<span class='badge bg-danger mt-1'>⚠️ Late</span>";
                                } else { 
                                    echo "<span class='badge bg-success mt-1'>✅ On Time</span>";
                                } 
                                ?>
                            </td>
                            <td>
                                <a href="uploads/<?php echo $s['File_Name']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                    👁️ View File
                                </a>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary p-2"><?php echo $s['Grade'] ? $s['Grade'] : 'Not Graded'; ?></span>
                            </td>
                            <td>
                                <small class="text-wrap d-block" style="max-width: 150px;">
                                    <?php echo $s['Feedback'] ? $s['Feedback'] : '<i class="text-muted">No feedback</i>'; ?>
                                </small>
                            </td>
                            <td>
                                <form method="POST" class="p-2 border rounded bg-light">
                                    <input type="hidden" name="submission_id" value="<?php echo $s['Submission_ID']; ?>">
                                    <div class="mb-1">
                                        <input type="text" name="grade" class="form-control form-control-sm" placeholder="Grade (e.g. A, 85/100)" required>
                                    </div>
                                    <div class="mb-1">
                                        <textarea name="feedback" class="form-control form-control-sm" placeholder="Feedback..." rows="1"></textarea>
                                    </div>
                                    <button type="submit" name="give_grade" class="btn btn-sm text-white w-100 py-1" style="background: #064e3b; font-size: 11px;">Save Grade</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">📂 No student has submitted an assignment yet.</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>