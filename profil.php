<?php
session_start();
require 'config.php';
if (!isset($_SESSION['okul_no'])) { header("Location: login.php"); exit; }

$liderler_sql = "SELECT u.ad_soyad, u.okul_no, COUNT(k.id) as toplam 
                 FROM users u
                 JOIN kargolar k ON k.alici_okul_no = u.okul_no 
                 WHERE k.durum = 'teslim_alindi' 
                 GROUP BY u.okul_no, u.ad_soyad 
                 ORDER BY toplam DESC LIMIT 10";
$liderler_res = mysqli_query($conn, $liderler_sql);
?>
<!DOCTYPE html>
<html>
<head><title>Liderlik Tablosu</title><link rel="stylesheet" href="folder.css/dashboard.css"></head>
<body>
<div class="container">
    <header><div class="logo">KYKARGO</div><a href="dashboard.php" class="logout-btn">Geri</a></header>
    <div class="request-box" style="margin-top:20px;">
        <h2 style="text-align:center">🏆 Kahramanlar Listesi</h2>
        <table style="width:100%; border-collapse:collapse;">
            <?php $r=1; while($row = mysqli_fetch_assoc($liderler_res)): ?>
                <tr style="border-bottom:1px solid #eee; <?php if($row['okul_no']==$_SESSION['okul_no']) echo 'background:#fff9e6;'; ?>">
                    <td style="padding:15px;">#<?php echo $r++; ?></td>
                    <td><?php echo htmlspecialchars($row['ad_soyad']); ?></td>
                    <td style="text-align:right"><b><?php echo $row['toplam']; ?> Teslimat</b></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>