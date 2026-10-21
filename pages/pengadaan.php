<?php
include __DIR__ . "/../config/koneksi.php";
include __DIR__ . "/../includes/header.php";

$hasil = mysqli_query($conn, "SELECT * FROM v_total_pengadaan_vendor");
?>

<h1>Total Pengadaan per Vendor</h1>
<table>
    <tr>
        <th>Vendor</th>
        <th>Jumlah Pengadaan</th>
        <th>Total Nilai</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>
    <tr>
        <td><?= $row['nama_vendor'] ?></td>
        <td><?= $row['jumlah_pengadaan'] ?></td>
        <td>Rp <?= number_format($row['total_nilai'], 0, ',', '.') ?></td>
    </tr>
    <?php } ?>
</table>

<?php include __DIR__ . "/../includes/footer.php"; ?>