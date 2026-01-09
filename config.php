<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "site";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Veritabanı bağlantısı hatası: " . mysqli_connect_error());
}
?>
