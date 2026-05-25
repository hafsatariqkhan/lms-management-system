<?php
session_start();
include 'db_connect.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$success_msg = "";
$error_msg = "";

$next_student_id = 101; 
$Std_ID_fetch = $conn->query("SELECT Std_ID FROM student ORDER BY CAST(Std_ID AS UNSIGNED) DESC LIMIT 1");
if($Std_ID_fetch && $Std_ID_fetch->num_rows > 0) {
    $row = $Std_ID_fetch->fetch_assoc();
    $next_student_id = (int)$row['Std_ID'] + 1; 
}

if(isset($_POST['add_student'])){
    $std_id = mysqli_real_escape_string($conn, $_POST['Std_ID']);
    $reg_date = mysqli_real_escape_string($conn, $_POST['reg_date']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $cnic = mysqli_real_escape_string($conn, $_POST['cnic']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $dept = mysqli_real_escape_string($conn, $_POST['dept']);
    $program = mysqli_real_escape_string($conn, $_POST['program']);
    $adm_year = mysqli_real_escape_string($conn, $_POST['adm_year']);
    $academic_session = mysqli_real_escape_string($conn, $_POST['academic_session']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $shift = mysqli_real_escape_string($conn, $_POST['shift']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $grad_year = mysqli_real_escape_string($conn, $_POST['grad_year']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^03[0-9]{9}$/', $phone) || !preg_match('/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/', $cnic)) {
        $error_msg = "❌ Invalid format detected in Email, Phone, or CNIC!";
    } else {
        $check = $conn->query("SELECT * FROM student WHERE Std_ID='$std_id'");
        if($check->num_rows > 0){
            $error_msg = "❌ Student ID already exists in the system!";
        } else {
            $insert_query = "INSERT INTO student (Std_ID, Registration_Date, Name, Email, Phone, CNIC, DOB, Department, Program, Admission_Year, Academic_Session, Semester, Shift, Gender, Graduation_Year, Status) 
                             VALUES ('$std_id', '$reg_date', '$name', '$email', '$phone', '$cnic', '$dob', '$dept', '$program', '$adm_year', '$academic_session', '$semester', '$shift', '$gender', '$grad_year', '$status')";
            
            if($conn->query($insert_query)){
                header("Location: manage_students.php?success=1");
                exit();
            } else {
                $error_msg = "❌ Failed to register student: " . $conn->error;
            }
        }
    }
}

if(isset($_GET['delete_id'])){
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $clean_enrollments = "DELETE FROM enrollment WHERE Std_ID='$delete_id'";
    $conn->query($clean_enrollments);
    $delete_query = "DELETE FROM student WHERE Std_ID='$delete_id'";
    
    if($conn->query($delete_query)){
        header("Location: manage_students.php?deleted=1");
        exit();
    } else {
        $error_msg = "❌ Failed to delete student: " . $conn->error;
    }
}

if(isset($_GET['success'])) { $success_msg = "✅ Student registered successfully!"; }
if(isset($_GET['deleted'])) { $success_msg = "🗑️ Student record cleared successfully!"; }

$students_result = $conn->query("SELECT * FROM student ORDER BY CAST(Std_ID AS UNSIGNED) ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students | Admin Panel</title>
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
              z-index: 1000; box-sizing: border-box; 
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
        .search-control { 
            border: 1px solid #cbd5e1 !important; 
            font-size: 14px; 
            height: 40px; 
            transition: all 0.25s ease-in-out !important;
         }
        .search-control:focus { 
            border-color: #94a3b8 !important;
             box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
              background-color: #f8fafc !important; 
            }
        .auto-fade-alert { 
            transition: opacity 0.5s ease-out; 
        }
        .clickable-row { 
            cursor: pointer;
             transition: background-color 0.2s ease; 
            }
        .clickable-row:hover { 
            background-color: rgba(239, 68, 68, 0.04) !important; 
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
                <a class="nav-link active" href="manage_students.php">👨‍🎓 Manage Students</a>
                <a class="nav-link" href="manage_instructors.php">👩‍🏫 Manage Instructors</a>
                <a class="nav-link" href="manage_courses.php">📚 Manage Courses</a>
                <a class="nav-link" href="course_allocation.php">🔗 Course Allocation</a>
            </div>
        </div>
        <a class="nav-link text-warning mb-3" href="logout.php" style="padding-top: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);">🚪 Logout</a>
    </nav>
    <div class="main-content">
        
        <?php if(!empty($success_msg)) { echo "<div id='statusAlert' class='alert alert-success shadow-sm auto-fade-alert'>$success_msg</div>"; } ?>
        <?php if(!empty($error_msg)) { echo "<div id='statusAlert' class='alert alert-danger shadow-sm auto-fade-alert'>$error_msg</div>"; } ?>

        <div class="card-box d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1" style="color: #0f172a; font-weight: 700;">👨‍🎓 Student Management</h3>
                <p class="text-muted mb-0">Click any student row to view and edit their full academic profile.</p>
            </div>
            <div>
                <button class="btn btn-danger px-4 py-2" data-bs-toggle="modal" data-bs-target="#addStudentModal" style="border-radius: 8px; font-weight: 600;">
                    ➕ Add New Student
                </button>
            </div>
        </div>

        <div class="card-box">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <h5 class="mb-0" style="color: #0f172a; font-weight: 700;">📋 Registered Students List</h5>
                <div style="width: 320px; max-width: 100%;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">🔍</span>
                        <input type="text" id="studentSearchInput" class="form-control border-start-0 ps-0 search-control" placeholder=" Search name, ID or program...">
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Contact Info</th>
                            <th>Academic Program</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody">
                        <?php if($students_result && $students_result->num_rows > 0) { ?>
                            <?php while($row = $students_result->fetch_assoc()) { 
                                $status_badge = (isset($row['Status']) && $row['Status'] == 'Active') ? 'bg-success' : 'bg-warning';
                            ?>
                            <tr class="clickable-row" onclick="window.location='admin_edit_profile.php?Std_ID=<?php echo $row['Std_ID']; ?>';">
                                <td><span class="badge bg-dark px-2 py-1"><?php echo $row['Std_ID']; ?></span></td>
                                <td><b class="text-dark"><?php echo $row['Name']; ?></b></td>
                                <td>
                                    <small class="d-block text-primary"><?php echo $row['Email']; ?></small>
                                    <small class="d-block text-muted">📞 <?php echo $row['Phone']; ?></small>
                                </td>
                                <td><span class="text-secondary font-weight-bold"><?php echo $row['Department']; ?> - <?php echo isset($row['Program']) ? $row['Program'] : 'N/A'; ?></span></td>
                                <td><span class="badge bg-light text-dark border px-2 py-1"><?php echo $row['Semester']; ?>th Sem</span></td>
                                <td><span class="badge <?php echo $status_badge; ?> px-2 py-1" style="font-size:11px;"><?php echo isset($row['Status']) ? $row['Status'] : 'Active'; ?></span></td>
                                <td class="text-center" onclick="event.stopPropagation();">
                                    <a href="admin_edit_profile.php?Std_ID=<?php echo $row['Std_ID']; ?>" class="btn btn-outline-primary btn-sm px-2 me-1" style="border-radius: 6px;">⚙️ Edit</a>
                                    <a href="manage_students.php?delete_id=<?php echo $row['Std_ID']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this student?');" 
                                       class="btn btn-outline-danger btn-sm px-2" style="border-radius: 6px;">
                                       🗑️ Delete
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr class="no-records-row">
                                <td colspan="7" class="text-center text-muted py-4">No student records found in the database.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 16px; border: none;">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">➕ Register New Student</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="px-4 pt-3"><div id="modalAlertError" class="alert alert-danger py-2 small font-weight-bold" style="display:none;"></div></div>

      <form action="manage_students.php" method="POST" id="studentRegistrationForm">
          <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">Student ID</label>
                        <input type="text" name="Std_ID" class="form-control bg-light" value="<?php echo $next_student_id; ?>" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">Registration Date</label>
                        <input type="date" id="registrationDate" name="reg_date" class="form-control bg-light" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">Official Email</label>
                        <input type="text" id="studentEmail" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">Phone Number</label>
                        <input type="text" id="studentPhone" name="phone" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">CNIC</label>
                        <input type="text" id="studentCNIC" name="cnic" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">Department</label>
                        <input type="text" name="dept" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">Degree Program</label>
                        <select id="studentProgram" name="program" class="form-select">
                            <option value="BSCS">BSCS (Computer Science)</option>
                            <option value="BSSE">BSSE (Software Engineering)</option>
                            <option value="BSAI">BS-AI (Artificial Intelligence)</option>
                            <option value="BSIT">BSIT (Information Tech)</option>
                            <option value="BBA">BBA (Business Admin)</option>
                            <option value="MSCS">MSCS (Postgraduate)</option>
                            <option value="MBA">MBA (Postgraduate)</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">Admission Year</label>
                        <input type="number" id="admissionYear" name="adm_year" class="form-control" value="2026" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">Academic Session</label>
                        <select name="academic_session" class="form-select">
                            <option value="Fall">Fall</option>
                            <option value="Spring">Spring</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">Semester</label>
                        <select name="semester" class="form-select">
                            <?php for($i=1; $i<=8; $i++) { echo "<option value='$i'>{$i}th</option>"; }?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">Shift</label>
                        <select name="shift" class="form-select">
                            <option value="Morning">Morning</option>
                            <option value="Evening">Evening</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">Expected Graduation</label>
                        <input type="text" id="expectedGraduation" name="grad_year" class="form-control bg-light" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label small font-weight-bold text-secondary">Account Status</label>
                        <select name="status" class="form-select">
                            <option value="Active">Active / Enrolled</option>
                            <option value="Suspended">Suspended / Frozen</option>
                            <option value="Graduated">Passed Out / Graduated</option>
                        </select>
                    </div>
                </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="add_student" class="btn btn-danger px-4">Register Student</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('studentSearchInput').addEventListener('keyup', function() {
    let filterValue = this.value.toLowerCase().trim();
    let rows = document.querySelectorAll('#studentTableBody tr:not(.no-records-row)');
    rows.forEach(function(row) {
        row.style.display = row.textContent.toLowerCase().includes(filterValue) ? '' : 'none';
    });
});

const programSelect = document.getElementById('studentProgram');
const admissionYearInput = document.getElementById('admissionYear');
const expectedGradInput = document.getElementById('expectedGraduation');
const regDateInput = document.getElementById('registrationDate');

function calculateGraduation() {
    if (!admissionYearInput || !programSelect || !expectedGradInput) return;
    let year = parseInt(admissionYearInput.value);
    let program = programSelect.value;
    if (isNaN(year)) return;
    expectedGradInput.value = (program.startsWith('MS') || program.startsWith('MB')) ? year + 2 : year + 4;
}

function initializeFormMetrics() {
    regDateInput.value = new Date().toISOString().split('T')[0];
    calculateGraduation();
    
    const alertBox = document.getElementById('statusAlert');
    if(alertBox) {
        setTimeout(() => { alertBox.style.opacity = '0'; setTimeout(() => alertBox.remove(), 500); }, 4000);
    }
}

programSelect.addEventListener('change', calculateGraduation);
admissionYearInput.addEventListener('input', calculateGraduation);
window.addEventListener('DOMContentLoaded', initializeFormMetrics);

document.getElementById('studentRegistrationForm').addEventListener('submit', function(e) {
    const errorAlert = document.getElementById('modalAlertError');
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const phonePattern = /^03\d{9}$/;
    const cnicPattern = /^\d{5}-\d{7}-\d{1}$/;
    
    let errorMessages = [];
    if(!emailPattern.test(document.getElementById('studentEmail').value.trim())) errorMessages.push("❌ Invalid Email.");
    if(!phonePattern.test(document.getElementById('studentPhone').value.trim())) errorMessages.push("❌ Phone must be 11 digits starting 03.");
    if(!cnicPattern.test(document.getElementById('studentCNIC').value.trim())) errorMessages.push("❌ Invalid CNIC format.");

    if(errorMessages.length > 0) {
        e.preventDefault();
        errorAlert.innerHTML = errorMessages.join("<br>");
        errorAlert.style.display = "block";
    }
});
</script>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>