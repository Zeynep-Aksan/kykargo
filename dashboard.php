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
        .btn-chat { background: #3498db; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 13px; }
        /* Hata Mesajı Stili */
        .error-msg { background: #ff7675; color: white; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <div class="logo">KYKARGO</div>
        <div class="nav-links">
            <a href="dashboard.php" class="logout-btn" style="border-color:#27ae60; color:#27ae60;">Ana Sayfa</a>
            <a href="kargolarim.php" class="logout-btn" style="border-color:#3498db; color:#3498db;">Kargo Takip</a>
            <a href="logout.php" class="logout-btn">Çıkış Yap</a>
        </div>
    </header>

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
                    <td><?php echo $row['kargo_kodu']; ?></td>
                    <td><?php echo $row['durum']; ?></td>
                    <td>
                        <?php if($row['durum'] != 'beklemede'): ?>
                            <a href="mesajlar.php?kargo_id=<?php echo $row['id']; ?>" class="btn-chat">💬 Mesaja Git</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>

</body>
</html>