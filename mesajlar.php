<?php
session_start();
require 'config.php';

if (!isset($_SESSION['okul_no'])) { header("Location: login.php"); exit; }
$current_user = $_SESSION['okul_no'];

if (!isset($_GET['kargo_id'])) { die("Hata: Kargo ID bulunamadı."); }
$kargo_id = intval($_GET['kargo_id']);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksiyon'])) {
    $yeni_durum = mysqli_real_escape_string($conn, $_POST['aksiyon']);
    
   
    $kontrol = mysqli_query($conn, "SELECT alici_okul_no FROM kargolar WHERE id = $kargo_id");
    $kargo_verisi = mysqli_fetch_assoc($kontrol);
    
    if ($kargo_verisi['alici_okul_no'] == $current_user) {
        mysqli_query($conn, "UPDATE kargolar SET durum = '$yeni_durum' WHERE id = $kargo_id");
        header("Location: mesajlar.php?kargo_id=" . $kargo_id);
        exit;
    }
}

$sql = "SELECT k.*, u1.ad_soyad as sahip_ad, u2.ad_soyad as alici_ad 
        FROM kargolar k 
        LEFT JOIN users u1 ON k.sahip_okul_no = u1.okul_no 
        LEFT JOIN users u2 ON k.alici_okul_no = u2.okul_no 
        WHERE k.id = $kargo_id";
$res = mysqli_query($conn, $sql);
$kargo = mysqli_fetch_assoc($res);

if (!$kargo) { die("Kargo bulunamadı."); }

$is_sahibi = ($current_user == $kargo['sahip_okul_no']);      // İlanı veren kişi
$is_yardim_eden = ($current_user == $kargo['alici_okul_no']); // Kargoyu getiren kişi
$durum = $kargo['durum'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Mesajlar ve Takip</title>
    <link rel="stylesheet" href="folder.css/dashboard.css">
    <style>
        .tracker-box { background: white; padding: 25px; border-radius: 20px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-top: 20px; }
        .status-badge { display: block; font-size: 1.2em; font-weight: bold; margin-bottom: 20px; padding: 10px; border-radius: 10px; }
        .role-indicator { font-size: 0.9em; color: #666; margin-bottom: 10px; }
        .btn-logic { border: none; padding: 12px 20px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.3s; margin: 5px; color: white; }
        .yellow { background: #f1c40f; color: #000; }
        .green { background: #27ae60; }
        .info-card { background: #f9f9f9; padding: 15px; border-radius: 10px; margin: 15px 0; border: 1px dashed #ddd; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <div class="logo">KYKARGO</div>
        <a href="kargolarim.php" class="logout-btn">Geri Dön</a>
    </header>

    <div class="tracker-box">
        <div class="role-indicator">
            <?php if($is_sahibi) echo "👤 <b>Kargo Sahibi</b> olarak görüntülüyorsunuz."; ?>
            <?php if($is_yardim_eden) echo "🚚 <b>Yardım Eden (Kargocu)</b> olarak görüntülüyorsunuz."; ?>
        </div>

        <div class="info-card">
            <strong>Kargo Kodu:</strong> <span style="font-size: 1.2em; color: #e67e22;"><?php echo $kargo['kargo_kodu']; ?></span><br>
            <strong>İlan Sahibi:</strong> <?php echo $kargo['sahip_ad']; ?><br>
            <strong>Yardım Eden:</strong> <?php echo $kargo['alici_ad'] ? $kargo['alici_ad'] : "<i>Henüz atanmadı</i>"; ?>
        </div>

        <div class="status-badge" style="background: #fdf2e9; color: #d35400;">
            <?php 
                if ($durum == 'kabul_edildi') {
                    echo "🤝 İşlem Başlatıldı. İletişime geçebilirsiniz.";
                } elseif ($durum == 'kargocu_bekleniyor') {
                    echo "🕒 Kargocu şu an sırada bekliyor...";
                } elseif ($durum == 'teslim_alindi') {
                    // İŞTE İSTEDİĞİN ÖZEL MESAJLAR BURASI:
                    if ($is_yardim_eden) {
                        echo "✅ İşlem tamamlandı, yardımınız için teşekkürler!";
                    } elseif ($is_sahibi) {
                        echo "✅ Kargonuz başarıyla teslim alındı!";
                    } else {
                        echo "✅ Bu işlem başarıyla tamamlandı.";
                    }
                }
            ?>
        </div>

        <?php if ($is_yardim_eden && $durum != 'teslim_alindi'): ?>
            <form method="POST">
                <p><small>Lütfen kargo sürecini buradan güncelleyin:</small></p>
                <?php if ($durum == 'kabul_edildi'): ?>
                    <button type="submit" name="aksiyon" value="kargocu_bekleniyor" class="btn-logic yellow">🕒 Sıradayım / Bekliyorum</button>
                <?php endif; ?>
                
                <button type="submit" name="aksiyon" value="teslim_alindi" class="btn-logic green">📦 Teslim Ettim / Aldım</button>
            </form>
        <?php endif; ?>

        <?php if ($is_sahibi && $durum != 'teslim_alindi'): ?>
            <p style="color: #666;"><small>Yardım eden kişi kargonuzu aldığında durum otomatik güncellenecektir.</small></p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>