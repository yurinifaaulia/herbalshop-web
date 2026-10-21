<?php
include __DIR__ . "/../config/koneksi.php";
include __DIR__ . "/../includes/header.php";

$hasil = mysqli_query($conn, "SELECT * FROM v_vendor_aktif");
?>

<h1>Vendor Aktif</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Nama Vendor</th>
        <th>Badan Hukum</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>
    <tr>
        <td><?= $row['idvendor'] ?></td>
        <td><?= $row['nama_vendor'] ?></td>
        <td><?= $row['badan_hukum'] == '1' ? 'Ya' : 'Tidak' ?></td>
    </tr>
    <?php } ?>
</table>

<?php include __DIR__ . "/../includes/footer.php"; ?>