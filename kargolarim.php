<?php
session_start();
require 'config.php';

if (!isset($_SESSION['okul_no'])) {
    header("Location: login.php");
    exit;
}
$current_user = $_SESSION['okul_no'];

if (isset($_POST['kabul_et'])) {
    $id = intval($_POST['kargo_id']);
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
    <title>KYKARGO | Kargo Takip</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="folder.css/dashboard.css">
    <style>
        .grid-container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; }
        .btn-action { background: #3498db; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 12px; border: none; cursor: pointer; }
        .status-badge { padding: 4px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; }
        .bg-orange { background: #ff793f; color: white; }
        .bg-green { background: #2ecc71; color: white; }
        @media (max-width: 768px) { .grid-container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="container">
    <header>
        <div class="logo">KYKARGO</div>
        <div class="nav-links">
            <a href="dashboard.php" class="logout-btn" style="border-color:#27ae60; color:#27ae60;">Ana Sayfa</a>
            <a href="logout.php" class="logout-btn">Çıkış Yap</a>
        </div>
    </header>

    <div class="grid-container">
        <section class="request-box">
            <h3 class="section-title" style="background: #34495e;">🌐 Genel Kargo Havuzu</h3>
            <table>
                <thead><tr><th>Gönderen</th><th>İşlem</th></tr></thead>
                <tbody>
                    <?php
                    $all_orders = mysqli_query($conn, "SELECT * FROM kargolar WHERE sahip_okul_no != '$current_user' ORDER BY id DESC LIMIT 20");
                    while($row = mysqli_fetch_assoc($all_orders)): ?>
                    <tr>
                        <td>👤 Öğrenci (...<?php echo substr($row['sahip_okul_no'], -3); ?>)</td>
                        <td>
                            <?php if($row['durum'] == 'beklemede'): ?>
                                <form method="POST"><input type="hidden" name="kargo_id" value="<?php echo $row['id']; ?>"><button type="submit" name="kabul_et" class="btn-action">🤝 Yardım Et</button></form>
                            <?php elseif($row['alici_okul_no'] == $current_user): ?>
                                <a href="mesajlar.php?kargo_id=<?php echo $row['id']; ?>" class="btn-action" style="background:#2ecc71;">💬 Sohbet</a>
                            <?php else: ?>
                                <span class="status-badge bg-green">Alındı</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <section class="request-box">
            <h3 class="section-title" style="background: #e67e22;">📑 Benim İsteklerim</h3>
            <table>
                <thead><tr><th>Kod</th><th>Durum</th><th>İşlem</th></tr></thead>
                <tbody>
                    <?php
                    $my_query = mysqli_query($conn, "SELECT * FROM kargolar WHERE sahip_okul_no='$current_user' ORDER BY id DESC");
                    while($row = mysqli_fetch_assoc($my_query)): ?>
                    <tr>
                        <td><strong><?php echo $row['kargo_kodu']; ?></strong></td>
                        <td>
                            <?php 
                                if($row['durum'] == 'beklemede') echo "⏳ Bekliyor";
                                elseif($row['durum'] == 'kabul_edildi') echo "🤝 Alındı";
                                elseif($row['durum'] == 'kargocu_bekleniyor') echo "🕒 Beklemede";
                                elseif($row['durum'] == 'teslim_alindi') echo "✅ Teslim Edildi";
                            ?>
                        </td>
                        <td>
                            <?php if($row['durum'] != 'beklemede'): ?>
                                <a href="mesajlar.php?kargo_id=<?php echo $row['id']; ?>" class="btn-action">💬 Mesajlar</a>
                            <?php else: ?>
                                <small>Henüz yok</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </div>
</div>
</body>
</html>