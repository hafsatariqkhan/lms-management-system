<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$student_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
$success_msg = "";
$error_msg = "";


if (isset($_POST['upload_photo'])) {
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['profile_pic']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = "avatar_" . $student_id . "." . $ext;
            $upload_path = "uploads/" . $new_filename;

            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_path)) {
                $conn->query("UPDATE student SET Profile_Pic='$upload_path' WHERE Std_ID='$student_id'");
                $success_msg = "📸 Profile picture uploaded successfully!";
            } else {
                $error_msg = "❌ Failed to save uploaded image.";
            }
        } else {
            $error_msg = "❌ Invalid format! Only JPG, JPEG, & PNG are allowed.";
        }
    } else {
        $error_msg = "❌ Please select a valid file to upload.";
    }
}

// 2. Handle Remove Photo Logic
if (isset($_POST['remove_photo'])) {
    $check_pic = $conn->query("SELECT Profile_Pic FROM student WHERE Std_ID='$student_id'");
    $pic_row = $check_pic->fetch_assoc();
    
    if (!empty($pic_row['Profile_Pic']) && file_exists($pic_row['Profile_Pic'])) {
        unlink($pic_row['Profile_Pic']); 
    }
    
    $conn->query("UPDATE student SET Profile_Pic=NULL WHERE Std_ID='$student_id'");
    $success_msg = "🗑️ Profile picture removed successfully!";
}

// Fetch Student Current Data
$fetch_student = $conn->query("SELECT * FROM student WHERE Std_ID='$student_id'");
if (!$fetch_student || $fetch_student->num_rows == 0) {
    echo "<h3 class='text-center mt-5'>Your Record Not Found!</h3>";
    exit();
}
$std = $fetch_student->fetch_assoc();

$student_name = $std['Name']; 
$first_letter = !empty($student_name) ? strtoupper(substr($student_name, 0, 1)) : '?';

