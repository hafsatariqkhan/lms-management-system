<?php
session_start();
include 'db_connect.php';
$id = $_SESSION['user_id'];

$query = "
SELECT 
    a.Assignment_ID,
    a.Assignment_Title,
    a.Description,
    a.Due_Date,
    c.Course_Name,
    s.Status
FROM enrollment e

JOIN assignment a 
ON e.Course_ID = a.Course_ID

JOIN course c
ON a.Course_ID = c.Course_ID

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
    <title>Assignments</title>

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


.main-content {
    margin-left: 240px; 
    padding: 25px;
    background: #f8fafc;
    min-height: 100vh;
    width: calc(100% - 240px);
}


.main-content {
    margin-left: 240px; 
    padding: 25px;
    background: #f8fafc;
    min-height: 100vh;
    width: calc(100% - 240px);
}
       
        .main-content{
            margin-left:240px;
            padding:25px;
        }

        .assignment-card{
            background:white;
            border-radius:14px;
            padding:20px;
            margin-bottom:20px;
            box-shadow:0 2px 10px rgba(0,0,0,0.08);
        }

        .badge-status{
            padding:8px 12px;
            border-radius:8px;
            font-size:13px;
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
  
    <h2 class="mb-4">📝 My Assignments</h2>

    <?php if($result->num_rows > 0){ ?>

        <?php while($row = $result->fetch_assoc()){ ?>

            <div class="assignment-card">

                <h4>
                    <?php echo $row['Assignment_Title']; ?>
                </h4>

                <p class="text-muted mb-2">
                    📚 Course:
                    <b><?php echo $row['Course_Name']; ?></b>
                </p>

                <p>
                    📅 Due Date:
                    <b><?php echo $row['Due_Date']; ?></b>
                </p>
                <p>
                    <?php echo $row['Description']; ?>
                </p>
                <p>
                    Status:

                    <?php
                    if($row['Status'] == 'Submitted'){
                        echo "<span class='badge bg-success'>Submitted</span>";
                    }
                    elseif($row['Status'] == 'Late'){
                        echo "<span class='badge bg-danger'>Late</span>";
                    }
                    else{
                        echo "<span class='badge bg-warning text-dark'>Pending</span>";
                    }
                    ?>
                </p>

                <a href="mysubmissions.php"
                  class="btn btn-primary">
                  Submit Assignment
</a>
            </div>

        <?php } ?>

    <?php } else { ?>

        <div class="alert alert-warning">
            ❌ No assignments available
        </div>

    <?php } ?>

</div>

</body>
</html>