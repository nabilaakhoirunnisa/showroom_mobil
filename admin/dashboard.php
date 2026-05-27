<?php
session_start();
include "../config/koneksi.php";

date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit;
}

$username = $_SESSION['username'] ?? 'Admin';

function getCount($koneksi, $query) {
    $result = mysqli_query($koneksi, $query);
    if (!$result) return 0;

    $row = mysqli_fetch_assoc($result);
    return (int) ($row['total'] ?? 0);
}

$totalMobil      = getCount($koneksi, "SELECT COUNT(*) AS total FROM mobil");
$totalPembeli    = getCount($koneksi, "SELECT COUNT(*) AS total FROM pembeli");
$totalPenawaran  = getCount($koneksi, "SELECT COUNT(*) AS total FROM penawaran");
$totalKurir      = getCount($koneksi, "SELECT COUNT(*) AS total FROM kurir");
$totalPemesanan  = getCount($koneksi, "SELECT COUNT(*) AS total FROM pemesanan");
$totalPembayaran = getCount($koneksi, "SELECT COUNT(*) AS total FROM pembayaran");

$mobilTersedia = getCount($koneksi, "SELECT COUNT(*) AS total FROM mobil WHERE status = 'tersedia'");
$mobilDipesan   = getCount($koneksi, "SELECT COUNT(*) AS total FROM mobil WHERE status = 'dipesan'");
$mobilTerjual   = getCount($koneksi, "SELECT COUNT(*) AS total FROM mobil WHERE status = 'terjual'");

$pemesananBooking = getCount($koneksi, "SELECT COUNT(*) AS total FROM pemesanan WHERE status = 'booking'");
$pemesananDp      = getCount($koneksi, "SELECT COUNT(*) AS total FROM pemesanan WHERE status = 'dp'");
$pemesananLunas   = getCount($koneksi, "SELECT COUNT(*) AS total FROM pemesanan WHERE status = 'lunas'");
$pemesananBatal   = getCount($koneksi, "SELECT COUNT(*) AS total FROM pemesanan WHERE status = 'batal'");

$persenLunas = ($totalPemesanan > 0) ? round(($pemesananLunas / $totalPemesanan) * 100) : 0;
$persenBooking = ($totalPemesanan > 0) ? round((($pemesananBooking + $pemesananDp) / $totalPemesanan) * 100) : 0;
$persenBatal = ($totalPemesanan > 0) ? round(($pemesananBatal / $totalPemesanan) * 100) : 0;

