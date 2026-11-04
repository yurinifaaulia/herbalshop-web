<?php
include __DIR__ . "/../config/koneksi.php";
include __DIR__ . "/../includes/fungsi.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$error = "";
$edit = ['idsatuan' => 0, 'nama_satuan' => ''];

// TAMBAH / UBAH
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int) ($_POST['idsatuan'] ?? 0);
    $nama = trim($_POST['nama_satuan']);

    try {
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "UPDATE satuan SET nama_satuan=? WHERE idsatuan=?");
            mysqli_stmt_bind_param($stmt, "si", $nama, $id);
            $msg = "diubah";
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO satuan (nama_satuan, status) VALUES (?, 1)");
            mysqli_stmt_bind_param($stmt, "s", $nama);
            $msg = "ditambah";
        }
        mysqli_stmt_execute($stmt);
        header("Location: satuan.php?msg=$msg");
        exit;
    } catch (mysqli_sql_exception $e) {
        $error = $e->getMessage();
        $edit = ['idsatuan' => $id, 'nama_satuan' => $nama];
    }
}

// AKTIFKAN / NONAKTIFKAN
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    mysqli_query($conn, "UPDATE satuan SET status = 1 - status WHERE idsatuan = $id");
    header("Location: satuan.php?msg=status");
    exit;
}

// ISI FORM SAAT EDIT
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $r = mysqli_query($conn, "SELECT * FROM satuan WHERE idsatuan = $id");
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

$data = mysqli_query($conn, "SELECT * FROM v_satuan ORDER BY idsatuan");

include __DIR__ . "/../includes/header.php";
?>

<h1>Data Satuan</h1>

<?php if ($pesan) { ?><p class="pesan-ok"><?= $pesan ?></p><?php } ?>
<?php if ($error) { ?><p class="pesan-err">Gagal: <?= htmlspecialchars($error) ?></p><?php } ?>

<form method="post" class="crud">
    <h3><?= $edit['idsatuan'] ? 'Ubah Satuan' : 'Tambah Satuan' ?></h3>
    <input type="hidden" name="idsatuan" value="<?= (int) $edit['idsatuan'] ?>">

    <label>Nama Satuan</label>
    <input type="text" name="nama_satuan" maxlength="45" required value="<?= htmlspecialchars($edit['nama_satuan'] ?? '') ?>">

    <p>
        <button type="submit" class="btn">Simpan</button>
        <?php if ($edit['idsatuan']) { ?><a href="satuan.php" class="btn btn-abu">Batal</a><?php } ?>
    </p>
</form>

<table>
    <tr>
        <th>ID</th><th>Nama Satuan</th><th>Status</th><th>Aksi</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($data)) { ?>
    <tr>
        <td><?= $row['idsatuan'] ?></td>
        <td><?= htmlspecialchars($row['nama_satuan'] ?? '') ?></td>
        <td><?= badge_status($row['status']) ?></td>
        <td>
            <a href="?edit=<?= $row['idsatuan'] ?>" class="btn btn-kecil">Ubah</a>
            <a href="?toggle=<?= $row['idsatuan'] ?>"
               class="btn btn-kecil <?= $row['status'] === 'Aktif' ? 'btn-merah' : '' ?>"
               onclick="return confirm('Ubah status satuan ini?')">
               <?= $row['status'] === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
            </a>
        </td>
    </tr>
    <?php } ?>
</table>

<?php include __DIR__ . "/../includes/footer.php"; ?>