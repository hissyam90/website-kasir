<?php 
require 'ceklogin.php';

// hitung jumlah pesanan   
$h1 = mysqli_query($con,"select * from masuk");
$h2 = mysqli_num_rows($h1);

$h3 = mysqli_query($con, "SELECT * FROM pesanan");
$h4 = mysqli_num_rows($h3);

$h5 = mysqli_query($con, "SELECT * FROM pelanggan");
$h6 = mysqli_num_rows($h5);

$level = $_SESSION['level']; // Ambil level dari session

if($level == 'Administrator') {
    // Tampilan atau fitur untuk admin
} elseif($level == 'Petugas') {
    // Tampilan atau fitur untuk petugas
} else {
    // Fitur default atau tidak ada akses
}

// Query untuk menghitung jumlah data arsip per tanggal
$query = "SELECT DATE(tanggal) AS tanggal, COUNT(*) AS total_records FROM archive GROUP BY DATE(tanggal)";
$result = mysqli_query($con, $query);

$tanggal = [];
$total_records = [];

while ($row = mysqli_fetch_assoc($result)) {
    $tanggal[] = $row['tanggal'];  // Menyimpan tanggal
    $total_records[] = (int)$row['total_records']; // Pastikan total_records adalah integer
}



// Query untuk menghitung jumlah barang masuk per tanggal dan mengurutkannya dari yang terbesar
$query = "SELECT DATE(tanggalmasuk) AS tanggal, COUNT(*) AS total_barang_masuk 
          FROM masuk 
          GROUP BY DATE(tanggal) 
          ORDER BY total_barang_masuk DESC";

$result = mysqli_query($con, $query);

// Cek apakah query berhasil
if (!$result) {
    die("Query Error: " . mysqli_error($con));
}

// Mengambil data total barang masuk per tanggal
$tanggal = [];
$total_barang_masuk = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tanggal[] = $row['tanggal']; // Menyimpan tanggal
    $total_barang_masuk[] = (int)$row['total_barang_masuk']; // Menyimpan total barang masuk per tanggal
}

// Query untuk menghitung jumlah pelanggan per tanggal
$query = "SELECT DATE(tanggalpelanggan) AS tanggal, COUNT(*) AS total_records_pelanggan FROM pelanggan GROUP BY DATE(tanggal)";
$result = mysqli_query($con, $query);

$tanggalpelanggan = [];
$total_records_pelanggan = [];

