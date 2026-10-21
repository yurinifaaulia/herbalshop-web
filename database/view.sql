USE hni;

-- SIMPLE VIEW: barang aktif beserta nama satuannya
CREATE OR REPLACE VIEW v_barang AS
SELECT b.idbarang, b.nama, s.nama_satuan AS satuan, b.harga
FROM barang b
JOIN satuan s ON b.idsatuan = s.idsatuan
WHERE b.status = 1;

-- SIMPLE VIEW: vendor aktif
CREATE OR REPLACE VIEW v_vendor_aktif AS
SELECT idvendor, nama_vendor, badan_hukum
FROM vendor
WHERE status = '1';

-- COMPLEX VIEW: total pengadaan per vendor
CREATE OR REPLACE VIEW v_total_pengadaan_vendor AS
SELECT v.nama_vendor,
       COUNT(p.idpengadaan) AS jumlah_pengadaan,
       IFNULL(SUM(p.total_nilai), 0) AS total_nilai
FROM vendor v
LEFT JOIN pengadaan p ON p.vendor_idvendor = v.idvendor
GROUP BY v.nama_vendor;

-- VIEW: role
CREATE OR REPLACE VIEW v_role AS
SELECT idrole, nama_role
FROM role;

-- VIEW: satuan (status angka diubah jadi teks)
CREATE OR REPLACE VIEW v_satuan AS
SELECT idsatuan, nama_satuan,
       IF(status = 1, 'Aktif', 'Nonaktif') AS status
FROM satuan;

-- VIEW: user (kolom password sengaja TIDAK ditampilkan)
CREATE OR REPLACE VIEW v_user AS
SELECT u.iduser, u.username, r.nama_role AS role
FROM user u
JOIN role r ON u.idrole = r.idrole;

-- VIEW: margin penjualan
CREATE OR REPLACE VIEW v_margin_penjualan AS
SELECT m.idmargin_penjualan, m.persen,
       IF(m.status = 1, 'Aktif', 'Nonaktif') AS status,
       u.username AS dibuat_oleh,
       m.created_at, m.updated_at
FROM margin_penjualan m
JOIN user u ON m.iduser = u.iduser;