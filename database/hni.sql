create database if not exists hni;
use hni;

create table role ( 
idrole int not null auto_increment, 
nama_role varchar(100) not null,
primary key (idrole)
); 


create table satuan (
 idsatuan int not null auto_increment,
 nama_satuan varchar(45),
 status tinyint not null,
 primary key (idsatuan)
 );
 
create table user( 
iduser int not null auto_increment, 
username varchar(45) not null, 
password varchar(100) not null, 
idrole int not null, 
primary key (iduser),
constraint userrole_fk
foreign key (idrole) references role(idrole) 
on update cascade
on delete cascade
); 

create table barang ( 
idbarang int not null auto_increment, 
jenis char(1) not null, 
nama varchar(45) not null, 
idsatuan int not null, 
status tinyint not null default 1, 
harga int not null default 0,
primary key (idbarang),  
constraint satuanbarang_fk  
foreign key (idsatuan) references satuan(idsatuan)
);

create table vendor( 
idvendor int not null auto_increment, 
nama_vendor varchar(100) not null, 
badan_hukum char(1) not null, 
status char(1) not null default '1',
primary key (idvendor)
); 

create table margin_penjualan( 
idmargin_penjualan int not null auto_increment, 
created_at timestamp not null, 
persen double not null default 0,
status tinyint not null, 
iduser int not null, 
updated_at datetime not null, 
primary key (idmargin_penjualan),
constraint marginpenjualan_user_fk 
foreign key (iduser) references user(iduser) 
); 

create table kartu_stok( 
idkartu_stok bigint not null auto_increment, 
jenis_transaksi char(1) not null, 
masuk int not null, 
keluar int not null, 
stock int not null, 
created_at timestamp not null, 
idtransaksi int not null, 
idbarang int not null, 
primary key (idkartu_stok),
constraint kartustok_barang_fk
foreign key (idbarang) references barang(idbarang) 
); 

create table pengadaan( 
idpengadaan bigint not null auto_increment, 
timestamp timestamp not null, 
user_iduser int not null, 
status char(1) not null, 
vendor_idvendor int not null, 
subtotal_nilai int not null, 
ppn int, 
total_nilai int, 
primary key (idpengadaan),
constraint user_pengadaan_fk
foreign key (user_iduser) references user(iduser), 
constraint vendor_pengadaan_fk
foreign key (vendor_idvendor) references vendor(idvendor) 
);

create table detail_pengadaan( 
iddetail_pengadaan bigint auto_increment, 
harga_satuan int not null, 
jumlah int not null, 
sub_total int not null, 
idbarang int not null, 
idpengadaan bigint not null, 
primary key (iddetail_pengadaan),
constraint barang_detailpengadaan_fk
foreign key (idbarang) references barang(idbarang), 
constraint fk_pengadaan_detailpengadaan 
foreign key (idpengadaan) references pengadaan(idpengadaan) 
); 

create table penerimaan( 
idpenerimaan bigint not null auto_increment, 
created_at timestamp not null, 
status char(1), 
idpengadaan bigint not null, 
iduser int not null,  
primary key (idpenerimaan),
constraint pengadaan_penerimaan_fk
foreign key (idpengadaan) references pengadaan(idpengadaan), 
constraint user_penerimaan_fk
foreign key (iduser) references user(iduser) 
); 

create table detail_penerimaan( 
iddetail_penerimaan bigint not null auto_increment, 
idpenerimaan bigint not null, 
barang_idbarang int not null, 
jumlah_terima int, 
harga_satuan_terima int, 
sub_total_terima int, 
primary key (iddetail_penerimaan),
constraint penerimaan_detailpenerimaan_fk
foreign key (idpenerimaan) references penerimaan(idpenerimaan), 
constraint barang_detailpenerimaan_fk  
foreign key (barang_idbarang) references barang(idbarang) 
); 

create table penjualan( 
idpenjualan int not null auto_increment, 
created_at timestamp, 
subtotal_nilai int, 
ppn int, 
total_nilai int, 
iduser int not null, 
idmargin_penjualan int not null, 
primary key (idpenjualan),
constraint user_penjualan_fk
foreign key (iduser) references user(iduser), 
constraint marginpenjualan_penjualan_fk
foreign key (idmargin_penjualan) references 
margin_penjualan(idmargin_penjualan) 
);

create table detail_penjualan( 
iddetail_penjualan bigint not null auto_increment, 
harga_satuan int not null, 
jumlah int not null, 
subtotal int, 
penjualan_idpenjualan int not null, 
idbarang int not null, 
primary key (iddetail_penjualan),
constraint penjualan_detailpenjualan_fk
foreign key (penjualan_idpenjualan) references    
penjualan(idpenjualan), 
constraint barang_detailpenjualan_fk 
foreign key (idbarang) references barang(idbarang) 
); 

create table `retur`( 
idretur bigint not null auto_increment, 
created_at timestamp, 
idpenerimaan bigint not null, 
iduser int not null, 
primary key (idretur),
constraint penerimaan_retur_fk 
foreign key (idpenerimaan) references penerimaan(idpenerimaan), 
constraint user_retur_fk 
foreign key (iduser) references user(iduser) 
); 

create table detail_retur( 
iddetail_retur int not null auto_increment,  
jumlah int not null, 
alasan varchar(200), 
idretur bigint not null, 
iddetail_penerimaan bigint not null,
primary key (iddetail_retur), 
constraint retur_detailretur_fk 
foreign key (idretur) references `retur`(idretur), 
constraint detailpenerimaan_detailretur_fk
foreign key (iddetail_penerimaan) references 
detail_penerimaan(iddetail_penerimaan) 
); 


insert into `role` (idrole, nama_role) values
(1, 'Admin'),
(2, 'Staf Gudang'),
(3, 'Kasir');

select * from `role`;


insert into satuan (idsatuan, nama_satuan, status) values
(1, 'Botol',  1),
(2, 'Box',    1),
(3, 'Toples', 1),
(4, 'Sachet', 1),
(5, 'Strip',  0);
 

insert into `user` (iduser, username, password, idrole) values
(1, 'Latifatul Akhfa',  "lalalala",  1),
(2, 'Rifky Ramadhan',   "turkey06", 2),
(3, 'Hawaul Maftuhati',  "hirono07",  3),
(4, 'Azro Nurrahman',  "fkunram25",  3);
 


insert into barang (idbarang, jenis, nama, idsatuan, status, harga) values
(1, 'O', 'Habbatussauda Kapsul 100 Kapsul',   1, 1, 45000),
(2, 'O', 'Minyak Habbatussauda 60 ml',        1, 1, 38000),
(3, 'S', 'Propolis Tetes 30 ml',              1, 1, 55000),
(4, 'M', 'Madu Hutan Murni 650 gr',           1, 1, 70000),
(5, 'M', 'Sari Kurma 500 ml',                 1, 1, 40000),
(6, 'S', 'Bee Pollen 100 gr',                 3, 1, 60000),
(7, 'O', 'Minyak Zaitun Extra Virgin 250 ml', 1, 1, 65000),
(8, 'M', 'Jahe Merah Instan (10 sachet)',     2, 1, 25000);
 

insert into vendor (idvendor, nama_vendor, badan_hukum, status) values
(1, 'PT Bumi Herbal Nusantara',        '1', '1'),
(2, 'CV Lebah Madu Sejahtera',         '1', '1'),
(3, 'UD Kurma Berkah',                 '0', '1'),
(4, 'PT Zaitun Mediterania Indonesia', '1', '1'),
(5, 'CV Rempah Jawa Timur',            '1', '0');
 

