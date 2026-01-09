<?php
session_start();
require 'config.php';


if (!isset($_SESSION['okul_no'])) { 
    header("Location: login.php"); 
    exit; 
}

$current_user = $_SESSION['okul_no'];


if (!isset($_GET['kargo_id'])) { 
    die("Hata: Kargo ID bulunamadı."); 
}
$kargo_id = intval($_GET['kargo_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksiyon'])) {
    $yeni_durum = mysqli_real_escape_string($conn, $_POST['aksiyon']);
    

    $kontrol = mysqli_query($conn, "SELECT alici_okul_no FROM kargolar WHERE id = $kargo_id");
    $kargo_verisi = mysqli_fetch_assoc($kontrol);
    
    if ($kargo_verisi && $kargo_verisi['alici_okul_no'] == $current_user) {
        mysqli_query($conn, "UPDATE kargolar SET durum = '$yeni_durum' WHERE id = $kargo_id");
        header("Location: mesajlar.php?kargo_id=" . $kargo_id . "&islem=tamam");
        exit;
    }
}

$sql = "SELECT k.*, 
        u1.ad_soyad as sahip_ad, 
        u2.ad_soyad as alici_ad 
        FROM kargolar k 
        LEFT JOIN users u1 ON k.sahip_okul_no = u1.okul_no 
        LEFT JOIN users u2 ON k.alici_okul_no = u2.okul_no 
        WHERE k.id = $kargo_id";

$sorgu = mysqli_query($conn, $sql);
$kargo = mysqli_fetch_assoc($sorgu);

if (!$kargo) {
    die("Kargo bulunamadı veya silinmiş.");
}

$is_sahibi = ($kargo['sahip_okul_no'] == $current_user);
$is_yardim_eden = ($kargo['alici_okul_no'] == $current_user);
$durum = $kargo['durum'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>KYKARGO | Mesajlar ve Takip</title>
    <link rel="stylesheet" href="folder.css/dashboard.css">
    <style>
        .chat-box { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-top: 20px; }
        .info-card { background: #f8f9fa; border-left: 5px solid #3498db; padding: 15px; margin-bottom: 20px; }
        .status-msg { padding: 15px; border-radius: 10px; text-align: center; font-weight: bold; margin-bottom: 15px; }
        .status-active { background: #e3f2fd; color: #1976d2; }
        .status-done { background: #e8f5e9; color: #2e7d32; }
        .btn-logic { width: 100%; padding: 12px; border: none; border-radius: 8px; color: white; cursor: pointer; font-weight: bold; margin-top: 10px; }
        .yellow { background: #f1c40f; }
        .green { background: #27ae60; }
        .btn-back { display: inline-block; margin-bottom: 10px; text-decoration: none; color: #666; font-size: 14px; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <div class="logo">KYKARGO</div>
        <a href="dashboard.php" class="logout-btn">Geri Dön</a>
    </header>

    <div class="chat-box">
        <a href="dashboard.php" class="btn-back">← İlanlara Dön</a>
        
        <div class="info-card">
            <p><strong>📦 Kargo Kodu:</strong> <?php echo $kargo['kargo_kodu']; ?></p>
            <p><strong>👤 Sahibi:</strong> <?php echo htmlspecialchars($kargo['sahip_ad']); ?></p>
            <p><strong>🤝 Yardım Eden:</strong> <?php echo htmlspecialchars($kargo['alici_ad'] ?? 'Bilinmiyor'); ?></p>
        </div>

        <div class="status-msg <?php echo ($durum == 'teslim_alindi') ? 'status-done' : 'status-active'; ?>">
            <?php 
                if ($durum == 'kabul_edildi') echo "🤝 Kargo kabul edildi. İletişime geçebilirsiniz.";
                elseif ($durum == 'kargocu_bekleniyor') echo "🕒 Kurye şu an sırada bekliyor...";
                elseif ($durum == 'teslim_alindi') echo "✅ İşlem başarıyla tamamlandı. Puan kuryeye eklendi!";
                else echo "⏳ Durum: " . $durum;
            ?>
        </div>

        <?php if ($is_yardim_eden && $durum != 'teslim_alindi'): ?>
            <div style="border-top: 1px solid #eee; padding-top: 15px;">
                <p style="font-size: 14px; color: #666;">Kargo sürecini güncelleyin:</p>
                <form method="POST">
                    <?php if ($durum == 'kabul_edildi'): ?>
                        <button type="submit" name="aksiyon" value="kargocu_bekleniyor" class="btn-logic yellow">🕒 Sıradayım / Bekliyorum</button>
                    <?php endif; ?>
                    
                    <button type="submit" name="aksiyon" value="teslim_alindi" class="btn-logic green">📦 Teslim Ettim / İşlemi Bitir</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($is_sahibi && $durum != 'teslim_alindi'): ?>
            <p style="text-align: center; color: #e67e22; font-size: 14px;">
                Kurye kargoyu teslim ettiğinde butonuna basacak ve işlem sonlanacaktır.
            </p>
        <?php endif; ?>

        <div style="margin-top: 20px; text-align: center;">
            <p>İletişim için WhatsApp veya yurt içinden ulaşabilirsiniz.</p>
        </div>
    </div>
</div>
</body>
</html>