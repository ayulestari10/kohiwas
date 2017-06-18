<?php  

class Buku_besar_m extends MY_Model{
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name']	= 'buku_besar';
		$this->data['primary_key']	= 'id_buku_besar';
	}

	public function get_previous_row($col, $id, $ket){
		$query = $this->db->query('select * from '.$this->data['table_name'].' having '.$col.' <  (select '.$col.' from '.$this->data['table_name'].' where '.$col.' = '.$id.' and ket= "'.$ket.'") order by '.$col.' desc limit 1');

		return $query->row();
	}

	public function get_prev_row_cond(){
		$query = $this->db->query();

		return $query->row();
	}

	public function total_debit_kredit($periode = [])
	{
		if (count($periode) <= 0)
			$sql 	= 'SELECT SUM(saldo_debit) AS total_saldo_debit, SUM(saldo_kredit) AS total_saldo_kredit FROM buku_besar';
		else
			$sql	= 'SELECT SUM(saldo_debit) AS total_saldo_debit, SUM(saldo_kredit) AS total_saldo_kredit FROM buku_besar WHERE tgl >= "' . $periode['min'] . '" AND tgl <= "' . $periode['max'] . '"';
		$query 	= $this->db->query($sql);
		return $query->row();
	}
}

?>