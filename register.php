<?php
require 'config.php';
$hata = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $okul_no = isset($_POST['okul_no']) ? mysqli_real_escape_string($conn, trim($_POST['okul_no'])) : "";
    $ad_soyad = isset($_POST['ad_soyad']) ? mysqli_real_escape_string($conn, trim($_POST['ad_soyad'])) : "";

    if (!empty($okul_no) && !empty($ad_soyad)) {
        
        $sql_check = "SELECT * FROM users WHERE okul_no='$okul_no'";
        $res_check = mysqli_query($conn, $sql_check);

        if(mysqli_num_rows($res_check) > 0){
            $hata = "Bu okul numarası zaten kayıtlı!";
        } else {
            
            $sql_ins = "INSERT INTO users (okul_no, ad_soyad) VALUES ('$okul_no', '$ad_soyad')";

            if(mysqli_query($conn, $sql_ins)) {
                header("Location: login.php");
                exit;
            } else {
                $hata = "Kayıt hatası: " . mysqli_error($conn);
            }
        }
    } else {
        $hata = "Lütfen tüm alanları doldurun!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>KYKARGO - Kayıt</title>
    <link rel="stylesheet" href="folder.css/login.css">
</head>
<body>
    <div class="login-box">
        <h2>Kayıt Ol</h2>
        <?php if($hata != ""): echo "<div class='error-msg'>$hata</div>"; endif; ?>
        <form method="POST">
            <input type="text" name="okul_no" placeholder="Okul Numaranız" required>
            <input type="text" name="ad_soyad" placeholder="Ad Soyad" required>
            <button type="submit" class="btn-submit">Kaydı Tamamla</button>
        </form>
        <div class="footer-text">Zaten hesabın var mı? <a href="login.php">Giriş Yap</a></div>
    </div>
</body>
</html>