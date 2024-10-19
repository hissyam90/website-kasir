
<?php 
require 'ceklogin.php';

// hitung jumlah pesanan   
$h1 = mysqli_query($con,"select * from archive");
$h2 = mysqli_num_rows($h1);

$level = $_SESSION['level']; // Ambil level dari session

if ($level == 'Administrator') {
    // Tampilan atau fitur untuk admin
} elseif ($level == 'Petugas') {
    // Tampilan atau fitur untuk petugas
} else {
    // Fitur default atau tidak ada akses
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
        <title>Data Arsip Pesanan</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3" href="dashboard.php">Website Kasir</a>
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
                                <!-- Order Dropdown -->
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
                            <!-- End Order Dropdown -->
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
                            <?php else: ?>
                                <p>Anda tidak memiliki akses ke menu ini.</p>
                            <?php endif; ?>
                            <a class="nav-link" href="pelanggan.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-tie"></i></div>
                                Kelola Pelanggan
                            <a class="nav-link" href="logout.php">
                                Logout
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as: <?=$_SESSION['level'];?></div>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Data Arsip Pesanan</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active"><vite>Berisi data orderan yang sudah berstatus accepted dan di arsipkan.</vite></li>
                        </ol>
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">[<i class="fa fa-archive"></i>] Jumlah Arsip: <?=$h2;?></div>
                                    </div>
                                </div>
                            </div> 

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Arsip Pesanan
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>ID Pesanan</th>
                                            <th>Nama Pelanggan</th>
                                            <th>Tanggal</th>
                                            <th>Total Harga</th>
                                            <th>Aksi</th>
                                        </tr>                            
                                    </thead>
                                    <tbody>
                                    <?php 
// Query untuk mengambil data arsip pesanan
$query = "
    SELECT p.idorder, p.tanggal, pl.namapelanggan, dp.qty, pr.harga 
    FROM archive p 
    JOIN pelanggan pl ON p.idpelanggan = pl.idpelanggan 
    JOIN detailpesanan dp ON dp.idpesanan = p.idorder
    JOIN produk pr ON dp.idproduk = pr.idproduk 
    ORDER BY p.idorder DESC
";

$get = mysqli_query($con, $query);

if ($get) {
    $i = 1;
    $current_order_id = null;
    $totalharga = 0;

    while ($p = mysqli_fetch_array($get)) {
        // Jika ID pesanan berubah, tampilkan total harga untuk pesanan sebelumnya
        if ($current_order_id !== $p['idorder']) {
            if ($current_order_id !== null) {
                // Tampilkan pesanan sebelumnya
                echo "<tr>
                    <td>$i</td>
                    <td>$current_order_id</td>
                    <td>$current_namapelanggan</td>
                    <td>$current_tanggal</td>
                    <td>Rp. " . number_format($totalharga) . "</td>
                    <td>
                        <a href='view2.php?idp=$current_order_id' class='btn btn-primary'><i class='fa fa-edit'></i></a>
                        <button type='button' class='btn btn-warning text-white' data-bs-toggle='modal' data-bs-target='#unarchive_$current_order_id'><i class='fa fa-undo'></i></button>
                    </td>
                </tr>";

                // Tambahkan modal untuk setiap pesanan
                echo "
                <!-- Modal Unarchive -->
                <div class='modal fade' id='unarchive_$current_order_id' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='exampleModalLabel'>Unarchive Pesanan</h5>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                Apakah Anda yakin ingin mengembalikan pesanan ID $current_order_id ke tabel pesanan?
                            </div>
                            <div class='modal-footer'>
                                <form method='POST' action=''>
                                    <input type='hidden' name='order_id' value='$current_order_id'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
                                    <button type='submit' name='unarchive_order' class='btn btn-warning text-white'>Unarchive</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>";

                $i++;
                $totalharga = 0; // Reset total harga untuk pesanan baru
            }

            // Set nilai untuk pesanan baru
            $current_order_id = $p['idorder'];
            $current_namapelanggan = $p['namapelanggan'];
            $current_tanggal = $p['tanggal'];
        }

        // Tambahkan subtotal ke total harga
        $totalharga += $p['qty'] * $p['harga'];
    }

    // Tampilkan pesanan terakhir di luar loop
    if ($current_order_id !== null) {
        echo "<tr>
            <td>$i</td>
            <td>$current_order_id</td>
            <td>$current_namapelanggan</td>
            <td>$current_tanggal</td>
            <td>Rp. " . number_format($totalharga) . "</td>
            <td>
                <a href='view2.php?idp=$current_order_id' class='btn btn-primary'><i class='fa fa-edit'></i></a>
                <button type='button' class='btn btn-warning text-white' data-bs-toggle='modal' data-bs-target='#unarchive_$current_order_id'><i class='fa fa-undo'></i></button>
            </td>
        </tr>";

        // Tambahkan modal untuk pesanan terakhir
        echo "
        <!-- Modal Unarchive -->
        <div class='modal fade' id='unarchive_$current_order_id' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='exampleModalLabel'>Unarchive Pesanan</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        Apakah Anda yakin ingin mengembalikan pesanan ID $current_order_id ke tabel pesanan?
                    </div>
                    <div class='modal-footer'>
                        <form method='POST' action=''>
                            <input type='hidden' name='order_id' value='$current_order_id'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
                            <button type='submit' name='unarchive_order' class='btn btn-warning text-white'>Unarchive</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>";
    }
}
?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>

