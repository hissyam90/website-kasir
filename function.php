<?php

session_start();    
$con = mysqli_connect('localhost','root','','kasir');

//login
if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check = mysqli_query($con,"SELECT * FROM user WHERE username='$username' AND password='$password'");
    $hitung = mysqli_num_rows($check);

    if($hitung > 0){
        $data = mysqli_fetch_assoc($check);

        // Simpan informasi login dan level ke session
        $_SESSION['login'] = true;
        $_SESSION['level'] = $data['role'];

        header('location:dashboard.php');
    } else {
        echo '
        <script>alert("Username atau password salah!");
        window.location.href="login.php";
        </script>
        ';
    } 
}

// Proses registrasi
if(isset($_POST['register'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Cek apakah username sudah ada
    $cekUser = mysqli_query($con, "SELECT * FROM user WHERE username='$username'");
    if(mysqli_num_rows($cekUser) > 0){
        echo "<script>alert('Username sudah terdaftar!');</script>";
    } else {
        // Insert ke database
        $insert = mysqli_query($con, "INSERT INTO user (username, password, role) VALUES ('$username', '$password', '$role')");
        if($insert){
            echo "<script>alert('Registrasi berhasil!');</script>";
        } else {
            echo "<script>alert('Registrasi gagal!');</script>";
        }
    }
}

if(isset($_POST['tambahbarang'])){
    $namaproduk = $_POST['namaproduk'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];
    $harga = $_POST['harga'];

    $insert = mysqli_query($con,"insert into produk (namaproduk,deskripsi,harga,stock) values ('$namaproduk','$deskripsi','$harga','$stock')");
    
    if($insert){
        header('location:stock.php');
        exit();
    } else {
        echo '
        <script>alert("Gagal menambah barang baru!");
        window.location.href="stock.php"
        </script>
        ';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ido = $_POST['ido'];

    if (isset($_POST['change_status'])) {
        $newstatus = $_POST['change_status'];
        $query = "UPDATE pesanan SET status='$newstatus' WHERE idorder='$ido'";
    } elseif (isset($_POST['returnstatus'])) {
        $returnstatus = $_POST['returnstatus'];
        $query = "UPDATE pesanan SET status='$returnstatus' WHERE idorder='$ido'";
    }

    if (isset($query)) {
        mysqli_query($con, $query);
    }

    header('Location: index.php'); // Redirect back to index.php
}


if(isset($_POST['tambahpelanggan'])){
    $namapelanggan = $_POST['namapelanggan'];
    $notelp = $_POST['notelp'];
    $alamat = $_POST['alamat'];

    $insert = mysqli_query($con,"insert into pelanggan (namapelanggan,notelp,alamat) values ('$namapelanggan','$notelp','$alamat')");
    if($insert){
        header('location:pelanggan.php');
    } else {
        echo '
        <script>alert("Gagal menambah pelanggan baru!");
        window.location.href="pelanggan.php"
        </script>
        ';
    }
}

if(isset($_POST['tambahpesanan'])){
    // Cek token
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['form_token']) {
        echo '<script>alert("Invalid submission!"); window.location.href="index.php";</script>';
        exit;
    }
    
    // Setelah validasi, hapus token untuk menghindari reuse
    unset($_SESSION['form_token']);
    
    $idpelanggan = $_POST['idpelanggan'];
    
    $insert = mysqli_query($con,"INSERT INTO pesanan (idpelanggan) VALUES ('$idpelanggan')");
    if($insert){
        header('location:index.php');
    } else {
        echo '<script>alert("Gagal menambah pesanan baru!"); window.location.href="index.php";</script>';
    }
}


if(isset($_POST['addproduk'])){
    $idproduk = $_POST['idproduk'];
    $idp = $_POST['idp'];
    $qty = $_POST['qty'];


   // hitung stock
   $hitung1 = mysqli_query($con,"select * from produk where idproduk='$idproduk'");
   $hitung2 = mysqli_fetch_array($hitung1);
   $stocksekarang = $hitung2['stock'];
   if($stocksekarang>=$qty){
    // ngurangin stock kalau di input
    $selisih = $stocksekarang-$qty;
        // stock cukup
        $insert = mysqli_query($con,"insert into detailpesanan (idpesanan,idproduk,qty) values ('$idp','$idproduk','$qty')");
        $update = mysqli_query($con,"update produk set stock='$selisih' where idproduk='$idproduk'");
        if($insert&&$update){
            header('location:view.php?idp='.$idp);
        } else {
            echo '
            <script>alert("Gagal menambah pesanan baru!");
            window.location.href="view.php?idp='.$idp.'"
            </script>
            ';
        }
   } else {
    // stock gacukup
    echo '
        <script>alert("Stocknya ga cukup, tunggu restock ya!");
        window.location.href="view.php?idp='.$idp.'"
        </script>
        ';

   }
}

// nambah barang masuk
if(isset($_POST['barangmasuk'])){
    $idproduk = $_POST['idproduk'];
    $qty = $_POST['qty'];

    // cari tau stock nya sekarang brp
    $caristock = mysqli_query($con,"select * from produk where idproduk='$idproduk'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stocksekarang = $caristock2['stock'];

    //hitung
    $newstock =  $stocksekarang+$qty;

    $insertb = mysqli_query($con,"insert into masuk (idproduk,qty) values('$idproduk','$qty')");
    $updatetb = mysqli_query($con,"update produk set stock='$newstock' where idproduk='$idproduk'");

    if ($insertb&&$updatetb){
        header("location:masuk.php");
    } else {
        echo '
        <script>alert("Gagal menambah barang masuk!");
        window.location.href="masuk.php"
        </script>
        ';
    }
}

//delete
if(isset($_POST['hapusprodukpesanan'])){
    $idp = $_POST['idp'];
    $idpr = $_POST['idpr']; 
    $idorder = $_POST['idorder'];

    // cek qty
    $cek1 = mysqli_query($con,"select * from detailpesanan where iddetailpesanan='$idp'");
    $cek2 = mysqli_fetch_array($cek1);
    $qtysekarang = $cek2['qty'];
    // cek stok
    $cek3 = mysqli_query($con,"select * from produk where idproduk='$idpr'");
    $cek4 = mysqli_fetch_array($cek3);
    $stocksekarang = $cek4['stock'];

    $hitung = $stocksekarang+$qtysekarang;
    $update = mysqli_query($con,"update produk set stock='$hitung' where idproduk='$idpr'");
    $hapus = mysqli_query($con,"delete from detailpesanan where idproduk='$idpr' and iddetailpesanan='$idp'");
    if($update&&$hapus){
        header('location:view.php?idp='.$idorder);
} else {
    echo '
        <script>alert("Gagal menghapus barang!");
        window.location.href="view.php?idp='.$idorder.'"
        </script>
        ';
    }
}

// edit barang
if(isset($_POST['editbarang'])){
    $namaproduk = $_POST['namaproduk'];
    $desc = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $idp = $_POST['idp']; //idproduk

    $query = mysqli_query($con,"update produk set namaproduk='$np', deskripsi='$desc', harga='$harga' where idproduk='$idp'");
    if ($query){
        header("location:stock.php");
    } else {
        echo '
        <script>alert("Gagal!");
        window.location.href="stock.php"
        </script>
        ';

    }
}

//hapus barang
if(isset($_POST['deleteitem'])){
    $idp = $_POST ['idp'];
    $query = mysqli_query($con,"delete from produk where idproduk='$idp'");
    if ($query){
        header("location:stock.php");
    } else {
        echo '
        <script>alert("Gagal!");
        window.location.href="stock.php"
        </script>
        ';

    }
}

//edit pelanggan
if(isset($_POST['editpelanggan'])){
    $np = $_POST['namapelanggan'];
    $nt = $_POST['notelp'];
    $a = $_POST['alamat'];
    $id = $_POST['idpl'];

    $query = mysqli_query($con,"update pelanggan set namapelanggan='$np', notelp='$nt', alamat='$a' where idpelanggan='$id'");
    if ($query){
        header("location:pelanggan.php");
    } else {
        echo '
        <script>alert("Gagal!");
        window.location.href="pelanggan.php"
        </script>
        ';

    }
}

//hapus pelanggan
if(isset($_POST['hapuspelanggan'])){
    $idpl = $_POST ['idpl'];
    $query = mysqli_query($con,"delete from pelanggan where idpelanggan='$idpl'");
    if ($query){
        header("location:pelanggan.php");
    } else {
        echo '
        <script>alert("Gagal!");
        window.location.href="pelanggan.php"
        </script>
        ';

    }
}


//mengubah data barang masuk   
if(isset($_POST['editdatabarangmasuk'])){
    $qty = $_POST['qty'];
    $idm = $_POST['idm']; //id masuk
    $idp = $_POST['idp']; //id produk   


    // cari tau qtynya brp
    $caritahu = mysqli_query($con,"select * from masuk where idmasuk='$idm'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang = $caritahu2['qty'];

    // cari tau stock nya sekarang brp
    $caristock = mysqli_query($con,"select * from produk where idproduk='$idp'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stocksekarang = $caristock2['stock'];

    if($qty >= $qtysekarang){
        // kalau input user lebih besar dari qty sekarang
        // hitung selisih
        $selisih = $qty-$qtysekarang;
        $newstock = $stocksekarang+$selisih;

        $query1 = mysqli_query($con,"update masuk set qty='$qty' where idmasuk='$idm'");
        $query2 = mysqli_query($con,"update produk set stock='$newstock' where idproduk='$idp'");
        

    if ($query1&&$query2){
        header("location:masuk.php");
    } else {
        echo '
        <script>alert("Gagal!");
        window.location.href="masuk.php"
        </script>
        ';

        }
    } else {
        // kalau lebih kecil
        // hitung selisih
        $selisih = $qtysekarang-$qty;
        $newstock = $stocksekarang-$selisih;

        $query1 = mysqli_query($con,"update masuk set qty='$qty' where idmasuk='$idm'");
        $query2 = mysqli_query($con,"update produk set stock='$newstock' where idproduk='$idp'");
        
    if ($query1&&$query2){
        header("location:masuk.php");
    } else {
        echo '
        <script>alert("Gagal!");
        window.location.href="masuk.php"
        </script>
        ';

    }
    }

    
}


// hapus data barang masuk
if(isset($_POST['hapusdatabarangmasuk'])){
    $idm = $_POST ['idm'];
    $idp = $_POST ['idp'];

    // cari tau qtynya brp
    $caritahu = mysqli_query($con,"select * from masuk where idmasuk='$idm'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang = $caritahu2['qty'];

    // cari tau stock nya sekarang brp
    $caristock = mysqli_query($con,"select * from produk where idproduk='$idp'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stocksekarang = $caristock2['stock'];

    // hitung selisih
    $newstock = $stocksekarang-$qtysekarang;

    $query1 = mysqli_query($con,"delete from masuk where idmasuk='$idm'");
    $query2 = mysqli_query($con,"update produk set stock='$newstock' where idproduk='$idp'");
        
    if ($query1&&$query2){
        header("location:masuk.php");
    } else {
        echo '
        <script>alert("Gagal!");
        window.location.href="masuk.php"
        </script>
        ';

    }
}

if (isset($_POST['hapusorder'])) {
    $ido = $_POST['ido']; // idorder

    $cekdata = mysqli_query($con, "SELECT * FROM detailpesanan WHERE idpesanan='$ido'");

    $success = true; // Flag untuk melacak apakah semua operasi berhasil

    while ($ok = mysqli_fetch_array($cekdata)) {
        // Balikin stok
        $qty = $ok['qty'];
        $idproduk = $ok['idproduk'];
        $iddp = $ok['iddetailpesanan'];

        // Cari tahu stok sekarang berapa
        $caristock = mysqli_query($con, "SELECT * FROM produk WHERE idproduk='$idproduk'");
        if ($caristock) {
            $caristock2 = mysqli_fetch_array($caristock);
            $stocksekarang = $caristock2['stock'];

            $newstock = $stocksekarang + $qty;

            $queryupdate = mysqli_query($con, "UPDATE produk SET stock='$newstock' WHERE idproduk='$idproduk'");
            if (!$queryupdate) {
                $success = false;
                break; // Keluar dari loop jika ada kegagalan
            }
        } else {
            $success = false;
            break;
        }

        // Hapus data detail pesanan
        $querydelete = mysqli_query($con, "DELETE FROM detailpesanan WHERE iddetailpesanan='$iddp'");
        if (!$querydelete) {
            $success = false;
            break;
        }
    }

    // Hapus data pesanan utama jika semua operasi sebelumnya berhasil
    if ($success) {
        $query = mysqli_query($con, "DELETE FROM pesanan WHERE idorder='$ido'");
        if ($query) {
            header("Location: index.php");
            exit; // Pastikan script berhenti setelah redirect
        } else {
            echo '<script>alert("Gagal menghapus pesanan!"); window.location.href="index.php";</script>';
        }
    } else {
        echo '<script>alert("Gagal memperbarui stok atau menghapus detail pesanan!"); window.location.href="index.php";</script>';
    }
}


//mengubah data detail pesanan   
if(isset($_POST['editdetailpesanan'])){
    $qty = $_POST['qty'];
    $iddp = $_POST['iddp']; //id detail pesanan
    $idpr = $_POST['idpr']; //id produk   
    $idp = $_POST['idp']; //id pesanan   



    // cari tau qtynya brp
    $caritahu = mysqli_query($con,"select * from detailpesanan where iddetailpesanan='$iddp'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang = $caritahu2['qty'];

    // cari tau stock nya sekarang brp
    $caristock = mysqli_query($con,"select * from produk where idproduk='$idpr'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stocksekarang = $caristock2['stock'];

    if($qty >= $qtysekarang){
        // kalau input user lebih besar dari qty sekarang
        // hitung selisih
        $selisih = $qty-$qtysekarang;
        $newstock = $stocksekarang-$selisih;

        $query1 = mysqli_query($con,"update detailpesanan set qty='$qty' where iddetailpesanan='$iddp'");
        $query2 = mysqli_query($con,"update produk set stock='$newstock' where idproduk='$idpr'");
        

    if ($query1&&$query2){
        header('location:view.php?idp='.$idp);
    } else {
        echo '
        <script>alert("Gagal!");
        window.location.href="view.php?idp='.$idp.'"
        </script>
        ';

        }
    } else {
        // kalau lebih kecil
        // hitung selisih
        $selisih = $qtysekarang-$qty;
        $newstock = $stocksekarang+$selisih;

        $query1 = mysqli_query($con,"update detailpesanan set qty='$qty' where iddetailpesanan='$iddp'");
        $query2 = mysqli_query($con,"update produk set stock='$newstock' where idproduk='$idpr'");
        
    if ($query1&&$query2){
        header('location:view.php?idp='.$idp);
    } else {
        echo '
        <script>alert("Gagal!");
        window.location.href="view.php?idp='.$idp.'"
        </script>
        ';

    }
    } 
}


// Cek apakah ada data yang dikirim untuk unarchive
if (isset($_POST['unarchive_order'])) {
    $order_id = intval($_POST['order_id']);

    // Mulai transaksi
    mysqli_begin_transaction($con);

    try {
        // Pindahkan data ke tabel pesanan
        $query = "INSERT INTO pesanan (idorder, tanggal, idpelanggan, status, jumlah_pembayaran, kembalian)
                  SELECT idorder, tanggal, idpelanggan, status, jumlah_pembayaran, kembalian
                  FROM archive
                  WHERE idorder = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $order_id);
        mysqli_stmt_execute($stmt);

        // Hapus data dari tabel archive
        $query = "DELETE FROM archive WHERE idorder = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $order_id);
        mysqli_stmt_execute($stmt);

        // Commit transaksi
        mysqli_commit($con);

        // Redirect ke index.php setelah berhasil
        header('Location: index.php');
        exit();
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi error
        mysqli_rollback($con);
        echo "Error: " . $e->getMessage();
    }
}


if (isset($_POST['move_to_archive'])) {
    $order_id = intval($_POST['order_id']);

    // Mulai transaksi
    mysqli_begin_transaction($con);

    try {
        // Pindahkan data ke tabel archive
        $query = "INSERT INTO archive (idorder, tanggal, idpelanggan, status, jumlah_pembayaran, kembalian)
                  SELECT idorder, tanggal, idpelanggan, status, jumlah_pembayaran, kembalian
                  FROM pesanan
                  WHERE idorder = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $order_id);
        mysqli_stmt_execute($stmt);

        // Hapus data dari tabel pesanan
        $query = "DELETE FROM pesanan WHERE idorder = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $order_id);
        mysqli_stmt_execute($stmt);

        // Commit transaksi
        mysqli_commit($con);

        // Redirect ke index.php setelah berhasil
        header('Location: archive.php');
        exit();
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi error
        mysqli_rollback($con);
        echo "Error: " . $e->getMessage();
    }
}

// Process payment
if (isset($_POST['prosesPembayaran'])) {
    $idorder = $_POST['idorder'];
    $jumlahPembayaran = $_POST['jumlahPembayaran'];
    $total = $_POST['total']; // Assuming you pass total from the modal to the form
    $kembalian = $jumlahPembayaran - $total;

    // Update the payment and change in the database
    $query = "UPDATE pesanan SET jumlah_pembayaran = ?, kembalian = ? WHERE idorder = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('dds', $jumlahPembayaran, $kembalian, $idorder);
    
    if ($stmt->execute()) {
        $_SESSION['jumlah_pembayaran'] = $jumlahPembayaran;
        $_SESSION['kembalian'] = $kembalian;
        header("Location: view.php?idp=$idorder");
    } else {
        echo "Error: " . $stmt->error;
    }
}

if (isset($_POST['return_to_order'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['new_status']; // Status yang diinginkan setelah dikembalikan

    // Mulai transaksi
    mysqli_begin_transaction($con);

    try {
        // Ambil data dari tabel archive
        $query = "SELECT idorder, tanggal, idpelanggan, status FROM archive WHERE idorder = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $order_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        if ($data) {
            // Insert data ke tabel pesanan
            $query = "INSERT INTO pesanan (idorder, tanggal, idpelanggan, status) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, 'isis', $data['idorder'], $data['tanggal'], $data['idpelanggan'], $new_status);
            mysqli_stmt_execute($stmt);

            // Hapus data dari tabel archive
            $query = "DELETE FROM archive WHERE idorder = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, 'i', $order_id);
            mysqli_stmt_execute($stmt);

            // Commit transaksi
            mysqli_commit($con);

            // Redirect ke index.php setelah berhasil
            header('Location: index.php');
            exit();
        } else {
            throw new Exception('Order not found in archive.');
        }
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi error
        mysqli_rollback($con);
        echo "Error: " . $e->getMessage();
    }
}

?>