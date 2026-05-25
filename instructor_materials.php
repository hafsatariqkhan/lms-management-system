<?php
session_start();
include 'db_connect.php';


if(!isset($_SESSION['instructor_id'])){
    die("Invalid Instructor ID");
}

$instructor_id = $_SESSION['instructor_id'];


if(isset($_POST['upload_material'])){

    
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $course_id   = mysqli_real_escape_string($conn, $_POST['course_id']);
    $file_name   = "";

    
    if(!empty($_FILES['material_file']['name'])){
        $file_name = time() . "_" . $_FILES['material_file']['name'];
        $temp_name = $_FILES['material_file']['tmp_name'];
        
        if (!is_dir('uploads/materials')) {
            mkdir('uploads/materials', 0777, true);
        }
        
        move_uploaded_file($temp_name, "uploads/materials/".$file_name);
    }

    
    $insert = "INSERT INTO module (Name, Description, File_Name, Course_ID) 
               VALUES ('$title', '$description', '$file_name', '$course_id')";

    if($conn->query($insert)){
        $success = "✅ Material Uploaded Successfully";

        
        $course_info = $conn->query("SELECT Course_Name FROM course WHERE Course_ID = '$course_id'")->fetch_assoc();
        $course_name = $course_info['Course_Name'];
        
       
        $enrolled_students = $conn->query("SELECT Std_ID FROM enrollment WHERE Course_ID = '$course_id'");
        
        if($enrolled_students->num_rows > 0){
   
            $msg = "📁 New Material Uploaded: " . $title . " has been added in your course " . $course_name . ".";
            
            
            $safe_msg = mysqli_real_escape_string($conn, $msg);
            
            
            while($student = $enrolled_students->fetch_assoc()){
                $s_id = $student['Std_ID'];
                $conn->query("INSERT INTO notifications (Std_ID, Message, Status) VALUES ('$s_id', '$safe_msg', 'unread')");
            }
        }
        

    }
    else{
        $error = "❌ Failed To Upload Material";
    }
}


if(isset($_POST['delete_material'])){
    $module_id = mysqli_real_escape_string($conn, $_POST['module_id']);
    $file_to_delete = mysqli_real_escape_string($conn, $_POST['delete_file']);
    
    if(file_exists("uploads/materials/" . $file_to_delete)){
        unlink("uploads/materials/" . $file_to_delete);
    }
    
    $delete_query = "DELETE FROM module WHERE Module_ID = '$module_id'";
    if($conn->query($delete_query)){
        $success = "🗑️ Material Deleted Successfully";
    }
}


$courses = $conn->query("
    SELECT c.Course_ID, c.Course_Name 
    FROM course c 
    JOIN teaches t ON c.Course_ID = t.Course_ID 
    WHERE t.Instructor_ID='$instructor_id'
");


$materials = $conn->query("
    SELECT m.Module_ID, m.Name, m.Description, m.File_Name, c.Course_ID, c.Course_Name 
    FROM module m 
    JOIN course c ON m.Course_ID = c.Course_ID 
    JOIN teaches t ON c.Course_ID = t.Course_ID 
    WHERE t.Instructor_ID='$instructor_id' 
    ORDER BY m.Module_ID DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Instructor Materials</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
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

   
    .main-content { 
        margin-left: 240px; 
        padding: 25px; 
        background: #f8fafc; 
        min-height: 100vh; 
        width: calc(100% - 240px);
        box-sizing: border-box;
    }
    

        /* CARDS */
        .card-box {
            background: white;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .material-card {
            border-left: 5px solid #198754;
            background: white;
        }
        .instructor-badge-title {
        font-weight: 700; 
        letter-spacing: 0.5px; 
        color: white;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 15px;
    }
    </style>
</head>
<body>

<div class="d-flex">
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

    <div class="card-box">
        <h3>📂 Course Materials</h3>
        <p class="text-muted mb-0">Upload and manage course materials, notes, and handouts.</p>
    </div>

    <?php
    if(isset($success)) echo "<div class='alert alert-success'>$success</div>";
    if(isset($error)) echo "<div class='alert alert-danger'>$error</div>";
    ?>

    <div class="row">
        <div class="col-md-5">
            <div class="card-box">
                <h5 class="mb-4" style="color: #064e3b; font-weight: 600;">➕ Upload Material</h5>
                <form method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="form-label">Select Course</label>
                        <select name="course_id" class="form-control" required>
                            <option value="">Select Course</option>
                            <?php while($c = $courses->fetch_assoc()){ ?>
                                <option value="<?php echo $c['Course_ID']; ?>">
                                    <?php echo $c['Course_Name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Material Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload File</label>
                        <input type="file" name="material_file" class="form-control" required>
                    </div>

                    <button type="submit" name="upload_material" class="btn text-white w-100" style="background: #064e3b;">
                        Upload Material
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card-box">
                <h5 class="mb-4" style="color: #064e3b; font-weight: 600;">📚 Uploaded Materials</h5>
                
                <?php if($materials->num_rows > 0){ ?>
                    <?php while($m = $materials->fetch_assoc()){ ?>
                        <div class="card material-card mb-3 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div style="max-width: 75%;">
                                        <h5 style="color: #064e3b; font-size: 16px;"><?php echo $m['Name']; ?></h5>
                                        <p class="text-muted mb-2" style="font-size: 14px;"><?php echo $m['Description']; ?></p>
                                        <small class="text-secondary d-block">
                                            📘 Course: <b><?php echo $m['Course_Name']; ?></b> (<?php echo $m['Course_ID']; ?>)
                                        </small>
                                        <small class="text-muted text-break">
                                            📄 File: <?php echo substr($m['File_Name'], 11); ?>
                                        </small>
                                    </div>
                                    
                                    <div class="d-flex flex-column align-items-end">
                                        <a href="uploads/materials/<?php echo $m['File_Name']; ?>" target="_blank" class="btn btn-sm btn-primary mb-2">
                                            Download
                                        </a>
                                        
                                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this material?');">
                                            <input type="hidden" name="module_id" value="<?php echo $m['Module_ID']; ?>">
                                            <input type="hidden" name="delete_file" value="<?php echo $m['File_Name']; ?>">
                                            <button type="submit" name="delete_material" class="btn btn-sm btn-outline-danger">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p class="text-muted text-center py-4 mb-0">No materials uploaded yet.</p>
                <?php } ?>
            </div>
        </div>
    </div>

</div>

</body>
</html>