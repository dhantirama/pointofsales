<?php
session_start();
require_once "../config/koneksi.php";

if (isset($_POST['simpan'])) {
    $email = $_SESSION['EMAILNYABRO'];

    $query = mysqli_query($koneksi, "SELECT id FROM user WHERE email = '$email'");
    $row = mysqli_fetch_assoc($query);

    $id_user            = $row['id'];
    $kode_transaksi     = $_POST['kode_transaksi'];
    $tanggal_transaksi  = $_POST['tanggal_transaksi'];
    $total_harga        = $_POST['total_harga'];
    $nominal_bayar      = $_POST['nominal_bayar'];
    $kembalian          = $_POST['kembalian'];

    $queryPenjualan = mysqli_query($koneksi, "INSERT INTO penjualan (id_user, kode_transaksi, tanggal_transaksi) VALUES ('$id_user', '$kode_transaksi', '$tanggal_transaksi')");

    $id_penjualan = mysqli_insert_id($koneksi);

    foreach ($_POST['id_barang'] as $key => $id_barang) {
        $jumlah = $_POST['jumlah'][$key];

        //ambil stock dan harga barang
        $barang = mysqli_query($koneksi, "SELECT harga, qty FROM barang WHERE id = '$id_barang'");

        $barangData = mysqli_fetch_assoc($barang);

        $harga = $barangData['harga'];
        $qty = $barangData['qty'];

        $total_harga_detail = $jumlah * $harga;

        $detailPenjualan = mysqli_query($koneksi, "INSERT INTO detail_penjualan (id_penjualan, id_barang, jumlah, qty, harga, total_harga, nominal_bayar, kembalian) VALUES ('$id_penjualan', '$id_barang', '$jumlah', '$qty', '$harga', '$total_harga', '$nominal_bayar', '$kembalian')");

        $updateQty = mysqli_query($koneksi, "UPDATE barang SET qty = qty - $jumlah WHERE id = '$id_barang'");
    }
    header("location: ../kasir.php");
    exit();
    // 
}
