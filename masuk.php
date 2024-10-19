<?php
require 'ceklogin.php';

$h1 = mysqli_query($con,"select * from masuk");
$h2 = mysqli_num_rows($h1);

$level = $_SESSION['level']; // Ambil level dari session

if($level == 'Administrator') {
    // Tampilan atau fitur untuk admin
} elseif($level == 'Petugas') {
    // Tampilan atau fitur untuk petugas
} else {
    // Fitur default atau tidak ada akses
}
// Hanya administrator yang boleh mengakses halaman ini
cekAkses($level, 'Administrator');
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Data Barang Masuk</title>
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
                        <a class="nav-link" href="pelanggan.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-tie"></i></div>
                            Kelola Pelanggan
                        </a>
                        <?php elseif($level == 'Petugas'): ?>
                        <?php else: ?>
                        <p>Anda tidak memiliki akses ke menu ini.</p>
                        <?php endif; ?>
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
                    <h1 class="mt-4">Data Barang Masuk</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Berisi stock barang masuk yang produknya sama namun tanggalnya berbeda-beda.</li>
                    </ol>  
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">[<i class="fas fa-truck"></i>] Jumlah Barang Masuk: <?=$h2;?></div>
                            </div> 
                        </div>
                    </div>  
                    <button type="button" class="btn btn-info mb-4 text-white" data-bs-toggle="modal" data-bs-target="#myModal">
                        Tambah Barang Masuk
                    </button>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Data Barang Masuk
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>Jumlah</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    <?php
                                    $get = mysqli_query($con,"select * from masuk m, produk p where m.idproduk=p.idproduk");
                                    $i = 1;
                                    while($p=mysqli_fetch_array($get)){
                                    $namaproduk = $p['namaproduk'];
                                    $deskripsi = $p['deskripsi'];  
                                    $qty = $p['qty'];
                                    $idproduk = $p['idproduk'];
                                    $idmasuk = $p['idmasuk'];     
                                    $tanggal = $p['tanggalmasuk'];
                                    ?>


                                    <tr>
                                        <td><?=$i++;?></td>
                                        <td><?=$namaproduk;?>: <?=$deskripsi;?></td>
                                        <td><?=$qty;?></td>
                                        <td><?=$tanggal;?></td>
                                        <td>
                                            <button type="button" class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#edit<?=$idmasuk;?>">
                                            <i class="fa fa-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete<?=$idmasuk;?>">
                                            <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit -->
                                    <div class="modal fade" id="edit<?=$idmasuk;?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Ubah Data Barang Masuk</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="post">
                                                    <div class="modal-body">
                                                        <input type="text" name="namaproduk" class="form-control" placeholder="Nama Produk" value="<?=$namaproduk;?>: <?=$deskripsi;?>" disabled>
                                                        <input type="number" name="qty" class="form-control mt-2" placeholder="Harga Produk" value="<?=$qty;?>" required>
                                                        <input type="hidden" name="idm" value="<?=$idmasuk;?>">
                                                        <input type="hidden" name="idp" value="<?=$idproduk;?>">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success" name="editdatabarangmasuk">Submit</button>
                                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Hapus -->
                                    <div class="modal fade" id="delete<?=$idmasuk;?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Hapus Data Barang Masuk</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="post">
                                                    <div class="modal-body">
                                                        Apakah Anda yakin ingin menghapus barang ini?
                                                        <input type="hidden" name="idp" value="<?=$idproduk;?>">
                                                        <input type="hidden" name="idm" value="<?=$idmasuk;?>">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success" name="hapusdatabarangmasuk">Submit</button>
                                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>        

                                    <?php 
                                    }; //end while
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
                            <div class="text-muted">&copy; 2024</div>
                            <div>
                            <strong>Version </strong>2024.01.01
                            </div>
                    </div>
                </div>
            </footer>
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
                <div class="modal-body">
                    Pilih Barang
                    <select name="idproduk" class="form-control">
                        <?php 
                        $getproduk = mysqli_query($con,"select * from produk");
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
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="barangmasuk">Submit</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>
</html>
