<?php
session_start();
include 'db_connect.php';


if (!isset($_SESSION['admin_id'])) { 
    header("Location: Login.php");
    exit();
}

$success_msg = "";
$error_msg = "";

if (isset($_GET['Std_ID'])) {
    $student_id = mysqli_real_escape_string($conn, $_GET['Std_ID']);
} else {
    echo "<h3 class='text-center mt-5'>No Student ID Specified!</h3>";
    exit();
}


if (isset($_POST['update_record'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $cnic = mysqli_real_escape_string($conn, $_POST['cnic']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $program = mysqli_real_escape_string($conn, $_POST['program']);
    $admission_year = mysqli_real_escape_string($conn, $_POST['admission_year']);
    $session = mysqli_real_escape_string($conn, $_POST['session']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $shift = mysqli_real_escape_string($conn, $_POST['shift']);
    $graduation_year = mysqli_real_escape_string($conn, $_POST['graduation_year']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $update_query = "UPDATE student SET 
        Name='$name', Email='$email', Phone='$phone', CNIC='$cnic', 
        DOB='$dob', Gender='$gender', Department='$department', Program='$program', 
        Admission_Year='$admission_year', Academic_Session='$session', Semester='$semester', 
        Shift='$shift', Graduation_Year='$graduation_year', Status='$status' 
        WHERE Std_ID='$student_id'";

    if ($conn->query($update_query)) {
        $success_msg = "✅ Student record updated successfully!";
    } else {
        $error_msg = "❌ Error updating record: " . $conn->error;
    }
}


$fetch_student = $conn->query("SELECT * FROM student WHERE Std_ID='$student_id'");
if (!$fetch_student || $fetch_student->num_rows == 0) {
    echo "<h3 class='text-center mt-5'>Student Record Not Found!</h3>";
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
    <title>Admin - Edit Student Profile</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
    <style>
        body { 
            background: #f1f5f9; 
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
            margin: 16px 0; 
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

        
        .profile-container { 
            margin-left: 260px;
            padding: 40px; 
            width: calc(100% - 260px); 
        }
        
        
        .avatar-view { 
            width: 140px; 
            height: 140px;
            object-fit: cover; 
            border-radius: 50%; 
            border: 4px solid #8b5cf6;
            box-shadow: 0 6px 16px rgba(139, 92, 246, 0.2); 
        }

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
            margin: 0 auto;
            text-shadow: 1px 2px 4px rgba(0, 0, 0, 0.15); 
            border: 4px solid #8b5cf6;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            text-transform: uppercase;
            user-select: none; 
        }

        .card-custom { 
            background: white; 
            border-radius: 16px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.02); 
            border: none; 
            padding: 30px; 
        }
        
        .read-only-box { 
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1; 
            border-radius: 8px; 
            padding: 10px 15px; 
            font-weight: 600; 
            color: #475569; 
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            font-weight: 500;
            color: #1e293b;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        
        .lock-box {
            background-color: #fef9c3;
            border: 1px solid #fef08a;
            border-radius: 12px;
            padding: 16px;
            color: #713f12;
            font-size: 13px;
            text-align: left;
            margin-top: 25px;
        }
    </style>
</head>
<body>

<nav class="admin-sidebar p-3">
        <div>
            <h4 class="text-center mt-2 mb-4 admin-badge-title">Admin Portal</h4>
            <div class="nav-links-wrapper">
                <a class="nav-link" href="admin_dashboard.php">📊 Dashboard</a>
                <a class="nav-link active" href="manage_students.php">👨‍🎓 Manage Students</a>
                <a class="nav-link" href="manage_instructors.php">👩‍🏫 Manage Instructors</a>
                <a class="nav-link" href="manage_courses.php">📚 Manage Courses</a>
                <a class="nav-link" href="course_allocation.php">🔗 Course Allocation</a>
            </div>
        </div>
        <a class="nav-link text-warning mb-3" href="logout.php" style="padding-top: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);">🚪 Logout</a>
    </nav>
    <div class="profile-container">
        <?php if(!empty($success_msg)): ?>
            <div class="alert alert-success shadow-sm mb-4"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if(!empty($error_msg)): ?>
            <div class="alert alert-danger shadow-sm mb-4"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
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
                        
                        <h3 class="fw-bold text-dark mb-1"><?php echo $std['Name']; ?></h3>
                        <span class="badge bg-danger px-3 py-1 mb-4">Admin Editing Mode</span>
                        
                        <div class="lock-box">
                            🔒 <strong>Photo Locked:</strong> Due to security reasons, the administrator cannot change the student's profile picture. This access is restricted to the student only.
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card-custom">
                        <h4 class="fw-bold mb-4 text-dark border-bottom pb-3">📋 Modify Student Academic & Personal Record</h4>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Student ID</label>
                                <div class="read-only-box"><?php echo $std['Std_ID']; ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Registration Date</label>
                                <div class="read-only-box"><?php echo isset($std['Registration_Date']) ? $std['Registration_Date'] : '2023-09-01'; ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo $std['Name']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Official Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $std['Email']; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo $std['Phone']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CNIC / B-Form No</label>
                                <input type="text" name="cnic" class="form-control" value="<?php echo $std['CNIC']; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" value="<?php echo $std['DOB']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option value="Female" <?php if($std['Gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                    <option value="Male" <?php if($std['Gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Faculty Department</label>
                                <input type="text" name="department" class="form-control" value="<?php echo $std['Department']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Degree Program</label>
                                <input type="text" name="program" class="form-control" value="<?php echo $std['Program']; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Admission Year</label>
                                <input type="number" name="admission_year" class="form-control" value="<?php echo $std['Admission_Year']; ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Session</label>
                                <select name="session" class="form-select" required>
                                    <option value="Fall" <?php if($std['Academic_Session'] == 'Fall') echo 'selected'; ?>>Fall</option>
                                    <option value="Spring" <?php if($std['Academic_Session'] == 'Spring') echo 'selected'; ?>>Spring</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Current Semester</label>
                                <select name="semester" class="form-select" required>
                                    <?php for($i=1; $i<=8; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php if($std['Semester'] == $i) echo 'selected'; ?>><?php echo $i; ?>th Sem</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Shift</label>
                                <select name="shift" class="form-select" required>
                                    <option value="Morning" <?php if($std['Shift'] == 'Morning') echo 'selected'; ?>>Morning</option>
                                    <option value="Evening" <?php if($std['Shift'] == 'Evening') echo 'selected'; ?>>Evening</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Expected Graduation</label>
                                <input type="number" name="graduation_year" class="form-control" value="<?php echo isset($std['Graduation_Year']) ? $std['Graduation_Year'] : ($std['Admission_Year'] + 4); ?>" required>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Account Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="Active" <?php if($std['Status'] == 'Active') echo 'selected'; ?>>Active</option>
                                    <option value="Suspended" <?php if($std['Status'] == 'Suspended') echo 'selected'; ?>>Suspended</option>
                                    <option value="Graduated" <?php if($std['Status'] == 'Graduated') echo 'selected'; ?>>Graduated</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-end border-top pt-3">
                            <button type="submit" name="update_record" class="btn btn-primary px-5 py-2 fw-bold" style="border-radius: 8px;">💾 Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

</body>
</html>