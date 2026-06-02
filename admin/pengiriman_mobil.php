<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit;
}

mysqli_query($koneksi, "ALTER TABLE pengiriman MODIFY status ENUM('diproses','dikirim','selesai','terkirim') DEFAULT 'diproses'");
mysqli_query($koneksi, "ALTER TABLE pengiriman ADD COLUMN IF NOT EXISTS bukti_pengiriman VARCHAR(255) DEFAULT NULL");
mysqli_query($koneksi, "ALTER TABLE pengiriman ADD COLUMN IF NOT EXISTS tanggal_terkirim DATETIME DEFAULT NULL");

/* HAPUS */
if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);

    mysqli_query($koneksi, "DELETE FROM surat_jalan WHERE id_pengiriman='$id'");
    mysqli_query($koneksi, "DELETE FROM pengiriman WHERE id_pengiriman='$id'");

    $_SESSION['success'] = "Data pengiriman berhasil dihapus.";
    header("Location: pengiriman_mobil.php");
    exit;
}

/* CETAK SURAT JALAN */
if(isset($_GET['surat_jalan'])){
    $id_pengiriman = mysqli_real_escape_string($koneksi, $_GET['surat_jalan']);

    $cek = mysqli_query($koneksi, "
        SELECT id_suratjalan 
        FROM surat_jalan 
        WHERE id_pengiriman='$id_pengiriman'
    ");

    if(mysqli_num_rows($cek) == 0){
        mysqli_query($koneksi, "
            INSERT INTO surat_jalan 
            (id_pengiriman, tanggal_cetak)
            VALUES
            ('$id_pengiriman', NOW())
        ");
    }

    header("Location: surat_jalan.php?id=$id_pengiriman");
    exit;
}

/* DATA PENGIRIMAN */
$data = mysqli_query($koneksi, "
    SELECT 
        pg.*,

        p.total_harga,
        p.status AS status_pemesanan,

        b.nama AS nama_pembeli,
        b.no_hp AS no_hp_pembeli,

        m.nama_mobil,
        m.tahun,

        k.nama AS nama_kurir,
        k.no_hp AS no_hp_kurir,

        sj.id_suratjalan,
        sj.tanggal_cetak

    FROM pengiriman pg
    JOIN pemesanan p ON pg.id_pemesanan = p.id_pemesanan
    JOIN pembeli b ON p.id_pembeli = b.id_pembeli
    JOIN mobil m ON p.id_mobil = m.id_mobil
    LEFT JOIN kurir k ON pg.id_kurir = k.id_kurir
    LEFT JOIN surat_jalan sj ON pg.id_pengiriman = sj.id_pengiriman

    ORDER BY pg.id_pengiriman DESC
");

if(!$data){
    die("Query error: " . mysqli_error($koneksi));
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
        .table th{
            font-size: 12px;
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
        }

        .table td{
            font-size: 13px;
            vertical-align: middle;
        }

        .table-responsive{
            overflow-x: auto;
        }

        #tablePengiriman{
            min-width: 1100px;
        }

        .badge{
            font-size: 11px;
            padding: 6px 9px;
        }

        .main-text{
            font-weight: 700;
            color: #2f3542;
        }

        .muted-text{
            font-size: 12px;
            color: #858796;
        }

        .btn-icon{
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_admin.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = "Pengiriman Mobil";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Pengiriman Mobil</h1>
                        <p class="mb-0 text-gray-600">Pelacakan distribusi armada unit terjual, penugasan kurir, dan berkas serah terima.</p>
                    </div>

                    <a href="tambah_pengiriman.php" class="btn btn-primary btn-sm shadow-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Pengiriman
                    </a>
                </div>

                <?php if(isset($_SESSION['success'])){ ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-1"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php } ?>

                <?php if(isset($_SESSION['error'])){ ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-1"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php } ?>

                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-truck mr-2"></i>Monitoring Logistik Showroom
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control form-control-sm"
                               style="max-width:280px;"
                               placeholder="Cari pembeli / mobil / kurir..."
                               onkeyup="filterTable()">
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover" id="tablePengiriman">

                                <thead class="thead-light">
                                    <tr>
                                        <th width="40">No</th>
                                        <th width="180">Penerima / Pembeli</th>
                                        <th width="160">Spesifikasi Unit</th>
                                        <th width="160">Petugas Kurir</th>
                                        <th>Destinasi Pengiriman</th>
                                        <th width="160">Status Logistik</th>
                                        <th width="110">Dokumen</th>
                                        <th width="120">Pilihan Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php if(mysqli_num_rows($data) > 0){ ?>

                                        <?php
                                        $no = 1;
                                        while($row = mysqli_fetch_assoc($data)){

                                            if($row['status'] == 'diproses'){
                                                $badge = 'warning';
                                                $textStatus = 'DIPROSES';
                                                $iconStatus = 'fa-clock';
                                            } elseif($row['status'] == 'dikirim'){
                                                $badge = 'info';
                                                $textStatus = 'DIKIRIM';
                                                $iconStatus = 'fa-truck-moving';
                                            } elseif($row['status'] == 'terkirim' || $row['status'] == 'selesai'){
                                                $badge = 'success';
                                                $textStatus = 'TERKIRIM';
                                                $iconStatus = 'fa-check-circle';
                                            } else {
                                                $badge = 'secondary';
                                                $textStatus = '-';
                                                $iconStatus = 'fa-question-circle';
                                            }
                                        ?>

                                        <tr>
                                            <td class="text-center font-weight-bold text-gray-700"><?= $no++; ?></td>

                                            <td>
                                                <div class="main-text"><?= htmlspecialchars($row['nama_pembeli']); ?></div>
                                                <div class="muted-text font-weight-bold text-primary">
                                                    <i class="fab fa-whatsapp mr-1"></i><?= htmlspecialchars($row['no_hp_pembeli']); ?>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="main-text"><?= htmlspecialchars($row['nama_mobil']); ?></div>
                                                <div class="muted-text font-weight-bold text-gray-700">Tahun <?= htmlspecialchars($row['tahun']); ?></div>
                                            </td>

                                            <td>
                                                <?php if(!empty($row['nama_kurir'])){ ?>
                                                    <div class="main-text"><?= htmlspecialchars($row['nama_kurir']); ?></div>
                                                    <div class="muted-text text-xs text-gray-600">
                                                        <i class="fas fa-phone-alt mr-1"></i><?= htmlspecialchars($row['no_hp_kurir']); ?>
                                                    </div>
                                                <?php } else { ?>
                                                    <span class="badge badge-light text-muted border"><i class="fas fa-user-times mr-1"></i>Belum Ada</span>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <div class="text-dark font-weight-normal text-left" style="line-height: 1.4;">
                                                    <i class="fas fa-map-marker-alt text-danger mr-1"></i><?= htmlspecialchars($row['alamat_kirim']); ?>
                                                </div>
                                            </td>

                                            <td class="text-center">
                                                <div class="mb-1">
                                                    <span class="badge badge-<?= $badge; ?> btn-block text-xs">
                                                        <i class="fas <?= $iconStatus; ?> mr-1"></i><?= $textStatus; ?>
                                                    </span>
                                                </div>
                                                
                                                <?php if(!empty($row['tanggal_terkirim'])){ ?>
                                                    <div class="muted-text text-xs text-success font-weight-bold mt-1">
                                                        Tiba: <?= date('d/m/Y H:i', strtotime($row['tanggal_terkirim'])); ?>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="muted-text text-xs text-gray-500 mt-1">Dalam Pemantauan</div>
                                                <?php } ?>
                                            </td>

                                            <td class="text-center">
                                                <?php if(!empty($row['bukti_pengiriman'])){ ?>
                                                    <a href="../uploads/<?= htmlspecialchars($row['bukti_pengiriman']); ?>"
                                                       target="_blank"
                                                       class="badge badge-light border text-primary btn-block text-xs py-1.5"
                                                       title="Klik untuk melihat lampiran foto penyerahan">
                                                        <i class="fas fa-images mr-1"></i> Foto Serah
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="badge badge-light border text-muted btn-block text-xs py-1.5">
                                                        <i class="fas fa-times mr-1"></i> Belum Upload
                                                    </span>
                                                <?php } ?>
                                            </td>

                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center style-gap" style="gap: 4px;">
                                                    

                                                    <a href="pengiriman_mobil.php?hapus=<?= $row['id_pengiriman']; ?>"
                                                       class="btn btn-sm btn-danger btn-icon"
                                                       title="Hapus Data Pengiriman"
                                                       onclick="return confirm('Apakah Anda yakin ingin menghapus data pengiriman dan surat jalan terkait ini?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>

                                        <?php } ?>

                                    <?php } else { ?>

                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-5">
                                                <i class="fas fa-shipping-fast fa-2x text-gray-300 mb-2 d-block"></i>
                                                Belum ada arsip logistik pengiriman mobil yang aktif saat ini.
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
        // Cek baris kosong agar tidak ikut tersembunyi jika data kosong
        if(row.cells.length > 1){
            row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
        }
    });
}
</script>

</body>
</html>