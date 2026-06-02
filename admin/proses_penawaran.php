<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit;
}

/*
|------------------------------------------------------
| PASTIKAN KOLOM TAMBAHAN ADA
|------------------------------------------------------
*/
$columns = [
    "status" => "ALTER TABLE penawaran ADD status ENUM('menunggu','diterima','ditolak') DEFAULT 'menunggu'",
    "catatan" => "ALTER TABLE penawaran ADD catatan TEXT DEFAULT NULL",
    "metode_pembayaran" => "ALTER TABLE penawaran ADD metode_pembayaran ENUM('tunai','transfer') DEFAULT NULL",
    "bukti_pembayaran" => "ALTER TABLE penawaran ADD bukti_pembayaran VARCHAR(255) DEFAULT NULL",
    "tanggal_keputusan" => "ALTER TABLE penawaran ADD tanggal_keputusan DATETIME DEFAULT NULL",
    "catatan_admin" => "ALTER TABLE penawaran ADD catatan_admin TEXT DEFAULT NULL"
];

foreach($columns as $col => $sql){
    $cek = mysqli_query($koneksi, "SHOW COLUMNS FROM penawaran LIKE '$col'");
    if($cek && mysqli_num_rows($cek) == 0){
        mysqli_query($koneksi, $sql);
    }
}

