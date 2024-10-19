<?php
require 'function.php';

if(isset($_SESSION['login'])){
    // Ambil level (role) dari session
    $level = $_SESSION['level'];
} else {
    // Belum login
    header('location:login.php');
    exit;
} 
// Fungsi untuk mengecek hak akses
function cekAkses($level, $requiredLevel) {
    if ($level != $requiredLevel) {
        header("Location: dashboard.php");
        exit;
    }
}
?>