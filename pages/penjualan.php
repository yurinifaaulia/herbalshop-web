<?php
include __DIR__ . "/../config/koneksi.php";
include __DIR__ . "/../includes/header.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$pesan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idbarang = (int) $_POST['idbarang'];
    $jumlah   = (int) $_POST['jumlah'];

    try {
        mysqli_begin_transaction($conn);

        // margin aktif terbaru (dibutuhkan kolom idmargin_penjualan)
        $m = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT idmargin_penjualan FROM margin_penjualan
             WHERE status = 1 ORDER BY created_at DESC LIMIT 1"));
        if (!$m) {
            throw new Exception("Margin aktif belum ada di tabel margin_penjualan");
        }

        // harga dari master barang
        $b = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT harga FROM barang WHERE idbarang = $idbarang"));

        // header penjualan (kasir = user 3)
        mysqli_query($conn,
            "INSERT INTO penjualan (created_at, subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan)
             VALUES (NOW(), 0, 0, 0, 3, {$m['idmargin_penjualan']})");
        $idpenjualan = mysqli_insert_id($conn);

        // detail: subtotal diisi 0, nanti diisi otomatis oleh TRIGGER
        mysqli_query($conn,
            "INSERT INTO detail_penjualan (harga_satuan, jumlah, subtotal, penjualan_idpenjualan, idbarang)
             VALUES ({$b['harga']}, $jumlah, 0, $idpenjualan, $idbarang)");

        // PROCEDURE: jumlahkan subtotal, hitung PPN (FUNCTION), isi total
        mysqli_query($conn, "CALL sp_hitung_total_penjualan($idpenjualan)");

        mysqli_commit($conn);
        $pesan = "<p style='color:green'>Penjualan berhasil disimpan.</p>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $pesan = "<p style='color:red'>Gagal: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

$barang = mysqli_query($conn, "SELECT idbarang, nama FROM v_barang WHERE status = 'Aktif'");
?>

<h1>Penjualan</h1>
<?= $pesan ?>

<form method="post">
    <select name="idbarang" required>
        <?php while ($br = mysqli_fetch_assoc($barang)) { ?>
            <option value="<?= $br['idbarang'] ?>"><?= htmlspecialchars($br['nama']) ?></option>
        <?php } ?>
    </select>
    <input type="number" name="jumlah" min="1" placeholder="Jumlah" required>
    <button type="submit">Jual</button>
</form>

<h2>Riwayat Penjualan</h2>
<table>
    <tr>
        <th>ID</th><th>Barang</th><th>Harga</th><th>Jumlah</th>
        <th>Subtotal</th><th>PPN</th><th>Total</th>
    </tr>
    <?php
    $hasil = mysqli_query($conn,
        "SELECT p.idpenjualan, b.nama, d.harga_satuan, d.jumlah, d.subtotal, p.ppn, p.total_nilai
         FROM penjualan p
         JOIN detail_penjualan d ON d.penjualan_idpenjualan = p.idpenjualan
         JOIN barang b ON b.idbarang = d.idbarang
         ORDER BY p.idpenjualan DESC");
    while ($r = mysqli_fetch_assoc($hasil)) { ?>
    <tr>
        <td><?= $r['idpenjualan'] ?></td>
        <td><?= htmlspecialchars($r['nama']) ?></td>
        <td>Rp <?= number_format($r['harga_satuan'], 0, ',', '.') ?></td>
        <td><?= $r['jumlah'] ?></td>
        <td>Rp <?= number_format($r['subtotal'], 0, ',', '.') ?></td>
        <td>Rp <?= number_format($r['ppn'], 0, ',', '.') ?></td>
        <td>Rp <?= number_format($r['total_nilai'], 0, ',', '.') ?></td>
    </tr>
    <?php } ?>
</table>

<?php include __DIR__ . "/../includes/footer.php"; ?>