$colors = ['#8b5cf6', '#3b82f6', '#10b981', '#ef4444', '#f59e0b', '#ec4899', '#6366f1', '#14b8a6'];
$color_index = strlen($student_name) % count($colors);
$avatar_bg = $colors[$color_index];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Student LMS</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">

    <style>
        body { 
            background: #f1f5f9; 
            font-family: 'Segoe UI', sans-serif; }
        .sidebar-mock { 
            background: #0f172a; 
            min-height: 100vh;
             color: white; 
             width: 240px; 
             position: fixed; }
        .profile-container { 
            margin-left: 240px;
             padding: 30px; 
             width: calc(100% - 240px); }
      
        .avatar-view { 
            width: 140px; 
            height: 140px;
            object-fit: cover; 
            border-radius: 50%; 
            border: 4px solid #38bdf8; /* Soft blue boundary match */
            box-shadow: 0 6px 16px rgba(56, 189, 248, 0.2); }

        .letter-avatar {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            color: white;
            font-size: 58px;
            font-weight: 700; 
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto; /* Horizontally center alignment inside card */
            text-shadow: 1px 2px 4px rgba(0, 0, 0, 0.15); 
            border: 4px solid #38bdf8;
            box-shadow: 0 6px 16px rgba(69, 34, 151, 0.1);
            text-transform: uppercase;
            user-select: none; 
        }

        .card-custom { 
            background: white; 
            border-radius: 16px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.02); 
            border: none; 
            padding: 25px; }
        .read-only-box { 
            background-color: #f8fafc;
             border: 1px solid #e2e8f0; 
             border-radius: 8px; 
             padding: 10px 15px; 
             font-weight: 600; 
             color: #334155; }
        .nav-link-custom { 
            color: #94a3b8; 
            padding: 12px 15px; 
            display: block; 
            text-decoration: none;
             font-size: 16px; 
             border-radius: 8px; }
        .nav-link-custom:hover { 
            color: #38bdf8; 
            background: rgba(56, 189, 248, 0.1); }
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

    /* MAIN CONTENT LAYOUT */
    .main-content {
        margin-left: 240px; 
        padding: 25px;
        background: #f8fafc;
        min-height: 100vh;
        width: calc(100% - 240px);
        box-sizing: border-box;
    }
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
        <?php if(!empty($success_msg)): ?>
            <div class="alert alert-success shadow-sm"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if(!empty($error_msg)): ?>
            <div class="alert alert-danger shadow-sm"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <div class="card-custom py-5">
                    
                    <?php if (!empty($std['Profile_Pic']) && file_exists($std['Profile_Pic'])): ?>
                        <img src="<?php echo $std['Profile_Pic']; ?>" alt="Avatar" class="avatar-view mb-3">
                    <?php else: ?>
                        <div class="letter-avatar mb-3" style="background-color: <?php echo $avatar_bg; ?>;">
                            <?php echo $first_letter; ?>
                        </div>
                    <?php endif; ?>
                    
                    <h4 class="fw-bold text-dark mb-1"><?php echo $std['Name']; ?></h4>
                    <span class="badge bg-primary px-3 py-1 mb-4"><?php echo $std['Program']; ?> Student</span>
                    
                    <div class="mt-2 border-top pt-3 text-start">
                        <form action="" method="POST" enctype="multipart/form-data" class="mb-3">
                            <label class="form-label small fw-bold text-muted">Update Profile Picture</label>
                            <input type="file" name="profile_pic" class="form-control form-control-sm mb-2" required>
                            <button type="submit" name="upload_photo" class="btn btn-sm btn-dark w-100">📤 Upload Photo</button>
                        </form>

                        <?php if(!empty($std['Profile_Pic'])): ?>
                            <form action="" method="POST" onsubmit="return confirm('Are you sure you want to remove your profile picture?');">
                                <button type="submit" name="remove_photo" class="btn btn-sm btn-outline-danger w-100">🗑️ Remove Photo</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card-custom">
                    <h5 class="fw-bold mb-4 text-dark border-bottom pb-2">📋 Personal & Academic Information</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-secondary fw-bold">Student ID</label>
                            <div class="read-only-box"><?php echo $std['Std_ID']; ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-secondary fw-bold">Registration Date</label>
                            <div class="read-only-box"><?php echo isset($std['Registration_Date']) ? $std['Registration_Date'] : 'N/A'; ?></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-secondary fw-bold">Full Name</label>
                            <div class="read-only-box"><?php echo $std['Name']; ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-secondary fw-bold">Official Email</label>
                            <div class="read-only-box"><?php echo $std['Email']; ?></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-secondary fw-bold">Phone Number</label>
                            <div class="read-only-box"><?php echo $std['Phone']; ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-secondary fw-bold">CNIC / B-Form No</label>
                            <div class="read-only-box"><?php echo $std['CNIC']; ?></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-secondary fw-bold">Date of Birth</label>
                            <div class="read-only-box"><?php echo $std['DOB']; ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-secondary fw-bold">Gender</label>
                            <div class="read-only-box"><?php echo $std['Gender']; ?></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-secondary fw-bold">Faculty Department</label>
                            <div class="read-only-box"><?php echo $std['Department']; ?></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-secondary fw-bold">Degree Program</label>
                            <div class="read-only-box"><?php echo $std['Program']; ?></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small text-secondary fw-bold">Admission Year</label>
                            <div class="read-only-box"><?php echo $std['Admission_Year']; ?></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small text-secondary fw-bold">Session</label>
                            <div class="read-only-box"><?php echo $std['Academic_Session']; ?></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small text-secondary fw-bold">Current Semester</label>
                            <div class="read-only-box"><?php echo $std['Semester']; ?>th Sem</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small text-secondary fw-bold">Shift</label>
                            <div class="read-only-box"><?php echo $std['Shift']; ?></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small text-secondary fw-bold">Expected Graduation</label>
                            <div class="read-only-box"><?php echo $std['Graduation_Year']; ?></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small text-secondary fw-bold">Account Status</label>
                            <div class="read-only-box"><?php echo $std['Status']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>