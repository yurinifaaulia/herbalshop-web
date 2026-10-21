<?php
include __DIR__ . "/../config/koneksi.php";
include __DIR__ . "/../includes/fungsi.php";
include __DIR__ . "/../includes/header.php";
?>
<h1>Margin Penjualan</h1>
<?php tampil_view($conn, "v_margin_penjualan"); ?>
<?php include __DIR__ . "/../includes/footer.php"; ?>