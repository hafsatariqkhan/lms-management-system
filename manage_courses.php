<?php
session_start();
include 'db_connect.php';


if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$success_msg = "";
$error_msg = "";


if(isset($_POST['add_course'])){
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $course_name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $credit_hours = mysqli_real_escape_string($conn, $_POST['credit_hours']);
    
   
    $check = $conn->query("SELECT * FROM course WHERE Course_ID='$course_id'");
    if($check->num_rows > 0){
        $error_msg = "❌ Course ID already exists in the system!";
    } else {
        // Query to insert course details
        $insert_query = "INSERT INTO course (Course_ID, Course_Name, Credit_Hours) 
                         VALUES ('$course_id', '$course_name', '$credit_hours')";
        
        if($conn->query($insert_query)){
            $success_msg = "✅ Course '$course_name' added successfully!";
        } else {
            $error_msg = "❌ Failed to add course: " . $conn->error;
        }
    }
}

if(isset($_GET['delete_id'])){
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    
    $delete_query = "DELETE FROM course WHERE Course_ID='$delete_id'";
    
    if($conn->query($delete_query)){
        $success_msg = "🗑️ Course record deleted successfully!";
    } else {
        $error_msg = "❌ Failed to delete course: " . $conn->error;
    }
}


$courses_result = $conn->query("SELECT * FROM course ORDER BY Course_ID ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses | Admin Panel</title>
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
            color: #f87171 ; 
            background: rgba(239, 68, 68, 0.08);
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

        
        .search-control {
            border: 1px solid #cbd5e1 !important; 
            font-size: 14px;
            height: 40px;
            transition: all 0.25s ease-in-out !important; 
        }

        .input-group:hover .search-control,
        .input-group:hover .input-group-text {
            border-color: #cbd5e1 !important; 
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.04) !important; 
            transform: translateY(-1px); 
        }

        .search-control:focus {
            border-color: #94a3b8 !important; 
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important; 
            background-color: #f8fafc !important; 
            transform: translateY(-1px);
        }

        .input-group:focus-within .input-group-text {
            border-color: #94a3b8 !important;
            background-color: #f8fafc !important;
            transform: translateY(-1px);
        }

        .input-group-text {
            border: 1px solid #cbd5e1 !important;
            transition: all 0.25s ease-in-out !important;
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
                <a class="nav-link active" href="manage_courses.php">📚 Manage Courses</a>
                <a class="nav-link" href="course_allocation.php">🔗 Course Allocation</a>
            </div>
        </div>
        <a class="nav-link text-warning mb-3" href="logout.php" style="padding-top: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);">🚪 Logout</a>
    </nav>
    <div class="main-content">
        
        <?php if(!empty($success_msg)) { echo "<div class='alert alert-success shadow-sm'>$success_msg</div>"; } ?>
        <?php if(!empty($error_msg)) { echo "<div class='alert alert-danger shadow-sm'>$error_msg</div>"; } ?>

        <div class="card-box d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1" style="color: #0f172a; font-weight: 700;">📚 Course Management</h3>
                <p class="text-muted mb-0">Create new curriculum courses, view total hours, or remove obsolete records.</p>
            </div>
            <div>
                <button class="btn btn-danger px-4 py-2" data-bs-toggle="modal" data-bs-target="#addCourseModal" style="border-radius: 8px; font-weight: 600;">
                    ➕ Add New Course
                </button>
            </div>
        </div>

        <div class="card-box">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <h5 class="mb-0" style="color: #0f172a; font-weight: 700;">📋 University Course Catalog</h5>
                
                <div style="width: 320px; max-width: 100%;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">🔍</span>
                        <input type="text" id="courseSearchInput" class="form-control border-start-0 ps-0 search-control" placeholder=" Search course ID or title..." style="border-top-right-radius: 8px; border-bottom-right-radius: 8px; box-shadow: none; border-color: #ced4da;">
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Course ID</th>
                            <th>Course Title</th>
                            <th class="text-center">Credit Hours</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="courseTableBody">
                        <?php if($courses_result && $courses_result->num_rows > 0) { ?>
                            <?php while($row = $courses_result->fetch_assoc()) { ?>
                            <tr>
                                <td><span class="badge bg-dark px-2.5 py-1.5" style="font-size: 12px;"><?php echo $row['Course_ID']; ?></span></td>
                                <td><b class="text-dark"><?php echo $row['Course_Name']; ?></b></td>
                                <td class="text-center">
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle font-weight-bold px-3 py-1.5" style="color: #b45309 !important;">
                                        🎯 <?php echo $row['Credit_Hours']; ?> CH
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="manage_courses.php?delete_id=<?php echo $row['Course_ID']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this course completely? It will remove allocations and student materials associated with it.');" 
                                       class="btn btn-outline-danger btn-sm px-3" style="border-radius: 6px; transition: all 0.2s ease;">
                                        🗑️ Delete
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr class="no-records-row">
                                <td colspan="4" class="text-center text-muted py-4">No courses registered in the database yet.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 16px; border: none;">
      <div class="modal-header bg-dark text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
        <h5 class="modal-title" id="addCourseModalLabel">➕ Add New Course to Catalog</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="manage_courses.php" method="POST">
          <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small font-weight-bold text-secondary">Course Code /ID</label>
                    <input type="text" name="course_id" class="form-control" placeholder="e.g., CS-201, CS-212" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small font-weight-bold text-secondary">Course Title / Name</label>
                    <input type="text" name="course_name" class="form-control" placeholder="e.g., Data Structures & Algorithms" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small font-weight-bold text-secondary">Credit Hours</label>
                    <select name="credit_hours" class="form-select">
                        <option value="3">3 Credit Hours (Theory)</option>
                        <option value="4">4 Credit Hours (Theory + Lab)</option>
                        <option value="2">2 Credit Hours (Lab Only)</option>
                        <option value="1">1 Credit Hour</option>
                    </select>
                </div>
          </div>
          <div class="modal-footer bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="add_course" class="btn btn-danger px-4">Save Course</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('courseSearchInput').addEventListener('keyup', function() {
    let filterValue = this.value.toLowerCase().trim();
    let rows = document.querySelectorAll('#courseTableBody tr:not(.no-records-row)');
    let matchesFound = false;

    rows.forEach(function(row) {
        let rowText = row.textContent.toLowerCase();
        if (rowText.includes(filterValue)) {
            row.style.display = '';
            matchesFound = true;
        } else {
            row.style.display = 'none';
        }
    });

    let existingNoMatch = document.getElementById('noMatchRow');
    if (!matchesFound && rows.length > 0) {
        if (!existingNoMatch) {
            let tr = document.createElement('tr');
            tr.id = 'noMatchRow';
            tr.innerHTML = `<td colspan="4" class="text-center text-muted py-4">No matching course found for "${this.value}"</td>`;
            document.getElementById('courseTableBody').appendChild(tr);
        }
    } else {
        if (existingNoMatch) {
            existingNoMatch.remove();
        }
    }
});
</script>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>