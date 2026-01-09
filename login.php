<?php
session_start();
require 'config.php';
if (isset($_POST['login'])) {
    $okul_no = mysqli_real_escape_string($conn, $_POST['okul_no']);
    $sifre = $_POST['sifre'];
    $res = mysqli_query($conn, "SELECT * FROM users WHERE okul_no = '$okul_no'");
    $user = mysqli_fetch_assoc($res);
    if ($user && password_verify($sifre, $user['sifre'])) {
        $_SESSION['okul_no'] = $user['okul_no'];
        $_SESSION['ad_soyad'] = $user['ad_soyad'];
        $_SESSION['rol'] = $user['rol'];
        header("Location: dashboard.php"); exit;
    } else { $hata = "Hatalı bilgi!"; }
}
?>
<!DOCTYPE html>
<html>
<head><title>Giriş Yap</title><link rel="stylesheet" href="folder.css/dashboard.css"></head>
<body>
    <div class="container" style="max-width:400px; margin-top:50px;">
        <form method="POST" class="request-box">
            <h2>KYKARGO Giriş</h2>
            <?php if(isset($hata)) echo "<p style='color:red'>$hata</p>"; ?>
            <input type="text" name="okul_no" placeholder="Okul No" required style="width:100%; margin-bottom:10px; padding:10px;">
            <input type="password" name="sifre" placeholder="Şifre" required style="width:100%; margin-bottom:10px; padding:10px;">
            <button type="submit" name="login" class="btn-primary" style="width:100%">Giriş</button>
            <div style="margin-top:10px; display:flex; justify-content: space-between; font-size:12px;">
                <a href="register.php">Kayıt Ol</a>
                <a href="sifre_sifirla.php" style="color:red;">Şifremi Unuttum</a>
            </div>
        </form>
    </div>
</body>
</html>