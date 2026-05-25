<?php
session_start();
include 'db_connect.php';


if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$success_msg = "";
$error_msg = "";


if(isset($_POST['allocate_course'])){
    $instructor_id = mysqli_real_escape_string($conn, $_POST['instructor_id']);
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    

    $check = $conn->query("SELECT * FROM course_allocation WHERE Instructor_ID='$instructor_id' AND Course_ID='$course_id'");
    
    if($check->num_rows > 0){
        $error_msg = "❌ This course is already allocated to the selected instructor!";
    } else {
        
        $insert_query = "INSERT INTO course_allocation (Instructor_ID, Course_ID) VALUES ('$instructor_id', '$course_id')";
        
        if($conn->query($insert_query)){
            $success_msg = "✅ Course allocated to instructor successfully!";
        } else {
            $error_msg = "❌ Allocation failed: " . $conn->error;
        }
    }
}


if(isset($_GET['remove_id']) && isset($_GET['course_id'])){
    $instructor_id = mysqli_real_escape_string($conn, $_GET['remove_id']);
    $course_id = mysqli_real_escape_string($conn, $_GET['course_id']);
    
    $delete_query = "DELETE FROM course_allocation WHERE Instructor_ID='$instructor_id' AND Course_ID='$course_id'";
    
    if($conn->query($delete_query)){
        $success_msg = "🗑️ Course allocation removed successfully!";
    } else {
        $error_msg = "❌ Failed to remove allocation: " . $conn->error;
    }
}


$instructors_list = $conn->query("SELECT Instructor_ID, Name FROM instructor ORDER BY Name ASC");
$courses_list = $conn->query("SELECT Course_ID, Course_Name FROM course ORDER BY Course_Name ASC");


