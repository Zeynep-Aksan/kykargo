<?php
require 'config.php';
if (isset($_POST['talep'])) {
    $no = mysqli_real_escape_string($conn, $_POST['okul_no']);
    mysqli_query($conn, "UPDATE users SET sifre_talep = 1 WHERE okul_no = '$no'");
    $m = "Talep iletildi. Admin şifrenizi sıfırladığında 123456 ile girebilirsiniz.";
}
?>
<!DOCTYPE html>
<html>
<head><title>Şifre Sıfırla</title><link rel="stylesheet" href="folder.css/dashboard.css"></head>
<body>
    <div class="container" style="max-width:400px; margin-top:50px;">
        <form method="POST" class="request-box">
            <h3>Şifre Sıfırlama Talebi</h3>
            <?php if(isset($m)) echo "<p style='color:green'>$m</p>"; ?>
            <input type="text" name="okul_no" placeholder="Okul No" required style="width:100%; margin-bottom:10px; padding:10px;">
            <button type="submit" name="talep" class="btn-primary" style="width:100%">Admin'e Bildir</button>
            <p><a href="login.php">Giriş Sayfasına Dön</a></p>
        </form>
    </div>
</body>
</html>