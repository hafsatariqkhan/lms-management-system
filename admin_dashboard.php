<?php
session_start();
include 'db_connect.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: Login.php");
    exit();
}
// Count Total Students
$std_res = $conn->query("SELECT COUNT(*) as total_students FROM student");
$total_students = $std_res->fetch_assoc()['total_students'];

// Count Total Instructors
$inst_res = $conn->query("SELECT COUNT(*) as total_instructors FROM instructor");
$total_instructors = $inst_res->fetch_assoc()['total_instructors'];

// Count Total Courses
$course_res = $conn->query("SELECT COUNT(*) as total_courses FROM course");
$total_courses = $course_res->fetch_assoc()['total_courses'];


$recent_courses = $conn->query("SELECT * FROM course ORDER BY Course_ID DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | NUML LMS</title>
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
        
        .admin-badge-title {
            font-weight: 700; 
            letter-spacing: 0.5px; 
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 15px;
        }

        /* STATS CARDS */
        .stat-card {
            border-radius: 16px;
            padding: 25px;
            color: white;
            margin-bottom: 25px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
        }

        .bg-gradient-purple { 
            background: linear-gradient(135deg, #7c3aed, #a78bfa); 
        }
        .bg-gradient-blue { 
            background: linear-gradient(135deg, #2563eb, #60a5fa);
        }
        .bg-gradient-orange {
            background: linear-gradient(135deg, #ea8a0c, #fbaf3c);
        }

        .card-box {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            border: none;
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
    </nav> <div class="main-content">
        
        <div class="card-box d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1" style="color: #0f172a; font-weight: 700;">Welcome, Administrator 👋</h3>
                <p class="text-muted mb-0">System Overview and Central Campus Controls.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
               <div class="stat-card bg-gradient-purple">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 text-uppercase small font-weight-bold">Total Students</h6>
                            <h1 class="display-5 font-weight-bold mb-0"><?php echo $total_students; ?></h1>
                        </div>
                        <div style="font-size: 40px; opacity: 0.4;">👨‍🎓</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="stat-card bg-gradient-blue">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 text-uppercase small font-weight-bold">Total Instructors</h6>
                            <h1 class="display-5 font-weight-bold mb-0"><?php echo $total_instructors; ?></h1>
                        </div>
                        <div style="font-size: 40px; opacity: 0.4;">👩‍🏫</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card bg-gradient-orange">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 text-uppercase small font-weight-bold">Total Courses</h6>
                            <h1 class="display-5 font-weight-bold mb-0"><?php echo $total_courses; ?></h1>
                        </div>
                        <div style="font-size: 40px; opacity: 0.4;">📚</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-12">
                <div class="card-box">
                    <h5 class="mb-4" style="color: #0f172a; font-weight: 700;">📌 System Overview (Recent Courses)</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Course ID</th>
                                    <th>Course Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($recent_courses && $recent_courses->num_rows > 0) { ?>
                                    <?php while($c = $recent_courses->fetch_assoc()) { ?>
                                    <tr>
                                        <td><span class="badge bg-secondary p-2" style="font-size: 13px; font-weight: 600;"><?php echo $c['Course_ID']; ?></span></td>
                                        <td><b class="text-dark"><?php echo $c['Course_Name']; ?></b></td>
                                    </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">No courses available in the system.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div> </div> <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>