$allocations_result = $conn->query("
    SELECT ca.Instructor_ID, ca.Course_ID, i.Name as Instructor_Name, c.Course_Name 
    FROM course_allocation ca
    INNER JOIN instructor i ON ca.Instructor_ID = i.Instructor_ID
    INNER JOIN course c ON ca.Course_ID = c.Course_ID
    ORDER BY i.Name ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Allocations | Admin Panel</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
    <style>
        body { 
            background:#f1f5f9; 
            font-family: 'Segoe UI', sans-serif; 
        }
        
      
        .admin-sidebar {
            min-height: 100vh;
            height: 100vh;
            background: #0f172a; 
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 1000;
            box-sizing: border-box;
            border-right: 3px solid #ef4444; 
        }

        .nav-links-wrapper {
            display: flex;
            flex-direction: column;
            flex-grow: 2;
        }
        
        .nav-link {
            color: #94a3b8;
            margin: 14px 0; 
            padding: 15px 20px;
            text-decoration: none;
            display: block;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease; 
            white-space: nowrap;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            background: transparent;
        }
        
        .nav-link:hover, .nav-link.active {
            color: #f87171 !important; 
            background: rgba(239, 68, 68, 0.08) !important;
            border-radius: 8px;
            padding-left: 24px; 
        }

        .main-content {
            margin-left: 240px;
            padding: 25px;
            min-height: 100vh;
            width: calc(100% - 240px);
            box-sizing: border-box;
            position: relative;
        }

        .admin-badge-title {
            font-weight: 700; 
            letter-spacing: 0.5px; 
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 15px;
        }

        .card-box {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            border: none;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <nav class="admin-sidebar p-3">
        <div>
            <h4 class="text-center mt-2 mb-4 admin-badge-title">Admin Portal</h4>
            <div class="nav-links-wrapper">
                <a class="nav-link" href="admin_dashboard.php">📊 Dashboard</a>
                <a class="nav-link" href="manage_students.php">👨‍🎓 Manage Students</a>
                <a class="nav-link" href="manage_instructors.php">👩‍🏫 Manage Instructors</a>
                <a class="nav-link" href="manage_courses.php">📚 Manage Courses</a>
                <a class="nav-link active" href="course_allocation.php">🔗 Course Allocation</a>
            </div>
        </div>
        <a class="nav-link text-warning mb-3" href="logout.php" style="padding-top: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);">🚪 Logout</a>
    </nav>

    <div class="main-content">
        
        <?php if(!empty($success_msg)) { echo "<div class='alert alert-success shadow-sm'>$success_msg</div>"; } ?>
        <?php if(!empty($error_msg)) { echo "<div class='alert alert-danger shadow-sm'>$error_msg</div>"; } ?>

        <div class="card-box d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1" style="color: #0f172a; font-weight: 700;">🔗 Course Allocation Panel</h3>
                <p class="text-muted mb-0">Assign courses to registered faculty members and manage active duties.</p>
            </div>
            <div>
                <button class="btn btn-danger px-4 py-2" data-bs-toggle="modal" data-bs-target="#allocateModal" style="border-radius: 8px; font-weight: 600;">
                    ➕ Assign New Course
                </button>
            </div>
        </div>

        <div class="card-box">
            <h5 class="mb-4" style="color: #0f172a; font-weight: 700;">📋 Current Faculty Workloads</h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Instructor Name</th>
                            <th>Assigned Course Code</th>
                            <th>Course Title</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($allocations_result && $allocations_result->num_rows > 0) { ?>
                            <?php while($row = $allocations_result->fetch_assoc()) { ?>
                            <tr>
                                <td>
                                    <b class="text-dark">👩‍🏫 <?php echo $row['Instructor_Name']; ?></b> 
                                    <br><small class="text-muted">ID: <?php echo $row['Instructor_ID']; ?></small>
                                </td>
                                <td><span class="badge bg-dark px-2.5 py-1.5"><?php echo $row['Course_ID']; ?></span></td>
                                <td><b class="text-secondary"><?php echo $row['Course_Name']; ?></b></td>
                                <td class="text-center">
                                    <a href="course_allocation.php?remove_id=<?php echo $row['Instructor_ID']; ?>&course_id=<?php echo $row['Course_ID']; ?>" 
                                       onclick="return confirm('Are you sure you want to revoke this course assignment? This will restrict teacher access to this course.');" 
                                       class="btn btn-sm btn-outline-danger px-3" style="border-radius: 6px;">
                                        ❌ Revoke
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No active course allocations found in the system.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="allocateModal" tabindex="-1" aria-labelledby="allocateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 16px; border: none;">
      <div class="modal-header bg-dark text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
        <h5 class="modal-title" id="allocateModalLabel">🔗 Assign Course Duty</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="course_allocation.php" method="POST">
          <div class="modal-body p-4">
              
                <div class="mb-4">
                    <label class="form-label small font-weight-bold text-secondary">Select Faculty Member</label>
                    <select name="instructor_id" class="form-select" required>
                        <option value="" disabled selected>Choose Instructor...</option>
                        <?php if($instructors_list && $instructors_list->num_rows > 0) { ?>
                            <?php while($inst = $instructors_list->fetch_assoc()) { ?>
                                <option value="<?php echo $inst['Instructor_ID']; ?>">
                                    <?php echo $inst['Name']; ?> (ID: <?php echo $inst['Instructor_ID']; ?>)
                                </option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small font-weight-bold text-secondary">Select Course to Assign</label>
                    <select name="course_id" class="form-select" required>
                        <option value="" disabled selected>Choose Course...</option>
                        <?php if($courses_list && $courses_list->num_rows > 0) { ?>
                            <?php while($crs = $courses_list->fetch_assoc()) { ?>
                                <option value="<?php echo $crs['Course_ID']; ?>">
                                    <?php echo $crs['Course_Name']; ?> (<?php echo $crs['Course_ID']; ?>)
                                </option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>

          </div>
          <div class="modal-footer bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="allocate_course" class="btn btn-danger px-4">Allocate Duty</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>