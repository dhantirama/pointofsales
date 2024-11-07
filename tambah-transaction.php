<?php
session_start();
session_regenerate_id(true);
require_once "config/koneksi.php";

date_default_timezone_set("Asia/Jakarta");

//Waktu:
$currentTime = date('Y-m-d');

function generateTransactionCode()
{
    $kode = date('ymdHis');
    return $kode;
}

//click_count 
if (empty($_SESSION['click_count'])) {
    $_SESSION['click_count'] = 0;
}
$query = mysqli_query($koneksi, "SELECT * FROM kategori_barang");
$categories = mysqli_fetch_all($query, MYSQLI_ASSOC);
// var_dump($categories);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <title>Document</title>
</head>

<body>
    <h1 class="text-center mt-4">WELCOME</h1>
    <?php include 'inc/navbar.php' ?>

    <div class="container justify-content-center">
        <div class="row">
            <div class="col-1"></div>
            <div class="col-10">
                <form action="controller/transaksi-store.php" method="post">
                    <div class="mb-1">
                        <label for="form-label">Kode Transaksi</label>
                        <input type="text" class="form-control w-50" id="kode_transaksi" name="kode_transaksi" readonly value="<?php echo "TR-" . generateTransactionCode() ?>">
                    </div>
                    <div class="mb-1">
                        <label for="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control w-50" id="tanggal_transaksi" name="tanggal_transaksi" readonly value="<?php echo $currentTime ?>">
                    </div>
                    <div class="mb-1">
                        <button class="btn btn-primary btn-sm" type="button" id="counterBtn">Tambah</button>
                        <input type="number" class="form-control" style="width: 50px; display: inline;" name="countDisplay" id="countDisplay" readonly value="<?php echo $_SESSION['click_count'] ?>">
                    </div>
                    <div class="table table-responsive">
                        <table class="table table-bordered">
                            <thead class="text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kategori</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Sisa Produk</th>
                                    <th>Harga</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">


                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th colspan="5">Total Harga</th>
                                    <td><input type="number" id="total_harga_keseluruhan" name="total_harga" class="form-control" readonly></td>
                                </tr>
                                <tr>
                                    <th colspan="5">Nominal Bayar</th>
                                    <td><input type="number" id="nominal_bayar_keseluruhan" name="nominal_bayar" class="form-control" required></td>
                                </tr>
                                <tr>
                                    <th colspan="5">Kembalian</th>
                                    <td><input type="number" id="kembalian_keseluruhan" name="kembalian" class="form-control" readonly></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <br><br>
                    <div class="mb-3">
                        <input type="submit" class="btn btn-primary" name="simpan" value="Hitung">
                        <a href="kasir.php" class="btn btn-danger">Kembali</a>
                    </div>
                </form>
            </div>
            <div class="col-1"></div>
        </div>
    </div>


    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- untuk membuat row di kasir.php -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            //fungsi tambah baris
            const button = document.getElementById('counterBtn');
            const countDisplay = document.getElementById('countDisplay');
            const tbody = document.getElementById('tbody');

            button.addEventListener('click', function() {
                let currentCount = parseInt(countDisplay.value) || 0;
                ++currentCount;
                countDisplay.value = currentCount;

                //fungsi tambah td
                let newRow = "<tr>";
                newRow += "<td>" + currentCount + "</td>";
                newRow += "<td><select class='form-control category-select' name='id_kategori[]' required>";
                newRow += "<option value=''>--Pilih Kategori--</option>";
                <?php foreach ($categories as $category) { ?>
                    newRow += "<option value='<?php echo $category['id'] ?>'><?php echo $category['nama_kategori'] ?></option>";
                <?php } ?>
                newRow += "</select></td>";
                newRow += "<td><select class='form-control item-select' name='id_barang[]' required>";
                newRow += "<option value=''>--Pilih Barang--</option>";
                newRow += "</select></td>";
                newRow += "<td><input type='number' name='jumlah[]' class='form-control jumlah-input' value='0' required></td>";
                newRow += "<td><input type= 'number' name='sisa_produk[]' class='form-control' readonly></td>";
                newRow += "<td><input type= 'number' name='harga[]' class='form-control' readonly></td>";
                newRow += "</tr>";
                tbody.insertAdjacentHTML('beforeend', newRow);

                attachCategoryChangeListener();
                attachItemChangeListener();
                attachJumlahChangeListener();

            });
            //fungsi untuk menampilkan barang beradsarkan kategori
            function attachCategoryChangeListener() {
                const categorySelects = document.querySelectorAll('.category-select'); //queryselectorall, artinya memilih semua data
                categorySelects.forEach(select => {

                    select.addEventListener('change', function() {
                        const categoryId = this.value;
                        const itemSelect = this.closest('tr').querySelector('.item-select'); //closest artinya memilih item terdekat dari tr
                        if (categoryId) {
                            fetch(`controller/get-product-dari-category.php?id_kategori=${categoryId}`)
                                .then(response => response.json())
                                .then(data => {
                                    itemSelect.innerHTML = "<option value= ''>--Pilih Barang--</option>";
                                    data.forEach(item => {
                                        itemSelect.innerHTML += `<option value='${item.id}'>${item.nama_barang}</option>`;
                                    });
                                });
                        } else {
                            itemSelect.innerHTML = "<option value= ''>--Pilih Barang--</option>";
                        }
                    });
                });
            }
            const totalHargaKeseluruhan = document.getElementById('total_harga_keseluruhan');
            const nominalBayarKeseluruhan = document.getElementById('nominal_bayar_keseluruhan');
            const kembalianKeseluruhanInput = document.getElementById('kembalian_keseluruhan');
            //fungsi untuk menampilkan sisa produk dan harga
            function attachItemChangeListener() {
                const itemSelects = document.querySelectorAll('.item-select');
                itemSelects.forEach(select => {
                    select.addEventListener('change', function() {
                        const itemId = this.value;
                        const row = this.closest('tr');
                        const sisaProdukInput = row.querySelector('input[name="sisa_produk[]"]');
                        const hargaInput = row.querySelector('input[name="harga[]"]');

                        if (itemId) {
                            fetch(`controller/get-details.php?id_barang=` + itemId)
                                .then(response => response.json())
                                .then(data => {
                                    sisaProdukInput.value = data.qty;
                                    hargaInput.value = data.harga;
                                })
                        } else {
                            sisaProdukInput.value = '';
                            hargaInput.value = '';
                        }
                    })
                })
            }

            function attachJumlahChangeListener() {
                const jumlahInputs = document.querySelectorAll('.jumlah-input');
                jumlahInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        const row = this.closest('tr');
                        const sisaProdukInput = row.querySelector('input[name="sisa_produk[]"]');
                        const hargaInput = row.querySelector('input[name="harga[]"]');
                        const totalHargaInput = document.getElementById('total_harga_keseluruhan');
                        const nominalBayarInput = document.getElementById('nominal_bayar_keseluruhan');
                        const kembalianInput = document.getElementById('kembalian_keseluruhan');
                        const jumlah = parseInt(this.value) || 0;
                        const sisaProduk = parseInt(sisaProdukInput.value) || 0;
                        const harga = parseFloat(hargaInput.value) || 0;

                        if (jumlah > sisaProduk) {
                            alert("Jumlah tidak boleh melebihi sisa produk");
                            this.value = sisaProduk; //
                            return;
                        }
                        updateTotalKeseluruhan();
                    });
                });
            }

            function updateTotalKeseluruhan() {
                let totalKeseluruhan = 0;
                const jumlahInput = document.querySelectorAll('.jumlah-input');
                jumlahInput.forEach(input => {
                    const row = input.closest('tr');
                    const hargaInput = row.querySelector('input[name="harga[]"]');
                    const harga = parseFloat(hargaInput.value) || 0;
                    const jumlah = parseInt(input.value) || 0;
                    totalKeseluruhan += jumlah * harga;
                });
                totalHargaKeseluruhan.value = totalKeseluruhan;
            }
            //untuk menghitung kembalian
            nominalBayarKeseluruhan.addEventListener('input', function() {
                const nominalBayar = parseFloat(this.value) || 0;
                const totalHarga = parseFloat(totalHargaKeseluruhan.value) || 0;
                kembalianKeseluruhanInput.value = nominalBayar - totalHarga;
            })

        });
    </script>
</body>

</html>