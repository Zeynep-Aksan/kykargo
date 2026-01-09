<?php
session_start();
require 'config.php';

if (!isset($_SESSION['okul_no'])) {
    header("Location: login.php");
    exit;
}
$current_user = $_SESSION['okul_no'];
$hata_mesaji = ""; 

if (isset($_POST['istek_olustur'])) {
    $kargo_kodu = mysqli_real_escape_string($conn, $_POST['kargo_kodu']);
    
    $check = mysqli_query($conn, "SELECT id FROM kargolar WHERE kargo_kodu = '$kargo_kodu' AND durum = 'beklemede'");
    
    if(mysqli_num_rows($check) > 0) {
        $hata_mesaji = "Bu kargo koduyla zaten yayında olan bir ilanınız bulunuyor!";
    } else {
        $sql = "INSERT INTO kargolar (sahip_okul_no, kargo_kodu, durum) VALUES ('$current_user', '$kargo_kodu', 'beklemede')";
        mysqli_query($conn, $sql);
        header("Location: dashboard.php?view=ilanlarim");
        exit;
    }
}

if (isset($_POST['kabul_et'])) {
    $id = $_POST['kargo_id'];
    $sql = "UPDATE kargolar SET alici_okul_no='$current_user', durum='kabul_edildi' WHERE id=$id";
    if(mysqli_query($conn, $sql)) {
        header("Location: mesajlar.php?kargo_id=" . $id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>KYKARGO | Canlı Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="folder.css/dashboard.css">
    <style>
        .status-icon { font-size: 18px; display: block; margin-top: 5px; }
        .pending { color: #f39c12; animation: blink 1.5s infinite; }
        .accepted { color: #27ae60; font-weight: bold; }
        @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.4; } 100% { opacity: 1; } }
        .section-title { background: #ff6a00; color: white; padding: 10px 20px; border-radius: 10px; display: inline-block; margin-bottom: 20px; }
        .btn-chat { background: #3498db; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 13px; margin-right: 5px; }
        .error-msg { background: #ff7675; color: white; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-weight: bold; }
        

        .admin-announcement { background: #fff3cd; color: #856404; padding: 15px; border-radius: 12px; border-left: 5px solid #ffc107; margin-bottom: 25px; display: flex; align-items: center; }
        .nav-hero { border-color: #ff6a00 !important; color: #ff6a00 !important; font-weight: bold; }
        .nav-admin { border-color: #e74c3c !important; color: #e74c3c !important; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <div class="logo">KYKARGO</div>
        <div class="nav-links">
            <a href="profil.php" class="logout-btn nav-hero">🏆 Kahramanlar</a>
            <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] == 1): ?>
                <a href="admin.php" class="logout-btn nav-admin">🛡️ Admin</a>
            <?php endif; ?>
            <a href="dashboard.php" class="logout-btn" style="border-color:#27ae60; color:#27ae60;">Ana Sayfa</a>
            <a href="kargolarim.php" class="logout-btn" style="border-color:#3498db; color:#3498db;">Kargo Takip</a>
            <a href="logout.php" class="logout-btn">Çıkış Yap</a>
        </div>
    </header>

    <?php
    $duyuru_res = mysqli_query($conn, "SELECT icerik FROM duyurular ORDER BY id DESC LIMIT 1");
    if($duyuru_res && mysqli_num_rows($duyuru_res) > 0):
        $duyuru = mysqli_fetch_assoc($duyuru_res);
    ?>
    <div class="admin-announcement">
        <span style="font-size: 20px; margin-right: 15px;">📢</span>
        <span><strong>Duyuru:</strong> <?php echo htmlspecialchars($duyuru['icerik']); ?></span>
    </div>
    <?php endif; ?>

    <section class="request-box">
        <h3>🚀 Yeni Kargo İlanı Ver</h3>
        <?php if($hata_mesaji != ""): ?>
            <div class="error-msg"><?php echo $hata_mesaji; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="kargo_kodu" placeholder="Kargo kodunu buraya yaz..." required>
            <button type="submit" name="istek_olustur" class="btn-primary">Hemen Yayınla</button>
        </form>
    </section>

    <section id="ilanlarim">
        <h3 class="section-title">📦 Yayınladığım Kargolar</h3>
        <table>
            <thead>
                <tr>
                    <th>Kargo Kodum</th>
                    <th>Durum</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $my_sql = "SELECT * FROM kargolar WHERE sahip_okul_no = '$current_user' ORDER BY id DESC";
                $my_res = mysqli_query($conn, $my_sql);
                while($row = mysqli_fetch_assoc($my_res)): ?>
                <tr>
                    <td><strong><?php echo $row['kargo_kodu']; ?></strong></td>
                    <td>
                        <?php 
                        if($row['durum'] == 'beklemede') echo "<span class='pending'>⏳ Beklemede</span>";
                        elseif($row['durum'] == 'teslim_edildi') echo "<span style='color:#2ecc71'>🎉 Teslim Edildi</span>";
                        else echo "<span class='accepted'>✅ Kabul Edildi</span>";
                        ?>
                    </td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <?php if($row['durum'] != 'beklemede'): ?>
                                <a href="mesajlar.php?kargo_id=<?php echo $row['id']; ?>" class="btn-chat">💬 Mesaj</a>
                            <?php endif; ?>

                            <?php if($row['sahip_okul_no'] == $current_user && $row['durum'] == 'kabul_edildi'): ?>
                                <a href="islem_yap.php?teslim_al=<?php echo $row['id']; ?>" class="btn-chat" style="background:#27ae60;">✅ Teslim Aldım</a>
                            <?php endif; ?>

                            <?php if($row['durum'] == 'beklemede'): ?>
                                <small style="color:#ccc;">Henüz alıcı yok</small>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>

</body>
</html>