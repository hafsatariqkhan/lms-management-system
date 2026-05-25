<?php
session_start();
include 'db_connect.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}


if(!isset($_GET['Instructor_ID'])){
    die("Instructor ID missing");
}

$instructor_id = $_GET['Instructor_ID'];



$result = $conn->query("SELECT * FROM instructor WHERE Instructor_ID='$instructor_id'");
$data = $result->fetch_assoc();

if(!$data){
    die("Instructor not found");
}



$success = "";
$error = "";

if(isset($_POST['update_instructor'])){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $dept = $_POST['dept'];
    $designation = $_POST['designation'];
    $status = $_POST['status'];
    $address = $_POST['address'];

    $cnic = $_POST['cnic'];
    $gender = $_POST['gender'];
    $joining = $_POST['joining'];
    $leaving = $_POST['leaving'];

    
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "❌ Invalid email format!";
    }
    else {

        $update = $conn->query("UPDATE instructor SET 
            Name='$name',
            Email='$email',
            Phone='$phone',
            Department='$dept',
            Designation='$designation',
            Status='$status',
            Address='$address',
            CNIC='$cnic',
            Gender='$gender',
            Joining_Date='$joining',
            Leaving_Date='$leaving'
            WHERE Instructor_ID='$instructor_id'
        ");

        if($update){
            $success = "✅ Instructor updated successfully!";
            header("Refresh:1");
        } else {
            $error = "❌ Update failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Instructor Profile (Admin)</title>

<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/responsive.css" rel="stylesheet">

<style>

body{
    background:#f1f5f9;
    font-family:Segoe UI;
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


.page{
    margin-left:240px;
    padding:25px;
}


.card-box{
    background:white;
    padding:25px;
    border-radius:18px;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
    margin-bottom:20px;
}


.header{
    display:flex;
    gap:20px;
    align-items:center;
}

.avatar{
    width:110px;
    height:110px;
    border-radius:50%;
    background:#0f172a;
    color:white;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:38px;
    font-weight:bold;
}

.img{
    width:110px;
    height:110px;
    border-radius:50%;
    object-fit:cover;
}

.section-title{
    font-weight:700;
    margin-top:20px;
    border-bottom:1px solid #ddd;
    padding-bottom:6px;
}

.label{
    font-size:13px;
    color:#64748b;
}

.value{
    font-weight:600;
    color:#0f172a;
}

</style>

</head>

<body>

<div class="d-flex">

    <!-- SIDEBAR -->
    <nav class="admin-sidebar p-3">
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

        <a class="nav-link text-warning mb-3" href="logout.php">
            🚪 Logout
        </a>
    </nav>


    
    <div class="page">

       
        <?php if($success){ ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>

        <?php if($error){ ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

       
        <div class="card-box">
            <div class="header">

                <?php if(!empty($data['Profile_Pic'])) { ?>
                    <img src="uploads/<?php echo $data['Profile_Pic']; ?>" class="img">
                <?php } else { ?>
                    <div class="avatar">
                        <?php echo strtoupper($data['Name'][0]); ?>
                    </div>
                <?php } ?>

                <div>
                    <h3><?php echo $data['Name']; ?></h3>
                    <p><?php echo $data['Designation']; ?></p>

                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
                        ✏️ Edit Profile
                    </button>
                </div>

            </div>
        </div>

        
        <div class="card-box">
            <h5 class="section-title">Complete Instructor Details</h5>

            <p><b>Instructor ID:</b> <?php echo $data['Instructor_ID']; ?></p>
            <p><b>Name:</b> <?php echo $data['Name']; ?></p>
            <p><b>Email:</b> <?php echo $data['Email']; ?></p>
            <p><b>Phone:</b> <?php echo $data['Phone']; ?></p>
            <p><b>CNIC:</b> <?php echo $data['CNIC']; ?></p>
            <p><b>Gender:</b> <?php echo $data['Gender']; ?></p>
            <p><b>Status:</b> <?php echo $data['Status']; ?></p>
            <p><b>Department:</b> <?php echo $data['Department']; ?></p>
            <p><b>Designation:</b> <?php echo $data['Designation']; ?></p>
            <p><b>Joining Date:</b> <?php echo $data['Joining_Date']; ?></p>
            <p><b>Leaving Date:</b> <?php echo $data['Leaving_Date']; ?></p>
            <p><b>Address:</b> <?php echo $data['Address']; ?></p>
        </div>

    </div>

</div>

</body>


<div class="modal fade" id="editModal">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form method="POST">

<div class="modal-header bg-dark text-white">
<h5>Edit Instructor</h5>
</div>

<div class="modal-body">

<div class="row">

<div class="col-md-6">
<label>Name</label>
<input type="text" name="name" value="<?php echo $data['Name']; ?>" class="form-control">
</div>

<div class="col-md-6">
<label>Email</label>
<input type="email" name="email" value="<?php echo $data['Email']; ?>" class="form-control">
</div>

<div class="col-md-6">
<label>Phone</label>
<input type="text" name="phone" value="<?php echo $data['Phone']; ?>" class="form-control">
</div>

<div class="col-md-6">
<label>CNIC</label>
<input type="text" name="cnic" value="<?php echo $data['CNIC']; ?>" class="form-control">
</div>

<div class="col-md-6">
<label>Department</label>
<input type="text" name="dept" value="<?php echo $data['Department']; ?>" class="form-control">
</div>

<div class="col-md-6">
<label>Designation</label>
<input type="text" name="designation" value="<?php echo $data['Designation']; ?>" class="form-control">
</div>

<div class="col-md-6">
<label>Status</label>
<input type="text" name="status" value="<?php echo $data['Status']; ?>" class="form-control">
</div>

<div class="col-md-6">
<label>Gender</label>
<input type="text" name="gender" value="<?php echo $data['Gender']; ?>" class="form-control">
</div>

<div class="col-md-6">
<label>Joining Date</label>
<input type="date" name="joining" value="<?php echo $data['Joining_Date']; ?>" class="form-control">
</div>

<div class="col-md-6">
<label>Leaving Date</label>
<input type="date" name="leaving" value="<?php echo $data['Leaving_Date']; ?>" class="form-control">
</div>

<div class="col-md-12">
<label>Address</label>
<textarea name="address" class="form-control"><?php echo $data['Address']; ?></textarea>
</div>

</div>

</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
<button class="btn btn-success" name="update_instructor">Update</button>
</div>

</form>

</div>
</div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>