<?php
session_start();
require 'config.php';
$hata = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $okul_no = mysqli_real_escape_string($conn, trim($_POST['okul_no']));

    if (!empty($okul_no)) {
        $sql = "SELECT * FROM users WHERE okul_no='$okul_no'";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            $_SESSION['okul_no'] = $row['okul_no'];
            $_SESSION['ad_soyad'] = $row['ad_soyad'];
            header("Location: dashboard.php");
            exit;
        } else {
            $hata = "Bu okul numarasıyla kayıtlı kullanıcı bulunamadı!";
        }
    } else {
        $hata = "Lütfen okul numaranızı girin!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>KYKARGO - Giriş</title>
    <link rel="stylesheet" href="folder.css/login.css">
</head>
<body>
    <div class="login-box">
        <h2>Hızlı Giriş</h2>
        <?php if($hata != ""): echo "<div class='error-msg'>$hata</div>"; endif; ?>
        <form method="POST">
            <input type="text" name="okul_no" placeholder="Okul Numaranız" required>
            <button type="submit" class="btn-submit">Giriş Yap</button>
        </form>
        <div class="footer-text">Henüz kayıt olmadın mı? <a href="register.php">Buradan Kayıt Ol</a></div>
    </div>
</body>
</html>