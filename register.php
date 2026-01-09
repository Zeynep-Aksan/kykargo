<?php
require 'config.php';

$hata = ""; 

if (isset($_POST['register'])) {
    $ad_soyad = mysqli_real_escape_string($conn, $_POST['ad_soyad']);
    $okul_no = mysqli_real_escape_string($conn, $_POST['okul_no']);
    $sifre_ham = $_POST['sifre'];
    

    $kontrol = mysqli_query($conn, "SELECT id FROM users WHERE okul_no = '$okul_no'");
    
    if (mysqli_num_rows($kontrol) > 0) {
  
        $hata = "Bu okul numarası ile daha önce kayıt olunmuş!";
    } else {
 
        $sifre_hash = password_hash($sifre_ham, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (ad_soyad, okul_no, sifre) VALUES ('$ad_soyad', '$okul_no', '$sifre_hash')";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: login.php?kayit=basarili");
            exit;
        } else {
            $hata = "Sistemsel bir hata oluştu, lütfen tekrar deneyin.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Kayıt Ol</title><link rel="stylesheet" href="folder.css/dashboard.css"></head>
<body>
    <div class="container" style="max-width:400px; margin-top:50px;">
        <form method="POST" class="request-box">
            <h2 style="text-align:center">KYKARGO Kayıt</h2>
            
            <?php if($hata != ""): ?>
                <div style="background: #ffcccc; color: #cc0000; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-size: 14px; text-align: center;">
                    ⚠️ <?php echo $hata; ?>
                </div>
            <?php endif; ?>

            <input type="text" name="ad_soyad" placeholder="Ad Soyad" required style="width:100%; margin-bottom:10px; padding:10px;">
            <input type="text" name="okul_no" placeholder="Okul No" required style="width:100%; margin-bottom:10px; padding:10px;">
            <input type="password" name="sifre" placeholder="Şifre" required style="width:100%; margin-bottom:10px; padding:10px;">
            <button type="submit" name="register" class="btn-primary" style="width:100%">Kayıt Ol</button>
            <p style="text-align:center;"><a href="login.php">Zaten hesabım var, Giriş Yap</a></p>
        </form>
    </div>
</body>
</html>