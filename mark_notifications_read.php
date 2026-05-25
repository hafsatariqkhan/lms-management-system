<?php
session_start();
include 'db_connect.php';

if(isset($_SESSION['user_id'])){
    $id = $_SESSION['user_id'];
    $conn->query("UPDATE notifications SET Status='read' WHERE Std_ID='$id'");
    echo "success";
}
?>
