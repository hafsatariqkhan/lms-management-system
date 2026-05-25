<?php
session_start();
include 'db_connect.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$success_msg = "";
$error_msg = "";

// DELETE INSTRUCTOR
if(isset($_GET['delete'])){
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $del = "DELETE FROM instructor WHERE Instructor_ID='$delete_id'";
    if($conn->query($del)){
        $success_msg = "🗑️ Instructor deleted successfully!";
    } else {
        $error_msg = "❌ Delete failed: " . $conn->error;
    }
}

// AUTO GENERATE INSTRUCTOR ID
$id_query = $conn->query("SELECT Instructor_ID FROM instructor ORDER BY CAST(SUBSTRING(Instructor_ID, 2) AS UNSIGNED) DESC LIMIT 1");
if($id_query && $id_query->num_rows > 0){
    $last_row = $id_query->fetch_assoc();
    $number = intval(substr($last_row['Instructor_ID'], 1));
    $new_instructor_id = "I" . ($number + 1);
} else {
    $new_instructor_id = "I301";
}

// ADD INSTRUCTOR
if(isset($_POST['add_instructor'])){
    $instructor_id = mysqli_real_escape_string($conn, $_POST['instructor_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $dept = mysqli_real_escape_string($conn, $_POST['dept']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);
    $qualification = mysqli_real_escape_string($conn, $_POST['qualification']);
    $nic = mysqli_real_escape_string($conn, $_POST['nic']);
    $joining_date = mysqli_real_escape_string($conn, $_POST['joining_date']);
    $leaving_date = mysqli_real_escape_string($conn, $_POST['leaving_date']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $reg_date = date("Y-m-d");

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error_msg = "❌ Invalid email format!";
    } else {
        $insert = "INSERT INTO instructor (Instructor_ID, Name, Email, Department, Phone, Designation, Qualification, CNIC, Joining_Date, Leaving_Date, Address, Gender, Status, Registration_Date)
                   VALUES ('$instructor_id', '$name', '$email', '$dept', '$phone', '$designation', '$qualification', '$nic', '$joining_date', '$leaving_date', '$address', '$gender', '$status', '$reg_date')";

        if($conn->query($insert)){
            $success_msg = "✅ Instructor registered successfully!";
        } else {
            $error_msg = "❌ Error: " . $conn->error;
        }
    }
}

$instructors_result = $conn->query("SELECT * FROM instructor ORDER BY Instructor_ID ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Instructors | Admin Panel</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
    <style>
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
            margin: 15px 0; 
            padding: 15px 20px;
            text-decoration: none;
            display: block;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease; 
            white-space: nowrap;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
       
        .nav-link:hover , .nav-link.active{
            color: #f87171; 
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

        .card-box { 
            background: white; 
            border-radius: 16px; 
            padding: 25px; 
            margin-bottom: 25px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        .search-control { 
            border: 1px solid #cbd5e1 !important; 
            height: 40px; }
        .clickable-row { 
            cursor: pointer; 
        }
        .clickable-row:hover { 
            background-color: rgba(239, 68, 68, 0.04) !important; 
        }
        .admin-badge-title {
            font-weight: 700; 
            letter-spacing: 0.5px; 
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 15px;
        }

    </style>
</head>
<body>

<div class="d-flex"> <nav class="admin-sidebar p-3">
        <div>
            <h4 class="text-center mt-2 mb-4 admin-badge-title">Admin Portal</h4>
            <div class="nav-links-wrapper">
                <a class="nav-link active" href="admin_dashboard.php">📊 Dashboard</a>
                <a class="nav-link" href="manage_students.php">👨‍🎓 Manage Students</a>
                <a class="nav-link" href="manage_instructors.php">👩‍🏫 Manage Instructors</a>
                <a class="nav-link" href="manage_courses.php">📚 Manage Courses</a>
                <a class="nav-link" href="course_allocation.php">🔗 Course Allocation</a>
            </div>
        </div>
        
        <a class="nav-link text-warning mb-3" href="logout.php" style="padding-top: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);">🚪 Logout</a>
    </nav>
    <div class="main-content w-100">
        <?php if($success_msg) echo "<div class='alert alert-success'>$success_msg</div>"; ?>
        <?php if($error_msg) echo "<div class='alert alert-danger'>$error_msg</div>"; ?>

        <div class="card-box d-flex justify-content-between align-items-center">
            <div>
                <h3>👩‍🏫 Instructor Management</h3>
                <p class="text-muted">Click row to view instructor profile.</p>
            </div>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addInstructorModal">➕ Add New Instructor</button>
        </div>

        <div class="card-box">
            <input type="text" id="insSearch" class="form-control search-control mb-3" placeholder="Search by name or ID...">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr><th>ID</th><th>Name</th><th>Email</th><th>Dept</th><th>Status</th><th class="text-center">Action</th></tr>
                    </thead>
                    <tbody id="insTableBody">
                    <?php while($row = $instructors_result->fetch_assoc()){ ?>
                        <tr class="clickable-row" onclick="window.location='instructor_admin_profile.php?Instructor_ID=<?php echo $row['Instructor_ID']; ?>';">
                            <td><span class="badge bg-dark"><?php echo $row['Instructor_ID']; ?></span></td>
                            <td><b><?php echo $row['Name']; ?></b></td>
                            <td><?php echo $row['Email']; ?></td>
                            <td><?php echo $row['Department']; ?></td>
                            <td><span class="badge <?php echo ($row['Status'] == 'Active') ? 'bg-success' : 'bg-warning'; ?>"><?php echo $row['Status']; ?></span></td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                <a href="instructor_admin_profile.php?Instructor_ID=<?php echo $row['Instructor_ID']; ?>" class="btn btn-sm btn-outline-primary">⚙️ Edit</a>
                                <a href="manage_instructors.php?delete=<?php echo $row['Instructor_ID']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Confirm delete?');">🗑️ Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addInstructorModal">
    <div class="modal-dialog modal-lg"><div class="modal-content p-3">
        <form method="POST">
            <div class="modal-header"><h5>Add New Instructor</h5></div>
            <div class="modal-body row">
                <div class="col-md-6 mb-2"><label>ID</label><input type="text" name="instructor_id" class="form-control" value="<?php echo $new_instructor_id; ?>" readonly></div>
                <div class="col-md-6 mb-2"><label>Name</label><input type="text" name="name" class="form-control" required></div>
                <div class="col-md-6 mb-2"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="col-md-6 mb-2"><label>Department</label><input type="text" name="dept" class="form-control"></div>
                <div class="col-md-6 mb-2"><label>Phone</label><input type="text" name="phone" class="form-control"></div>
                <div class="col-md-6 mb-2"><label>Designation</label><input type="text" name="designation" class="form-control"></div>
                <div class="col-md-6 mb-2"><label>Qualification</label><input type="text" name="qualification" class="form-control"></div>
                <div class="col-md-6 mb-2"><label>CNIC</label><input type="text" name="nic" class="form-control"></div>
                <div class="col-md-6 mb-2"><label>Joining Date</label><input type="date" name="joining_date" class="form-control"></div>
                <div class="col-md-6 mb-2"><label>Gender</label><select name="gender" class="form-control"><option>Male</option><option>Female</option></select></div>
                <div class="col-md-6 mb-2"><label>Status</label><select name="status" class="form-control"><option>Active</option><option>Inactive</option></select></div>
                <div class="col-12 mb-2"><label>Address</label><textarea name="address" class="form-control"></textarea></div>
            </div>
            <div class="modal-footer"><button type="submit" name="add_instructor" class="btn btn-danger">Register Instructor</button></div>
        </form>
    </div></div>
</div>

<script>
document.getElementById('insSearch').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    document.querySelectorAll('#insTableBody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
    });
});
</script>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>