$queryTransaksi = mysqli_query($koneksi, "
    SELECT 
        p.id_pemesanan,
        p.tanggal_pesan,
        p.total_harga,
        p.status AS status_pemesanan,
        m.nama_mobil,
        b.nama AS nama_pembeli
    FROM pemesanan p
    LEFT JOIN mobil m ON p.id_mobil = m.id_mobil
    LEFT JOIN pembeli b ON p.id_pembeli = b.id_pembeli
    ORDER BY p.id_pemesanan DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">

</head>

<body id="page-top">

<div id="wrapper">

    <?php include '../includes/sidebar_admin.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php include '../includes/topbar.php'; ?>

            <div class="container-fluid">

                <div class="dashboard-header d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Dashboard Admin</h1>
                        <p class="mb-0 text-gray-600">
                            Selamat datang, <?= htmlspecialchars($username); ?>.
                            Berikut ringkasan data terbaru Galaxy Showroom.
                        </p>
                    </div>

                    <div class="d-none d-sm-inline-block">
                        <span id="jamRealtime" class="btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-calendar-alt fa-sm text-white-50 mr-1"></i>
                            <?= date('d F Y  |  H:i:s'); ?> WIB
                        </span>
                    </div>
                </div>

                <div class="row">

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-primary h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Mobil
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $totalMobil; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat-icon icon-primary">
                                            <i class="fas fa-car"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-success h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Total Pembeli
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $totalPembeli; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat-icon icon-success">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-warning h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Total Pemesanan
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $totalPemesanan; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat-icon icon-warning">
                                            <i class="fas fa-shopping-cart"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-info h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total Penawaran
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $totalPenawaran; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat-icon icon-info">
                                            <i class="fas fa-handshake"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    Diagram Data Showroom
                                </h6>
                                <i class="fas fa-chart-bar text-gray-400"></i>
                            </div>
                            <div class="card-body">
                                <div style="height: 320px;">
                                    <canvas id="dashboardChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    Status Mobil
                                </h6>
                                <i class="fas fa-chart-pie text-gray-400"></i>
                            </div>
                            <div class="card-body">
                                <div style="height: 260px;">
                                    <canvas id="statusMobilChart"></canvas>
                                </div>

                                <div class="mt-4 small">
                                    <p class="mb-2">
                                        <i class="fas fa-circle text-success"></i>
                                        Tersedia: <?= $mobilTersedia; ?>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-circle text-warning"></i>
                                        Dipesan: <?= $mobilDipesan; ?>
                                    </p>
                                    <p class="mb-0">
                                        <i class="fas fa-circle text-danger"></i>
                                        Terjual: <?= $mobilTerjual; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-lg-5 mb-4">
                        <div class="card">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    Status Pemesanan
                                </h6>
                            </div>
                            <div class="card-body">

                                <h4 class="small font-weight-bold">
                                    Lunas
                                    <span class="float-right"><?= $persenLunas; ?>%</span>
                                </h4>
                                <div class="progress mb-4">
                                    <div class="progress-bar bg-success" style="width: <?= $persenLunas; ?>%"></div>
                                </div>

                                <h4 class="small font-weight-bold">
                                    Booking / DP
                                    <span class="float-right"><?= $persenBooking; ?>%</span>
                                </h4>
                                <div class="progress mb-4">
                                    <div class="progress-bar bg-warning" style="width: <?= $persenBooking; ?>%"></div>
                                </div>

                                <h4 class="small font-weight-bold">
                                    Batal
                                    <span class="float-right"><?= $persenBatal; ?>%</span>
                                </h4>
                                <div class="progress mb-4">
                                    <div class="progress-bar bg-danger" style="width: <?= $persenBatal; ?>%"></div>
                                </div>

                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Status pemesanan diambil dari tabel <b>pemesanan</b>.
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7 mb-4">
                        <div class="card">
                            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    Transaksi Terbaru
                                </h6>
                                <a href="../admin/transaksi.php" class="btn btn-sm btn-primary">
                                    Lihat Semua
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Pembeli</th>
                                                <th>Mobil</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($queryTransaksi && mysqli_num_rows($queryTransaksi) > 0): ?>
                                                <?php $no = 1; ?>
                                                <?php while ($row = mysqli_fetch_assoc($queryTransaksi)): ?>
                                                    <tr>
                                                        <td><?= $no++; ?></td>
                                                        <td>
                                                            <?= !empty($row['tanggal_pesan']) ? date('d/m/Y', strtotime($row['tanggal_pesan'])) : '-'; ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($row['nama_pembeli'] ?? '-'); ?></td>
                                                        <td><?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?></td>
                                                        <td>
                                                            <?php if (($row['status_pemesanan'] ?? '') == 'lunas'): ?>
                                                                <span class="badge badge-success">Lunas</span>
                                                            <?php elseif (($row['status_pemesanan'] ?? '') == 'booking'): ?>
                                                                <span class="badge badge-warning">Booking</span>
                                                            <?php elseif (($row['status_pemesanan'] ?? '') == 'dp'): ?>
                                                                <span class="badge badge-info">DP</span>
                                                            <?php elseif (($row['status_pemesanan'] ?? '') == 'batal'): ?>
                                                                <span class="badge badge-danger">Batal</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-secondary">Tidak Ada</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">
                                                        Belum ada transaksi terbaru.
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-lg-12 mb-4">
                        <div class="card">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    Akses Cepat Admin
                                </h6>
                            </div>
                            <div class="card-body quick-actions">

                                <a href="../admin/data_mobil_admin.php" class="btn btn-primary btn-icon-split mb-2 mr-2">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-car"></i>
                                    </span>
                                    <span class="text">Data Mobil</span>
                                </a>

                                <a href="../admin/data_pembeli_admin.php" class="btn btn-success btn-icon-split mb-2 mr-2">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <span class="text">Data Pembeli</span>
                                </a>

                                <a href="../admin/transaksi.php" class="btn btn-warning btn-icon-split mb-2 mr-2">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-shopping-cart"></i>
                                    </span>
                                    <span class="text">Transaksi</span>
                                </a>

                                <a href="../admin/laporan.php" class="btn btn-info btn-icon-split mb-2 mr-2">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-file-alt"></i>
                                    </span>
                                    <span class="text">Laporan</span>
                                </a>

                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>
<script src="../assets/vendor/chart.js/Chart.min.js"></script>

<script>
var ctxBar = document.getElementById("dashboardChart").getContext("2d");

new Chart(ctxBar, {
    type: "bar",
    data: {
        labels: ["Mobil", "Pembeli", "Penawaran", "Kurir", "Pemesanan", "Pembayaran"],
        datasets: [{
            label: "Jumlah Data",
            backgroundColor: [
                "#4e73df",
                "#1cc88a",
                "#36b9cc",
                "#858796",
                "#f6c23e",
                "#e74a3b"
            ],
            data: [
                <?= $totalMobil; ?>,
                <?= $totalPembeli; ?>,
                <?= $totalPenawaran; ?>,
                <?= $totalKurir; ?>,
                <?= $totalPemesanan; ?>,
                <?= $totalPembayaran; ?>
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    precision: 0
                }
            }]
        }
    }
});

var ctxPie = document.getElementById("statusMobilChart").getContext("2d");

new Chart(ctxPie, {
    type: "doughnut",
    data: {
        labels: ["Tersedia", "Dipesan", "Terjual"],
        datasets: [{
            data: [
                <?= $mobilTersedia; ?>,
                <?= $mobilDipesan; ?>,
                <?= $mobilTerjual; ?>
            ],
            backgroundColor: [
                "#1cc88a",
                "#f6c23e",
                "#e74a3b"
            ],
            hoverBackgroundColor: [
                "#17a673",
                "#dda20a",
                "#be2617"
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        cutoutPercentage: 70
    }
});
</script>

<script>
function updateJam() {

    const sekarang = new Date();

    const bulan = [
        "Januari", "Februari", "Maret", "April",
        "Mei", "Juni", "Juli", "Agustus",
        "September", "Oktober", "November", "Desember"
    ];

    let tanggal = sekarang.getDate();
    let namaBulan = bulan[sekarang.getMonth()];
    let tahun = sekarang.getFullYear();

    let jam = String(sekarang.getHours()).padStart(2, '0');
    let menit = String(sekarang.getMinutes()).padStart(2, '0');
    let detik = String(sekarang.getSeconds()).padStart(2, '0');

    document.getElementById("jamRealtime").innerHTML =
        `<i class="fas fa-calendar-alt fa-sm text-white-50 mr-1"></i> ${tanggal} ${namaBulan} ${tahun} | ${jam}:${menit}:${detik} WIB`;
}

setInterval(updateJam, 1000);

updateJam();
</script>

</body>
</html>