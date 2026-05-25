<?php
session_start();
include 'db_connect.php';

$id = $_SESSION['user_id'];

/*  DELETE SUBMISSION */
if(isset($_POST['delete_submission'])){

    $assignment_id = $_POST['delete_submission'];

    $old = $conn->query("
        SELECT File_Name FROM submission 
        WHERE Std_ID='$id' AND Assignment_ID='$assignment_id'
    ")->fetch_assoc();

    if($old && file_exists("uploads/".$old['File_Name'])){
        unlink("uploads/".$old['File_Name']);
    }

    $conn->query("
        DELETE FROM submission 
        WHERE Std_ID='$id' AND Assignment_ID='$assignment_id'
    ");

    $success = "🗑 Submission deleted successfully.";
}

/* SUBMIT / RE-SUBMIT */
if(isset($_POST['submit_assignment'])){

    $assignment_id = $_POST['assignment_id'];

    $file = $_FILES['file']['name'];
    $temp = $_FILES['file']['tmp_name'];

    $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    $allowed = ['pdf', 'doc', 'docx'];

    if(!in_array($file_extension, $allowed)){
        $error = "❌ Only PDF and Word files are allowed.";
    }
    else{

        $folder = "uploads/".$file;
        move_uploaded_file($temp, $folder);

        $due_query = "SELECT Due_Date FROM assignment WHERE Assignment_ID='$assignment_id'";
        $due_result = $conn->query($due_query);
        $due_row = $due_result->fetch_assoc();

        $today = date("Y-m-d");

        $status = ($today > $due_row['Due_Date']) ? "Late" : "Submitted";

        $check_query = "
        SELECT * FROM submission
        WHERE Std_ID='$id'
        AND Assignment_ID='$assignment_id'
        ";

        $check_result = $conn->query($check_query);

        if($check_result->num_rows > 0){

            $conn->query("
                UPDATE submission SET
                File_Name='$file',
                Submitted_Date='$today',
                Status='$status'
                WHERE Std_ID='$id' AND Assignment_ID='$assignment_id'
            ");

            $success = "✅ Assignment re-submitted successfully!";
        }
        else{

            $conn->query("
                INSERT INTO submission
                (Std_ID, Assignment_ID, File_Name, Submitted_Date, Status)
                VALUES
                ('$id','$assignment_id','$file','$today','$status')
            ");

            $success = "✅ Assignment uploaded successfully!";
        }
    }
}

/*COUNTS*/

$total_query = "
SELECT COUNT(*) as total
FROM assignment a
JOIN enrollment e ON a.Course_ID = e.Course_ID
WHERE e.Std_ID='$id'
";
$total = $conn->query($total_query)->fetch_assoc()['total'];

$submitted_query = "
SELECT COUNT(DISTINCT s.Assignment_ID) as submitted
FROM submission s
WHERE s.Std_ID='$id'
AND s.Status IN ('Submitted','Late')
";
$submitted = $conn->query($submitted_query)->fetch_assoc()['submitted'];

$pending_query = "
SELECT COUNT(*) as pending
FROM assignment a
JOIN enrollment e ON a.Course_ID = e.Course_ID
LEFT JOIN submission s 
ON a.Assignment_ID = s.Assignment_ID 
AND s.Std_ID='$id'
WHERE e.Std_ID='$id'
AND (s.Status IS NULL OR s.Status = 'Pending')
";
$pending = $conn->query($pending_query)->fetch_assoc()['pending'];

/* ASSIGNMENTS LIST*/
$query = "
SELECT
    a.Assignment_ID,
    a.Assignment_Title,
    a.Description,
    a.Due_Date,
    c.Course_Name,
    s.File_Name,
    s.Submitted_Date,
    s.Status
FROM enrollment e
JOIN assignment a ON e.Course_ID = a.Course_ID
JOIN course c ON a.Course_ID = c.Course_ID
LEFT JOIN submission s 
ON a.Assignment_ID = s.Assignment_ID
AND s.Std_ID = '$id'
WHERE e.Std_ID = '$id'
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Submissions</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">

<style>
body{
    background:#f8fafc;
}

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

.main-content{
    margin-left:240px; !important
    padding:25px;
    padding: 30px;
    background: #f8fafc;
    min-height: 100vh;
    width: calc(100% - 240px);
    box-sizing: border-box;


}

.summary-card{
    border-radius:14px;
    padding:20px;
    color:white;
    text-align:center;
    margin-bottom:20px;
}

.submission-card{
    background:white;
    border-radius:14px;
    padding:20px;
    margin-bottom:20px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}
.bg1{
    background:#3b82f6;
}

.bg2{
    background:#22c55e;
}
.bg3{
    background:#f59e0b;
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

<div class="main-content">

<h2>📂 My Submissions</h2>

<input class="form-control mb-3" id="searchBox" placeholder="Search assignment...">

<div class="mb-3">
<button class="btn btn-primary filter-btn" data-filter="all">All</button>
<button class="btn btn-success filter-btn" data-filter="Submitted">Submitted</button>
<button class="btn btn-warning filter-btn" data-filter="Pending">Pending</button>
<button class="btn btn-danger filter-btn" data-filter="Late">Late</button>
</div>

<?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
<?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<div class="row">
<div class="col-md-4"><div class="summary-card bg1">
    <h5>Total</h5><h2><?php echo $total; ?></h2>
</div></div>

<div class="col-md-4"><div class="summary-card bg2">
    <h5>Submitted</h5><h2><?php echo $submitted; ?></h2>
</div></div>

<div class="col-md-4"><div class="summary-card bg3">
    <h5>Pending</h5><h2><?php echo $pending; ?></h2>
</div></div>
</div>

<?php while($row = $result->fetch_assoc()){ 
    $status = $row['Status'] ?? 'Pending';
?>

<div class="submission-card assignment-item"
data-title="<?php echo strtolower($row['Assignment_Title']); ?>"
data-status="<?php echo $status; ?>">

    <h4><?php echo $row['Assignment_Title']; ?></h4>
    <p><b><?php echo $row['Course_Name']; ?></b></p>
    <p><?php echo $row['Description']; ?></p>
    <p><b>Due Date:</b> <?php echo $row['Due_Date']; ?></p>

    <p><b>Status:</b>
    <?php
    if($status == 'Submitted'){
        echo "<span class='badge bg-success'>Submitted</span>";
    }
    elseif($status == 'Late'){
        echo "<span class='badge bg-danger'>Late</span>";
    }
    else{
        echo "<span class='badge bg-warning text-dark'>Pending</span>";
    }
    ?>
    </p>

    <div class="mt-3">
    <?php if(!empty($row['File_Name'])){ ?>
        
        <a href="uploads/<?php echo $row['File_Name']; ?>"
           target="_blank"
           class="btn btn-info btn-sm me-2">
           👁 View File
        </a>

        <form method="POST" style="display:inline;">
            <button class="btn btn-danger btn-sm"
                    name="delete_submission"
                    value="<?php echo $row['Assignment_ID']; ?>"
                    onclick="return confirm('Are you sure you want to delete this submission?')">
                🗑 Delete Submission
            </button>
        </form>

    <?php } else { ?>

        <form method="POST" enctype="multipart/form-data" class="row g-2 align-items-center">
            <input type="hidden" name="assignment_id" value="<?php echo $row['Assignment_ID']; ?>">
            <div class="col-auto">
                <input type="file" name="file" class="form-control form-control-sm" required>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-sm" name="submit_assignment">
                    Submit Assignment
                </button>
            </div>
        </form>

    <?php } ?>
    </div>

</div>

<?php } ?>

</div>

<script>
let searchBox = document.getElementById("searchBox");
let items = document.querySelectorAll(".assignment-item");

searchBox.addEventListener("keyup", function(){
let val = this.value.toLowerCase();

items.forEach(i=>{
let t = i.getAttribute("data-title");
i.style.display = t.includes(val) ? "" : "none";
});
});

document.querySelectorAll(".filter-btn").forEach(btn=>{
btn.addEventListener("click", function(){
let f = this.getAttribute("data-filter");

items.forEach(i=>{
let s = i.getAttribute("data-status");

i.style.display =
(f=="all" || s==f) ? "" : "none";
});
});
});
</script>

</body>
</html>