<?php
session_start();
include 'db_connect.php';

// Check if logged in
if(!isset($_SESSION['instructor_id'])){ header("Location: login.php"); exit(); }

$ins_id = $_SESSION['instructor_id'];

// Update Profile Logic
if(isset($_POST['update_profile'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Image Upload
    if(!empty($_FILES['profile_pic']['name'])){
        $img_name = time() . '_' . $_FILES['profile_pic']['name'];
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], 'uploads/'.$img_name);
        $conn->query("UPDATE instructor SET profile_pic='$img_name' WHERE Instructor_ID='$ins_id'");
    }
    
    $conn->query("UPDATE instructor SET Name='$name', Phone='$phone', Address='$address' WHERE Instructor_ID='$ins_id'");
    $msg = "Profile Updated Successfully!";
}

$result = $conn->query("SELECT * FROM instructor WHERE Instructor_ID='$ins_id'");
$row = $result ? $result->fetch_assoc() : null;
$course_query = "
SELECT c.Course_Name
FROM course_allocation ca
INNER JOIN course c
ON ca.Course_ID = c.Course_ID
WHERE ca.Instructor_ID = '$ins_id'
";

$course_result = $conn->query($course_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructor Profile</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; }
        .sidebar { width: 240px; background: #0f172a; height: 100vh; position: fixed; color: white; }
        .main-content { margin-left: 240px; padding: 25px; }
        .avatar-circle { width: 120px; height: 120px; background: #ef4444; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 50px; margin: auto; }
        .card-box { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="d-flex">
    <div class="sidebar p-3">
        <h4>Instructor Portal</h4>
        <a class="nav-link text-white" href="instructor_dashboard.php">Dashboard</a>
        <a class="nav-link text-white" href="instructor_profile.php">My Profile</a>
    </div>

    <div class="main-content">
        <div class="card-box">
            <div class="text-center mb-4">
               <?php if(!empty($row['profile_pic'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($row['profile_pic']); ?>" class="rounded-circle" width="120" height="120">
                <?php else: ?>
                    <div class="avatar-circle"><?php echo strtoupper(substr($row['Name'], 0, 1)); ?></div>
                <?php endif; ?>
                <h3 class="mt-3"><?php echo $row['Name'] ?? 'Instructor'; ?></h3>
                <div class="mt-4">

    <h5 class="mb-3">Assigned Courses</h5>

    <?php if($course_result && $course_result->num_rows > 0){ ?>

        <ul class="list-group">

            <?php while($course = $course_result->fetch_assoc()){ ?>

                <li class="list-group-item">
                    📘 <?php echo $course['Course_Name']; ?>
                </li>

            <?php } ?>

        </ul>

    <?php } else { ?>

        <div class="alert alert-warning">
            No Courses Assigned Yet.
        </div>

    <?php } ?>

</div>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Profile Picture</label>
                    <input type="file" name="profile_pic" class="form-control">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?php echo $row['Name']; ?>" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Phone</label>
                        <input type="text" name="phone" value="<?php echo $row['Phone']; ?>" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label>Address</label>
                    <textarea name="address" class="form-control"><?php echo $row['Address']; ?></textarea>
                </div>
                <button type="submit" name="update_profile" class="btn btn-danger">Update Profile</button>
            </form>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>