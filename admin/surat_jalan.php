<?php
session_start();
include "../config/koneksi.php";

if(
   !isset($_SESSION['role']) ||
   (
      $_SESSION['role'] != "admin" &&
      $_SESSION['role'] != "kurir"
   )
){
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if(!$id){
    header("Location: pengiriman_mobil.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $id);

$q = mysqli_query($koneksi, "
    SELECT 
        pg.*,
        p.id_pemesanan,
        p.tanggal_pesan,
        p.total_harga,

        b.nama AS nama_pembeli,
        b.alamat AS alamat_pembeli,
        b.no_hp AS no_hp_pembeli,

        m.nama_mobil,
        m.tahun,

        k.nama AS nama_kurir,
        k.no_hp AS no_hp_kurir,

        sj.tanggal_cetak

    FROM pengiriman pg
    JOIN pemesanan p ON pg.id_pemesanan = p.id_pemesanan
    JOIN pembeli b ON p.id_pembeli = b.id_pembeli
    JOIN mobil m ON p.id_mobil = m.id_mobil
    LEFT JOIN kurir k ON pg.id_kurir = k.id_kurir
    LEFT JOIN surat_jalan sj ON pg.id_pengiriman = sj.id_pengiriman
    WHERE pg.id_pengiriman='$id'
");

$data = mysqli_fetch_assoc($q);

if(!$data){
    die("Data surat jalan tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan</title>

    <style>
        body{
            font-family: Arial, sans-serif;
            color:#111;
            padding:40px;
        }

        .kop{
            text-align:center;
            border-bottom:3px solid #111;
            padding-bottom:15px;
            margin-bottom:25px;
        }

        .kop h2{
            margin:0;
            font-size:26px;
        }

        .kop p{
            margin:5px 0 0;
            font-size:14px;
        }

        .title{
            text-align:center;
            margin:25px 0;
            text-decoration:underline;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:15px;
        }

        td{
            padding:8px 4px;
            vertical-align:top;
            font-size:14px;
        }

        .box{
            border:1px solid #111;
            padding:15px;
            margin-top:20px;
        }

        .ttd{
            margin-top:60px;
            display:flex;
            justify-content:space-between;
            text-align:center;
        }

        .ttd div{
            width:30%;
        }

        .print-btn{
            margin-bottom:20px;
        }

        @media print{
            .print-btn{
                display:none;
            }

            body{
                padding:20px;
            }
        }
    </style>
</head>

<body>

<button onclick="window.print()" class="print-btn">
    Cetak Surat Jalan
</button>

<div class="kop">
    <h2>GALAXY SHOWROOM</h2>
    <p>Surat Jalan Pengiriman Mobil</p>
</div>

<h3 class="title">SURAT JALAN</h3>

<table>
    <tr>
        <td width="180">No. Surat Jalan</td>
        <td width="10">:</td>
        <td>SJ-<?= str_pad($data['id_pengiriman'], 5, '0', STR_PAD_LEFT); ?></td>
    </tr>

    <tr>
        <td>Tanggal Cetak</td>
        <td>:</td>
        <td>
            <?= !empty($data['tanggal_cetak']) ? date('d-m-Y H:i', strtotime($data['tanggal_cetak'])) : date('d-m-Y H:i'); ?>
        </td>
    </tr>

    <tr>
        <td>ID Pemesanan</td>
        <td>:</td>
        <td>#<?= $data['id_pemesanan']; ?></td>
    </tr>
</table>

<div class="box">
    <strong>Data Pembeli</strong>

    <table>
        <tr>
            <td width="180">Nama Pembeli</td>
            <td width="10">:</td>
            <td><?= htmlspecialchars($data['nama_pembeli']); ?></td>
        </tr>

        <tr>
            <td>No. HP</td>
            <td>:</td>
            <td><?= htmlspecialchars($data['no_hp_pembeli']); ?></td>
        </tr>

        <tr>
            <td>Alamat Tujuan</td>
            <td>:</td>
            <td><?= htmlspecialchars($data['alamat_kirim']); ?></td>
        </tr>
    </table>
</div>

<div class="box">
    <strong>Data Kendaraan</strong>

    <table>
        <tr>
            <td width="180">Nama Mobil</td>
            <td width="10">:</td>
            <td><?= htmlspecialchars($data['nama_mobil']); ?></td>
        </tr>

        <tr>
            <td>Tahun</td>
            <td>:</td>
            <td><?= htmlspecialchars($data['tahun']); ?></td>
        </tr>

        <tr>
            <td>Total Pembelian</td>
            <td>:</td>
            <td>Rp <?= number_format($data['total_harga'], 0, ',', '.'); ?></td>
        </tr>
    </table>
</div>

<div class="box">
    <strong>Data Kurir</strong>

    <table>
        <tr>
            <td width="180">Nama Kurir</td>
            <td width="10">:</td>
            <td><?= htmlspecialchars($data['nama_kurir'] ?? '-'); ?></td>
        </tr>

        <tr>
            <td>No. HP Kurir</td>
            <td>:</td>
            <td><?= htmlspecialchars($data['no_hp_kurir'] ?? '-'); ?></td>
        </tr>

        <tr>
            <td>Status Pengiriman</td>
            <td>:</td>
            <td><?= ucfirst($data['status']); ?></td>
        </tr>
    </table>
</div>

<p style="margin-top:25px; font-size:14px;">
    Dengan surat jalan ini, kendaraan tersebut dinyatakan sedang dalam proses pengiriman
    dari pihak showroom kepada pembeli sesuai data yang tercantum.
</p>

<div class="ttd">
    <div>
        Admin
        <br><br><br><br>
        __________________
    </div>

    <div>
        Kurir
        <br><br><br><br>
        __________________
    </div>

    <div>
        Pembeli
        <br><br><br><br>
        __________________
    </div>
</div>

<script>
window.onload = function(){
    // boleh dikosongkan kalau tidak mau otomatis print
};
</script>

</body>
</html>