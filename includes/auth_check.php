<?php

session_start();

if (!isset($_SESSION['user_id'])){
    echo "<script>
            alert('Akses ditolak! Silakan login terlebih dahulu.');
            window.location.href = '../auth/login.php';
          </script>";
          exit();
}