<?php
$host       = "localhost";
$user       = "root";
$password   = "";
$db         = "angkatan3_kasir";

$koneksi = mysqli_connect($host, $user, $password, $db);

if (empty($koneksi)) {
    echo "Koneksi gagal";
}
