<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userid = mysqli_real_escape_string($conn, $_POST['userid']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    if ($role == "student") {
        $query = "SELECT * FROM student WHERE Std_ID = '$userid'";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            
            
            if ($password === 'student123') {
                $_SESSION['user_id'] = $user_data['Std_ID'];
                $_SESSION['role'] = 'student';
                header("Location: student_dashboard.php");
                exit();
            } else {
                $_SESSION['login_error'] = "❌ Incorrect Password!";
                header("Location: Login.php");
                exit();
            }
        } else {
            $_SESSION['login_error'] = "❌ Invalid Student ID!";
            header("Location: Login.php");
            exit();
        }

    } elseif ($role == "instructor") {
        $query = "SELECT * FROM instructor WHERE Instructor_ID = '$userid'";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            
           
            if ($password === 'ins123') {
                $_SESSION['instructor_id'] = $user_data['Instructor_ID'];
                $_SESSION['role'] = 'instructor';
                header("Location: instructor_overview.php");
                exit();
            } else {
                $_SESSION['login_error'] = "❌ Incorrect Password!";
                header("Location: Login.php");
                exit();
            }
        } else {
            $_SESSION['login_error'] = "❌ Invalid Instructor ID!";
            header("Location: Login.php");
            exit();
        }

    } elseif ($role == "admin") {
        $query = "SELECT * FROM admin WHERE Admin_ID = '$userid'";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            
            // Admin ka fixed password check
            if ($password === 'admin123') {
                $_SESSION['admin_id'] = $user_data['Admin_ID'];
                $_SESSION['role'] = 'admin';
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $_SESSION['login_error'] = "❌ Incorrect Password!";
                header("Location: Login.php");
                exit();
            }
        } else {
            $_SESSION['login_error'] = "❌ Invalid Admin ID!";
            header("Location: Login.php");
            exit();
        }
    }
}
?>