while ($row = mysqli_fetch_assoc($result)) {
    $tanggalpelanggan[] = $row['tanggal'];  // Menyimpan tanggal
    $total_records_pelanggan[] = (int)$row['total_records_pelanggan']; // Pastikan total_records adalah integer
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
        <title>Kasir</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>

    <style>
    div.mycontainer div {  
        width:31%;
        display:inline-block;
        overflow:auto;
        margin-left: 15px;
        padding: 10px;    
    }

    div.mycontainer a {
        color: white;
        cursor: pointer;
    }

    div.mycontainer h2:hover {
    color:turquoise;
    }

    .mychart {
    display: flex;
    overflow: hidden;
    width: 100%;
    gap: 20px; /* menambahkan jarak antara div chart */
    }

    .mychart > div {
    flex: 1; /* memastikan setiap chart mengambil ruang yang sama */
    display: flex; /* untuk mengisi div dengan tepat */
    flex-direction: column; /* mengatur agar isi div menjadi kolom */
    }

    .card-body {
        flex: 1; /* memastikan body card mengisi sisa ruang */
        display: flex; /* menggunakan flex untuk isi card */
        justify-content: center; /* memusatkan isi secara horizontal */
        align-items: center; /* memusatkan isi secara vertikal */
    }

    canvas {
        width: 100% !important; /* mengisi lebar penuh dari div */
        height: 100% !important; /* mengisi tinggi penuh dari div */
    }
    </style>
    
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
                        <div class="small">Logged in as: <?=$level?></div>        
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Welcome</li>
                        </ol>

                    <div class="mycontainer">

                    <!-- masuk.php -->
                    <div class="card bg-primary text-white mb-4" onclick="handleCardClick()">
                        <h2>[<i class="fas fa-truck"></i>] Jumlah Barang Masuk:</h2>
                        <h4><?=$h2;?></h4>
                    </div>
                    
                    <!-- index.php -->
                    <a href="index.php"><div class="card bg-warning text-white mb-4">
                    <h2>[<i class="fa fa-briefcase"></i>] Jumlah Pesanan:</h2>
                    <h4><?=$h4;?></h4>
                    </div>
                    </a>
                    
                    <!-- pelanggan.php -->
                    <a href="pelanggan.php"><div class="card bg-success text-white mb-4">
                    <h2>[<i class="fas fa-user-tie"></i>] Jumlah Pelanggan:</h2>
                    <h4><?=$h6;?></h4>
                    </div>
                    </a>

                    </div>
                     
                    
                    <div class="mychart">
                        
                        <!-- chart pembelian -->
                        <div class="col-xl-6" id="a">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-area me-1"></i>
                                    Chart Total Pembelian per Tanggal
                                </div>
                                <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
                            </div>
                        </div>
                        
                        <!-- chart barang masuk -->
                        <div class="col-lg-6" id="b">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-1"></i>
                            Pie Chart Total Barang Masuk per Tanggal
                        </div>
                        <div class="card-body">
                            <canvas id="myPieChart" width="100%" height="40"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <br>

                        <!-- chart pelanggan masuk -->
                        <div class="col-xl-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Bar Chart Jumlah Pelanggan Berlangganan per Tanggal
                        </div>
                        <div class="card-body"><canvas id="myBarChart" width="50%" height="10"></canvas></div>
                    </div>
                </div>
            </div>
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

            <!-- Modal -->
            <div class="modal fade" id="accessModal" tabindex="-1" aria-labelledby="accessModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="accessModalLabel">Akses Terbatas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Anda tidak memiliki akses ke menu ini.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>            

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


        <script>
        // Get data from PHP
        var tanggal = <?php echo json_encode($tanggal); ?>;
        var total_records = <?php echo json_encode($total_records); ?>;

        // Setup Chart.js
        var ctx = document.getElementById('myAreaChart').getContext('2d');
        var myAreaChart = new Chart(ctx, {
            type: 'line', 
            data: {
                labels: tanggal, 
                datasets: [{
                    label: "Jumlah Pembelian per Tanggal",
                    data: total_records, 
                    backgroundColor: 'rgba(75, 192, 192, 0.2)', 
                    borderColor: 'rgba(75, 192, 192, 1)', 
                    borderWidth: 1,
                    fill: true,
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Jumlah Data'
                        },
                        beginAtZero: true,
                        min: 0, 
                        max: 10, 
                        ticks: {
                            stepSize: 1 
                        }
                    }
                }
            }
        });
        </script>

        <script>
        var tanggals = <?php echo json_encode($tanggal); ?>;
        var total_barang_masuk = <?php echo json_encode($total_barang_masuk); ?>;

        var ctx = document.getElementById("myPieChart");
        var myPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: tanggals,
                datasets: [{
                    label: "Jumlah Barang Masuk",
                    data: total_barang_masuk,
                    backgroundColor: [
                        "rgba(255, 99, 132, 0.5)",
                        "rgba(54, 162, 235, 0.5)",
                        "rgba(255, 206, 86, 0.5)",
                        "rgba(75, 192, 192, 0.5)",
                        "rgba(153, 102, 255, 0.5)",
                        "rgba(255, 159, 64, 0.5)"
                    ],
                    borderColor: [
                        "rgba(255, 99, 132, 1)",
                        "rgba(54, 162, 235, 1)",
                        "rgba(255, 206, 86, 1)",
                        "rgba(75, 192, 192, 1)",
                        "rgba(153, 102, 255, 1)",
                        "rgba(255, 159, 64, 1)"
                    ],
                    borderWidth: 1,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });
        </script>

        <script>
        function handleCardClick() {
            var userLevel = '<?php echo $level; ?>'; // Mengambil level dari PHP
            if (userLevel === 'Petugas') {
                // Menampilkan modal jika level adalah Petugas
                $('#accessModal').modal('show');
            } else {
                // Jika bukan Petugas, redirect ke halaman masuk.php
                window.location.href = 'masuk.php';
            }
        }
        </script>

<script>
        // Get data from PHP
        var tanggalpelanggan = <?php echo json_encode($tanggalpelanggan); ?>;
        var total_records_pelanggan = <?php echo json_encode($total_records_pelanggan); ?>;

        // Setup Chart.js
        var ctx = document.getElementById('myBarChart').getContext('2d');
        var myBarChart = new Chart(ctx, {
            type: 'bar', 
            data: {
                labels: tanggalpelanggan, 
                datasets: [{
                    label: "Jumlah Pelanggan per Tanggal",
                    data: total_records_pelanggan, 
                    backgroundColor: 'rgba(75, 192, 192, 0.2)', 
                    borderColor: 'rgba(75, 192, 192, 1)', 
                    borderWidth: 1,
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Jumlah Data'
                        },
                        beginAtZero: true,
                        min: 0, 
                        max: 10, 
                        ticks: {
                            stepSize: 1 
                        }
                    }
                }
            }
        });
        </script>

    </body>
</html>
