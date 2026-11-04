<?php
include __DIR__ . "/../config/koneksi.php";
include __DIR__ . "/../includes/fungsi.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$error = "";
$edit = ['idbarang' => 0, 'jenis' => '', 'nama' => '', 'idsatuan' => 0, 'harga' => ''];

// TAMBAH / UBAH
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = (int) ($_POST['idbarang'] ?? 0);
    $jenis    = strtoupper(trim($_POST['jenis']));
    $nama     = trim($_POST['nama']);
    $idsatuan = (int) $_POST['idsatuan'];
    $harga    = (int) $_POST['harga'];

    try {
        if ($id > 0) {
            $stmt = mysqli_prepare($conn,
                "UPDATE barang SET jenis=?, nama=?, idsatuan=?, harga=? WHERE idbarang=?");
            mysqli_stmt_bind_param($stmt, "ssiii", $jenis, $nama, $idsatuan, $harga, $id);
            $msg = "diubah";
        } else {
            $stmt = mysqli_prepare($conn,
                "INSERT INTO barang (jenis, nama, idsatuan, harga, status) VALUES (?, ?, ?, ?, 1)");
            mysqli_stmt_bind_param($stmt, "ssii", $jenis, $nama, $idsatuan, $harga);
            $msg = "ditambah";
        }
        mysqli_stmt_execute($stmt);
        header("Location: barang.php?msg=$msg");
        exit;
    } catch (mysqli_sql_exception $e) {
        $error = $e->getMessage();
        $edit = ['idbarang' => $id, 'jenis' => $jenis, 'nama' => $nama,
                 'idsatuan' => $idsatuan, 'harga' => $harga];
    }
}

// AKTIFKAN / NONAKTIFKAN (pengganti hapus)
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    mysqli_query($conn, "UPDATE barang SET status = 1 - status WHERE idbarang = $id");
    header("Location: barang.php?msg=status");
    exit;
}

// ISI FORM SAAT EDIT
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $r = mysqli_query($conn, "SELECT * FROM barang WHERE idbarang = $id");
    if ($row = mysqli_fetch_assoc($r)) {
        $edit = $row;
    }
}

$daftar_pesan = [
    'ditambah' => 'Data berhasil ditambah.',
    'diubah'   => 'Data berhasil diubah.',
    'status'   => 'Status berhasil diubah.',
];
$pesan = $daftar_pesan[$_GET['msg'] ?? ''] ?? '';

$satuan = mysqli_query($conn,
    "SELECT idsatuan, nama_satuan FROM satuan
     WHERE status = 1 OR idsatuan = " . (int) $edit['idsatuan']);
$data = mysqli_query($conn, "SELECT * FROM v_barang ORDER BY idbarang");

include __DIR__ . "/../includes/header.php";
?>

<h1>Data Barang</h1>

<?php if ($pesan) { ?><p class="pesan-ok"><?= $pesan ?></p><?php } ?>
<?php if ($error) { ?><p class="pesan-err">Gagal: <?= htmlspecialchars($error) ?></p><?php } ?>

<form method="post" class="crud">
    <h3><?= $edit['idbarang'] ? 'Ubah Barang' : 'Tambah Barang' ?></h3>
    <input type="hidden" name="idbarang" value="<?= (int) $edit['idbarang'] ?>">

    <label>Jenis (1 huruf, contoh: O / S / M)</label>
    <input type="text" name="jenis" maxlength="1" required value="<?= htmlspecialchars($edit['jenis']) ?>">

    <label>Nama Barang</label>
    <input type="text" name="nama" maxlength="45" required value="<?= htmlspecialchars($edit['nama']) ?>">

    <label>Satuan</label>
    <select name="idsatuan" required>
        <?php while ($s = mysqli_fetch_assoc($satuan)) { ?>
            <option value="<?= $s['idsatuan'] ?>" <?= $s['idsatuan'] == $edit['idsatuan'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['nama_satuan']) ?>
            </option>
        <?php } ?>
    </select>

    <label>Harga</label>
    <input type="number" name="harga" min="0" required value="<?= htmlspecialchars($edit['harga']) ?>">

    <p>
        <button type="submit" class="btn">Simpan</button>
        <?php if ($edit['idbarang']) { ?><a href="barang.php" class="btn btn-abu">Batal</a><?php } ?>
    </p>
</form>

<table>
    <tr>
        <th>ID</th><th>Nama Barang</th><th>Satuan</th><th>Harga</th><th>Status</th><th>Aksi</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($data)) { ?>
    <tr>
        <td><?= $row['idbarang'] ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= htmlspecialchars($row['satuan']) ?></td>
        <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
        <td><?= badge_status($row['status']) ?></td>
        <td>
            <a href="?edit=<?= $row['idbarang'] ?>" class="btn btn-kecil">Ubah</a>
            <a href="?toggle=<?= $row['idbarang'] ?>"
               class="btn btn-kecil <?= $row['status'] === 'Aktif' ? 'btn-merah' : '' ?>"
               onclick="return confirm('Ubah status barang ini?')">
               <?= $row['status'] === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
            </a>
        </td>
    </tr>
    <?php } ?>
</table>

<?php include __DIR__ . "/../includes/footer.php"; ?>