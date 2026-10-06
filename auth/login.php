<?php

session_start();
require_once'../config/database.php';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password_asli = $_POST['password'];
    $role = $_POST['role'];
    
    
    
    
    }