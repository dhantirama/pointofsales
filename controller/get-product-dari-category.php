<?php
require_once "../config/koneksi.php";

if (isset($_GET['id_kategori'])) {
    $id_kategori = $_GET['id_kategori'];

    $kategori = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_kategori = '$id_kategori'");
    $items = [];
    if (mysqli_num_rows($kategori) > 0) {
        while ($row = mysqli_fetch_assoc($kategori)) {
            $items[] = $row;
        }
        //mengembalikan hasil dalam bentuk json
    }
    header('Content-Type: application/json');
    echo json_encode($items);
}
