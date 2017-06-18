<?php  

class Jurnal_umum_m extends MY_Model{
	public function __construct()
	{
		parent::__construct();
		
		$this->data['table_name']	= 'jurnal_umum';
		$this->data['primary_key']	= 'id_jurnal';
	}

	public function total_debit_kredit($periode = [])
	{
		if (count($periode) <= 0)
			$sql 	= 'SELECT SUM(debit) AS total_debit, SUM(kredit) AS total_kredit FROM jurnal_umum';
		else
			$sql	= 'SELECT SUM(debit) AS total_debit, SUM(kredit) AS total_kredit FROM jurnal_umum WHERE tgl >= "' . $periode['min'] . '" AND tgl <= "' . $periode['max'] . '"';
		$query 	= $this->db->query($sql);
		return $query->row();
	}
}

?>