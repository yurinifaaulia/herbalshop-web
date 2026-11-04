<?php
include __DIR__ . "/../config/koneksi.php";
include __DIR__ . "/../includes/fungsi.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$error = "";
$edit = ['idvendor' => 0, 'nama_vendor' => '', 'badan_hukum' => '1'];

// TAMBAH / UBAH
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = (int) ($_POST['idvendor'] ?? 0);
    $nama  = trim($_POST['nama_vendor']);
    $badan = ($_POST['badan_hukum'] ?? '1') === '1' ? '1' : '0';

    try {
        if ($id > 0) {
            $stmt = mysqli_prepare($conn,
                "UPDATE vendor SET nama_vendor=?, badan_hukum=? WHERE idvendor=?");
            mysqli_stmt_bind_param($stmt, "ssi", $nama, $badan, $id);
            $msg = "diubah";
        } else {
            $stmt = mysqli_prepare($conn,
                "INSERT INTO vendor (nama_vendor, badan_hukum, status) VALUES (?, ?, '1')");
            mysqli_stmt_bind_param($stmt, "ss", $nama, $badan);
            $msg = "ditambah";
        }
        mysqli_stmt_execute($stmt);
        header("Location: vendor.php?msg=$msg");
        exit;
    } catch (mysqli_sql_exception $e) {
        $error = $e->getMessage();
        $edit = ['idvendor' => $id, 'nama_vendor' => $nama, 'badan_hukum' => $badan];
    }
}

// AKTIFKAN / NONAKTIFKAN
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    mysqli_query($conn, "UPDATE vendor SET status = IF(status = '1', '0', '1') WHERE idvendor = $id");
    header("Location: vendor.php?msg=status");
    exit;
}

// ISI FORM SAAT EDIT
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $r = mysqli_query($conn, "SELECT * FROM vendor WHERE idvendor = $id");
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

$data = mysqli_query($conn, "SELECT * FROM v_vendor ORDER BY idvendor");

include __DIR__ . "/../includes/header.php";
?>

<h1>Data Vendor</h1>

<?php if ($pesan) { ?><p class="pesan-ok"><?= $pesan ?></p><?php } ?>
<?php if ($error) { ?><p class="pesan-err">Gagal: <?= htmlspecialchars($error) ?></p><?php } ?>

<form method="post" class="crud">
    <h3><?= $edit['idvendor'] ? 'Ubah Vendor' : 'Tambah Vendor' ?></h3>
    <input type="hidden" name="idvendor" value="<?= (int) $edit['idvendor'] ?>">

    <label>Nama Vendor</label>
    <input type="text" name="nama_vendor" maxlength="100" required value="<?= htmlspecialchars($edit['nama_vendor']) ?>">

    <label>Badan Hukum</label>
    <select name="badan_hukum">
        <option value="1" <?= $edit['badan_hukum'] == '1' ? 'selected' : '' ?>>Ya</option>
        <option value="0" <?= $edit['badan_hukum'] == '0' ? 'selected' : '' ?>>Tidak</option>
    </select>

    <p>
        <button type="submit" class="btn">Simpan</button>
        <?php if ($edit['idvendor']) { ?><a href="vendor.php" class="btn btn-abu">Batal</a><?php } ?>
    </p>
</form>

<table>
    <tr>
        <th>ID</th><th>Nama Vendor</th><th>Badan Hukum</th><th>Status</th><th>Aksi</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($data)) { ?>
    <tr>
        <td><?= $row['idvendor'] ?></td>
        <td><?= htmlspecialchars($row['nama_vendor']) ?></td>
        <td><?= $row['badan_hukum'] == '1' ? 'Ya' : 'Tidak' ?></td>
        <td><?= badge_status($row['status']) ?></td>
        <td>
            <a href="?edit=<?= $row['idvendor'] ?>" class="btn btn-kecil">Ubah</a>
            <a href="?toggle=<?= $row['idvendor'] ?>"
               class="btn btn-kecil <?= $row['status'] === 'Aktif' ? 'btn-merah' : '' ?>"
               onclick="return confirm('Ubah status vendor ini?')">
               <?= $row['status'] === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
            </a>
        </td>
    </tr>
    <?php } ?>
</table>

<?php include __DIR__ . "/../includes/footer.php"; ?>