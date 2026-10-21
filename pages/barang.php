<?php
include __DIR__ . "/../config/koneksi.php";
include __DIR__ . "/../includes/header.php";

$hasil = mysqli_query($conn, "SELECT * FROM v_barang");
?>

<h1>Daftar Barang</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Nama Barang</th>
        <th>Satuan</th>
        <th>Harga</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>
    <tr>
        <td><?= $row['idbarang'] ?></td>
        <td><?= $row['nama'] ?></td>
        <td><?= $row['satuan'] ?></td>
        <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
    </tr>
    <?php } ?>
</table>

<?php include __DIR__ . "/../includes/footer.php"; ?>