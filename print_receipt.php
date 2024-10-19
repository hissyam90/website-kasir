<?php 
require 'ceklogin.php';

// atur waktu ke jekardah
date_default_timezone_set('Asia/Jakarta');

// ambil idp dari url
if(isset($_GET['idp'])){
    $idp = $_GET['idp'];
    
    // kueri ambil data pesanan
    $orderQuery = mysqli_query($con, "SELECT p.*, pl.namapelanggan FROM pesanan p JOIN pelanggan pl ON p.idpelanggan = pl.idpelanggan WHERE p.idorder = '$idp'");
    $order = mysqli_fetch_assoc($orderQuery);
} else {
    echo "Pesanan tidak ditemukan.";
    exit();
}

// jakarta + 1 jam = samarinda
$currentDateTime = new DateTime();
$currentDateTime->add(new DateInterval('PT1H'));
$localTime = $currentDateTime->format('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .receipt { max-width: 400px; margin: auto; border: 1px solid #ccc; padding: 20px; }
        .header, .footer { text-align: center; }
        .details { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
        .total { font-weight: bold; }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="header">
            <h2>Struk Pesanan</h2>
            <p>Tanggal: <?= $localTime; ?></p>
        </div>
        <div class="details">
            <p>Pelanggan: <?= htmlspecialchars($order['namapelanggan']); ?></p>
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // kueri ambil detail pesanan wak
                    $detailsQuery = mysqli_query($con, "SELECT * FROM detailpesanan dp JOIN produk pr ON dp.idproduk = pr.idproduk WHERE dp.idpesanan='$idp'");
                    while($detail = mysqli_fetch_assoc($detailsQuery)) { ?>
                    <tr>
                        <td><?= htmlspecialchars($detail['namaproduk']); ?></td>
                        <td><?= htmlspecialchars($detail['qty']); ?></td>
                        <td>Rp.<?= number_format($detail['harga']); ?></td>
                        <td>Rp.<?= number_format($detail['qty'] * $detail['harga']); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="footer">
        <p style="text-align: right;">Total: Rp.<?php 
            // kueri buat total
            $result = mysqli_query($con, "SELECT SUM(qty * harga) as total FROM detailpesanan dp JOIN produk pr ON dp.idproduk=pr.idproduk WHERE dp.idpesanan='$idp'");
            $total = mysqli_fetch_assoc($result)['total'];
            echo number_format($total, 0, ',', '.');
        ?></p>
        <p style="text-align: right;">Jumlah Pembayaran: Rp.<?= number_format($order['jumlah_pembayaran'], 0, ',', '.'); ?></p>
        <p style="text-align: right;">Kembalian: Rp.<?= number_format($order['kembalian'], 0, ',', '.'); ?></p>
        </div>
    </div>
</body>
</html>
