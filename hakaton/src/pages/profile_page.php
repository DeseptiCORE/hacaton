<?php
session_start();
use config\Database;
$db = Database::getConnection();

if(!isset($_SESSION['register_id'])){
    echo "<script>alert('Авторизуйтесь'); window.location='/login';</script>";
    exit();
}

$register_id = $_SESSION['register_id'];
$role = $_SESSION['register_role'];

if($role == '1'){
    require_once 'profile_user.php';
} elseif($role == '2'){
    require_once 'profile_promoter.php';
} elseif($role == '3'){
    require_once 'profile_coordinator.php';
}
?>