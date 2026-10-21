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