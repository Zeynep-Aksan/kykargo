<?php
session_start();
require 'config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    die("Bu sayfaya erişim yetkiniz yok. Lütfen admin hesabı ile giriş yapın.");
}


$toplam_ogrenci = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE rol = 0"))['count'];
$toplam_kargo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM kargolar"))['count'];
$bekleyen_talepler = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE sifre_talep = 1"))['count'];


if (isset($_GET['sifre_reset_id'])) {
    $uid = intval($_GET['sifre_reset_id']);
    $yeni_sifre = password_hash("123456", PASSWORD_DEFAULT);
    $sql = "UPDATE users SET sifre='$yeni_sifre', sifre_talep=0 WHERE id=$uid";
    if(mysqli_query($conn, $sql)) {
        header("Location: admin.php?durum=sifre_ok");
        exit;
    }
}

if (isset($_GET['kargo_sil'])) {
    $id = intval($_GET['kargo_sil']);
    mysqli_query($conn, "DELETE FROM kargolar WHERE id = $id");
    header("Location: admin.php?durum=kargo_silindi");
    exit;
}

if (isset($_POST['duyuru_yayinla'])) {
    $mesaj = mysqli_real_escape_string($conn, $_POST['duyuru_metni']);
    $sql = "INSERT INTO duyurular (icerik) VALUES ('$mesaj')";
    if(mysqli_query($conn, $sql)) {
        header("Location: admin.php?durum=duyuru_eklendi");
        exit;
    }
}

if (isset($_GET['duyuru_sil'])) {
    $id = intval($_GET['duyuru_sil']);
    mysqli_query($conn, "DELETE FROM duyurular WHERE id = $id");
    header("Location: admin.php?durum=duyuru_silindi");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>KYKARGO | Süper Admin Paneli</title>
    <link rel="stylesheet" href="folder.css/dashboard.css">
    <style>
        .admin-container { padding: 20px; max-width: 1200px; margin: auto; }
        .admin-card { background: white; padding: 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .stat-item { background: #fff; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-bottom: 4px solid #ff6a00; }
        .stat-item h4 { margin: 0; color: #666; font-size: 14px; }
        .stat-item p { margin: 10px 0 0; font-size: 24px; font-weight: bold; color: #ff6a00; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .btn-reset { background: #27ae60; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; border:none; cursor:pointer; }
        .btn-del { color: #e74c3c; text-decoration: none; font-weight: bold; }
        textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; resize: vertical; }
    </style>
</head>
<body>
<div class="admin-container">
    <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2>🚀 Sistem Yönetim Paneli</h2>
        <a href="dashboard.php" class="logout-btn">Siteye Dön</a>
    </header>

    <div class="stats-grid">
        <div class="stat-item">
            <h4>Toplam Öğrenci</h4>
            <p><?php echo $toplam_ogrenci; ?></p>
        </div>
        <div class="stat-item">
            <h4>Sistemdeki Kargolar</h4>
            <p><?php echo $toplam_kargo; ?></p>
        </div>
        <div class="stat-item" style="border-bottom-color: #e74c3c;">
            <h4>Bekleyen Şifre Talebi</h4>
            <p><?php echo $bekleyen_talepler; ?></p>
        </div>
    </div>

    <div class="admin-card" style="border-top: 5px solid #ff6a00;">
        <h3>📢 Yeni Duyuru Yayınla</h3>
        <form method="POST">
            <textarea name="duyuru_metni" rows="3" placeholder="Duyuru mesajınızı buraya yazın..." required></textarea>
            <button type="submit" name="duyuru_yayinla" class="btn-primary">Duyuruyu Paylaş</button>
        </form>
    </div>

    <div class="admin-card">
        <h3>📜 Mevcut Duyurular</h3>
        <table>
            <thead><tr><th>Duyuru İçeriği</th><th>Tarih</th><th>İşlem</th></tr></thead>
            <tbody>
                <?php
                $d_res = mysqli_query($conn, "SELECT * FROM duyurular ORDER BY tarih DESC");
                while($d = mysqli_fetch_assoc($d_res)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($d['icerik']); ?></td>
                    <td><small><?php echo $d['tarih']; ?></small></td>
                    <td><a href="?duyuru_sil=<?php echo $d['id']; ?>" class="btn-del">Sil</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="admin-card" style="border-top: 5px solid #e74c3c;">
        <h3>🔔 Şifre Sıfırlama Bekleyenler</h3>
        <table>
            <thead><tr><th>Ad Soyad</th><th>Okul No</th><th>İşlem</th></tr></thead>
            <tbody>
                <?php
                $talepler_query = mysqli_query($conn, "SELECT * FROM users WHERE sifre_talep = 1");
                while($t = mysqli_fetch_assoc($talepler_query)): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($t['ad_soyad']); ?></strong></td>
                    <td><?php echo $t['okul_no']; ?></td>
                    <td><a href="?sifre_reset_id=<?php echo $t['id']; ?>" class="btn-reset">✅ Sıfırla (123456)</a></td>
                </tr>
                <?php endwhile; if(mysqli_num_rows($talepler_query)==0) echo "<tr><td colspan='3'>Talep yok.</td></tr>"; ?>
            </tbody>
        </table>
    </div>

    <div class="admin-card">
        <h3>👥 Kayıtlı Öğrenciler</h3>
        <table>
            <thead><tr><th>Ad Soyad</th><th>Okul No</th><th>Kayıt Tarihi</th></tr></thead>
            <tbody>
                <?php
                $ogrenciler = mysqli_query($conn, "SELECT * FROM users WHERE rol = 0 ORDER BY id DESC");
                while($o = mysqli_fetch_assoc($ogrenciler)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($o['ad_soyad']); ?></td>
                    <td><?php echo $o['okul_no']; ?></td>
                    <td><small>Aktif Üye</small></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="admin-card">
        <h3>📦 Tüm Kargo Hareketleri</h3>
        <table>
            <thead><tr><th>Kargo Kodu</th><th>Durum</th><th>İşlem</th></tr></thead>
            <tbody>
                <?php
                $kargolar = mysqli_query($conn, "SELECT * FROM kargolar ORDER BY id DESC");
                while($k = mysqli_fetch_assoc($kargolar)): ?>
                <tr>
                    <td><code><?php echo $k['kargo_kodu']; ?></code></td>
                    <td><?php echo $k['durum']; ?></td>
                    <td><a href="?kargo_sil=<?php echo $k['id']; ?>" class="btn-del">Sil</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>