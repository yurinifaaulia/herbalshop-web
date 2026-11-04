use hni;

drop function if exists f_hitung_ppn;
delimiter $$
create function f_hitung_ppn(p_subtotal int)
returns int
deterministic
begin
	return round(p_subtotal * 0.11);
end $$
delimiter ;


drop procedure if exists sp_hitung_total_penjualan;
delimiter $$
create procedure sp_hitung_total_penjualan(in p_idpenjualan int)
begin
	declare v_subtotal int default 0;

	select ifnull(sum(subtotal), 0) into v_subtotal
	from detail_penjualan d
	where d.penjualan_idpenjualan = p_idpenjualan;

	update penjualan p
	set p.subtotal_nilai = v_subtotal, 
		p.ppn = f_hitung_ppn(v_subtotal),
		p.total_nilai = v_subtotal + f_hitung_ppn(v_subtotal)
	where p.idpenjualan = p_idpenjualan;
end $$
delimiter ;

drop trigger if exists trg_subtotal_detail_penjualan;
delimiter $$
create trigger trg_subtotal_detail_penjualan
before insert on detail_penjualan
for each row
begin
	set new.subtotal = new.harga_satuan * new.jumlah;
end $$
delimiter ;