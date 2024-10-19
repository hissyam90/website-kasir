<?php 
require 'ceklogin.php';

if(isset($_GET['idp'])){
    $idp = $_GET['idp'];
    $ambilnamapelanggan = mysqli_query($con, "SELECT p.*, pl.namapelanggan, p.status FROM pesanan p, pelanggan pl WHERE p.idpelanggan = pl.idpelanggan AND p.idorder = '$idp'");
    $np = mysqli_fetch_array($ambilnamapelanggan);
    $namapel = $np["namapelanggan"];
    $status = $np["status"];  // Ambil status pesanan
} else {
    header('location:index.php');
}

// Initialize the session level if not set
$level = isset($_SESSION['level']) ? $_SESSION['level'] : 'Guest';

// Initialize payment and change from session or default to 0
$jumlahPembayaran = isset($_SESSION['jumlah_pembayaran']) ? $_SESSION['jumlah_pembayaran'] : 0;
$kembalian = isset($_SESSION['kembalian']) ? $_SESSION['kembalian'] : 0;

// Ambil idp dari URL, default 0 jika tidak ada
$idp = isset($_GET['idp']) ? intval($_GET['idp']) : 0;

// Query untuk mengambil data berdasarkan idp
$query = "SELECT * FROM pesanan WHERE idorder = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('i', $idp);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
    $jumlahPembayaran = $order['jumlah_pembayaran'];
    $kembalian = $order['kembalian'];
    $total = $order['total']; // Misalkan total juga ada di database
} else {
    // Jika tidak ditemukan
    $jumlahPembayaran = 0;
    $kembalian = 0;
    $total = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Data Order</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="dashboard.php">Website Kasir</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Menu</div>
                        <a class="nav-link" href="dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas  fa-tachometer"></i></div>
                            Dashboard
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseOrder" aria-expanded="false" aria-controls="collapseOrder">
                            <div class="sb-nav-link-icon"><i class="fa fa-shopping-bag"></i></div>
                            Kasir
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseOrder" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="index.php">
                                    <div class="sb-nav-link-icon"><i class="fa fa-briefcase"></i></div>
                                    Order
                                </a> 
                                <a class="nav-link" href="archive.php">
                                    <div class="sb-nav-link-icon"><i class="fa fa-archive"></i></div>
                                    Arsip Pesanan
                                </a>
                            </nav>
                        </div>
                        <?php if($level == 'Administrator'): ?>
                        <a class="nav-link" href="stock.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tag"></i></div>
                            Stock Barang
                        </a>
                        <a class="nav-link" href="masuk.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-truck"></i></div>
                            Barang Masuk
                        </a>
                        <?php elseif($level == 'Petugas'): ?>
                        <!-- Features for Petugas if any -->
                        <?php else: ?>
                        <p>Anda tidak memiliki akses ke menu ini.</p>
                        <?php endif; ?>
                        <a class="nav-link" href="pelanggan.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-tie"></i></div>
                            Kelola Pelanggan
                        </a>
                        <a class="nav-link" href="logout.php">
                            Logout
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as: <?=$level?></div> 
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Data Pesanan: <?=$idp;?></h1>
                    <h4 class="mt-4">Nama Pelanggan: <?=$namapel;?></h4>
                    
                    <!-- Menampilkan jumlah pembayaran dan kembalian -->
                    <h5 class="mt-4">Jumlah Pembayaran: Rp.<?=number_format($jumlahPembayaran);?></h5>
                    <h5 class="mt-4">Kembalian: Rp.<?=number_format($kembalian);?></h5>

                    <h5 class="mt-4">Total: Rp.<?php 
                    $result = mysqli_query($con, "SELECT SUM(qty * harga) as total FROM detailpesanan p JOIN produk pr ON p.idproduk=pr.idproduk WHERE idpesanan='$idp'");
                    $total = mysqli_fetch_assoc($result)['total'];
                    echo number_format($total);
                    ?></h5>

                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Welcome</li>
                    </ol>  

                    <button type="button" class="btn btn-info mb-4 text-white" data-bs-toggle="modal" data-bs-target="#myModal">
                    Tambah Barang
                    </button>

                    <button type="button" class="btn btn-success mb-4" data-bs-toggle="modal" data-bs-target="#modalPembayaran">Pembayaran</button>

                     
                    

                    <?php if($status == 'Accepted'): ?>
                    <form method="post" action="archive.php">
                        <input type="hidden" name="order_id" value="<?php echo $idp; ?>">
                        <button type="submit" name="move_to_archive" class="btn btn-warning text-white">Move to Archive</button>
                        <!-- Tombol Cetak Struk -->
                    <button type="button" class="btn btn-secondary" onclick="printReceipt()">Cetak Struk</button>
                    </form><br>

                    <?php endif; ?>

                    <script>
                    function printReceipt() {
                        // Membuka halaman baru untuk menampilkan struk
                        window.open('print_receipt.php?idp=<?=$idp;?>', '_blank');
                    }
                    </script>
                    
                    
                    <br>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Data Pesanan
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>Harga Satuan</th>
                                        <th>Jumlah</th>
                                        <th>Sub-total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    
                                <?php
                                $get = mysqli_query($con, "SELECT * FROM detailpesanan p JOIN produk pr ON p.idproduk=pr.idproduk WHERE idpesanan='$idp'");
                                $i = 1;
                                while($p = mysqli_fetch_array($get)){
                                    $idpr = $p['idproduk'];
                                    $iddp = $p['iddetailpesanan'];
                                    $qty = $p['qty'];
                                    $harga = $p['harga'];  
                                    $namaproduk = $p['namaproduk']; 
                                    $desc = $p['deskripsi'];
                                    $subtotal = $qty * $harga;                                     
                                ?>
            
                                <tr>
                                    <td><?=$i++;?></td>
                                    <td><?=$namaproduk;?> (<?=$desc?>)</td>
                                    <td>Rp.<?=number_format($harga);?></td>
                                    <td><?=number_format($qty);?></td>
                                    <td>Rp.<?=number_format($subtotal);?></td>
                                    <td>
                                    <button type="button" class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#edit<?=$idpr;?>">
                                    <i class="fa fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete<?=$idpr;?>">
                                    <i class="fa fa-trash"></i>
                                    </button>
                                    </td>
                                </tr>

                                <!-- Modal Edit -->
                                <div class="modal fade" id="edit<?=$idpr;?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Ubah Data Detail Pesanan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="post">
                                                <div class="modal-body">
                                                    <input type="text" name="namaproduk" class="form-control" placeholder="Nama Produk" value="<?=$namaproduk;?>: <?=$desc;?>" disabled>
                                                    <input type="number" name="qty" class="form-control mt-2" placeholder="Jumlah Produk" value="<?=$qty;?>" required>
                                                    <input type="hidden" name="iddp" value="<?=$iddp;?>">
                                                    <input type="hidden" name="idp" value="<?=$idp;?>">
                                                    <input type="hidden" name="idpr" value="<?=$idpr;?>">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success" name="editdetailpesanan">Submit</button>
                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                               <!-- modal delete -->
                               <div class="modal fade" id="delete<?=$idpr;?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Apakah anda yakin ingin menghapus barang ini?</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <form method="post">


                                <!-- modal body -->                 
                                <div class="modal-body">
                                    Apakah anda yakin ingin menghapus barang ini?
                                    <input type="hidden" name="idp" value="<?=$iddp;?>">
                                    <input type="hidden" name="idpr" value="<?=$idpr;?>">
                                    <input type="hidden" name="idorder" value="<?=$idp;?>">
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success" name="hapusprodukpesanan">Ya</button>
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                </div>
                                </form>

                            </div>
                        </div>
                    </div>

                                <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4">Total</th>
                                        <th>Rp.<?php 
                                        $result = mysqli_query($con, "SELECT SUM(qty * harga) as total FROM detailpesanan p JOIN produk pr ON p.idproduk=pr.idproduk WHERE idpesanan='$idp'");
                                        $total = mysqli_fetch_assoc($result)['total'];
                                        echo number_format($total);
                                        ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <div class="container-fluid px-4">
            <a href="index.php" class="btn btn-primary mb-4">
                Kembali
            </a>
            </div>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">&copy;2024</div>
                        <div>
                        <strong>Version </strong>2024.01.01
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal Pembayaran -->
<div class="modal fade" id="modalPembayaran" tabindex="-1" aria-labelledby="modalPembayaranLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPembayaranLabel">Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="function.php">
                <div class="modal-body">
                    <!-- Tampilkan total harga -->
                    <h5>Total Harga: Rp. <?= number_format($total); ?></h5>

                    <!-- Input jumlah pembayaran -->
                    <div class="form-group mt-3">
                        <label for="jumlahPembayaran">Jumlah Pembayaran</label>
                        <input type="number" id="jumlahPembayaran" name="jumlahPembayaran" class="form-control" required min="<?= $total ?>" placeholder="Masukkan jumlah pembayaran" oninput="hitungKembalian()">
                    </div>

                    <!-- Tampilkan kembalian -->
                    <div class="form-group mt-3">
                        <label for="kembalian">Kembalian</label>
                        <input type="text" id="kembalian" name="kembalian" class="form-control" readonly>
                    </div>

                    <input type="hidden" name="idorder" value="<?= $idp; ?>">
                    <input type="hidden" name="total" value="<?= $total; ?>">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="prosesPembayaran">Proses Pembayaran</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post">

                <!-- modal body -->                 
                <div class="modal-body">
                    Pilih Barang
                    <select name="idproduk" class="form-control" required>
                    <?php 
                    $getproduk = mysqli_query($con,"select * from produk where idproduk not in (select idproduk from detailpesanan where idpesanan='$idp')");
                    while($pl=mysqli_fetch_array($getproduk)){
                    $namaproduk = $pl['namaproduk'];
                    $stock = $pl['stock'];
                    $deskripsi = $pl['deskripsi']; 
                    $idproduk = $pl['idproduk']; 
                    ?>
                    <option value="<?=$idproduk;?>"><?=$namaproduk;?> - <?=$deskripsi;?> (Stock: <?=$stock;?>)</option>
                    <?php   
                    }
                    ?>
                    </select>
                    <input type="number" name="qty" class="form-control mt-4" placeholder="Jumlah" min="1" required>
                    <input type="hidden" name="idp" value="<?=$idp;?>">
                    </div>

                    <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="addproduk">Submit</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
                </form>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script src="js/datatables-simple-demo.js"></script>
    <script>
    // Fungsi untuk menghitung kembalian
    function hitungKembalian() {
        const total = <?= $total; ?>;
        const pembayaran = document.getElementById('jumlahPembayaran').value;
        const kembalian = pembayaran - total;

        document.getElementById('kembalian').value = kembalian >= 0 ? `Rp. ${kembalian.toLocaleString()}` : 'Pembayaran tidak cukup';
    }
</script>
</body>
</html>


