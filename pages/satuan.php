<?php
include __DIR__ . "/../config/koneksi.php";
include __DIR__ . "/../includes/fungsi.php";
include __DIR__ . "/../includes/header.php";
?>
<h1>Data Satuan</h1>
<?php tampil_view($conn, "v_satuan"); ?>
<?php include __DIR__ . "/../includes/footer.php"; ?>