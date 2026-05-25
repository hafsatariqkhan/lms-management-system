<?php
session_start();
include 'db_connect.php';

if(!isset($_SESSION['instructor_id'])){
    header("Location: login.php");
    exit();
}

$id = $_SESSION['instructor_id'];

$query = $conn->query("SELECT * FROM instructor WHERE Instructor_ID='$id'");
$data = $query->fetch_assoc();

if(!$data){
    die("Instructor not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>

<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/responsive.css" rel="stylesheet">

<style>
body{
    background:#f1f5f9;
    font-family:Segoe UI;
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
    .main-content{
    margin-left:240px;
    width: calc(100% - 240px);
    padding:20px;
}

   
.profile-card{
    background:white;
    border-radius:18px;
    padding:30px;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
}

.header{
    display:flex;
    align-items:center;
    gap:20px;
    margin-bottom:25px;
}

.avatar{
    width:120px;
    height:120px;
    border-radius:50%;
    background:#0f172a;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:40px;
    font-weight:bold;
}

.img{
    width:120px;
    height:120px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid #e2e8f0;
}

.section-title{
    font-weight:700;
    margin-top:25px;
    border-bottom:1px solid #e2e8f0;
    padding-bottom:8px;
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
<div class="profile-card">

<!-- HEADER -->
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
        <p class="text-muted mb-0"><?php echo $data['Designation']; ?></p>
        <small>Instructor Dashboard Profile</small>
    </div>

</div>



<!-- BASIC INFO -->
<h5 class="section-title">Basic Information</h5>

<div class="row mt-3">

    <div class="col-md-4 mb-3">
        <div class="label">Instructor ID</div>
        <div class="value"><?php echo $data['Instructor_ID']; ?></div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="label">Email</div>
        <div class="value"><?php echo $data['Email']; ?></div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="label">Phone</div>
        <div class="value"><?php echo $data['Phone']; ?></div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="label">CNIC</div>
        <div class="value"><?php echo $data['CNIC']; ?></div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="label">Gender</div>
        <div class="value"><?php echo $data['Gender']; ?></div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="label">Status</div>
        <div class="value"><?php echo $data['Status']; ?></div>
    </div>

</div>



<!-- ACADEMIC -->
<h5 class="section-title">Academic Information</h5>

<div class="row mt-3">

    <div class="col-md-4 mb-3">
        <div class="label">Department</div>
        <div class="value"><?php echo $data['Department']; ?></div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="label">Designation</div>
        <div class="value"><?php echo $data['Designation']; ?></div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="label">Qualification</div>
        <div class="value"><?php echo $data['Qualification']; ?></div>
    </div>


</div>



<!-- EMPLOYMENT -->
<h5 class="section-title">Employment Details</h5>

<div class="row mt-3">

    <div class="col-md-4 mb-3">
        <div class="label">Joining Date</div>
        <div class="value"><?php echo $data['Joining_Date']; ?></div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="label">Leaving Date</div>
        <div class="value">
            <?php echo !empty($data['Leaving_Date']) ? $data['Leaving_Date'] : "N/A"; ?>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="label">Registration Date</div>
        <div class="value"><?php echo $data['Registration_Date']; ?></div>
    </div>

</div>



<!-- ADDRESS -->
<h5 class="section-title">Address</h5>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="value"><?php echo $data['Address']; ?></div>
    </div>
</div>

</div>

</div>

</body>
</html>