/*
|------------------------------------------------------
| TERIMA PENAWARAN
|------------------------------------------------------
*/
if(isset($_POST['proses_verifikasi']) && $_POST['keputusan_status'] == 'diterima'){

    $id_penawaran = mysqli_real_escape_string($koneksi, $_POST['id_penawaran']);
    $metode       = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran']);
    $catatan      = mysqli_real_escape_string($koneksi, $_POST['catatan_admin'] ?? '');

    if(empty($id_penawaran) || empty($metode)){
        $_SESSION['error'] = "ID penawaran dan metode pembayaran wajib diisi.";
        header("Location: penawaran.php");
        exit;
    }

    if(!in_array($metode, ['tunai', 'transfer'])){
        $_SESSION['error'] = "Metode pembayaran tidak valid.";
        header("Location: penawaran.php");
        exit;
    }

    /*
    |------------------------------------------------------
    | CEK DATA PENAWARAN
    |------------------------------------------------------
    */
    $q_penawaran = mysqli_query($koneksi, "
        SELECT 
            id_penawaran,
            id_mobil,
            harga_tawar,
            status
        FROM penawaran
        WHERE id_penawaran='$id_penawaran'
        LIMIT 1
    ");

    if(!$q_penawaran){
        die("Query penawaran error: " . mysqli_error($koneksi));
    }

    $penawaran = mysqli_fetch_assoc($q_penawaran);

    if(!$penawaran){
        $_SESSION['error'] = "Data penawaran tidak ditemukan.";
        header("Location: penawaran.php");
        exit;
    }

    if($penawaran['status'] == 'diterima'){
        $_SESSION['error'] = "Penawaran ini sudah diterima.";
        header("Location: penawaran.php");
        exit;
    }

    if($penawaran['status'] == 'ditolak'){
        $_SESSION['error'] = "Penawaran ini sudah ditolak.";
        header("Location: penawaran.php");
        exit;
    }

    $id_mobil    = $penawaran['id_mobil'];
    $harga_tawar = $penawaran['harga_tawar'];

    /*
    |------------------------------------------------------
    | UPLOAD BUKTI JIKA TRANSFER
    |------------------------------------------------------
    */
    $bukti_pembayaran = null;

    if($metode == "transfer"){

        if(empty($_FILES['bukti_pembayaran']['name'])){
            $_SESSION['error'] = "Bukti pembayaran wajib diupload jika metode transfer.";
            header("Location: penawaran.php");
            exit;
        }

        $nama_file = $_FILES['bukti_pembayaran']['name'];
        $tmp_file  = $_FILES['bukti_pembayaran']['tmp_name'];
        $ukuran    = $_FILES['bukti_pembayaran']['size'];

        $ext = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if(!in_array($ext, $allowed)){
            $_SESSION['error'] = "Format bukti pembayaran harus JPG, JPEG, PNG, atau WEBP.";
            header("Location: penawaran.php");
            exit;
        }

        if($ukuran > 2 * 1024 * 1024){
            $_SESSION['error'] = "Ukuran bukti pembayaran maksimal 2 MB.";
            header("Location: penawaran.php");
            exit;
        }

        $folder_upload = "../uploads/";

        if(!is_dir($folder_upload)){
            mkdir($folder_upload, 0777, true);
        }

        $bukti_pembayaran = "bukti_penawaran_" . time() . "_" . rand(1000,9999) . "." . $ext;
        $path = $folder_upload . $bukti_pembayaran;

        if(!move_uploaded_file($tmp_file, $path)){
            $_SESSION['error'] = "Gagal upload bukti pembayaran.";
            header("Location: penawaran.php");
            exit;
        }
    }

    /*
    |------------------------------------------------------
    | UPDATE PENAWARAN + UPDATE HARGA MOBIL
    |------------------------------------------------------
    */
    mysqli_begin_transaction($koneksi);

    if($metode == "transfer"){
        $update_penawaran = mysqli_query($koneksi, "
            UPDATE penawaran SET
                status='diterima',
                metode_pembayaran='transfer',
                bukti_pembayaran='$bukti_pembayaran',
                catatan_admin='$catatan',
                tanggal_keputusan=NOW()
            WHERE id_penawaran='$id_penawaran'
        ");
    } else {
        $update_penawaran = mysqli_query($koneksi, "
            UPDATE penawaran SET
                status='diterima',
                metode_pembayaran='tunai',
                bukti_pembayaran=NULL,
                catatan_admin='$catatan',
                tanggal_keputusan=NOW()
            WHERE id_penawaran='$id_penawaran'
        ");
    }

    $update_mobil = mysqli_query($koneksi, "
        UPDATE mobil SET
            harga='$harga_tawar'
        WHERE id_mobil='$id_mobil'
    ");

    if($update_penawaran && $update_mobil){
        mysqli_commit($koneksi);

        $_SESSION['success'] = "Penawaran diterima dan harga mobil berhasil diperbarui.";
        header("Location: penawaran.php");
        exit;
    } else {
        mysqli_rollback($koneksi);

        $_SESSION['error'] = "Gagal memproses penawaran.";
        header("Location: penawaran.php");
        exit;
    }
}

/*
|------------------------------------------------------
| TOLAK PENAWARAN
|------------------------------------------------------
*/
if(isset($_POST['proses_verifikasi']) && $_POST['keputusan_status'] == 'tolak'){

    $id_penawaran = mysqli_real_escape_string($koneksi, $_POST['id_penawaran']);
    $catatan      = mysqli_real_escape_string($koneksi, $_POST['catatan_admin'] ?? '');

    if(empty($id_penawaran)){
        $_SESSION['error'] = "ID penawaran tidak ditemukan.";
        header("Location: penawaran.php");
        exit;
    }

    $cek_penawaran = mysqli_query($koneksi, "
        SELECT id_penawaran, status
        FROM penawaran
        WHERE id_penawaran='$id_penawaran'
        LIMIT 1
    ");

    if(!$cek_penawaran){
        die("Query penawaran error: " . mysqli_error($koneksi));
    }

    $penawaran = mysqli_fetch_assoc($cek_penawaran);

    if(!$penawaran){
        $_SESSION['error'] = "Data penawaran tidak ditemukan.";
        header("Location: penawaran.php");
        exit;
    }

    if($penawaran['status'] == 'diterima'){
        $_SESSION['error'] = "Penawaran yang sudah diterima tidak bisa ditolak.";
        header("Location: penawaran.php");
        exit;
    }

    if($penawaran['status'] == 'ditolak'){
        $_SESSION['error'] = "Penawaran ini sudah ditolak.";
        header("Location: penawaran.php");
        exit;
    }

    $update = mysqli_query($koneksi, "
        UPDATE penawaran SET
            status='ditolak',
            metode_pembayaran=NULL,
            bukti_pembayaran=NULL,
            catatan_admin='$catatan',
            tanggal_keputusan=NOW()
        WHERE id_penawaran='$id_penawaran'
    ");

    if(!$update){
        die("Gagal menolak penawaran: " . mysqli_error($koneksi));
    }

    $_SESSION['success'] = "Penawaran berhasil ditolak.";
    header("Location: penawaran.php");
    exit;
}

header("Location: penawaran.php");
exit;
?>