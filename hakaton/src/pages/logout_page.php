<?php
session_start();
session_destroy();
echo "<script>alert('Вы вышли из аккаунта'); window.location='/';</script>";
?>