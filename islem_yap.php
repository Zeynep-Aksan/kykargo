<?php
session_start();
require 'config.php';

if (isset($_GET['teslim_al'])) {
    $id = intval($_GET['teslim_al']);
    $user = $_SESSION['okul_no'];

    $sql = "UPDATE kargolar SET durum='teslim_edildi' WHERE id=$id AND sahip_okul_no='$user'";
    
    if(mysqli_query($conn, $sql)) {
        header("Location: profil.php?mesaj=basarili");
    } else {
    
        die("MySQL Hatası: " . mysqli_error($conn));
    }
    exit;
}
?>