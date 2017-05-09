<?php 

class Bahan_baku_m extends MY_Model 
{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name'] 	= 'bahan_baku';
		$this->data['primary_key']	= 'id_bahan_baku';
	}

	public function stok_minimum($id_bahan_baku = 0)
	{
		date_default_timezone_set("Asia/Jakarta");
		$sql = 'SELECT * FROM bahan_baku WHERE stok - (
			SELECT SUM(jumlah_permintaan) FROM detail_permintaan WHERE detail_permintaan.id_bahan_baku = bahan_baku.id_bahan_baku AND (SELECT '.time().' < UNIX_TIMESTAMP(permintaan_bahan_baku.batas_waktu) FROM permintaan_bahan_baku WHERE detail_permintaan.id_permintaan = permintaan_bahan_baku.id_permintaan) GROUP BY detail_permintaan.id_bahan_baku  
			) < stok_min';	
		$query = $this->db->query($sql);
		return $query->result();
	}
}