<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "kurir"){
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

/* AMBIL DATA KURIR LOGIN */
$q_kurir = mysqli_query($koneksi, "
    SELECT * FROM kurir 
    WHERE id_user='$id_user' 
    LIMIT 1
");

if(!$q_kurir){
    die("Query kurir error: " . mysqli_error($koneksi));
}

$kurir = mysqli_fetch_assoc($q_kurir);

if(!$kurir){
    die("Data kurir tidak ditemukan. Pastikan akun kurir sudah terhubung dengan tabel kurir.");
}

$id_kurir = $kurir['id_kurir'];

/* TAMBAH KOLOM JIKA BELUM ADA */
mysqli_query($koneksi, "ALTER TABLE pengiriman ADD COLUMN IF NOT EXISTS bukti_pengiriman VARCHAR(255) DEFAULT NULL");
mysqli_query($koneksi, "ALTER TABLE pengiriman ADD COLUMN IF NOT EXISTS tanggal_terkirim DATETIME DEFAULT NULL");

/* MULAI KIRIM */
if(isset($_GET['mulai'])){
    $id_pengiriman = mysqli_real_escape_string($koneksi, $_GET['mulai']);

    mysqli_query($koneksi, "
        UPDATE pengiriman SET 
            status='dikirim'
        WHERE id_pengiriman='$id_pengiriman'
        AND id_kurir='$id_kurir'
    ");

    $_SESSION['success'] = "Status pengiriman berhasil diubah menjadi dikirim.";
    header("Location: pengiriman_mobil.php");
    exit;
}

/* UPLOAD BUKTI */
if(isset($_POST['upload_bukti'])){

    $id_pengiriman = mysqli_real_escape_string($koneksi, $_POST['id_pengiriman']);

    if(empty($_FILES['bukti_pengiriman']['name'])){
        $_SESSION['error'] = "Pilih bukti pengiriman terlebih dahulu.";
        header("Location: pengiriman_mobil.php");
        exit;
    }

    $nama_file = $_FILES['bukti_pengiriman']['name'];
    $tmp_file  = $_FILES['bukti_pengiriman']['tmp_name'];

    $ext = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if(!in_array($ext, $allowed)){
        $_SESSION['error'] = "Format bukti harus JPG, JPEG, PNG, atau WEBP.";
        header("Location: pengiriman_mobil.php");
        exit;
    }

    if($_FILES['bukti_pengiriman']['size'] > 2 * 1024 * 1024){
        $_SESSION['error'] = "Ukuran bukti maksimal 2 MB.";
        header("Location: pengiriman_mobil.php");
        exit;
    }

    $nama_baru = "bukti_pengiriman_" . time() . "_" . rand(1000,9999) . "." . $ext;
    $path = "../uploads/" . $nama_baru;

    if(move_uploaded_file($tmp_file, $path)){

        mysqli_query($koneksi, "
            UPDATE pengiriman SET
                status='terkirim',
                bukti_pengiriman='$nama_baru',
                tanggal_terkirim=NOW()
            WHERE id_pengiriman='$id_pengiriman'
            AND id_kurir='$id_kurir'
        ");

        $_SESSION['success'] = "Bukti berhasil diupload. Status pengiriman menjadi telah terkirim.";
    } else {
        $_SESSION['error'] = "Gagal upload bukti pengiriman.";
    }

    header("Location: pengiriman_mobil.php");
    exit;
}

/* DATA PENGIRIMAN UNTUK KURIR LOGIN */
$data = mysqli_query($koneksi, "
    SELECT 
        pg.id_pengiriman,
        pg.id_pemesanan,
        pg.id_kurir,
        pg.alamat_kirim,
        pg.status,
        pg.bukti_pengiriman,
        pg.tanggal_terkirim,

        p.total_harga,
        p.status AS status_pemesanan,

        b.nama AS nama_pembeli,
        b.no_hp AS no_hp_pembeli,
        b.alamat AS alamat_pembeli,

        m.nama_mobil,
        m.tahun

    FROM pengiriman pg
    JOIN pemesanan p ON pg.id_pemesanan = p.id_pemesanan
    JOIN pembeli b ON p.id_pembeli = b.id_pembeli
    JOIN mobil m ON p.id_mobil = m.id_mobil

    WHERE pg.id_kurir='$id_kurir'

    ORDER BY pg.id_pengiriman DESC
");

if(!$data){
    die("Query pengiriman error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengiriman Mobil</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">

    <style>
        .table td{
            vertical-align: middle;
            font-size: 14px;
        }

        .table th{
            font-size: 14px;
            white-space: nowrap;
        }

        .badge{
            font-size: 12px;
            padding: 6px 9px;
        }

        .small-text{
            font-size: 12px;
            color: #858796;
        }
    </style>
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_kurir.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = "Pengiriman Mobil";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <h1 class="h3 mb-4 text-gray-800">
                    Pengiriman Mobil
                </h1>

                <?php if(isset($_SESSION['success'])){ ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php } ?>

                <?php if(isset($_SESSION['error'])){ ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php } ?>

                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Tugas Pengiriman Mobil
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control form-control-sm"
                               style="max-width: 280px;"
                               placeholder="Cari pembeli / mobil..."
                               onkeyup="filterTable()">
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover text-center" id="tablePengiriman" width="100%" cellspacing="0">

                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Pembeli</th>
                                        <th>Mobil</th>
                                        <th>Alamat Kirim</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Bukti</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php if(mysqli_num_rows($data) > 0){ ?>

                                        <?php 
                                        $no = 1;
                                        while($row = mysqli_fetch_assoc($data)){ 

                                            if($row['status'] == 'diproses'){
                                                $badge = 'warning';
                                                $textStatus = 'Diproses';
                                            } elseif($row['status'] == 'dikirim'){
                                                $badge = 'info';
                                                $textStatus = 'Dikirim';
                                            } elseif($row['status'] == 'terkirim' || $row['status'] == 'selesai'){
                                                $badge = 'success';
                                                $textStatus = 'Telah Terkirim';
                                            } else {
                                                $badge = 'secondary';
                                                $textStatus = ucfirst($row['status']);
                                            }
                                        ?>

                                        <tr>
                                            <td><?= $no++; ?></td>

                                            <td>
                                                <strong><?= htmlspecialchars($row['nama_pembeli']); ?></strong><br>
                                                <small class="small-text">
                                                    <?= htmlspecialchars($row['no_hp_pembeli']); ?>
                                                </small>
                                            </td>

                                            <td>
                                                <strong><?= htmlspecialchars($row['nama_mobil']); ?></strong><br>
                                                <small class="small-text">
                                                    Tahun <?= htmlspecialchars($row['tahun']); ?>
                                                </small>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['alamat_kirim']); ?>
                                            </td>

                                            <td>
                                                Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?>
                                            </td>

                                            <td>
                                                <span class="badge badge-<?= $badge; ?>">
                                                    <?= $textStatus; ?>
                                                </span>

                                                <?php if(!empty($row['tanggal_terkirim'])){ ?>
                                                    <br>
                                                    <small class="small-text">
                                                        <?= date('d-m-Y H:i', strtotime($row['tanggal_terkirim'])); ?>
                                                    </small>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <?php if(!empty($row['bukti_pengiriman'])){ ?>
                                                    <a href="../uploads/<?= htmlspecialchars($row['bukti_pengiriman']); ?>"
                                                       target="_blank"
                                                       class="btn btn-sm btn-success">
                                                        <i class="fas fa-eye"></i> Lihat
                                                    </a>
                                                <?php } else { ?>
                                                    -
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <?php if($row['status'] == 'diproses'){ ?>

                                                    <a href="../admin/surat_jalan.php?id=<?= $row['id_pengiriman']; ?>"
                                                    target="_blank"
                                                    class="btn btn-sm btn-success mb-1">
                                                    <i class="fas fa-print"></i> Surat Jalan
                                                    </a>
                                                    <br>
                                                    <a href="pengiriman_mobil.php?mulai=<?= $row['id_pengiriman']; ?>"
                                                    class="btn btn-sm btn-info"
                                                    onclick="return confirm('Mulai kirim mobil ini?')">
                                                    <i class="fas fa-truck"></i> Mulai Kirim
                                                    </a>

                                                <?php } elseif($row['status'] == 'dikirim'){ ?>

                                                    <form method="POST" enctype="multipart/form-data">
                                                        <input type="hidden"
                                                               name="id_pengiriman"
                                                               value="<?= $row['id_pengiriman']; ?>">

                                                        <input type="file"
                                                               name="bukti_pengiriman"
                                                               class="form-control-file mb-2"
                                                               accept="image/jpeg,image/png,image/webp"
                                                               required>

                                                        <button type="submit"
                                                                name="upload_bukti"
                                                                class="btn btn-sm btn-primary"
                                                                onclick="return confirm('Upload bukti dan selesaikan pengiriman?')">
                                                            <i class="fas fa-upload"></i> Upload Bukti
                                                        </button>
                                                    </form>

                                                <?php } else { ?>

                                                    <span class="badge badge-success">
                                                        Selesai
                                                    </span>

                                                <?php } ?>
                                            </td>
                                        </tr>

                                        <?php } ?>

                                    <?php } else { ?>

                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                Belum ada tugas pengiriman mobil.
                                            </td>
                                        </tr>

                                    <?php } ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

<script>
function filterTable(){
    let input = document.getElementById("searchInput").value.toLowerCase();
    let rows = document.querySelectorAll("#tablePengiriman tbody tr");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
    });
}
</script>

</body>
</html>